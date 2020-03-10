<?php
@session_start();
require_once("adminsecurity.php");
include_once($_SESSION['library_path']."helper.php");
if($_POST['served'] != "")
{
?>

<form name="formduedatecalculation" id="formduedatecalculation">
<div class="form-group">
    <label for="recipient-name" class="col-form-label">
    Served Date: 
    </label>  
	<?php echo $_POST['served']; ?>
    <input type="hidden"  name="servereddate" value="<?php echo $_POST['served']; ?>"/>
</div>
<div class="form-group">
    <label for="recipient-name" class="col-form-label">Extension Days:</label>
    <select class="form-control" id="extensiondays" name="extensiondays">
      <option value="1">0 Days – Personal</option> 
      <option value="2" selected="selected">2 Court days – Express Mail, overnight, or electronic</option>
      <option value="3">5 Calendar days – U.S. Mail </option>
      <option value="4">10 Calendar days – Service outside California</option>
      <option value="5">20 Calendar days – Service outside the U.S.</option>
    </select>
</div>

<div class="form-group">
	<p><i class="fa fa-university" aria-hidden="true"></i> Code Civ.Proc., &sect;&sect; 1013 <?php  echo instruction(14) ?>, 1010.6 <?php  echo instruction(15) ?>, 2016.060 <?php  echo instruction(12) ?>.</p>
</div>
<div class="form-group">
	<button type="button" class="btn btn-primary" onclick="calculatedduedateaction()"><i class="fa fa-calculator"></i> Calculate</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal" style="float:right"><i class="fa fa-close"></i> Cancel</button>
</div>
</form>
<?php
}
else
{
?>
<div class="form-group">
    <div class="alert alert-danger text-center" role="alert">
    	Please select served date.
    </div>
</div>
<div class="form-group  text-right">
	<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
</div>
<?php
}
?>