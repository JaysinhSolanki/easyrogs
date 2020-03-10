<?php
require_once("adminsecurity.php");
$case_id		=	$_POST['case_id'];

$parties		=	$AdminDAO->getrows("clients","*", "case_id = :case_id ", array(":case_id"=>$case_id));
$servicelists	=	$AdminDAO->getrows("attorney","*", "case_id = :case_id  AND attorney_type = :attorney_type", array(":case_id"=>$case_id,":attorney_type"=>2));

if(sizeof($parties) > 0 /*&& sizeof($servicelists) == 0*/)
{
?> 
<div class="row">
<div class="col-md-11" style="text-align:right">
<a href="javascript:;"  class="pull-right btn btn-success btn-small" onclick="loadServiceListModal('<?php echo $case_id; ?>')" style="margin-bottom:10px !important"><i class="fa fa-plus"></i> Add New</a>
</div>
<div class="col-md-1"></div>
</div>    
<?php
}
?>

<div class="row">  
    <div class="col-md-1"></div>
    <div class="col-md-2">
    <label>Service List</label>
    </div>
    <?php
   if(sizeof($parties) > 0)
    {
    ?>
    <div class="col-md-8" id="loadattoneys2">
    
    </div>
    <?php
    }
    else
    {
    ?>
    <div class="col-md-8" style="margin-top: 7px;">
        <i>(Parties must be added before Service List is created.)</i>
    </div>
    <?php
    }
    ?>
    <div class="col-md-1"></div>
</div>
      
<script>
$( document ).ready(function() 
{
loadAttoneysFunction(<?php echo $case_id; ?>,2,"loadattoneys2");
});
</script>