<?php
  class KnowledgeBaseController extends BaseController {

    const CONTEXT_SIDEBAR = 'sidebar';
    const CONTEXT_INDEX   = 'index';

    function index() { global $kbModel, $smarty, $currentUser, $couponsModel;
      $coupon = @$_GET['coupon'];
      
      if ( !$currentUser && !$couponsModel->findActiveCoupon($coupon) ) {
        return HttpResponse::unauthorized();
      }

      $smarty->assign([
        'kb' => [
          [
            'id'    => KB::AREA_DEFINITIONS,
            'name'  => 'Definitions',
            'items' => $kbModel->getByAreaId(KB::AREA_DEFINITIONS)
          ],
          [
            'id'    => KB::AREA_OBJECTIONS,
            'name'  => 'Objections',
            'items' => $kbModel->getByAreaId(KB::AREA_OBJECTIONS)
          ],
          [
            'id'     => KB::AREA_OBJECTION_KILLERS,
            'name'   => 'Objection Killers',
            'items'  => $kbModel->getByAreaId(KB::AREA_OBJECTION_KILLERS)
          ],
        ],
        'loggedIn' => $currentUser,
        'coupon'   => $coupon
      ]);
      $smarty->display('knowledge_base/show.tpl');
      die();
    }
    
  }