<?php
@session_start();
require_once("adminsecurity.php");

$case_id	=	$_GET['pid'];
$iscancel	=	@$_GET['iscancel'];
if($iscancel != 1)
{
	$iscancel == 0;
}
$discoveries	=	$AdminDAO->getrows(
										"discoveries,cases,forms,system_addressbook",
										"	discoveries.propounding_uid,
											discoveries.responding_uid,							
											discoveries.id,
											discoveries.uid as d_uid,
											discoveries.propounding,
											discoveries.responding,
											discoveries.served,
											discoveries.due,
											discoveries.type,
											discoveries.attorney_id as creator_id,
											discoveries.discovery_name,
											CONCAT(firstname,' ', lastname) AS attorney,
											CONCAT(firstname,' ', lastname) AS creator,
											CONCAT(case_title,' (', case_number,')') AS this_case,
											case_title,
											form_name,
											is_served,
											form_id,
											short_form_name,
											set_number,
											IF(send_date='0000-00-00 00:00:00', '-', send_date) send_date
										",
										"
											cases.id 				= 	discoveries.case_id 	AND
											forms.id 				= 	discoveries.form_id 	AND
											pkaddressbookid 		= 	discoveries.attorney_id AND
											discoveries.parentid	=	0						AND
											cases.id 				=  	:case_id ORDER BY discoveries.discovery_name ASC
										",
										array(":case_id"		=>	$case_id ));

function getclientname($id)
{
	global $AdminDAO;
	$clients			=	$AdminDAO->getrows('clients',"*","id= :id",array('id'=>$id));
	return $clients[0]['client_name'];
}
function getNameFromAddressbook($id)
{
	global $AdminDAO;
	$data			=	$AdminDAO->getrows('system_addressbook',"*","pkaddressbookid= :id",array('id'=>$id));
	return $data[0]['firstname']." ".$data[0]['lastname'];
}
$totalInternal	=	0;
$totalExternal	=	0;
foreach($discoveries as $discoveryData)
{
	$creator_id		=	$discoveryData['creator_id'];
	if($discoveryData['type'] == 2)
	{
		$totalInternal++;
	}
	else
	{
		$totalExternal++;
	}
}
$loggedin_email		=	$_SESSION['loggedin_email'];
//$AdminDAO->displayquery=1;
$iscaseteammember	=	$AdminDAO->getrows("attorney a,case_team ct",
											"ct.id",
											"a.id 				= 	ct.attorney_id 	AND 
											ct.is_deleted 		= 	0 				AND 
											ct.fkcaseid 		= 	:fkcaseid 		AND 
											a.attorney_email 	= 	:email",
											array("email"=>$loggedin_email,"fkcaseid"=>$case_id));

//$AdminDAO->displayquery=0;
?>
<style>
.list-menu
{
	font-size:15px !important;
	border-bottom:1px solid #CCC !important;
}
</style>
<div id="screenfrmdiv" style="display: block;">
<div class="col-lg-12">
    <div class="hpanel">
        <div class="panel-heading" align="center">
            <h3 align="center"><small>Discovery for</small><br /><strong><?php echo $discoveries[0]['case_title'];?></strong></h3>  
        </div>
        <div class="panel-body">
            <div class="panel panel-primary">
            <div class="panel-heading">
            <div class="row">
            	<div class="col-md-8">
                <!--<span style="font-size:18px; font-weight:600">Discovery</span>-->
                </div>
                <div class="col-md-4" align="right">
                	<p style="text-align: center;width: 290px;"> Create Discovery as the:</p>
                	<button  class="btn btn-success" onclick="javascript: selecttab('47_tab','discovery.php?pid=<?php echo $case_id;?>&type=1','47');"> <!--<i class="fa fa-play fa-rotate-180"></i>--> Propounder <?php instruction(4) ?></button>
                	<span style="padding-left:3px;padding-right:3px">or</span>
                    <button  class="btn btn-success" onclick="javascript: selecttab('47_tab','discovery.php?pid=<?php echo $case_id;?>&type=2','47');"> <!--<i class="fa fa-play"></i>--> Respondent <?php instruction(5) ?></button>
                </div>
            </div>
            </div>
            <div class="panel-body">
            <div class="row">
                <div class="col-md-12" style="margin-top:10px">
                    <table class="table table-bordered" id="datatable2">
            	<thead>
                	<tr>
                    <?php /*?>	<th>Name</th><?php */?>
                    	<td width="20px"></td>
                    	<th>Title</th>
                    	<th>Propounding</th>
                    	<th>Responding</th>
                        <th	 width="9%">Served On</th>
                    	<th  width="9%">Response Due</th>
                        <th>Created By</th>
                        <th>Service Method</th>
                    	<th>Action</th>
                    </tr>
                </thead>
                <tbody>
                	<?php
					if(sizeof($discoveries) > 0)
					{
						foreach($discoveries as $discovery)
						{
							$discoveryName	=	$discovery['discovery_name'];
							$discoverySet	=	$discovery['set_number'];
							$discoveryUID	=	$discovery['d_uid'];  
							$PDF_FileName	=	strtoupper($discoveryName." [Set ".$discoverySet."]").".pdf";
							$RequestPDF_FileName	=	UPLOAD_URL."documents/".$discoveryUID."/".$PDF_FileName;
							$ResponsePDF_FileName	=	UPLOAD_URL."documents/".$discoveryUID."/"."RESPONSE TO ".$PDF_FileName;		
							//$RequestPDF_FileName	=	"makepdf.php?id=".$discovery['d_uid']."&view=1";
							//$ResponsePDF_FileName	=	"makepdf.php?id=".$discovery['d_uid']."&view=0";
							$totalChilds			=	0;
							$totalChildsNotIncludes	=	0;
							$d_id			=	$discovery['id']; 
							$creator_id		=	$discovery['creator_id'];
							$is_submitted	=	$discovery['is_submitted']; 
							$is_served		=	$discovery['is_served'];
							$discoveryType	=	$discovery['type']; //I External 2: Internal
							$discovery_ACL	=	array();
							
							if($discoveryType == 2)
							{
								/*
								 * Internal Discovery should be shown to creator only 
								 */
								 
								if($creator_id == $_SESSION['addressbookid'] || !empty($iscaseteammember))
								{
									
								}
								else
								{
									continue;
								}
								
								/**
								* Check to see login user is responding party attorney or not
								* If he is the attorney of responding party then we give him option to respond in external discovery.
								**/
								$client_responding			=	$discovery['responding'];
								$isResPartyAttorney			=	$AdminDAO->getrows('attorney a,client_attorney ca',"*","ca.client_id = :client_id AND a.id = ca.attorney_id AND ca.case_id = :case_id ",array('client_id'=>$client_responding,'case_id'=>$case_id));
								$respondingPartyAttr		=	array();
								foreach($isResPartyAttorney as $data_attr)
								{
									$respondingPartyAttr[]	=	$data_attr['attorney_email'];
								}
								/**
								* GET RESPONSES OF PARENT INTERNAL DISCOVERY
								**/
								$showDueDate	=	1;
								$responses		=	$AdminDAO->getrows('responses',"*","fkdiscoveryid = :fkdiscovery_id ",array('fkdiscovery_id'=>$d_id));
								foreach($responses as $responsedata)
								{
									if($responsedata['isserved'] == 1)
									{
										$showDueDate	=	0;
									}
								}
								
								/**
								* SET UP ACL FOR DISCOVERIES
								**/
								//if($is_served == 1)
								{
									$discovery_ACL[]	= "request-pdf";
								}
								$discovery_ACL[]	= "view";
								$discovery_ACL[]	= "edit";
								$discovery_ACL[]	= "delete";
								if(sizeof($responses) == 0)
								{
									$discovery_ACL[]	= "respond";
								}
								?>
								<tr style="background-color:#18fd4736">
									<td style="text-align:center; vertical-align:middle">
									<a href="javascript:;" id="plusBtn<?php echo $d_id ?>"  onclick="showHide(1,'<?php echo $d_id ?>')">
										<img src="<?php echo ASSETS_URL."images/plus.png" ?>" width="15px" />
									</a>
									<a href="javascript:;" id="minusBtn<?php echo $d_id ?>" style="display:none" onclick="showHide(2,'<?php echo $d_id ?>')">
										<img src="<?php echo ASSETS_URL."images/minus.png" ?>" width="15px" />
									</a>
									</td>
									<td>
									<?php 
									if($discovery['discovery_name'] == '')
									{
										echo $discovery['form_name']." [Set ".$discovery['set_number']."]";//." (".$discovery['short_form_name'].")"
									}
									else
									{
										echo $discovery['discovery_name']." [Set ".$discovery['set_number']."]";
									}
									
									?>
									</td>
									<td><?php echo getclientname($discovery['propounding'])?></td>
									<td><?php echo getclientname($discovery['responding']);?></td>
									<td><?php echo dateformat($discovery['served'])?></td>
									<td>
									<?php 
									if(/*sizeof($responses) == 0 || */$showDueDate == 1)
									{
										echo dateformat($discovery['due']);
                                    }
                                    else
                                    {
                                    	echo "-";
                                    }
									?>
                                    </td>
									<td>
									<?php echo $discovery['creator']; ?>
									</td> 
                                    <td>
									<?php
                                    if($discoveryType == 1)
                                    {
                                        echo "EasyRogs";//"External";
                                    }
                                    else
                                    {
                                        echo "Other";//"Internal";
                                    }
                                    ?>
                                    </td>
									<td align="center">
										<div class="dropdown">
											<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action
												<span class="caret"></span>
											</button>
											<ul class="dropdown-menu dropdown-menu-right" role="menu">
												<?php
												if(in_array("view",$discovery_ACL))
												{
												?> 
													<li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','view.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $discovery['d_uid']?>&response_id=0&view=1','49');"><i class="fa fa-eye"></i> View</a></li>
													<?php /*?><li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','discoverydetails.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $discovery['d_uid']?>&view=1&respond=0','49');"><i class="fa fa-eye"></i> View</a></li><?php */?>
												<?php
												}
												if(in_array("respond",$discovery_ACL))
												{
												?>
													<li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','discoverydetails.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $discovery['d_uid']?>&view=0&respond=1&response_id=0','49');"><i class="fa fa-edit"></i> Respond</a></li>
												<?php
												}
												if(in_array("edit",$discovery_ACL))
												{
												?>
													<li class="list-menu"><a href="javascript:;"  onclick="javascript: selecttab('47_tab','discovery.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $discovery['id']?>&supp=0','47');"><i class="fa fa-edit"></i> Edit</a></li>
												<?php
												}
												if(in_array("request-pdf",$discovery_ACL))
												{
												?>
													<li class="list-menu"><a href="makepdf.php?id=<?php echo $discovery['d_uid'];?>&view=1" target="_blank"   ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
												<?php
												}
												if(in_array("delete",$discovery_ACL))
												{
												?>
													<li class="list-menu"><a href="javascript:;"   onclick="javascript: buttondeletediscovery('<?php echo $discovery['d_uid']?>');"><i class="fa fa-trash"></i> Delete</a></li>
												<?php
												}
												?>
											</ul>
										</div> 
									</td>
								</tr>
								<?php
								$totalChilds = $totalChilds	+ sizeof($responses);
								if(!empty($responses))
								{
									foreach($responses as $response_data)
									{
										$response_id	=	$response_data['id'];
										
										$response_ACL	=	array('response-pdf','view');
										
										if($response_data['isserved'] == 1)
										{
											$response_ACL[]	= "supp-amend";
											if($loggedin_email == 'jeff@jeffschwartzlaw.com')
											{
												$response_ACL[]	= "unserve";
											}
											
										}
										else
										{
											$response_ACL[]	= "edit";
											$response_ACL[]	= "delete";
										}
										
										?>
										<tr class="group_<?php echo $d_id ?>" style="display:none">
										<td></td>
										<td><?php echo $response_data['responsename'];?></td>
										<td><?php echo getclientname($discovery['propounding'])?></td>
										<td><?php echo getclientname($discovery['responding']);?></td>
										<td><?php if($response_data['isserved'] == 1) {echo dateformat($response_data['servedate']);} else{echo "";}?></td>
										<td><?php if($response_data['isserved'] != 1) {echo dateformat($discovery['due']);} else{echo "-";}?></td>
										<td>
										<?php echo $discovery['creator']; ?>
										</td>
										<td>
										<?php
                                        if($discoveryType == 1)
										{
											echo "EasyRogs";//"External";
										}
										else
										{
											echo "Other";//"Internal";
										}
                                        ?>
                                        </td>
										<td align="center">
											<div class="dropdown">
												<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action
													<span class="caret"></span>
												</button>
												<ul class="dropdown-menu dropdown-menu-right" role="menu">
													<?php
													
													if(in_array("view",$response_ACL))
													{
													?>
														<li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','view.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $discovery['d_uid']?>&view=0&response_id=<?php echo $response_id?>','49');"><i class="fa fa-eye"></i> View</a></li>
													<?php
													}
													if(in_array("edit",$response_ACL))
													{
													?>
														<li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','discoverydetails.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $discovery['d_uid']?>&view=0&respond=1&response_id=<?php echo $response_id?>','49');"><i class="fa fa-edit"></i> Edit</a></li>
													<?php
													}
													if(in_array("supp-amend",$response_ACL))
													{
													?>
														<li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','discoverydetails.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $discovery['d_uid']?>&supp=1&view=0&respond=1&response_id=<?php echo $response_id?>','49');"><i class="fa fa-refresh"></i> Supp/Amend</a></li>
													<?php
													}
													
													if(in_array("response-pdf",$response_ACL))
													{
													?>
														<li class="list-menu"><a href="makepdf.php?id=<?php echo $discovery['d_uid'];?>&view=0&response_id=<?php echo $response_id?>" target="_blank" ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
													<?php
													}
													if(in_array("unserve",$response_ACL))
													{
													?>
														<li class="list-menu"><a href="javascript:;"   onclick="javascript: buttonunserveresponse('<?php echo $response_id?>');"><i class="fa fa-undo"></i> Unserve</a></li>
													<?php
													}
													if(in_array("delete",$response_ACL))
													{
													?>
														<li class="list-menu"><a href="javascript:;"   onclick="javascript: buttondeleteresponse('<?php echo $response_id?>');"><i class="fa fa-trash"></i> Delete</a></li>
													<?php
													}
													?>
												</ul>
											</div>
										</td>
										</tr>
										<?php
									}
								}
								if($totalChilds == 0)
								{
								?>
								<script>
								//$( document ).ready(function() 
								//{
									$("#plusBtn<?php echo $d_id ?>").hide();
									$("#minusBtn<?php echo $d_id ?>").hide();
								//});
								</script>
								<?php
								}
							}
							else if($discoveryType == 1)
							{
								//if($creator_id == $_SESSION['addressbookid'])
								{
									$RequestPDF_FileName	=	"makepdf.php?id=".$discovery['d_uid']."&view=1";
									//$ResponsePDF_FileName	=	"makepdf.php?id=".$discovery['d_uid']."&view=0";
								}
								if($creator_id == $_SESSION['addressbookid'] || !empty($iscaseteammember))
								{
									//OWNER HERE
								}
								else
								{
									if(!$is_served)
									{
										$totalChildsNotIncludes++;
										continue;
									}
								}
								/*if($is_served != 1 && $creator_id != $_SESSION['addressbookid'])
								{
									continue;
								}*/
								/**
								* Check to see login user is responding party attorney or not
								* If he is the attorney of responding party then we give him option to respond in external discovery.
								**/
								$client_responding	=	$discovery['responding'];
								$isResPartyAttorney	=	$AdminDAO->getrows('attorney a,client_attorney ca',"*","ca.client_id = :client_id AND a.id = ca.attorney_id AND ca.case_id = :case_id ",array('client_id'=>$client_responding,'case_id'=>$case_id));
								$respondingPartyAttr=	array();
								foreach($isResPartyAttorney as $data_attr)
								{
									$respondingPartyAttr[]	=	$data_attr['attorney_email'];
								}
								/**
								* GET RESPONSES OF PARENT EXTERNAL DISCOVERY
								**/
								$responses		=	$AdminDAO->getrows('responses',"*","fkdiscoveryid = :fkdiscovery_id ",array('fkdiscovery_id'=>$d_id));
								$showDueDate	=	1;
								foreach($responses as $responsedata)
								{
									if($responsedata['isserved'] == 1)
									{
										$showDueDate	=	0;
									}
								}
								
								/**
								* SET UP ACL FOR DISCOVERIES
								**/  
								if($is_served == 1)
								{
									$discovery_ACL[]	= "view";
									$discovery_ACL[]	= "request-pdf";
									if($creator_id == $_SESSION['addressbookid'])
									{
										$discovery_ACL[]	= "supp-amend";	
										
									}
									if($loggedin_email == 'jeff@jeffschwartzlaw.com')
									{
										$discovery_ACL[]	= "unserve";
									}
									if(in_array($_SESSION['loggedin_email'],$respondingPartyAttr) && sizeof($responses) == 0)
									{
										$discovery_ACL[]	= "respond";
										//$discovery_ACL[]	= "response-pdf";
									}
								}
								else
								{
									$discovery_ACL[]	= "edit";
									$discovery_ACL[]	= "delete";
									$discovery_ACL[]	= "request-pdf";
								}
								
								
								?> 
								<tr style="background-color:#18fd4736">
									<td style="text-align:center; vertical-align:middle">
									 <a href="javascript:;" id="plusBtn<?php echo $d_id ?>" onclick="showHide(1,'<?php echo $d_id ?>')">
										<img src="<?php echo ASSETS_URL."images/plus.png" ?>" width="15px" />
									</a>
									<a href="javascript:;" id="minusBtn<?php echo $d_id ?>" style="display:none" onclick="showHide(2,'<?php echo $d_id ?>')">
										<img src="<?php echo ASSETS_URL."images/minus.png" ?>" width="15px" />
									</a>
									</td>
									<td>
									<?php 
									if($discovery['discovery_name'] == '')
									{
										echo $discovery['form_name']." [Set ".$discovery['set_number']."]";//." (".$discovery['short_form_name'].")"
									}
									else
									{
										echo $discovery['discovery_name']." [Set ".$discovery['set_number']."]";
									}
									
									?>
									</td>
									<td><?php echo getclientname($discovery['propounding'])?></td>
									<td><?php echo getclientname($discovery['responding']);?></td>
									<td><?php echo dateformat($discovery['served'])?></td>
									<td>
                                    <?php 
									if(/*sizeof($responses) == 0*/ $showDueDate == 1)
									{
										echo dateformat($discovery['due']);
                                    }
                                    else
                                    {
                                    	echo "-";
                                    }
									?>
                                    </td>
									<td>
									<?php echo $discovery['creator']; ?>
									</td>
                                    <td>
                                    <?php
									if($discoveryType == 1)
                                    {
                                        echo "EasyRogs";//"External";
                                    }
                                    else
                                    {
                                        echo "Other";//"Internal";
                                    }
									?>
                                    </td>
									
									<td align="center">
										<div class="dropdown">
											<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action
												<span class="caret"></span>
											</button>
											<ul class="dropdown-menu dropdown-menu-right" role="menu">
												<?php
												if(in_array("view",$discovery_ACL))
												{
												?>
													<li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','view.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $discovery['d_uid']?>&response_id=0&view=1','49');"><i class="fa fa-eye"></i> View</a></li>
													<?php /*?><li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','discoverydetails.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $discovery['d_uid']?>&view=1&respond=0','49');"><i class="fa fa-eye"></i> View</a></li><?php */?>
												<?php
												}
												if(in_array("edit",$discovery_ACL))
												{
												?>
													<li class="list-menu"><a href="javascript:;"  onclick="javascript: selecttab('47_tab','discovery.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $discovery['id']?>&supp=0','47');"><i class="fa fa-edit"></i> Edit</a></li>
												<?php
												}
												if(in_array("respond",$discovery_ACL))
												{
												?>
													<li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','discoverydetails.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $discovery['d_uid']?>&view=0&respond=1&response_id=0','49');"><i class="fa fa-edit"></i> Respond</a></li>
												<?php
												}
												if(in_array("supp-amend",$discovery_ACL))
												{
												?>
													<li class="list-menu"><a href="javascript:;"  onclick="javascript: selecttab('47_tab','discovery.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $discovery['id']?>&supp=1','47');"><i class="fa fa-refresh"></i> Supp/Amend</a></li>
												<?php
												}
												
												if(in_array("response-pdf",$discovery_ACL))
												{
												?>
													<li class="list-menu"><a href="<?php echo $ResponsePDF_FileName ?>" target="_blank" ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
												<?php
												}
												if(in_array("request-pdf",$discovery_ACL))
												{
												?>
													<li class="list-menu"><a href="<?php echo $RequestPDF_FileName ?>" target="_blank"   ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
												<?php
												}
												if(in_array("unserve",$discovery_ACL))
												{
												?>
													<li class="list-menu"><a href="javascript:;"   onclick="javascript: buttonunservediscovery('<?php echo $discovery['d_uid']?>');"><i class="fa fa-undo"></i> Unserve</a></li>
												<?php
												}
												if(in_array("delete",$discovery_ACL))
												{
												?>
													<li class="list-menu"><a href="javascript:;"   onclick="javascript: buttondeletediscovery('<?php echo $discovery['d_uid']?>');"><i class="fa fa-trash"></i> Delete</a></li>
												<?php
												}
												?>
											</ul>
										</div>
									</td>
								</tr>
								<?php
								$totalChilds	=	$totalChilds+sizeof($responses);
								
								if(!empty($responses))
								{
									foreach($responses as $response_data)
									{
										$response_creator_id	=	$response_data['created_by'];
										$isserved				=	$response_data['isserved'];
										$response_id			=	$response_data['id'];
										$response_ACL			=	array();
										//if($response_creator_id == $_SESSION['addressbookid'])
										{
											//$RequestPDF_FileName	=	"makepdf.php?id=".$discovery['d_uid']."&view=1";
											$ResponsePDF_FileName	=	"makepdf.php?id=".$discovery['d_uid']."&view=0&response_id=".$response_id;
										}
										if($isserved != 1 && $response_creator_id != $_SESSION['addressbookid'])
										{
											$totalChildsNotIncludes++;
											continue;
										}
										
										/*if(in_array($_SESSION['loggedin_email'],$respondingPartyAttr))
										{
											$response_ACL[]		= "edit";
										}*/
										if($isserved == 1)
										{
											if( $response_creator_id == $_SESSION['addressbookid'])
											{
												$response_ACL[]		= "supp-amend";
												
											}
											if($loggedin_email == 'jeff@jeffschwartzlaw.com')
											{
												$response_ACL[]		=	"unserve";
											}
											$response_ACL[]		= "view";
											$response_ACL[]		= "response-pdf";
										}
										else
										{
											if( $response_creator_id == $_SESSION['addressbookid'])
											{
												$response_ACL[]	= "edit";
												$response_ACL[]	= "delete";
												$response_ACL[]	= "response-pdf";
											}
										}
										?> 
										<tr class="group_<?php echo $d_id ?>" style="display:none">
										<td><?php 
										
										/*echo "isserved:$isserved<br>";
										echo "response_creator_id:$response_creator_id<br>";
										echo "loggedin:".$_SESSION['addressbookid']."<br>";
										dump($responses); */
										
										
										?></td>
										<td><?php echo $response_data['responsename'];?></td>
										<td><?php echo getclientname($discovery['propounding'])?></td>
										<td><?php echo getclientname($discovery['responding']);?></td>
										<td><?php if($isserved == 1){echo dateformat($response_data['servedate']);}?></td>
                                        <td><?php if($isserved != 1) {echo dateformat($discovery['due']);} else{echo "-";}?></td>
										<td>
										<?php echo  getNameFromAddressbook($response_data['created_by']);?>
										</td>
                                        <td>
										<?php
                                        if($discoveryType == 1)
										{
											echo "EasyRogs";//"External";
										}
										else
										{
											echo "Other";//"Internal";
										}
                                        ?>
                                        </td>
										<td align="center">
											<div class="dropdown">
												<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action
													<span class="caret"></span>
												</button>
												<ul class="dropdown-menu dropdown-menu-right" role="menu">
													<?php
													if(in_array("view",$response_ACL))
													{
													?>
														<li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','view.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $discovery['d_uid']?>&view=0&response_id=<?php echo $response_id?>','49');"><i class="fa fa-eye"></i> View</a></li>
													<?php
													}
													if(in_array("edit",$response_ACL))
													{
													?>
														<li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','discoverydetails.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $discovery['d_uid']?>&view=0&respond=1&response_id=<?php echo $response_id?>','49');"><i class="fa fa-edit"></i> Edit</a></li>
													<?php
													}
													if(in_array("supp-amend",$response_ACL))
													{
													?>
                                                    	<li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','discoverydetails.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $discovery['d_uid']?>&supp=1&view=0&respond=1&response_id=<?php echo $response_id?>','49');"><i class="fa fa-refresh"></i> Supp/Amend</a></li>
                                                    	<?php /*?><li class="list-menu"><a href="javascript:;"  onclick="createResponseSupp('<?php echo $response_id?>','<?php echo $d_id?>');"><i class="fa fa-refresh"></i> Supp/Amend</a></li><?php */?>
													<?php
													}
													
													if(in_array("request-pdf",$response_ACL))
													{
													?>
														<li class="list-menu"><a href="<?php echo $RequestPDF_FileName ?>"   ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
													<?php
													}
													if(in_array("response-pdf",$response_ACL))
													{
													?>
														<li class="list-menu"><a href="<?php echo $ResponsePDF_FileName ?>" target="_blank" ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
													<?php
													}
													if(in_array("unserve",$response_ACL))
													{
													?>
														<li class="list-menu"><a href="javascript:;"   onclick="javascript: buttonunserveresponse('<?php echo $response_id?>');"><i class="fa fa-undo"></i> Unserve</a></li>
													<?php
													}
													if(in_array("delete",$response_ACL))
													{
													?>
														<li class="list-menu"><a href="javascript:;"   onclick="javascript: buttondeleteresponse('<?php echo $response_id?>');"><i class="fa fa-trash"></i> Delete</a></li>
													<?php
													}
													?>
												</ul>
											</div>
										</td>
										</tr>
										<?php
									}
								}
								if($d_id > 0)
								{
								/*******************************************
								* 		Supplement Amending Discoveries
								*******************************************/
								//$AdminDAO->displayquery=1;
								$supp_discoveries	=	$AdminDAO->getrows(
													"discoveries,cases,forms,system_addressbook",
													"	discoveries.propounding_uid,
														discoveries.responding_uid,							
														discoveries.id,
														discoveries.uid as d_uid,
														discoveries.propounding,
														discoveries.responding,
														discoveries.served,
														discoveries.due,
														discoveries.type,
														discoveries.attorney_id as creator_id,
														discoveries.discovery_name,
														CONCAT(firstname,' ', lastname) AS attorney,
														CONCAT(firstname,' ', lastname) AS creator,
														CONCAT(case_title,' (', case_number,')') AS this_case,
														case_title,
														form_name,
														is_served,
														form_id,
														short_form_name,
														set_number,
														IF(send_date='0000-00-00 00:00:00', '-', send_date) send_date
													",
													"
														cases.id 				= 	discoveries.case_id 	AND
														forms.id 				= 	discoveries.form_id 	AND
														pkaddressbookid 		= 	discoveries.attorney_id AND
														discoveries.grand_parent_id	=	'$d_id'					AND
														cases.id 				=  	:case_id 	
													",
													/*discoveries.attorney_id = 	:attorney_id 			AND*/
													array(
														":case_id"		=>	$case_id 
														/*,":attorney_id"	=>	$_SESSION['addressbookid']*/)
												  );
									//$AdminDAO->displayquery=0;
									//dump($supp_discoveries);
									$totalChilds	=	$totalChilds+sizeof($supp_discoveries);
									
									foreach($supp_discoveries as $suppdiscovery)
									{
										$supp_d_id			=	$suppdiscovery['id'];
										$supp_creator_id	 =	$suppdiscovery['creator_id'];
										$supp_is_submitted	=	$suppdiscovery['is_submitted'];
										$supp_is_served		=	$suppdiscovery['is_served'];
										$supp_discoveryType	=	$suppdiscovery['type']; //I External 2: Internal
										$supp_discovery_ACL	=	array();
										//if($supp_creator_id == $_SESSION['addressbookid'])
										//{
											$RequestPDF_FileName	=	"makepdf.php?id=".$suppdiscovery['d_uid']."&view=1";
											//$ResponsePDF_FileName	=	"makepdf.php?id=".$suppdiscovery['d_uid']."&view=0";
										//}
										//else 
										//{
											//$suppdiscoveryName		=	$suppdiscovery['discovery_name'];
											//$suppdiscoverySet		=	$suppdiscovery['set_number'];
											//$suppdiscoveryUID		=	$suppdiscovery['d_uid'];
											//$PDF_FileName			=	strtoupper($suppdiscoveryName." [Set ".$suppdiscoverySet."]").".pdf";
											//$RequestPDF_FileName	=	UPLOAD_URL."documents/".$suppdiscoveryUID."/".$PDF_FileName;
											//$RequestPDF_FileName	=	"makepdf.php?id=".$suppdiscovery['d_uid']."&view=0&response_id=".$response_id;
										//}
										if($supp_discoveryType == 2)
										{
											$totalChildsNotIncludes++;
											continue;
										}
										
										if($supp_creator_id == $_SESSION['addressbookid'] || !empty($iscaseteammember))
										{
											//OWNER HERE
										}
										else
										{
											if(!$supp_is_served)
											{
												$totalChildsNotIncludes++;
												continue;
											}
										}
										/*if($supp_is_served != 1 && $supp_creator_id != $_SESSION['addressbookid'])
										{
											$totalChildsNotIncludes++;
											continue;
										}*/
										/**
										* Check to see login user is responding party attorney or not
										* If he is the attorney of responding party then we give him option to respond in external discovery.
										**/
										$supp_client_responding	=	$suppdiscovery['responding'];
										$supp_isResPartyAttorney	=	$AdminDAO->getrows('attorney a,client_attorney ca',"*","ca.client_id = :client_id AND a.id = ca.attorney_id AND ca.case_id = :case_id ",array('client_id'=>$supp_client_responding,'case_id'=>$case_id));
										$supp_respondingPartyAttr=	array();
										foreach($supp_isResPartyAttorney as $supp_data_attr)
										{
											$supp_respondingPartyAttr[]	=	$supp_data_attr['attorney_email'];
										}
										
										/**
										* GET RESPONSES OF SUPP/EMEND EXTERNAL DISCOVERY
										**/
										$showDueDateSupp	=	1;
										$suppresponses	=	$AdminDAO->getrows('responses',"*","fkdiscoveryid = :fkdiscovery_id ",array('fkdiscovery_id'=>$supp_d_id));
										foreach($suppresponses as $suppresponsesdata)
										{
											if($suppresponsesdata['isserved'] == 1)
											{
												$showDueDateSupp	=	0;
											}
										}
										
										/**
										* SET UP ACL FOR DISCOVERIES
										**/
										if($supp_is_served == 1)
										{
											$supp_discovery_ACL[]	= "view";
											$supp_discovery_ACL[]	= "request-pdf";
											if($supp_creator_id == $_SESSION['addressbookid'])
											{
												$supp_discovery_ACL[]	= "supp-amend";
												
											}
											if($loggedin_email == 'jeff@jeffschwartzlaw.com')
											{
												$supp_discovery_ACL[]	= "unserve";
											}
											if(in_array($_SESSION['loggedin_email'],$supp_respondingPartyAttr) && sizeof($suppresponses) == 0)
											{
												$supp_discovery_ACL[]	= "respond";
											}
										}
										else
										{
											$supp_discovery_ACL[]	= "request-pdf";
											$supp_discovery_ACL[]	= "edit";
											$supp_discovery_ACL[]	= "delete";
										}
										
										//dump($discovery_ACL);
										?>
										<tr class="group_<?php echo  $d_id ?>" style="display:none">
											<td><?php //dump($supp_respondingPartyAttr); ?></td>
											<td>
											<?php 
											if($suppdiscovery['discovery_name'] == '')
											{
												echo $suppdiscovery['form_name']." [Set ".$suppdiscovery['set_number']."]";
											}
											else
											{
												echo $suppdiscovery['discovery_name']." [Set ".$suppdiscovery['set_number']."]";
											}
											?>
											</td>
											<td><?php echo getclientname($suppdiscovery['propounding'])?></td>
											<td><?php echo getclientname($suppdiscovery['responding']);?></td>
											<td><?php echo dateformat($suppdiscovery['served'])?></td>
											<td>
                                            <?php 
											if(/*sizeof($suppresponses) == 0*/ $showDueDateSupp == 1)
											{
												echo dateformat($suppdiscovery['due']);
											}
											else
											{
												echo "-";
											}
											?>
                                            </td>
                                            <td>
											<?php echo $suppdiscovery['creator']; ?>
											</td>
                                            <td>
											<?php
                                            if($discoveryType == 1)
											{
												echo "EasyRogs";//"External";
											}
											else
											{
												echo "Other";//"Internal";
											}
                                            ?>
                                            </td>
											<td align="center">
												<div class="dropdown">
													<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action
														<span class="caret"></span>
													</button>
													<ul class="dropdown-menu dropdown-menu-right" role="menu">
														<?php
														if(in_array("view",$supp_discovery_ACL))
														{
														?>
															<li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','view.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $suppdiscovery['d_uid']?>&response_id=0&view=1','49');"><i class="fa fa-eye"></i> View</a></li>
															<?php /*?><li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','discoverydetails.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $suppdiscovery['d_uid']?>&view=1&respond=0','49');"><i class="fa fa-eye"></i> View</a></li><?php */?>
														<?php
														}
														if(in_array("edit",$supp_discovery_ACL))
														{
														?>
															<li class="list-menu"><a href="javascript:;"  onclick="javascript: selecttab('47_tab','discovery.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $suppdiscovery['id']?>&supp=0','47');"><i class="fa fa-edit"></i> Edit</a></li>
														<?php
														}
														if(in_array("respond",$supp_discovery_ACL))
														{
														?>
															<li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','discoverydetails.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $suppdiscovery['d_uid']?>&view=0&respond=1&response_id=0','49');"><i class="fa fa-edit"></i> Respond</a></li>
														<?php
														}
														if(in_array("supp-amend",$supp_discovery_ACL))
														{
														?>
															<li class="list-menu"><a href="javascript:;"  onclick="javascript: selecttab('47_tab','discovery.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $suppdiscovery['id']?>&supp=1','47');"><i class="fa fa-refresh"></i> Supp/Amend</a></li>
														<?php
														}
														if(in_array("response-pdf",$supp_discovery_ACL))
														{
														?>
															<li class="list-menu"><a href="<?php echo $ResponsePDF_FileName; ?>" target="_blank" ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
														<?php
														}
														if(in_array("request-pdf",$supp_discovery_ACL))
														{
														?>
															<li class="list-menu"><a href="<?php echo $RequestPDF_FileName ?>" target="_blank"   ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
														<?php
														}
														if(in_array("unserve",$supp_discovery_ACL))
														{
														?>
															<li class="list-menu"><a href="javascript:;"   onclick="javascript: buttonunservediscovery('<?php echo $suppdiscovery['d_uid']?>');"><i class="fa fa-undo"></i> Unserve</a></li>
														<?php
														}
														if(in_array("delete",$supp_discovery_ACL))
														{
														?>
															<li class="list-menu"><a href="javascript:;"   onclick="javascript: buttondeletediscovery('<?php echo $suppdiscovery['d_uid']?>');"><i class="fa fa-trash"></i> Delete</a></li>
														<?php
														}
														?>
													</ul>
												</div>
											</td>
										</tr>
										<?php  
										$totalChilds	=	$totalChilds+sizeof($suppresponses);
										
										if(!empty($suppresponses))
										{
											foreach($suppresponses as $response_data) 
											{
												$response_id				=	$response_data['id'];
												$supp_response_creator_id	=	$response_data['created_by'];
												$supp_isserved				=	$response_data['isserved'];
												$supp_response_ACL			=	array();
												//if($supp_response_creator_id == $_SESSION['addressbookid'])
												{
													$ResponsePDF_FileName	=	"makepdf.php?id=".$suppdiscovery['d_uid']."&view=0&response_id=".$response_id;
												}
												if($supp_isserved != 1 && $supp_response_creator_id != $_SESSION['addressbookid'])
												{
													$totalChildsNotIncludes++;
													continue;
												}
												/*if(in_array($_SESSION['loggedin_email'],$respondingPartyAttr))
												{
													$supp_response_ACL[]		= "edit";
												}*/
												
												if($supp_isserved == 1)
												{
													if( $supp_response_creator_id == $_SESSION['addressbookid'])
													{
														$supp_response_ACL[]		= "supp-amend";
														
													}
													if($loggedin_email == 'jeff@jeffschwartzlaw.com')
													{
														$supp_response_ACL[]		= "unserve";
													}
													$supp_response_ACL[]		= "view";
													$supp_response_ACL[]		= "response-pdf";
												}
												else
												{
													if( $supp_response_creator_id == $_SESSION['addressbookid'])
													{
														$supp_response_ACL[]	= "edit";
														$supp_response_ACL[]	= "delete";
														$supp_response_ACL[]	= "response-pdf";
													} 
												} 
												?>
												<tr class="group_<?php echo $d_id ?>" style="display:none">
												<td></td>
												<td><?php echo $response_data['responsename'];?></td>
												<td><?php echo getclientname($suppdiscovery['propounding'])?></td>
												<td><?php echo getclientname($suppdiscovery['responding']);?></td>
												<td><?php if($supp_isserved == 1){echo dateformat($response_data['servedate']);}?></td>
                                                <td><?php if($supp_isserved != 1) {echo dateformat($suppdiscovery['due']);} else{echo "-";}?></td>
										
												<td>
												<?php echo  getNameFromAddressbook($response_data['created_by']);?>
												</td>
                                                <td>
												<?php
												if($discoveryType == 1)
												{
													echo "EasyRogs";//"External";
												}
												else
												{
													echo "Other";//"Internal";
												}
												?>
												</td>
												<td align="center">
													<div class="dropdown">
														<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action
															<span class="caret"></span>
														</button>
														<ul class="dropdown-menu dropdown-menu-right" role="menu">
															<?php
															
															if(in_array("view",$supp_response_ACL))
															{
															?>
																<li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','view.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $suppdiscovery['d_uid']?>&view=0&response_id=<?php echo $response_id?>','49');"><i class="fa fa-eye"></i> View</a></li>
															<?php
															}
															if(in_array("edit",$supp_response_ACL))
															{
															?>
																<li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','discoverydetails.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $suppdiscovery['d_uid']?>&view=0&respond=1&response_id=<?php echo $response_id?>','49');"><i class="fa fa-edit"></i> Edit</a></li>
															<?php
															}
															if(in_array("supp-amend",$supp_response_ACL))
															{
															?>
                                                            	<li class="list-menu"><a href="javascript:;"   onclick="javascript: selecttab('49_tab','discoverydetails.php?pid=<?php echo $_GET['pid'];?>&id=<?php echo $suppdiscovery['d_uid']?>&supp=1&view=0&respond=1&response_id=<?php echo $response_id?>','49');"><i class="fa fa-refresh"></i> Supp/Amend</a></li>
																<?php /*?><li class="list-menu"><a href="javascript:;"  onclick="createResponseSupp('<?php echo $response_id?>','<?php echo $supp_d_id?>');"><i class="fa fa-refresh"></i> Supp/Amend</a></li><?php */?>
															<?php
															}
															
															if(in_array("response-pdf",$supp_response_ACL))
															{
															?>
																<li class="list-menu"><a href="<?php echo $ResponsePDF_FileName; ?>" target="_blank" ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
															<?php
															}
															
															if(in_array("unserve",$supp_response_ACL))
															{
															?>
																<li class="list-menu"><a href="javascript:;"   onclick="javascript: buttonunserveresponse('<?php echo $response_id?>');"><i class="fa fa-undo"></i> Unserve</a></li>
															<?php
															}
															if(in_array("delete",$supp_response_ACL))
															{
															?>
																<li class="list-menu"><a href="javascript:;"   onclick="javascript: buttondeleteresponse('<?php echo $response_id?>');"><i class="fa fa-trash"></i> Delete</a></li>
															<?php
															}
															?>
														</ul>
													</div>
												</td>
												</tr>
												<?php
											}
										}
									}
								}
								
								$totalChilds = $totalChilds - $totalChildsNotIncludes;
								
								if($totalChilds <= 0)
								{
								?>
								<script>
								//$( document ).ready(function() 
								//{
									$("#plusBtn<?php echo $d_id ?>").hide();
									$("#minusBtn<?php echo $d_id ?>").hide();
								//});
								</script>
								<?php
								}
							
							}
						
							
						}
					}
                    else
					{
						?>
                        <tr>
                        	<td align="center" colspan="9">
                                <div class="alert alert-danger text-center" role="alert">
                                Sorry no discovery found.
                                </div>
                            </td>
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
<script>
<?php
if(sizeof($discoveries) > 0)
{
?>
$(function () {
        $('#datatable').dataTable({
		  "searching": false,
		   "ordering": false, 
		});

    });
	
<?php
} 
?>
$(document).ready(function(){
  $('.tooltipshow').tooltip({
	   container: 'body'
	  });
});
</script>
<script src="<?php echo VENDOR_URL; ?>sweetalert.min.js"></script>
<script>
/*function createResponseSupp(response_id,discovery_id)
{
	$.post( "responsesuppaction.php", { discovery_id: discovery_id,response_id: response_id}).done(function( data ) 
	{
		var obj = JSON.parse(data); 
		selecttab('49_tab','discoverydetails.php?pid='+obj.case_id+'&id='+obj.uid+'&view=0&respond=1&response_id='+obj.response_id,'49');
	});
}*/


function buttondeleteresponse(response_id)
{
	var title = "Are you sure to delete this response?";
	swal({
		title: title,
		text: "You will not be able to undo this action!",
		icon: "warning",
		buttons: true,
		dangerMode: true,
	})
	.then((willDelete) => {
		if (willDelete) 
		{
			$.post( "deleteresponse.php", { response_id: response_id}).done(function( case_id ) 
			{
				javascript: selecttab('45_tab','discoveries.php?pid=<?php echo $case_id ?>','45');
			});
		}	 
	});
	$( ".swal-button-container:first" ).css( "float", "right" );
}
function buttondeletediscovery(discovery_uid)
{
	var title = "Are you sure to delete this discovery?";
	swal({
		title: title,
		text: "You will not be able to undo this action!",
		icon: "warning",
		buttons: true,
		dangerMode: true,
	})
	.then((willDelete) => {
		if (willDelete) 
		{
			$.post( "deletediscovery.php", { discovery_uid: discovery_uid}).done(function( case_id ) 
			{
				javascript: selecttab('45_tab','discoveries.php?pid='+case_id,'45');
			});
		}	 
	});
	$( ".swal-button-container:first" ).css( "float", "right" );
}
function buttonunservediscovery(discovery_uid)
{
	var title = "Are you sure to Unserve this discovery?";
	swal({
		title: title,
		text: "You will not be able to undo this action!",
		icon: "warning",
		buttons: true,
		dangerMode: true,
	})
	.then((willDelete) => {
		if (willDelete) 
		{
			$.post( "unservediscovery.php", { discovery_uid: discovery_uid}).done(function( case_id ) 
			{
				javascript: selecttab('45_tab','discoveries.php?pid='+case_id,'45');
			});
		}	 
	});
	$( ".swal-button-container:first" ).css( "float", "right" );
}
function buttonunserveresponse(response_id)
{
	var title = "Are you sure to Unserve this response?";
	swal({
		title: title,
		text: "You will not be able to undo this action!",
		icon: "warning",
		buttons: true,
		dangerMode: true,
	})
	.then((willDelete) => {
		if (willDelete) 
		{
			$.post( "unserveresponse.php", { response_id: response_id}).done(function( case_id ) 
			{
				javascript: selecttab('45_tab','discoveries.php?pid=<?php echo $case_id ?>','45');
			});
		}	 
	});
	$( ".swal-button-container:first" ).css( "float", "right" );
}
function showHide(action,discovery_id)
{
	if(action == 2)
	{
		$(".group_"+discovery_id).hide();
		$("#plusBtn"+discovery_id).show();
		$("#minusBtn"+discovery_id).hide();
	}
	else
	{
		$(".group_"+discovery_id).show();
		$("#plusBtn"+discovery_id).hide();
		$("#minusBtn"+discovery_id).show();
	}
}
</script>