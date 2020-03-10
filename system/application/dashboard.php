<?php
@session_start();
//include_once("adminsecurity.php");
require_once($_SESSION['framework_path']."adminsecurity.php");
$cases	=	$AdminDAO->getrows(
								"cases c,attorneys_cases ac"
								,
								"								
								c.id as id,
								c.uid,
								c.plaintiff,
								c.defendant,
								case_title,
								case_number,
								jurisdiction,
								county_name,
								judge_name,
								date_filed 
								",
								"ac.attorney_id  = :attorney_id AND
								c.id = ac.case_id
								",
								array(
										":attorney_id"=>$_SESSION['addressbookid']
									 )
							  );
//dump($cases);
?>

<?php /*?><div id="screenfrmdiv" style="display: block;">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-12">
                <div class="col-lg-12 text-center welcome-message">
                    <h2> Welcome to <strong>Jeff Attorneys</strong> </h2>
                    <p> Special <strong>Admin Theme</strong> for medium and large web applications with very clean and
                        aesthetic style and feel. </p>
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
                    	
                        <div class="col-md-offset-4 col-md-4">
                        <h1 align="center"><strong>Main Menu</strong></h1>
                            <ul class="unstyled ul-cls">
                                <?php
								if(sizeof($cases) > 0)
								{
								?>
                                <li><a href="javascript:;" onclick="javascript: selecttab('44_tab','cases.php','44');">Work on Cases.</a></li>
                                <?php
								}
                                else
                                {
								?>
                                 <li><a href="javascript:;" onclick="javascript: selecttab('46_tab','case.php','46');">Work on Cases.</a></li>
                                <?php
                               	}
                                ?>
                                <li><a href="javascript:;" onclick="javascript: selecttab('8_tab','profile.php','8');">Edit My Profile.</a></li>
                                <li><a href="javascript:;" onclick="javascript: selecttab('44_tab','cases.php','44');">Logout.</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><?php */?>
<script>
$(document).ready(function(){
	<?php
	if(sizeof($cases) > 0)
	{
	?>
	selecttab('44_tab','cases.php','44');
	<?php
	}
	else
	{
	?>
	selecttab('46_tab','case.php','46');
	<?php
	}
	?>
});
function loadpage(url)
{
	$.get(url).done(function(resp){$('.wrapper').html(resp);});
}
</script>