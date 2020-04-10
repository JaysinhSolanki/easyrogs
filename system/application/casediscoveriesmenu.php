<?php
@session_start();
include_once("adminsecurity.php");
$uid	=	$_GET['uid'];
$cases	=	$AdminDAO->getrows("cases","*","uid  = :uid ",array(":uid"=>$uid));
$case	=	$cases[0];
?>

<div id="screenfrmdiv" style="display: block;">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-12">
                <div class="col-lg-12 text-center welcome-message">
                    <h2> <?php echo $case['case_title']." (".$case['case_number'].")";?> Discoveries </h2>
                    
                </div>
            </div>
            <style>
			.ul-cls {
					font-size:25px !important;
				   }

			</style>
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body" style="display: block;">
                    	<h1 align="center"><strong>What would you like to do?</strong></h1>
                        <div class="col-md-offset-4 col-md-4">
                            <ul class="unstyled ul-cls">
                                <li><a href="javascript:;" onclick="javascript: selecttab('47_tab','discovery.php?pid=<?php echo $case['id'];?>','47');">Create new Discovery.</a></li>
                                <li><a href="javascript:;" onclick="javascript: selecttab('45_tab','discoveries.php?pid=<?php echo $case['id'];?>','45');">Edit existing Discovery.</a></li>
                                <li><a href="javascript:;" onclick="javascript: selecttab('45_tab','discoveries.php?pid=<?php echo $case['id'];?>','45');">Respond to Discovery.</a></li>
                                <li><a href="javascript:;" onclick="javascript: selecttab('45_tab','get-cases.php','45');">Delete Case.</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function loadpage(url)
{
	$.get(url).done(function(resp){$('.wrapper').html(resp);});
}
</script>