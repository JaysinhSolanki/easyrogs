<?php
@session_start();
include_once("../includes/classes/adminsecurity.php");
$filterid		=	$_GET['id'];
$amount			=	$_GET['amount'];
$discounttype	=	$_GET['discounttype'];
?>
<div class="form-group">
<label class="col-sm-2 control-label">Filter By <?php if($filterid	==	1){echo "Brand";}else if($filterid	==	2){echo "Category";}else{ echo "Product Group";}?>
<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
<div class="col-sm-8">
<select id="optid" name="optid" class="form-control m-b" onchange="products(<?php echo $filterid; ?>,this.value,<?php echo $amount; ?>,<?php echo $discounttype; ?>)">
<?php
if($filterid	==	1)
{
	?>
    <option value="">Select Brand</option>
    <?php
	$filters	=	$AdminDAO->getrows('shop_tblbrand',"*");
	foreach($filters as $filter)
	{
	?>
		<option value="<?php echo $filter['pkbrandid']; ?>"><?php echo  $filter['brandname']; ?></option>
	<?php
	}
}
else if($filterid	==	2)
{
	?>
    <option value="">Select Category</option>
    <?php
	$filters	=	$AdminDAO->getrows('shop_tblcategory',"*");
	foreach($filters as $filter)
	{
	?>
		<option value="<?php echo  $filter['pkcategoryid']; ?>"><?php echo  $filter['categoryname']; ?></option>
	<?php
	}	
}
else
{
	?>
    <option value="">Select Product Group</option>
    <?php
	$filters	=	$AdminDAO->getrows('shop_tblproductgroup',"*");	
	foreach($filters as $filter)
	{
	?>
		<option value="<?php echo  $filter['pkproductgroupid']; ?>"><?php echo  $filter['productgroup']; ?></option>
	<?php
	}
}
?>
</select>
</div>
</div>
<script>

function products(filterid,optid,amount,discounttype)
{
	$("#loadproducts").show();
	$("#loadproducts").load("loadproduct.php?filterid="+filterid+"&optid="+optid+"&amount="+amount+"&discounttype="+discounttype);
}
</script>