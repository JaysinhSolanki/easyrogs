<?php
require_once("adminsecurity.php");
$languagelabels		=   getlabel(10);
$sessiongroupid		=	$_SESSION['groupid'];
$ab_id				=	$_SESSION['addressbookid'];
$logincheckuser		=	$AdminDAO->getrows('system_addressbook,system_groups',"pkaddressbookid","fkgroupid	=	3 AND pkaddressbookid = '$ab_id'");
$issuperadmin		=	$logincheckuser[0]['pkaddressbookid'];

$id	=	$_GET['id'];
if(!$id)
{
	$id	=	$_SESSION['addressbookid'];
}


if($id)
{   
	
	$user			=	$AdminDAO->getrows('system_addressbook LEFT JOIN system_state ON ( fkstateid = pkstateid ) LEFT JOIN system_city ON ( fkcityid = pkcityid )',"*"," pkaddressbookid =	'$id' ");
	
	$firstname		=	$user[0]['firstname']; 
	$lastname		=	$user[0]['lastname'];
	$username		=	$user[0]['username'];
	$email			=	$user[0]['email'];
	$semail			=	$user[0]['secondaryemail'];
	$password		=	$user[0]['password']; 
	$phone			=	$user[0]['phone']; 
	$mobile			=	$user[0]['mobile'];
	$fax			=	$user[0]['fax'];
	$fkgroupid		=	$user[0]['fkgroupid'];
	$countryid		=	$user[0]['fkcountryid'];
	$stateid		=	$user[0]['fkstateid'];
	$cityid			=	$user[0]['fkcityid']; 
	$zip			=	$user[0]['zip'];
	$street			=	$user[0]['street']; 
	$address		=	$user[0]['address'];
	$description	=	$user[0]['description'];
	$isblocked		=	$user[0]['isblocked'];
	$chartsvalues	=	$user[0]['chart'];
	$chartsvalue	=	explode(',',$chartsvalues);

	$states		=	$AdminDAO->getrows('system_state','*'," fkcountryid = '$countryid' ",'statename', 'ASC');
	$citystateid=	$_GET['state'];
	$cities		=	$AdminDAO->getrows('system_city','*'," fkstateid = '$stateid' ",'cityname', 'ASC');

}

$countries		=	$AdminDAO->getrows('system_country','*'," 1 ",'countryname', 'ASC');



/****************************************************************************/
?>
<script src="js/jquery.form.js"></script>
<script language="javascript" type="text/javascript">
function getstates(country)
{
	$('#states').load('loadstates.php?fkcountryid='+country);
	//alert($('#fkstateid').val());
	$('#cities').load('loadcities.php');
}
function getcities(state)
{
    $('#cities').load('loadcities.php?fkstateid='+state);
}
$(document).ready(function(){
});
function changecityftn(id)
{
	$('#cities').load("loadcities.php?fkstateid="+id);
}
</script>

<div id="userdiv">
<br />

<div id="adduserdiv">

<div class="col-lg-12">
    <div class="hpanel">
        <div class="panel-heading">
            <?php 
			if($id=='-1')
			{echo "Add User";}
			else
			{echo "Edit User >> $firstname $lastname";}
			?>	
        </div>
        <div class="panel-body">
            <form enctype="multipart/form-data" name="userform" id="userform" style="width:920px;" class="form">

<input type="hidden" name="store" id="store" value="1" />
<input type="hidden" name="issuperadmin" id="issuperadmin" value="<?php echo $issuperadmin; ?>" />


<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $languagelabels['firstname'];?> <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-10">
    	<input type="text" placeholder="<?php echo $languagelabels['firstname'];?>:" class="form-control m-b"  name="fname" id="fname" value="<?php echo $firstname; ?>">
    </div>
</div>


<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $languagelabels['lastname'];?> <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-10">
    	<input type="text" placeholder="<?php echo $languagelabels['lastname'];?>" class="form-control m-b"  name="lname" id="lname" value="<?php echo $lastname; ?>">
    </div>
</div>


<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $languagelabels['username'];?> <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-10">
    	<input type="text" placeholder="<?php echo $languagelabels['username'];?>" class="form-control m-b"  name="username" id="username" value="<?php echo $username; ?>">
    </div>
</div>


<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $languagelabels['email'];?> <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-10">
    	<input type="text" placeholder="<?php echo $languagelabels['email'];?>" class="form-control m-b"  name="email" id="email" value="<?php echo $email; ?>" autocomplete='off'>
    </div>
</div>


<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $languagelabels['secondaryemail'];?> </label>
	<div class="col-sm-10">
    	<input type="text" placeholder="<?php echo $languagelabels['secondaryemail'];?>" class="form-control m-b"  name="semail" id="semail" value="<?php echo $semail; ?>" autocomplete='off'>
    </div>
</div>


<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $languagelabels['password'];?> <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-10">
    	<input type="password" placeholder="<?php echo $languagelabels['password'];?>" class="form-control m-b"  name="pass" id="pass" value="<?php echo $password; ?>" >
    </div>
</div>


<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $languagelabels['phone'];?> </label>
	<div class="col-sm-10">
    	<input type="text" placeholder="<?php echo $languagelabels['phone'];?>" class="form-control m-b"  name="phone" id="phone" value="<?php echo $phone; ?>" >
    </div>
</div>


<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $languagelabels['mobile'];?> </label>
	<div class="col-sm-10">
    	<input type="text" placeholder="<?php echo $languagelabels['mobile'];?>" class="form-control m-b"  name="mobile" id="mobile" value="<?php echo $mobile; ?>" >
    </div>
</div>


<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $languagelabels['fax'];?> </label>
	<div class="col-sm-10">
    	<input type="text" placeholder="<?php echo $languagelabels['fax'];?>" class="form-control m-b"  name="fax" id="fax" value="<?php echo $fax; ?>" >
    </div>
</div>


<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $languagelabels['fkcountryid'];?> <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-10">
		<select name="fkcountryid" class="form-control m-b" data-placeholder="Assign Countries" onchange="getstates(this.value)">
        	<option value="0">Select Country</option>
			<?php
            for($i=0;$i<sizeof($countries);$i++)
            {
            ?>
                <option <?php if($countryid == $countries[$i]['pkcountryid']) {echo "SELECTED=SELECTED";} ?> value="<?php echo $countries[$i]['pkcountryid'];?>"><?php echo $countries[$i]['countryname'];?></option>
           <?php
            }
           ?>
        </select>
    </div>
</div>


<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $languagelabels['fkstateid'];?> <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-10" id="states">
		<select name="fkstateid" class="form-control m-b" data-placeholder="Assign State" onchange="getcities(this.value)" >
        	<option value="0">Select States</option>
			<?php
            for($i=0;$i<sizeof($states);$i++)
            {
            ?>
                <option <?php if($stateid == $states[$i]['pkstateid']) {echo "SELECTED=SELECTED";} ?> value="<?php echo $states[$i]['pkstateid'];?>"><?php echo $states[$i]['statename'];?></option>
           <?php
            }
           ?>
        </select>
    </div>
</div>
 

<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $languagelabels['fkcityid'];?> <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-10" id="cities">
		<select name="fkcityid" class="form-control m-b" data-placeholder="Assign City"  >
        	<option value="0">Select City</option>
			<?php
            for($i=0;$i<sizeof($cities);$i++)
            {
            ?>
                <option <?php if($cityid == $cities[$i]['pkcityid']) {echo "SELECTED=SELECTED";} ?> value="<?php echo $cities[$i]['pkcityid'];?>"><?php echo $cities[$i]['cityname'];?></option>
           <?php
            }
           ?>
        </select>
    </div>
</div>
                  
              
<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $languagelabels['fax'];?> </label>
	<div class="col-sm-10">
    	<input type="text" placeholder="<?php echo $languagelabels['fax'];?>" class="form-control m-b"  name="fax" id="fax" value="<?php echo $fax; ?>" >
    </div>
</div>
 

<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $languagelabels['street'];?> </label>
	<div class="col-sm-10">
    	<input type="text" placeholder="<?php echo $languagelabels['street'];?>" class="form-control m-b"  name="street" id="street" value="<?php echo $street; ?>" >
    </div>
</div>


<div class="form-group">
	<label class="col-sm-2 control-label"><?php echo $languagelabels['zip'];?> </label>
	<div class="col-sm-10">
    	<input type="text" placeholder="<?php echo $languagelabels['zip'];?>" class="form-control m-b"  name="zip" id="zip" value="<?php echo $zip; ?>" >
    </div>
</div>


<?php
	buttons('insertuser.php','userform','center-column','dashboard.php?msg=1',0)
?>
  <input type="hidden" name="passhidden" id="passhidden" value="<?php echo base64_encode($password); ?>" />
<input type="hidden" name="id" value ="<?php echo $id;?>" />
<input type="hidden" name="addressbookid" value="<?php echo $addressbookid;?>" />
</form>              
        </div>
    </div>
</div>




    
		
        


</div>
</div>
<script language="javascript" type="text/javascript">
	document.getElementById('fname').focus();
      
    loading('Loading Form...');
//    $(".chzn-select").chosen();
  //  $(".chzn-select-deselect").chosen({allow_single_deselect: true});
</script>
