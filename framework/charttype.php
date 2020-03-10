<?php
require_once("adminsecurity.php");
$id	=	$_GET['id']; 
if($id != '-1')
{
	$charts			=	$AdminDAO->getrows('system_charttype',"*","pkcharttypeid	=	'$id'");
	$chart			=	$charts[0];
}
$charttypes	=	$AdminDAO->getrows('system_charttype',"*","status	=	'1'");
/****************************************************************************/
?>
<script src="js/jquery.form.js"></script>
<script type="text/javascript">
$(document).ready(function(){
$('html, body').animate({ scrollTop: 0 }, 0);
})
</script>
<div id="screenfrmdiv" style="display: block;">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-heading">
                <?php 
			if($id=='-1')
			{echo "Add Chart Type";}
			else
			{echo "Edit Chart Type>>&nbsp;".$chart['charttypename'];}
			?>
            </div>
            <div class="panel-body">
                <form  name="charttypeform" id="charttypeform" class="form form-horizontal" method="post">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Name<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                        <div class="col-sm-8">
                            <input type="text"  name="charttypename" id="charttypename" placeholder="Name" value="<?php echo  $chart['charttypename'];?>" class="form-control m-b"  >
                        </div>
                        
                    </div>
                    
                    <input type="hidden" name="id" value ="<?php echo $id;?>" />
                    <?php
					buttons('charttypeaction.php','charttypeform','maindiv','main.php?pkscreenid=20',0)
					?>
                </form>
            </div>
        </div>
    </div>
</div>
