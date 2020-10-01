<?php
ob_start();
@session_start();
$domain	=	"";
require_once("../bootstrap.php");
if(!isset($_SESSION['addressbookid']))
{
	header("Location: ".DOMAIN."userlogin.php");
	exit;
}
$_SESSION['system_path']	=	SYSTEMPATH;//"d:/wamp/www/gumption/michaelwuest/amnis/system4/";
//$_SESSION['system_path']	=	"/home/amniscash/public_html/test/system4/";
$_SESSION['admin_path']		=	$_SESSION['framework_path'];
$_SESSION['includes_path']	=	"{$_SESSION['system_path']}includes/";
require_once("{$_SESSION['admin_path']}head.php");

?>
<!-- Simple splash screen-->
<!-- Header -->
<?php
//echo $_SESSION['framework_path'];
require_once($_SESSION['framework_path']."header.php");
?>
<!-- Navigation -->
<?php
//echo "<h1>LEFT MENU</h1>";
//echo __LINE__;
//require_once($_SESSION['framework_path']."leftmenu.php");
include_once(SYSTEMPATH.'body.php');
?>
<div id="overlay" style="display:none"><img src="<?= ASSETS_URL ?>images/ownageLoader/loader4.gif" id="imageloaderid"></div>
<!-- Main Wrapper -->
<div id="wrapper" class="loading" style="background-color:#f7f9fa !important;">
<?php
require_once($_SESSION['framework_path']."footer.php");
ob_end_flush();
?>
<div class="modal fade" id="general_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document" id="general-width">
    <div class="modal-content">
      <div class="modal-header"  style="padding:13px !important">
        <h5 class="modal-title text-center" id="general_modal_title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -50px;font-size: 35px;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="load_general_modal_content">
       <div class="text-center"> Loading...</div>
      </div>
    </div>
  </div>
</div>
<style>
.w-900 {
	width:900px !important
}
</style>
<script>
  jQuery( $ => {
  // handle redirect actions

  const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        switch( urlParams.get('notify') ) {
          case 'granted-join-request': toastr.info('Request Granted successfully!'); break;
          case 'denied-join-request':  toastr.info('Request Denied successfully.'); break;
        }

        //  loadsection('center-column','managearrivals.php');
        selecttab('7_tab','dashboard.php');
        //window.history.forward(1);
  } );
</script>
