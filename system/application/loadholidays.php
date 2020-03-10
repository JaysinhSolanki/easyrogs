<?php
@session_start();
require_once("adminsecurity.php");
$year		=	$_GET['year'];
$holidays	=	$AdminDAO->getrows('holidays',"*","year = '$year'");
foreach($holidays as $holiday)
{
?>
<div class="form-group">
    <label class=" col-sm-2 control-label">Date: <span class="redstar" style="color:#F00" title="This field is compulsory"></span></label>
    <div class="col-sm-8">
        <input type="text" name="holidays[]"  placeholder="Select Dates"  min="1" class="form-control m-b datepicker">
    </div>
</div>
<?php
}
?>
<script>
$(function () 
{
 $('.datepicker').datepicker({format: 'yyyy-mm-dd',autoclose:true});
});
</script>