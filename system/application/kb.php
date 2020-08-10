<?php
require_once __DIR__ .'/../bootstrap.php';
require_once __DIR__ .'/kb_common.php';

global $logger; 

$areaId    = @$_REQUEST['area'] ?: '0';
$sectionId = filter_var( @$_REQUEST['section_filter'] ?: "", FILTER_SANITIZE_STRING );

$kbSections = $AdminDAO->getrows(	"kb_section", "*",
                                    $kb_section_id ? "id = :section_id" : '', 
                                    array( ":section_id" => $sectionId ) );
if( empty($sectionId) ) {
    $kbItems = $AdminDAO->getrows(	"kb", "kb.*",
                                    "area_id = :area_id", 
                                    [":area_id" => $areaId] );
} else {
    $kbItems = $AdminDAO->getrows(	"kb, kb_section, kb_kb_section", "kb.*,kb_section.id as section_id",
                                    "area_id = :area_id 
                                    AND kb.id = kb_id 
                                    AND section_id = :section_id 
                                    AND kb_section.id = section_id",
                                    [":area_id" => $areaId, 
                                     ":section_id" => $sectionId] );
}
//$logger->browser_log($kbItems, "item.count:".count($kbItems).", section_id:".$sectionId );

$smarty->assign([
    'items'      => $kbItems,
    'section'    => $sectionId ? $kbSections[0] : "",
    'area_id'    => $areaId,
]);

switch( $areaId ) {
    case KB_AREA_OBJECTION_TEMPLATES:
        $smarty->assign([
            'dockSide'  => DOCK_SIDE,
            'fn'        => 'insertObjectionTemplateHere',
        ]);
        $body = $smarty->fetch('kb-sidebar.tpl');
        break;
    case KB_AREA_OBJECTION_KILLERS:
        $body = $smarty->fetch('kb-modal.tpl');
        break;
    default:
        // $logger->browser_log('', "UNKNOWN area=$areaId");
}
echo $body;
?>
<!-- KB -->
