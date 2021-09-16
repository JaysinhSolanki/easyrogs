<?php
require_once __DIR__ .'/../bootstrap.php';
require_once __DIR__ .'/kb_common.php';

global $logger;

$context   = @$_REQUEST['context'] ?: KnowledgeBaseController::CONTEXT_SIDEBAR; // default to sidebar, backward comp
$areaId    = @$_REQUEST['area']   ?: '0';
$sectionId = filter_var( @$_REQUEST['section_filter'] ?: "", FILTER_SANITIZE_STRING );

if ($context == KnowledgeBaseController::CONTEXT_INDEX) {
  $kbController = new KnowledgeBaseController();
  $kbController->index();
}

require_once __DIR__ . "/adminsecurity.php";

$kbSections = [ 'Form Interrogatories - General',
                'Form Interrogatories - Employment',
                'Special Interrogatories',
                'Requests for Admission',
                'Requests for Production of Documents',
                'Commonly used but invalid objections' ];

switch( $sectionId ) {
    case Discovery::FORM_FED_FROGS :
    case Discovery::FORM_CA_FROGS :
        $sectionFilter = "AND frogs > 0"; break;
    case Discovery::FORM_FED_FROGSE :
    case Discovery::FORM_CA_FROGSE :
        $sectionFilter = "AND frogse > 0"; break;
    case Discovery::FORM_FED_SROGS :
    case Discovery::FORM_CA_SROGS :
        $sectionFilter = "AND srogs > 0"; break;
    case Discovery::FORM_FED_RFAS :
    case Discovery::FORM_CA_RFAS :
        $sectionFilter = "AND rfas > 0"; break;
    case Discovery::FORM_FED_RPDS :
    case Discovery::FORM_CA_RPDS :
        $sectionFilter = "AND rpds > 0"; break;
    default :
        $sectionFilter = "";
}
//$logger->browser_log("areaId: $areaId, sectionId: $sectionId, filter: $sectionFilter");

$kbItems = $AdminDAO->getrows(	"kb", "*",
                                    "area_id = :area_id
                                    $sectionFilter",
                                    [":area_id" => $areaId] );

array_walk( $kbItems, function(&$item,$key) {
    if( !$item['explanation'] ) {
        $item['explanation'] = $item['solution'];
    } else if( !$item['solution'] ) {
        $item['solution'] = $item['explanation'];
    }
} );

//$logger->browser_log($kbItems, "item.count:".count($kbItems).", section_id:".$sectionId );
$smarty->assign([
    'items'      => $kbItems,
    'section'    => $sectionId ? $kbSections[$sectionId-1] : "",
    'area_id'    => $areaId,
]);

switch( $areaId ) {
    case KB_AREA_OBJECTION_TEMPLATES:
        $smarty->assign([
            'dockSide'  => DOCK_SIDE,
            'fn'        => 'insertTemplateHere',
            'itemType'  => 'objection',
        ]);
        $body = $smarty->fetch('kb-sidebar.tpl');
        break;
    case KB_AREA_DEFINITIONS:
        $smarty->assign([
            'dockSide'  => DOCK_SIDE,
            'fn'        => 'insertTemplateHere',
            'itemType'  => 'definition',
        ]);
        $body = $smarty->fetch('kb-sidebar.tpl');
        break;
    case KB_AREA_OBJECTION_KILLERS:
        $smarty->assign([
            'dockSide'  => DOCK_SIDE,
            'fn'        => 'insertTemplateHere',
            'itemType'  => 'objection-killer',
        ]);
        $body = $smarty->fetch('kb-sidebar.tpl');
        break;
    default:
        // $logger->browser_log('', "UNKNOWN area=$areaId");
}
echo $body;
?>
<!-- KB -->
