<?php
@session_start(); 
require_once("adminsecurity.php");
/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/
$id				=	(int)@$_GET['id']; 
$addressbookid	=	$_SESSION['addressbookid'];
if($id<1)
{
	$saveUid	=	$AdminDAO->generateuid('cases');
	$id			=	getDraft('cases','id',array('attorney_id' => $addressbookid,'uid'=>$saveUid,'allow_reminders'=>1),"attorney_id = '$addressbookid'");
	$pagetitle	=	"Add Case";
}
else
{
	$pagetitle	=	"Edit Case";
}
if($id > 0) 
{
	$cases			=	$AdminDAO->getrows('cases c,system_addressbook sa',"c.*,sa.email,sa.firstname,sa.lastname,sa.middlename","c.id = :id AND c.attorney_id = sa.pkaddressbookid",array(":id"=>$id));
	
	$case			=	$cases[0];
	$casename		=	$case['case_title'];
	$case_attorney  =	$case['case_attorney'];
	$ownernameemail	=	$case['firstname'];
	$case_attorney	=	$case['case_attorney'];
	if($case['middlename'] != "")
	{
		$ownernameemail	.=	" ".$case['middlename'];
	}
	if($case['lastname'] != "")
	{
		$ownernameemail	.=	" ".$case['lastname'];
	}
	if($case['email'] != "")
	{
		$ownernameemail	.=	" (".$case['email'].")";
	}
	$is_draft	=	$case['is_draft'];
	$uid		=	$case['uid'];
	$attorney_id	=	$case['attorney_id'];
	$pagetitle	=	$casename;
	$loggedin_email		=	$_SESSION['loggedin_email'];
	
	/*
	* Check Owner is logged in or not
	*/
	
	if($case_attorney == $_SESSION['loggedin_email'])
	{
		$caseteamattorney	=	1;
	}
	else
	{
		$caseteamattorney		=	0;
	}
	if($caseowner == 1 || $caseteammember == 1 || $caseteamattorney == 1)
	{
		$owner			=	1;
		$disabledClass	=	"";
	}
	else
	{
		$owner			=	0;
		$disabledClass	=	"readonly";
	}
	
}

//If is draft is 1 then delete those parties and clients that added during draft phase
//Next time when someone create new case these clients and parties should not created already
if($is_draft == 1)
{
	$AdminDAO->deleterows('client_attorney'," case_id = :id", array("id"=>$id));
	$AdminDAO->deleterows('clients'," case_id = :id", array("id"=>$id));
	$AdminDAO->deleterows('attorney'," case_id = :id AND attorney_type != 1", array("id"=>$id));
}

if($pagetitle == "")
{
	$pagetitle	=	"Add Case";
}
$states		=	$AdminDAO->getrows('system_state','*',"fkcountryid = :fkcountryid AND statecode = 'CA' ",array(":fkcountryid"=>254), 'statename', 'ASC');
$counties	=	$AdminDAO->getrows('system_county','*',"",array(), 'countyname', 'ASC');

$cases = new CaseModel();
$parties = $cases->getClients($id);

$parties =	array_merge($parties, $AdminDAO->getrows("clients","*", "case_id = :case_id ", array(":case_id"=>$id), "client_name", "ASC"));
//$brokercompanies	=	$AdminDAO->getrows('broker_companies','*');


/*
* Getting data from Cookies for STATE and CITY
*/
$cookie_attorney_county	=	"";
//$verification_city	=	"";
if(!empty($_COOKIE['ER_ATTORNEY_COUNTY']))
{
	$cookie_attorney_county	=	$_COOKIE['ER_ATTORNEY_COUNTY'];
}


?>
<style>
body.modal-open 
{
    position: static !important;
}
.modal-header .close {
    margin-top: -45px !important;
}
.modal-title {
    font-size: 24px !important;
}
.close {
    font-size: 25px !important;
}
.modal-header
{
	padding:10px !important
}
.swal2-popup {
  font-size: 15px !important;
}
</style>

<div id="screenfrmdiv" style="display: block;">

<div class="col-lg-12">
    <div class="hpanel">
        <div class="panel-heading text-center">
            <?php 
			/*if($id>0)
			{echo "<h3><strong>$casename</strong></h3>";}
			else
			{echo "<h3><strong>Add Case</strong></h3>";}*/
			
			?>	
            <h3><strong>
			<?php 
			echo $pagetitle; 
			?>
            
            </strong></h3>
        </div>
        <div class="panel-body">
            <form  name="clientform" id="clientform" class="form form-horizontal">
            <div class="form-group row">
            	<div class="col-md-3"></div>
                <div class="col-md-3" align="left"> 
				<?php
					buttonsave('caseaction.php','clientform','wrapper','get-cases.php?pkscreenid=44',0);
					buttoncancel(44,'get-cases.php');
					
					?> 
                </div>
                <div class="col-md-5" align="right"> 
				<?php
					if($owner == 1) 
					{
					?>
					<a href="javascript:;" class="btn btn-black" title="Delete case" id="newcase" onclick="javascript: deleteLeaveCases('<?php echo $id;?>',1);"><i class="fa fa-trash"></i> Delete </a>
					<?php	
					}
					else
					{
					?>
					<a href="javascript:;" class="btn btn-black" title="Leave case" id="newcase" onclick="javascript: deleteLeaveCases('<?php echo $id;?>',2);"><i class="fa fa-sign-out"></i> Remove me from this case</a>
					<?php
					}
					?> 
                </div>
            </div>
            <input type="hidden" name="jurisdiction" class="form-control" id="jurisdiction" value='CA'>
            
            <div class="row">
            	<div class="col-md-1"></div>
                <div class="col-md-2">
                    <label>Number<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                </div>
                <div class="col-md-3">
                    <input type="text" placeholder="Number" class="form-control m-b"  name="case_number" id="case_number" value="<?php echo htmlentities($case['case_number']); ?>" <?php echo $disabledClass; ?>>
                </div>
                <div class="col-md-6">
                </div>
                <!--<div class="col-md-2">
                    <label>Filed<span class="redstar" style="color:#F00" title="This field is compulsory"></span></label>
                </div>
                <div class="col-md-3">
                    <input type="text"  name="filed" id="filed" placeholder="Filed Date" class="form-control m-b datepicker" value="<?php echo $case['date_filed']=='0000-00-00'?'':dateformat($case['date_filed']);?>" <?php echo $disabledClass; ?>>
                </div>
                <div class="col-md-1"></div>-->
							</div>
            
            <div class="row">
            	<div class="col-md-1"></div>
                <div class="col-md-2">
                    <label>Name<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                </div>
                <div class="col-md-3">
                    <input type="text" placeholder="Case Name" class="form-control m-b"  name="case_title" id="case_title" value="<?php echo htmlentities($case['case_title']); ?>" <?php echo $disabledClass; ?>>
                </div>
                <div class="col-md-2">
                    <label>County</label>
                </div>
                <div class="col-md-3">
                    <select name="county_name" class="form-control  m-b" id="county_name" <?php echo $disabledClass; ?>>
						<?php
                        foreach($counties as $county)
                        {
                        ?>
                            <option value="<?php echo $county['countyname'];?>" <?php if($county['countyname']== $case['county_name'] || $county['countyname'] == $cookie_attorney_county) {echo " SELECTED ";}?>><?php echo $county['countyname'];?></option>
                        <?php
                        }
                        ?>
					</select>
                </div>
                <div class="col-md-1"></div>
            </div>
            
            <!--<div class="row">
            	<div class="col-md-1"></div>
                <div class="col-md-2">
                    <label>Judge</label>
                </div>
                <div class="col-md-3">
                    <input type="text" placeholder="Judge's Name" class="form-control m-b"  name="judge_name" id="judge_name" value="<?php echo htmlentities($case['judge_name']);?>" <?php echo $disabledClass; ?>>
                </div>
                <div class="col-md-2">
                    <label>Department</label>
                </div>
                <div class="col-md-3">
                    <input type="text" placeholder="Department" class="form-control m-b"  name="department" id="department" value="<?php echo htmlentities($case['department']); ?>" <?php echo $disabledClass; ?>>
                </div>
                <div class="col-md-1"></div>
            </div>-->
            
            
            <div class="row">
            	<div class="col-md-1"></div>
                <div class="col-md-2">
                   <label class="control-label">Plaintiff<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                </div>
                <div class="col-md-3">
                    <textarea placeholder="Plaintiff(s)" class="form-control m-b"  name="plaintiff" id="plaintiff" <?php echo $disabledClass; ?>><?php echo htmlentities($case['plaintiff']); ?></textarea>
                </div>
                <div class="col-md-2">
                   <label class="control-label">Defendant<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                </div>
                <div class="col-md-3">
                    <textarea placeholder="Defendant(s)" class="form-control m-b"  name="defendant" id="defendant" <?php echo $disabledClass; ?>><?php echo htmlentities($case['defendant']); ?></textarea>
                </div>
                <div class="col-md-1"></div>
            </div>
						
						<?php if ($caseowner): ?>
							<div class="row" style="margin-bottom:10px">
								<div class="col-md-1"></div>
									<div class="col-md-2">
											<label>Trial<span class="redstar" style="color:#F00" title="This field is compulsory"></span></label>
									</div>
									<div class="col-md-3">
											<input type="text"  onchange="calculated_discovery_cutoff_date(this.value)" name="trial" id="trial" placeholder="Trial Date" class="form-control m-b datepicker" value="<?php echo $case['trial']=='0000-00-00'?'':dateformat($case['trial']);?>" <?php echo $disabledClass; ?> data-date-start-date="0d" data-date-end-date="+5y">
									</div>
									<div class="col-md-2">
										<label>Discovery Cutoff<span class="redstar" style="color:#F00" title="This field is compulsory"></span></label>
									</div>
									<div class="col-md-3">
											<input type="text"  name="discovery_cutoff" id="discovery_cutoff" placeholder="Discovery Cutoff" class="form-control datepicker" value="<?php echo $case['discovery_cutoff']=='0000-00-00'?'':dateformat($case['discovery_cutoff']);?>" <?php echo $disabledClass; ?> data-date-start-date="0d" data-date-end-date="+5y">
										<i class="fa fa-university" aria-hidden="true"></i> Code Civ.Proc., &sect;&sect; 2016.060 <?php  echo instruction(12) ?>, 2024.020 <?php  echo instruction(13) ?>.
									</div>
									<div class="col-md-1"></div>
								</div>
						<?php endif; ?>		
						
						<div class="row">
							<div class="col-md-1"></div>
							<div class="col-md-2">
                    <label>Primary Attorney<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                </div>
                <div class="col-md-3">
									<select class="er-team-attorney-select form-control" name="case_attorney" id="case_attorney" data-value="<?= $currentPrimaryAttorneyId ?>" <?php echo $disabledClass; ?>></select>
								</div>
                <div class="col-md-2">
                    <label>Masthead<span class="redstar" style="color:#F00" title="This field is compulsory"></span></label>
                </div>
                <div class="col-md-3">
                  <textarea name="masterhead" class="form-control" cols="50" rows="7" wrap="off" style="overflow: hidden"><?= $currentSide ? $currentSide['masterhead'] : '' ?></textarea>
                </div>
                <div class="col-md-1"></div>
            	</div>
            
            <?php /*?><div class="row">
            	<div class="col-md-1"></div>
                <div class="col-md-2">
                    <label>Send Reminders? <?php  echo instruction(3) ?></label>
                </div>
                <div class="col-md-3">
                	<label><input name="allow_reminders" <?php if($case['allow_reminders'] == 1){echo "checked";} ?> onClick="showreminders()" id="allow_reminders" type="checkbox" value="1" <?php echo $disabledClass; ?>> Enable</label>
                </div>
                <div class="col-md-2 showreminders">
                    <label>View Reminders Schedule</label>
                </div>
                <div class="col-md-3 showreminders">
                    <a href="javascript:;" class="btn btn-info" data-toggle="modal" data-target="#viewreminders" <?php echo $disabledClass; ?>><i class="fa fa-bell"></i> View </a>
                </div>
                <div class="col-md-1"></div>
            </div><?php */?>
             <hr />
						 
           	<?php
			if($owner == 1)
			{
			?>
            <div class="row">
            <div class="col-md-11" style="text-align:right">
                <a href="javascript:;"  class="pull-right btn btn-success btn-small" onclick="addparty('', <?= $id ?>)" style="margin-bottom:10px !important"><i class="fa fa-plus"></i> Add New</a>
            </div>
            <div class="col-md-1"></div>
            </div>
            <?php
			}
			?>
            <div class="row">  
                <div class="col-md-1"></div>
                <div class="col-md-2">
                    <label>Parties</label>
                </div>
                <div class="col-md-8" id="loadclients"></div>
								
                <div class="col-md-1"></div>
            </div>
            <br />
            <div class="row">
            	<div class="col-md-11" style="text-align:right">
                <a href="javascript:;"  class="pull-right btn btn-success btn-small" id="add-user-btn" style="margin-bottom:10px !important"><i class="fa fa-plus"></i> Add New</a>
            	</div>
            	<div class="col-md-1"></div>
            </div>

            <div class="row">  
                <div class="col-md-1"></div>
                <div class="col-md-2">
                    <label>Team</label>
                </div>
                <div class="col-md-8" id="loadusers"></div>
                <div class="col-md-1"></div>
            </div>
            <br />
            <div id="loadservicelistsDiv">
            	<?php
				if(sizeof($parties) > 0 && $owner == 1)
				{
				?>
                <div class="row">
                <div class="col-md-11" style="text-align:right"> 
                <a href="javascript:;"  class="pull-right btn btn-success btn-small" onclick="loadServiceListModal('<?php echo $id; ?>')" style="margin-bottom:10px !important"><i class="fa fa-plus"></i> Add New</a>
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
                
            </div>
            <br />
            <?php
			/*
			<div class="row">  
                <div class="col-md-1"></div>
                <div class="col-md-2">
                    <label>Case Team <?php echo instruction(2) ?></label>
                </div>
                <div class="col-md-8" id="loadattoneys3">
                    
                </div>
                <div class="col-md-1"></div>
            </div>
            if($caseowner == 1)
			{
			?>
                <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-2">
                    <label>Case Attorney<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                </div>
                <div class="col-md-3">
                    <select name="case_attorney" class="form-control  m-b" id="case_attorney" onchange="loadmasterhead()">
                        
                    </select>
                </div>
                </div>
                <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-2">
                    <label>Masthead<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                </div>
                <div class="col-md-8" id="masterheadDiv">
                    <textarea style="width: 383px; height: 135px;resize: none;" placeholder="Masthead " class="form-control m-b"  name="masterhead" id="masterhead"><?php echo htmlentities($case['masterhead']); ?></textarea>
                </div>
                </div>
            <?php
			}*/
			?>
						 <div class="container" >
							 <div id="sides-container">
						 </div>

            
            <input type="hidden" name="id" value ="<?php echo $id;?>" />
            <input type="hidden" name="uid" value ="<?php echo $uid;?>" />
            <input type="hidden" name="owner" value ="<?php echo $owner;?>" />
            <input type="hidden" name="caseteammember" value ="<?php echo $caseteammember;?>" />
            <input type="hidden" name="caseteamattorney" value ="<?php echo $caseteamattorney;?>" />
            <input type="hidden" name="caseowner" value ="<?php echo $caseowner?>" />
            
            <div class="form-group">
            	<div class="col-sm-offset-3 col-sm-3" align="left">
				<?php
					buttonsave('caseaction.php','clientform','wrapper','get-cases.php?pkscreenid=44',0);
					buttoncancel(44,'get-cases.php');
					?> 
                
                </div>
                <div class="col-md-5"  align="right">
                	<?php
					if($owner == 1)
					{
					?>
					<a href="javascript:;" class="btn btn-black" title="Delete case" id="newcase" onclick="javascript: deleteLeaveCases('<?php echo $id;?>',1);"><i class="fa fa-trash"></i> Delete </a>
					<?php	
					}
					else
					{
					?>
					<a href="javascript:;" class="btn btn-black" title="Leave case" id="newcase" onclick="javascript: deleteLeaveCases('<?php echo $id;?>',2);"><i class="fa fa-sign-out"></i> Remove me from this case</a>
					<?php
					}
					?>
                </div>
            </div>         
            </form>
        </div>
    </div>
</div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalcaseteam" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" id="modalcaseteam_content">
      
    </div>
  </div>
</div>

<div id="serviceListModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content" id="serviceListModalContent">
        
    </div>
  </div>
</div>
<div id="partyModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="padding: 15px;">
        <h5 class="modal-title" id="exampleModalLongTitle" style="font-size: 22px;">Enter Party</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
        	<input type="hidden" id="party_client_id" name="party_client_id" value="" />
            <label for="caseteam_attr_name">Name</label><span class="redstar" style="color:#F00" title="This field is compulsory">*</span>
            <input type="text" placeholder="Party Name" class="form-control m-b"  name="client_name" id="client_name">
        </div>
        <div class="form-group">
            <label for="caseteam_attr_email">Role</label><span class="redstar" style="color:#F00" title="This field is compulsory">*</span>
            <select name="clientroles" id="clientroles" class="form-control">
                <option value="">Party Role</option>
                <option value="Plaintiff">Plaintiff</option>
                <option value="Defendant">Defendant</option>
                <option value="Plaintiff and Cross-defendant">Plaintiff and Cross-defendant</option>
                <option value="Defendant and Cross-plaintiff">Defendant and Cross-plaintiff</option>
            </select>
        </div>
        <div class="form-group">
            <label for="caseteam_attr_name">Representation</label><span class="redstar" style="color:#F00" title="This field is compulsory">*</span>
            <select name="clienttypes" id="clienttypes" class="form-control" onchange="addAttryFunction(this.value)">
							<option value="">Who represents this Party?</option>
							<option value="Us">Us</option>
							<option value="Others">Another Attorney</option>
							<option value="Pro per">Pro per</option>
            </select>
        </div>
        <div class="form-group" id="div_attr" style="display:none;">
					<label for="caseteam_attr_name">Attorney:</label><span class="redstar" style="color:#F00" title="This field is compulsory">*</span>
					<select class="er-team-attorney-select form-control" name="primary_attorney_id" id="primary_attorney_id" value="<?= $currentPrimaryAttorneyId ?>"></select>
        </div>
				<div class="form-group" style="display: none" id="div_attr_email">
					<label for="caseteam_attr_name">Email</label><span class="redstar" style="color:#F00" title="This field is compulsory">*</span>
					<input type="text" placeholder="Party Email" class="form-control m-b"  name="client_email" id="client_email" >
				</div>
      </div>

      <div class="modal-footer">
        <a class="btn btn-success" href="javascript:;" onclick="addCaseClient(<?php echo $id; ?>)"><i class="fa fa-save"></i> Save</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
        
        <i id="msgClient" style="color:red"></i>
      </div>
    </div>

  </div>
</div>

<!-- user modal -->
<div id="userModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
		<!-- Modal content-->
    <div class="modal-content">
			<form id="submit-user-form">
				<div class="modal-header" style="padding: 15px;">
					<h5 class="modal-title" id="exampleModalLongTitle" style="font-size: 22px;">Enter User</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="user_name">Name</label>
						<input type="text" placeholder="Name" class="form-control m-b"  name="name" id="user_name" required />
					</div>
					<div class="form-group">
							<label for="user_email">Email</label>
							<input type="text" placeholder="Email" class="form-control m-b" name="email" id="user_email" required />
					</div>
					<input type="hidden" name="case_id" value="<?= $id ?>" />
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</a>
					<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
					<i id="msgClient" style="color:red"></i>
				</div>
			</form>
    </div>
  </div>
</div>


<div id="viewreminders" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="padding: 15px;">
        <h5 class="modal-title" id="exampleModalLongTitle" style="font-size: 22px;">Scheduled Reminders</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <label for="caseteam_attr_email">EasyRogs sends the following reminders:</label>
        <ol>
        	<li><b>To the Attorney:</b> a week before the Response is due.</li>
            <li><b>To the Responding Party:</b> 5 days before their answers are due back to the Attorney. And 5 days after the Attorney sent it, if the Party hasn't at least looked at it.</li>
        </ol>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button> 
      </div>
    </div>

  </div>
</div>
<div class="modal fade" id="general_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="general_modal_title">Calculate Date</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="load_general_modal_content">
       <div class="text-center"> Loading...</div>
      </div>
      <!--<div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>-->
    </div>
  </div>
</div>
<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="width:450px !important">
    <div class="modal-content">
  		<div class="modal-body" id="deletemodalcotent">
        
      </div>
    </div>
  </div>
</div>
<?php /*?><script src="<?php echo VENDOR_URL; ?>sweetalert.min.js"></script><?php */?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">

const setMastHead = (attorneyId = null) => {
  const id = attorneyId ? attorneyId : $('#case_attorney').val();
  getAttorney(id, 
    (attorney) => $('textarea[name=masterhead]').html(attorney.masterhead),
    (e) => console.error(e)
  );
}
$(document).on( 'change', '#case_attorney', () => { setMastHead() });


function loadServiceListModal(case_id,attr_id = '')
{
	$.post( "loadservicelistmodal.php",{case_id: case_id,attr_id:attr_id}).done(function(resp)
	{
		$("#serviceListModalContent").html(resp);
		$("#serviceListModal").modal("toggle");
		$("#msgAttr").html("");
	});
}

function addparty(id='', caseId)
{
	$("#party_client_id").val(0);
	$('#partyModal').modal('show');
	$("#party_client_id").val("");
	$("#client_name").val("");
	$("#clientroles").val("");
	$("#clienttypes").val("");
	$("#client_email").val("");
	$('#div_attr_email').hide();
	if(id>0)
	{
		$.post( "clientsload.php",{id: id, case_id: caseId}).done(function(resp)
		{
			var obj = JSON.parse(resp);
			$("#party_client_id").val(obj.id);
			$("#client_name").val(obj.client_name);
			$("#clientroles").val(obj.client_role);
			$("#clienttypes").val(obj.client_type);
			$("#client_email").val(obj.client_email);
			if(obj.client_type == 'Us' || obj.client_type == 'Pro per')
			{
				$('#div_attr_email').show();
			}
		});
	}
}
$( document ).ready(function() 
{
	$('#submit-user-form').on('submit', (e) => {
		e.preventDefault();
		const formData = new FormData(e.target);
		const params = Array.from(formData.keys()).reduce(
			(acc, key) => {acc[key] = formData.get(key); return acc; }, {}
		);
		$.post('post-case-user.php', params, (response) => {
			$('#userModal').modal('hide');
			loadCasePeople(params.case_id);
		}).fail((response) => {
			$('#userModal').modal('hide');
			showResponseMessage(JSON.parse(response.responseText));
		});
	});
	$('#add-user-btn').on('click', () => $('#userModal').modal('show') );
	$(document).on('click', '.delete-user-btn', async (e) => {
		const params = $(e.target).parent().data();
		confirm = await confirmAction();
		if ( confirm.value ) {
			$.post('delete-case-user.php', params, (response) => {
				loadCasePeople(params.case_id);
				showResponseMessage(response);				
			}).fail((response) => showResponseMessage(response))
		}
	});

	<?php
	if($case_attorney == 0 || $case_attorney == "")
	{
	?>
	setTimeout(function(){ loadmasterhead(); }, 2000);
	
	<?php
	}
	?>
	showreminders(<?php echo $case['allow_reminders']; ?>);
	$('.datepicker').datepicker({format: 'm-d-yyyy',autoclose:true});

	loadAttoneysFunction(<?php echo $id; ?>,2,"loadattoneys2");
	loadCasePeople(<?php echo $id; ?>);
	loadSides(<?php echo $id; ?>);
	addMyAttorneyToCase(<?php echo $id; ?>);
});

function loadCaseAttorneys(case_id)
{
	$.post( "loadcaseattorney.php",{case_id:case_id}).done(function( data ) 
	{
		$("#case_attorney").html(data);
	});
}

function deleteLeaveCases(case_id,delete_or_leave)
{
	if(delete_or_leave == 1)
	{
		var title = "Are you sure to delete this case?";
	}
	else
	{
		var title = "Are you sure to want to leave this case?";
	}
	Swal.fire({
	title:title,
	text: "You will not be able to undo this action!",
	icon: 'warning',
	showCancelButton: true,
	confirmButtonColor: '#187204',
	cancelButtonColor: '#C2391B',
	confirmButtonText: "Yes, delete it!"
	}).then((result) => {
	if (result.value) {
	$.post( "deleteleavecase.php", { case_id: case_id, delete_or_leave: delete_or_leave }).done(function( data ) 
	{
		selecttab('44_tab','get-cases.php','44');
	});
	}
	});
	$( ".swal-button-container:first" ).css( "float", "right" );
}

function loadCasePeople(case_id) {
	loadSides(case_id);
	$("#loadclients").load("get-case-clients.php?format=html&case_id="+case_id)
	$("#loadusers").load("get-case-users.php?format=html&case_id="+case_id)
}

function loadSides(caseId)
{
	//$("#sides-container").load(`get-sides.php?case_id=${caseId}&format=html`);
	
}

function addCaseClient(case_id)
{
	var other_attorney_id	= 	{};
	
	var id					=	$("#party_client_id").val();
	var client_name			=	$("#client_name").val();
	var clientroles			=	$("#clientroles").val();
	var clienttypes			=	$("#clienttypes").val();
	var client_email		=	$("#client_email").val();
	var other_attorney_id		=	$("select[name=other_attorney_id]").val();
	var other_attorney_name		=	$("input[name=other_attorney_name]").val();
	var other_attorney_email		=	$("input[name=other_attorney_email]").val();
	var primary_attorney_id		=	$("select[name=primary_attorney_id]").val();
	
	$("#msgClient").html("");
	$.post( "post-case-client.php", {
		id:id, 
		case_id:case_id,
		client_name: client_name, 
		clientroles: clientroles,
		clienttypes:clienttypes,
		client_email:client_email,
		other_attorney_id:other_attorney_id,
		other_attorney_name:  other_attorney_name,
		other_attorney_email: other_attorney_email,
		primary_attorney_id: primary_attorney_id
	}, 'json').done(function( data ) 
	{
		obj = data;
		if(obj.type == "success")
		{
			$('.modal').modal('hide');
			$("#client_name").val('');
			$("#clientroles").val('');
			$("#clienttypes").val('');
			//$("#other_attorney_id").val([]);
			$('#other_attorney_id').val(null).trigger('change');
			$("#client_email").val('');
			loadCasePeople(obj.case_id);
			loadSides(obj.case_id);
			//attDropdownFunction();
			checkPartiesFoundShowServiceList(obj.case_id);
			//loadAttoneysFunction(obj.case_id,2,"loadattoneys2");
			
		}
		else
		{			
			showResponseMessage(obj);
		}
	}).fail( (e) => {
		const obj = JSON.parse(e.responseText);
		showResponseMessage(obj);
		if (obj.field == 'primary_attorney_id') {
				$("#div_attr").show();
			}
	});
}
function checkPartiesFoundShowServiceList(case_id)
{
	$.post("checkpartiesfoundshowservicelist.php", { case_id: case_id})
	.done(function( data ) 
	{
		$("#loadservicelistsDiv").html(data);
	});
}

function deleteCaseClient(id, case_id)
{
	Swal.fire({
	title: "Are you sure you want to delete this Party?",
	text: "You will not be able to undo this action!",
	icon: 'warning',
	showCancelButton: true,
	confirmButtonColor: '#187204',
	cancelButtonColor: '#C2391B',
	confirmButtonText: "Yes, delete it!"
	}).then((result) => {
	if (result.value) {
		$("#client_"+id).remove();
		$.post( "delete-case-client.php", { id: id, case_id: case_id}, () => { loadCasePeople(case_id)} );
		checkPartiesFoundShowServiceList(<?php echo $id ?>);
	}
	});
	$( ".swal-button-container:first" ).css( "float", "right" );
}

function addAttryFunction(type)
{
	$(".disableclass").val("");
	if (type == 'Others' || type == 'Us') {
		$("#bring_team_checkbox").show();
	}
	else {
		$("#bring_team_checkbox").hide();
	}
	if(type == 'Others')
	{
		$("#div_attr_email").hide();
	}
	else if(type == '')
	{
		$("#div_attr").hide();
		$("#div_attr_email").hide();
	}
	else
	{
		$("#div_attr").hide();
		$("#div_attr_email").show();
	}
}
function loadmodaldelete(id,attorney_type,case_team_id,case_id,is_userteammember)
{
	$.post( "loaddeletemodal.php", { id: id,attorney_type: attorney_type,case_team_id: case_team_id,case_id: case_id,is_userteammember:is_userteammember})
	.done(function( data ) {
	$("#deletemodalcotent").html(data);
	$("#delete_modal").modal("toggle");
	});
}
function modaldeleteaction()
{
	$.post( "caseattorneydelete.php",$( "#deleteform" ).serialize()).done(function( data ) 
	{
    	loadAttoneysFunction(data,3,"loadattoneys3");
		//loadCaseAttorneys(data);
		$("#delete_modal").modal("toggle");
  	});
}
/*function calculateduedatepopup(served)
{
	$.post( "loadpopupcontent.php",{ served: served,popuptype: 2}).done(function( data ) 
	{
		$("#load_general_modal_content").html(data);
	});
	$('#general_modal').modal('toggle');
}*/
function calculated_discovery_cutoff_date(trail_date)
{
	$.post( "calculatecutoffdateaction.php",{ trail_date: trail_date}).done(function( data ) 
	{
		$("#discovery_cutoff").val(data);
	});
}
function showreminders()
{
	 if($("#allow_reminders").prop("checked") == true)
	 {
		 $(".showreminders").show();
	 }
	 else
	 {
		 $(".showreminders").hide();
	 }
}
function loadmasterhead()
{
	var case_attorney_email	=	$( "#case_attorney option:selected" ).text();
	var case_attorney		=	$( "#case_attorney option:selected" ).val();
	$.post( "loadmasterhead.php",{ case_attorney_email: case_attorney_email,case_attorney:case_attorney}).done(function( data ) 
	{
		$("#masterheadDiv").html(data);
	});
}

  erInviteControl();
  <?php if ($currentPrimaryAttorneyId): ?>
    erTeamAttorneySelectControl();
		setMastHead(<?= $currentPrimaryAttorneyId ?>);
  <?php else: ?>  
    erTeamAttorneySelectControl( setMastHead );
  <?php endif; ?>  	
</script>