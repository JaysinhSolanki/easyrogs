<?php
@session_start();
require_once("common.php");

//unset($_SESSION['screenids'][10]);
//dump($_SESSION);
//echo $screenid;
/******************DELETE*****************************/
$adbookid			=	$_SESSION['addressbookid'];
$sessiongroupid		=	$_SESSION['groupid'];
/**/
//require_once("notificationsettings.php");
/**/
$oper	=	$_GET['oper'];
$param	=	$_GET['param'];
if($oper=='del' || $oper=='update')
{
	$ids		=	trim($_GET['id'],",");
	require_once("{$_SESSION['admin_path']}$deletefilename");
}
$dbgridflag	=	1;
//if($dbgridflag	==	1)
//{
?>
<div id="sugrid"></div>
<div id="maindiv" style="padding:0px 15px 0px 15px" class="row">
<?php
//require_once("commoncustom.php");

//echo $query;
?>
<div id="loading" class="loading" style="display:none; position:absolute; color:#F00;"></div>
<div class="col-lg-12" style="">
<?php 
//dump($labels);
//dump($fields);
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionsarray,$orderby,$customfilters);
?>
<script type="text/javascript">
$(document).ready(function() {
  setTimeout(function(){$(".js-select2").select2();},500);
});
$(document).ready(function(){
	<?php
	if($delmsg != "")
	{
		?>
		msg('<?php echo $delmsg;?>');
		<?php
	}
	?>
	$('html, body').animate({ scrollTop: 0 }, 0);
})
</script>
</div>
<?php
//}

?>