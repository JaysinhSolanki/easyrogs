<?php
@session_start();
require_once("adminsecurity.php");
$loggedin_email		=	$_SESSION['loggedin_email'];
/**************************************************
*START::If Responded Attornery comes from serve link
***************************************************/
$addressbookid	=	$_SESSION['addressbookid'];
//Add user details to case_attorney and attorney table
$case_uid		=	@$_SESSION['responded_case_uid'];
$res_attr_uid	=	@$_SESSION['responded_attrorney_uid'];
$discovery_uid	=	@$_SESSION['responded_discovery_uid'];

if($res_attr_uid != "")
{
	$attrfields		=	array('fkaddressbookid');
	$attrvalues		=	array($addressbookid);
	$AdminDAO->updaterow("attorney",$attrfields,$attrvalues,"uid = :uid",array("uid"=>$res_attr_uid));
	
	//Get case id form case_uid
	$getCaseDetails	=	$AdminDAO->getrows("cases","id","uid = '$case_uid'");
	$case_id		=	$getCaseDetails[0]['id'];
	
	//Check already attached with case or not
	$checkAlreadyExists	=	$AdminDAO->getrows("attorneys_cases","id","case_id = '$case_id' AND attorney_id = '$addressbookid'");
	if(sizeof($checkAlreadyExists) == 0)
	{
		$attrcase_fields		=	array('case_id','attorney_id');
		$attrcase_values		=	array($case_id,$_SESSION['addressbookid']);
		$AdminDAO->insertrow("attorneys_cases",$attrcase_fields,$attrcase_values);
	}
	
	$_SESSION['responded_case_uid']			=	'';
	$_SESSION['responded_attrorney_uid']	=	'';
	$_SESSION['responded_discovery_uid']	=	'';
}
/***********************************************
*END::If Responded Attornery comes from serve link
************************************************/

//$AdminDAO->displayquery=1;
$cases	=	$AdminDAO->getrows("cases c,attorneys_cases ac",
								"c.id as id,c.case_attorney,c.trial,c.uid,c.plaintiff,c.defendant,case_title,case_number,jurisdiction,county_name,judge_name,date_filed,c.attorney_id,c.allow_reminders",
								"c.id = ac.case_id AND (ac.attorney_id = :addressbookid OR c.case_attorney = :case_attorney)   GROUP BY c.id",
								array("addressbookid"=>$addressbookid,"case_attorney"=>$loggedin_email),
								"c.id","DESC");
/*$cases	=	$AdminDAO->getrows("cases c",
								"c.id as id,c.case_attorney,c.trial,c.uid,c.plaintiff,c.defendant,case_title,case_number,jurisdiction,county_name,judge_name,date_filed,c.attorney_id,c.allow_reminders",
								"c.attorney_id = :addressbookid   GROUP BY c.id",
								array("addressbookid"=>$addressbookid),"c.id","DESC");*/ 

//print_r($cases);
//$AdminDAO->displayquery=0;

?>
<div id="screenfrmdiv" style="display: block;">
<div class="col-lg-12">
    <div class="hpanel">
        <div class="panel-heading" align="center">
            <h3 align="center"><strong>My Cases</strong></h3>  
        </div>
        <div class="panel-body">
        	<div class="panel panel-primary">
            <div class="panel-heading">
            <div class="row">
            	<div class="col-md-4">
                <?php /*?><table>
                	<tr style="padding:5px">
                    	<td><div class="swatch" style="background-color: rgb(51, 158, 53);width: 48px;height: 25px;border-radius: 5px;border: 1px solid #666; margin:5px"></div></td>
                        <td>Us</td>
                    	<td><div class="swatch" style="background-color: rgb(255, 0, 0);width: 48px;height: 25px;border-radius: 5px;border: 1px solid #666; margin:5px"></div></td>
                        <td>Others</td>
                    </tr>
                </table><?php */?>
                </div>
                <div class="col-md-8" align="right">
                	<a href="javascript:;" class="btn btn-success" onclick="javascript: selecttab('46_tab','case.php','46');"><i class="fa fa-plus"></i> Add New Case</a>
                </div>
            </div>
            </div>
            
            <div class="panel-body">
            	<div class="row">
                <div class="col-md-12" style="margin-top:10px">
                    <table class="table table-bordered table-hover" id="datatable">
                        <thead style="background-color: #F7F9FA;">
                            <tr>
                                <?php /*?><th>Case Title</th>
                                <th width="50px">
                                
                                </th><?php */?>
                                <th>Case Name</th>
                                <th>Case Number</th>
                                <th>County</th>
                                <th>Trial Date</th>
                                <th width="17%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($cases as $case)
                            {
								$fkcaseid			=	$case['id'];
								$loggedin_email		=	$_SESSION['loggedin_email'];
								$case_attorney		=	$case['case_attorney'];
								
								//$AdminDAO->displayquery=1;
								$iscaseteammember	=	$AdminDAO->getrows("attorney a,case_team ct",
															"ct.id",
															"a.id 				= 	ct.attorney_id 	AND 
															ct.is_deleted 		= 	0 				AND 
															ct.fkcaseid 		= 	:fkcaseid 		AND 
															a.attorney_email 	= 	:email",
															array("email"=>$loggedin_email,"fkcaseid"=>$fkcaseid));
								
								//$AdminDAO->displayquery=0;
								$attorney_id	=	$case['attorney_id'];
								
								if(!empty($iscaseteammember) && $attorney_id != $_SESSION['addressbookid'])
								{
									continue;
								}
								/**
								* 1) Creator 2) Case Team Membber 3) Primary Attorney
								**/
								if($attorney_id == $_SESSION['addressbookid']  || $case_attorney == $loggedin_email)
								{
									$isowner	=	1;
									$style		=	"style='background-color:rgb(51, 158, 53)'";
								}
								else
								{
									$isowner	=	0;
									$style		=	"style='background-color:rgb(255, 0, 0)'";
								}
                                ?>
                                <tr>
                                <?php /*?><td <?php  echo $style; ?>> <?php //echo count($iscaseteammember);?></td>
                                    <td>
                                        <a href="javascript:;" onclick="javascript: selecttab('45_tab','discoveries.php?pid=<?php echo $case['id'];?>','45');">
                                        <?php echo $case['case_title'].$case['id']?>
                                        </a>
                                    </td><?php */?>
                                    <td><?php echo $case['case_title']?></td>
                                    <td><?php echo $case['case_number']?></td>
                                    <td><?php echo $case['county_name']?></td>
                                    <td>
                                    <?php if($case['trial'] != "0000-00-00") {echo dateformat($case['trial']);} else{echo "-";}?>
                                    </td>
                                    <td align="right">
                                    <a href="javascript:;" class="btn btn-info" title="Edit case" id="newcase" onclick="javascript: selecttab('46_tab','case.php?id=<?php echo $case['id'];?>','46');"><i class="fa fa-edit"></i> Edit</a>
                                    <?php
									/*if($isowner==1)
									{
									?>
                                    <a href="javascript:;" class="btn btn-info" title="Edit case" id="newcase" onclick="javascript: selecttab('46_tab','case.php?id=<?php echo $case['id'];?>','46');"><i class="fa fa-edit"></i> Edit</a>
									<?php
									}
									else
									{
									?>
                                    <a href="javascript:;" class="btn btn-info" title="View Detail" onclick="javascript: selecttab('51_tab','casedetail.php?id=<?php echo $case['id'];?>','51');"><i class="fa fa-list"></i> View Detail</a>
                                    <?php
									}*/
									?>
                                    <a href="javascript:;" class="btn btn-purple"  title="Click to view discoveries" onclick="javascript: selecttab('45_tab','discoveries.php?pid=<?php echo $case['id'];?>','45');"><i class="fa fa-eye"></i> Discovery</a></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            </div>
            </div>
        	
        </div>
    </div>
</div>
</div>
<script src="<?php echo VENDOR_URL; ?>sweetalert.min.js"></script>
<script>
$(function () 
{
	/*$('#datatable').dataTable(
	{
		"searching": false,
		"paging":   false,
		"ordering": false,
		"info":     false
	});*/
});

</script>
