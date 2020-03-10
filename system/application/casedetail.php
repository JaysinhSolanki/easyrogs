<?php
@session_start(); 
require_once("adminsecurity.php");
/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/
$id					=	(int)@$_GET['id'];
$addressbookid		=	$_SESSION['addressbookid'];
$loggedin_email		=	$_SESSION['loggedin_email'];
$cases				=	$AdminDAO->getrows('cases',"*","id = :id",array(":id"=>$id));
$case				=	$cases[0];
$case_title			=	$case['case_title'];
$plaintiff			=	$case['plaintiff'];
$defendant			=	$case['defendant'];
$case_number		=	$case['case_number'];
$jurisdiction		=	$case['jurisdiction'];
$county_name		=	$case['county_name'];
$judge_name			=	$case['judge_name'];
$court_address		=	$case['court_address'];
$department			=	$case['department'];
$allow_reminders	=	$case['allow_reminders'];
$trial				=	$case['trial'];
$discovery_cutoff	=	$case['discovery_cutoff'];
$filed				=	$case['filed'];
$updated_at			=	$case['updated_at'];
$updated_by			=	$case['updated_by'];
//dump($cases);

//$states		=	$AdminDAO->getrows('system_state','*',"fkcountryid = :fkcountryid AND statecode = 'CA' ",array(":fkcountryid"=>254), 'statename', 'ASC');
//$counties	=	$AdminDAO->getrows('system_county','*',"",array(), 'countyname', 'ASC');
//$parties	=	$AdminDAO->getrows("clients","*", "case_id = :case_id ", array(":case_id"=>$id), "client_name", "ASC");
$clients			=	$AdminDAO->getrows("clients","*", "case_id = :case_id ", array(":case_id"=>$id), "client_name", "ASC");
$servicelists		=	$AdminDAO->getrows(
											"
											attorney 
												LEFT JOIN client_attorney 
												ON 		
												attorney.case_id			=	client_attorney.case_id AND
												client_attorney.attorney_id	=	attorney.id
												LEFT JOIN clients 
												ON 		
												clients.id		=	client_attorney.client_id
													
											","attorney.*,clients.client_name", "attorney.case_id = :case_id AND attorney.attorney_type = :attorney_type AND attorney_email != '$loggedin_email'", array(":case_id"=>$id,":attorney_type"=>2), "attorney.attorney_name", "ASC");
$caseteammembers	=	$AdminDAO->getrows("attorney a ,case_team ct","a.*,ct.id as case_team_id", "ct.attorney_id = a.id AND ct.fkcaseid = :case_id AND ct.is_deleted  = 0", array(":case_id"=>$id), "attorney_name", "ASC");
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
</style>
<div id="screenfrmdiv" style="display: block;">

<div class="col-lg-12">
    <div class="hpanel">
        <div class="panel-heading text-center">
            <h3><strong><?php echo $case_title; ?></strong></h3>
        </div>
        <div class="panel-body">
            <div class="row">
            	<div class="col-md-1"></div>
                <div class="col-md-10">
                	<h4>Case Details <small class="pull-right"><?php
                        buttoncancel(44,'cases.php');
                    ?> </small></h4>
                    
                    <hr />
                    <table class="table table-bordered table-hover table-striped" >
                        <tbody>
                            <tr>
                                <th width="20%">Number</th>
                                <td><?php echo $case_number ?></td>
                                <th width="20%">Filed</th>
                                <td><?php echo dateformat($filed) ?></td>
                            </tr>
                            <tr>
                                <th width="20%">Name</th>
                                <td><?php echo $case_title ?></td>
                                <th width="20%">County</th>
                                <td><?php echo $county_name ?></td>
                            </tr>
                            <tr>
                                <th width="20%">Judge</th>
                                <td><?php echo $judge_name ?></td>
                                <th width="20%">Department</th>
                                <td><?php echo $department ?></td>
                            </tr>
                            <tr>
                                <th width="20%">Plaintiff</th>
                                <td><?php echo $plaintiff ?></td>
                                <th width="20%">Defendant</th>
                                <td><?php echo $defendant ?></td>
                            </tr>
                            <tr>
                                <th width="20%">Number</th>
                                <td><?php echo $case_number ?></td>
                                <th width="20%">Filed</th>
                                <td><?php echo dateformat($filed) ?></td>
                            </tr>
                            <tr>
                                <th width="20%">Trial</th>
                                <td><?php echo dateformat($trial); ?></td>
                                <th width="20%">Discovery Cutoff</th>
                                <td><?php echo dateformat($discovery_cutoff) ?></td>
                            </tr>
                            <tr>
                                <th width="20%">Send Reminders? <?php  echo instruction(3) ?></th>
                                <td><?php if($allow_reminders == 1){echo "Yes";}else{echo "No";} ?></td>
                                <?php
                                if($allow_reminders == 1)
                                {
                                ?>
                                <th width="20%">View Reminders Schedule </th>
                                <td>
                                    <a href="javascript:;" class="btn btn-info" data-toggle="modal" data-target="#viewreminders"><i class="fa fa-bell"></i> View </a>
                                </td>
                                <?php
                                }
                                ?>
                            </tr>
                        </tbody>
                    </table>
                    <h4>Parties</h4>
                    <hr />
                    <table class="table table-bordered table-hover table-striped" width="100%">
                    <tr>
                        <th width="20%">Name</th>
                        <th width="20%">Role</th>
                        <th width="20%">Type</th>
                        <th width="10%">Email</th>
                        <th width="15%">Attorney</th>
                    </tr>
                    <?php
                    if(sizeof($clients) > 0)
                    {
                        foreach($clients as $data)
                        {
                            $client_id			=	$data['id'];
                            $attorneyDetails	=	$AdminDAO->getrows("attorney,client_attorney","attorney.*", "attorney.id = client_attorney.attorney_id AND client_id = :client_id", array(":client_id"=>$client_id), "attorney_name", "ASC");
                            $client_attorneys	=	"";
                            if(sizeof($attorneyDetails) > 0)
                            {
                                foreach($attorneyDetails as $attorneyDetail)
                                {
                                    $client_attorneys	.=	 $attorneyDetail['attorney_name']." (".$attorneyDetail['attorney_email'].") <br>"; 
                                }
                            }
                            else
                            {
                                $client_attorneys	=	"-";
                            } 
                            
                            ?>
                            <tr id="client_<?php echo $data['id']; ?>">
                                <td><?php echo $data['client_name']; ?></td>
                                <td><?php echo $data['client_role']; ?></td>
                                <td><?php echo $data['client_type']; ?></td>
                                <td><?php if( $data['client_email'] != ""){echo $data['client_email'];}else{echo "-";} ?></td>
                                <td><?php echo $client_attorneys; ?></td>
                            </tr>
                            <?php
                        }
                    }
                    else
                    {
                        ?>
                            <tr>
                                <td colspan="6" align="center">No record found.</td>
                            </tr>
                        <?php
                    }
                    ?>
                    </table>
                    <h4>Service List</h4>
                    <hr />
                    <table class="table table-bordered table-hover table-striped" id="table_attornys" >
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Party</th>
                        </tr>
                        <?php
                        if(sizeof($servicelists) > 0)
                        {
                            foreach($servicelists as $data)
                            {   
                            ?>
                            <tr id="attr_<?php echo $data['id']; ?>">
                                <td><?php echo $data['attorney_name']; ?></td>
                                <td><?php echo $data['attorney_email']; ?></td>
                                <td><?php echo $data['client_name']; ?></td> 
                            </tr>
                            <?php
                            }
                        }
                        else
                        {
                        ?>
                            <tr>
                                <td colspan="3" align="center">No record found.</td>
                            </tr>
                        <?php	
                        }
                        ?>
                    </table>
                    <h4>Case Team <?php echo instruction(2) ?></h4>
                    <hr />
                    <table class="table table-bordered table-hover table-striped">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                        <?php
                        if(sizeof($caseteammembers) > 0)
                        {
                            foreach($caseteammembers as $data)
                            {   
                            ?>
                            <tr id="attr_<?php echo $data['id']; ?>">
                                <td><?php echo $data['attorney_name']; ?></td>
                                <td><?php echo $data['attorney_email']; ?></td>
                            </tr>
                            <?php
                            }
                        }
                        else
                        {
                        ?>
                            <tr>
                                <td colspan="3" align="center">No record found.</td>
                            </tr>
                        <?php	
                        }
                        ?>
                    </table>
                </div>
                <div class="col-md-1"></div>
            </div>
            <div class="form-group row">
            	<div class="col-sm-offset-3 col-sm-8" align="right"> 
					<?php
                        buttoncancel(44,'cases.php');
                    ?> 
                </div>
            </div>
        </div>
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
<script src="custom.js"></script>





