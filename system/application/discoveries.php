<?php
require_once __DIR__ . '/../bootstrap.php';
require_once("adminsecurity.php");

use function EasyRogs\_assert as _assert;

$loggedin_email	= $currentUser->user['email'];//$_SESSION['loggedin_email'];
$case_id = $_GET['pid'];

$discoveries = $discoveriesModel->getByUserAndCase($currentUser->id, $case_id );
$currentSide = $sidesModel->getByUserAndCase($currentUser->id, $case_id);
Side::legacyTranslateCaseData($case_id, $discoveries);

function checkSides( $userId1, $userId2 = null ) {
	global $sidesModel, $currentUser, $logger,
			$case_id;
	static $usersAndSides;

	if( !isset($usersAndSides) ) $usersAndSides = $sidesModel->getSidesUsersByCase($case_id);
	if( !isset($userId2) ) $userId2 = $currentUser->id;

	_assert( [$userId1, $userId2] );
	$user1 = searchValue($usersAndSides, $userId1, 'user_id' );
	$user2 = searchValue($usersAndSides, $userId2, 'user_id' );
	_assert( [$user1, $user2], "Something seems wrong with the DB.. corrupted?" );

	return ($user1['side_id'] == $user2['side_id']) ? Side::SAME_SIDE : Side::OTHER_SIDE;
}

function partyEmailAddrs( $clientId ) { global $AdminDAO, $case_id;

	_assert( [$case_id, $clientId] );
	$rows = $AdminDAO->getrows( 'attorney a,client_attorney c_a',"*",
									"c_a.client_id = :client_id AND
									 a.id = c_a.attorney_id AND
									 c_a.case_id = :case_id",
									[ 'client_id'=>$clientId, 'case_id'=>$case_id ] );
	$result = [];
	foreach( $rows as $row ) {
		$result[] = $row['attorney_email'];
	}
	return array_unique($result);
}

?>
<style>
.list-menu {
	font-size:15px !important;
	border-bottom:1px solid #CCC !important;
}
</style>
<div id="screenfrmdiv" style="display: block;">
<div class="col-lg-12">
    <div class="hpanel">
        <div class="panel-heading" align="center">
            <h3 align="center"><small>Discovery for</small><br /><strong><?= $currentSide['case_title'] ?></strong></h3>
			<!-- case:<?= $case_id ?>, <?= $currentSide['case_number'] ?> -->
        </div>
        <div class="panel-body">
            <div class="panel panel-primary">
            <div class="panel-heading">
            <div class="row">
            	<div class="col-md-8">
					<?php if( $_ENV['APP_ENV'] == 'local' ) { ?>
					<a href="#" class="btn btn-info" title="Edit case" onclick="selecttab('46_tab','get-case.php?id=<?= $case_id ?>','46');"><i class="fa fa-edit"></i> Edit</a>
					<?php } ?>
                </div>
                <div class="col-md-4" align="right">
                	<p style="text-align: center;width: 290px;"> Create Discovery as the:</p>

					<button  class="btn btn-success" onclick="selecttab('47_tab','discovery.php?pid=<?= $case_id ?>&type=<?= Discovery::TYPE_EXTERNAL ?>','47');"> Propounder <?= instruction(4, '#fff') ?></button>

                	<span style="padding-left:3px;padding-right:3px">or</span>

                    <button  class="btn btn-success" onclick="selecttab('47_tab','discovery.php?pid=<?= $case_id ?>&type=<?= Discovery::TYPE_INTERNAL ?>','47');"> Respondent <?= instruction(5, '#fff') ?></button>
                </div>
            </div>
            </div>
            <div class="panel-body">
            <div class="row">
                <div class="col-md-12" style="margin-top:10px">
                    <table class="table table-bordered" id="datatable2">
            	<thead>
                	<tr>
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
					if( !sizeof($discoveries) ) {
?>
                        <tr>
                        	<td align="center" colspan="9">
                                <!--<div class="alert alert-danger text-center" role="alert">
                                Sorry no discovery found.
                                </div>-->
                            </td>
                        </tr>
<?php
					}
					else {
						foreach( $discoveries as $discovery ) {
							$id  = $discovery['id'];
							$uid = $discovery['uid'];
							$propoundingClient = $discovery['propounding'];
							$propoundingAttorney = $discovery['propounding_attorney'] ?: -1;
							$respondingClient = $discovery['responding'];
							$creator_id  = $discovery['creator_id'];
							//$is_submitted	= $discovery['is_submitted'];
							$is_served		= $discovery['is_served'];
							$discoveryType	= $discovery['type'];

							$RequestPDF_FileName  = UPLOAD_URL ."documents/". $uid ."/". $discoveriesModel->getTitle($discovery) .".pdf";
							$ResponsePDF_FileName = UPLOAD_URL ."documents/". $uid ."/". $responsesModel->getTitle(0,$discovery) .".pdf";
							$totalChilds			= 0;
							$totalChildsNotIncludes	= 0;

							if( $discoveryType == Discovery::TYPE_INTERNAL ) {
								if( checkSides($creator_id) != Side::SAME_SIDE ) {
									continue; // Internal Discovery should be shown to creator/team only
								}

								/**
								* Check to see login user is responding party attorney or not
								* If he is the attorney of responding party then we give him option to respond in external discovery.
								**/
								$respondingParty = partyEmailAddrs( $discovery['responding'] );
								/**
								* GET RESPONSES OF PARENT INTERNAL DISCOVERY
								**/
								$responses = $responsesModel->getByDiscovery($id);
								$showDueDate = !$responsesModel->isAnyServed($responses);

								/**
								* SET UP ACL FOR DISCOVERIES
								**/
								$discovery_ACL = [];

								$discovery_ACL[] = "request-pdf";
								$discovery_ACL[] = "view";

								$discovery_ACL[] = "edit"; // same side, so we can edit/delete
								$discovery_ACL[] = "delete";

								if( !sizeof($responses) ) {
									$discovery_ACL[] = "respond"; // see the `continue` a few lines before: there's no need to check anything else
								}
?>
								<tr style="background-color:#18fd4736">
									<!-- 1 ("internal" discoveries) -->
									<td style="text-align:center; vertical-align:middle">
										<a href="javascript:" id="plusBtn<?= $id ?>" onclick="showHide('show','<?= $id ?>')">
											<img src="<?= ASSETS_URL."images/plus.png" ?>" width="15px" />
										</a>
										<a href="javascript:" id="minusBtn<?= $id ?>" onclick="showHide('hide','<?= $id ?>')" style="display:none">
											<img src="<?= ASSETS_URL."images/minus.png" ?>" width="15px" />
										</a>
									</td>
									<td> <?= ($_ENV['APP_ENV'] == 'local' ? $id.":" : "") .$discoveriesModel->getTitle($discovery) ?> </td>
									<td> <?= getClientName($discovery['propounding'])                                              ?> </td>
									<td> <?= getClientName($discovery['responding'])                                               ?> </td>
									<td> <?= dateformat($discovery['served'])                                                      ?> </td>
									<td> <?= $showDueDate == 1 ? dateformat($discovery['due']) : "-"                               ?> </td>
									<td> <?= $discovery['creator']                                                                 ?> </td>
                                    <td> <?=  ($discoveryType == Discovery::TYPE_EXTERNAL) ? "AI4Discovery" : "Other"                  ?> 
</td>
									<td align="center">
										<div class="dropdown">
											<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action
												<span class="caret"></span>
											</button>
											<ul class="dropdown-menu dropdown-menu-right" role="menu">
<?php
												if( in_array("view",$discovery_ACL) ) {
?>
													<!-- 1:view --> <li class="list-menu"><a href="javascript:" onclick="selecttab('49_tab','view.php?pid=<?= $case_id ?>&id=<?= $discovery['uid'] ?>&view=<?= Discovery::VIEW_PROPOUNDING ?>&response_id=0','49');"><i class="fa fa-eye"></i> View</a></li>
<?php
												}
												if( in_array("respond",$discovery_ACL) ) {
?>
													<!-- 1:respond --> <li class="list-menu"><a href="javascript:" onclick="selecttab('49_tab','discoverydetails.php?pid=<?= $case_id ?>&id=<?= $discovery['uid'] ?>&view=<?= Discovery::VIEW_RESPONDING ?>&respond=<?= Discovery::TYPE_EXTERNAL ?>&response_id=0','49');"><i class="fa fa-edit"></i> Respond</a></li>
<?php
												}
												if( in_array("edit",$discovery_ACL) ) {
?>
													<!-- 1:edit --> <li class="list-menu"><a href="javascript:"  onclick="selecttab('47_tab','discovery.php?pid=<?= $case_id ?>&id=<?= $discovery['id'] ?>&supp=0','47');"><i class="fa fa-edit"></i> Edit</a></li>
<?php
												}
												if( in_array("request-pdf",$discovery_ACL) ) {
?>
													<!-- 1:pdf --> <li class="list-menu"><a href="makepdf.php?id=<?= $discovery['uid'] ?>&view=<?= Discovery::VIEW_PROPOUNDING ?>" target="_blank"   ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
<?php
												}
												if( in_array("delete",$discovery_ACL) ) {
?>
													<!-- 1:delete --> <li class="list-menu"><a href="javascript:" onclick="doAction('delete','discovery','<?= $discovery['uid'] ?>');"><i class="fa fa-trash"></i> Delete</a></li>
<?php
												}
?>
											</ul>
										</div>
									</td>
								</tr>
<?php
								$totalChilds += sizeof($responses);
								//if( !empty($responses) ) {
									foreach( $responses ?: [] as $response_data ) {
										$response_id = $response_data['id'];
										$mc = $meetConferModel->findByResponseId($response_id, false);

										/**
										* SET UP ACL FOR RESPONSES
										**/
										$response_ACL = [];

										$response_ACL[] = 'response-pdf';
										$response_ACL[] = 'view';
										if( checkSides( $response_id ) == Side::OTHER_SIDE ) {
											$response_ACL[] = 'meet-confer';
										}

										if( $response_data['isserved'] ) {
											$response_ACL[]	= "supp-amend";
											if( $loggedin_email == 'jeff@jeffschwartzlaw.com' ) { // ??
												$response_ACL[]	= "unserve";
											}
										}
										else {
											$response_ACL[]	= "edit";
											$response_ACL[]	= "delete";
										}

?>
										<tr class="group_<?= $id ?>" style="display:none">
											<!-- 2 ("internal" responses) -->
											<td>
												<?php if($mc && $mc['served']): ?>
													<a title="Meet & Confer Letter" href="#meet-and-confer/<?= $response_id ?>" class="meet-confer-button" data-response-id="<?= $response_id ?>"><i class="fa fa-comments-o"></i></a>
												<?php endif; ?>
											</td>
											<td> <?= ($_ENV['APP_ENV'] == 'local' ? $response_data['id'].":" : "") .$responsesModel->getTitle($response_data) ?> </td>
											<td> <?= getClientName($discovery['propounding'])                                                                 ?> </td>
											<td> <?= getClientName($discovery['responding'])                                                                  ?> </td>
											<td> <?= $response_data['isserved'] ? dateformat($response_data['servedate']) : ""                                ?> </td>
											<td> <?= !$response_data['isserved'] ? dateformat($discovery['due']) : "-"                                        ?> </td>
											<td> <?= $discovery['creator'];                                                                                   ?> </td>
											<td> <?= ( $discoveryType == 
Discovery::TYPE_EXTERNAL ) ? "AI4Discovery" : "Other"                                    ?> </td>

										<td align="center">
											<div class="dropdown">
												<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action
													<span class="caret"></span>
												</button>
												<ul class="dropdown-menu dropdown-menu-right" role="menu">
<?php

													if( in_array("view",$response_ACL) ) {
?>
														<!-- 2:view --> <li class="list-menu"><a href="javascript:" onclick="selecttab('49_tab','view.php?pid=<?= $case_id ?>&id=<?= $discovery['uid'] ?>&view=<?= Discovery::VIEW_RESPONDING ?>&response_id=<?= $response_id ?>','49');"><i class="fa fa-eye"></i> View</a></li>
<?php
													}
													if( in_array("edit",$response_ACL) ) {
?>
														<!-- 2:edit --> <li class="list-menu"><a href="javascript:" onclick="selecttab('49_tab','discoverydetails.php?pid=<?= $case_id ?>&id=<?= $discovery['uid'] ?>&view=<?= Discovery::VIEW_RESPONDING ?>&respond=<?= Discovery::TYPE_EXTERNAL ?>&response_id=<?= $response_id ?>','49');"><i class="fa fa-edit"></i> Edit</a></li>
<?php
													}
													if( in_array("supp-amend",$response_ACL) ) {
?>
														<!-- 2:supp --> <li class="list-menu"><a href="javascript:" onclick="selecttab('49_tab','discoverydetails.php?pid=<?= $case_id ?>&id=<?= $discovery['uid'] ?>&supp=1&view=<?= Discovery::VIEW_RESPONDING ?>&respond=<?= Discovery::TYPE_EXTERNAL ?>&response_id=<?= $response_id ?>','49');"><i class="fa fa-refresh"></i> Supp/Amend</a></li>
<?php
													}

													if( in_array("response-pdf",$response_ACL) ) {
?>
														<!-- 2:pdf/response --> <li class="list-menu"><a href="makepdf.php?id=<?= $discovery['uid'] ?>&view=<?= Discovery::VIEW_RESPONDING ?>&response_id=<?= $response_id ?>" target="_blank" ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
<?php
													}
													if( in_array("unserve",$response_ACL) ) {
?>
														<!-- 2:unserve --> <li class="list-menu"><a href="javascript:" onclick="doAction('unserve','response','<?= $response_id ?>');"><i class="fa fa-undo"></i> Unserve</a></li>
<?php
													}
													if( in_array("delete",$response_ACL) ) {
?>
														<!-- 2:delete --> <li class="list-menu"><a href="javascript:" onclick="doAction('delete','response','<?= $response_id ?>');"><i class="fa fa-trash"></i> Delete</a></li>
<?php
													}
													if( in_array("meet-confer",$response_ACL) ) {
?>
														<!-- 2:m&c --> <li class="list-menu"><a href="#meet-and-confer/<?= $response_id ?>" class="meet-confer-button" data-response-id="<?= $response_id ?>"><i class="fa fa-comments-o"></i> Meet & Confer</a></li>
<?php
												  }
?>
												</ul>
											</div>
										</td>
										</tr>
<?php
									}
								//}
								if( !$totalChilds ) {
?>
								<script>
									$("#plusBtn<?= $id ?>").hide();
									$("#minusBtn<?= $id ?>").hide();
								</script>
								<?php
								}
							}
							else { // Discovery::TYPE_EXTERNAL
								_assert( $discoveryType == Discovery::TYPE_EXTERNAL, $discovery );

								//if($creator_id == $_SESSION['addressbookid'])
								{
									$RequestPDF_FileName	= "makepdf.php?id=$uid&view=". Discovery::VIEW_PROPOUNDING;
									//$ResponsePDF_FileName	= "makepdf.php?id=$uid&view=". Discovery::VIEW_RESPONDING;
								}

								if( checkSides($creator_id) != Side::SAME_SIDE ) {
									if( !$is_served ) {
										$totalChildsNotIncludes++;
										continue;
									}
								}
								/**
								* Check to see login user is responding party attorney or not
								* If he is the attorney of responding party then we give him option to respond in external discovery.
								**/
								$respondingParty = partyEmailAddrs( $discovery['responding'] );
								/**
								* GET RESPONSES OF PARENT EXTERNAL DISCOVERY
								**/
								$responses   = $responsesModel->getByDiscovery($id);
								$showDueDate = !$responsesModel->isAnyServed($responses);

								/**
								* SETUP ACL FOR DISCOVERIES
								**/
								$discovery_ACL = [];
								$discovery_ACL[] = "request-pdf"; // always allow PDFing

								if( $is_served ) {
									$discovery_ACL[] = "view";

									if( checkSides( $creator_id ) == Side::SAME_SIDE ) {
										$discovery_ACL[] = "supp-amend";
										$discovery_ACL[] = "change-due-date";

										if( !sizeof($responses) ) {
											$discovery_ACL[] = "add-response";
										}
									}
									if( $loggedin_email == 'jeff@jeffschwartzlaw.com' ) {
										$discovery_ACL[] = "unserve";
									}
									if( in_array( $loggedin_email, $respondingParty ) && !sizeof($responses) ) { //!! TODO if-same-side?
										$discovery_ACL[] = "respond";
									}
								}
								else {
									$discovery_ACL[] = "edit";
									$discovery_ACL[] = "delete";
								}
?>
								<tr style="background-color:#18fd4736">
									<!-- 3 ("external" discoveries) -->
									<td style="text-align:center; vertical-align:middle">
									 <a href="javascript:" id="plusBtn<?= $id ?>" onclick="showHide('show','<?= $id ?>')">
										<img src="<?= ASSETS_URL."images/plus.png" ?>" width="15px" />
									</a>
									<a href="javascript:" id="minusBtn<?= $id ?>" style="display:none" onclick="showHide('hide','<?= $id ?>')">
										<img src="<?= ASSETS_URL."images/minus.png" ?>" width="15px" />
									</a>
									</td>
									<td> <?= ($_ENV['APP_ENV'] == 'local' ? $id.":" : "") .$discoveriesModel->getTitle($discovery) ?> </td>
									<td> <?= getClientName($discovery['propounding'])                                              ?> </td>
									<td> <?= getClientName($discovery['responding'])                                               ?> </td>
									<td> <?= dateformat($discovery['served'])                                                      ?> </td>
									<td> <?= $showDueDate == 1 ? dateformat($discovery['due']) : "-"                               ?> </td>
									<td> <?= $discovery['creator']                                                                 ?> </td>
                                    <td><?= ( $discoveryType == Discovery::TYPE_EXTERNAL ) ? "AI4Discovery" : "Other"                  ?> 
</td>

									<td align="center">
										<div class="dropdown">
											<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action
												<span class="caret"></span>
											</button>
											<ul class="dropdown-menu dropdown-menu-right" role="menu">
<?php
												if( in_array("view",$discovery_ACL) ) {
?>
													<!-- 3:view --> <li class="list-menu"><a href="javascript:" onclick="selecttab('49_tab','view.php?pid=<?= $case_id ?>&id=<?= $discovery['uid'] ?>&view=<?= Discovery::VIEW_PROPOUNDING ?>&response_id=0','49');"><i class="fa fa-eye"></i> View</a></li>
<?php
												}
												if( in_array("edit",$discovery_ACL) ) {
?>
													<!-- 3:edit --> <li class="list-menu"><a href="javascript:"  onclick="selecttab('47_tab','discovery.php?pid=<?= $case_id ?>&id=<?= $discovery['id'] ?>&supp=0','47');"><i class="fa fa-edit"></i> Edit</a></li>
<?php
												}
												if( in_array("respond",$discovery_ACL) ) {
?>
													<!-- 3:respond --> <li class="list-menu"><a href="javascript:" onclick="selecttab('49_tab','discoverydetails.php?pid=<?= $case_id ?>&id=<?= $discovery['uid'] ?>&view=<?= Discovery::VIEW_RESPONDING ?>&respond=<?= Discovery::TYPE_EXTERNAL ?>&response_id=0','49');"><i class="fa fa-edit"></i> Respond</a></li>
<?php
												}
												if( in_array("add-response",$discovery_ACL) ) {
?>
													<!-- 3:add-response --> <li class="list-menu"><a href="javascript:" onclick="selecttab('49_tab','discoverydetails.php?pid=<?= $case_id ?>&id=<?= $discovery['uid'] ?>&view=<?= Discovery::VIEW_RESPONDING ?>&respond=<?= Discovery::TYPE_INTERNAL ?>&response_id=0','49');"><i class="fa fa-edit"></i> Add response</a></li>
<?php
												}
												if( in_array("supp-amend",$discovery_ACL) ) {
?>
													<!-- 3:supp --> <li class="list-menu"><a href="javascript:"  onclick="selecttab('47_tab','discovery.php?pid=<?= $case_id ?>&id=<?= $discovery['id'] ?>&supp=1','47');"><i class="fa fa-refresh"></i> Supp/Amend</a></li>
<?php
												}

												if( in_array("response-pdf",$discovery_ACL) ) {
?>
													<!-- 3:pdf/response --> <li class="list-menu"><a href="<?= $ResponsePDF_FileName ?>" target="_blank" ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
<?php
												}
												if( in_array("request-pdf",$discovery_ACL) ) {
?>
													<!-- 3:pdf --> <li class="list-menu"><a href="<?= $RequestPDF_FileName ?>" target="_blank"   ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
<?php
												}
												if( in_array("change-due-date",$discovery_ACL) ) {
												?>
													<!-- 3:redate --> <li class="list-menu"><a href="javascript:" class="discovery-change-due-date" data-discovery-id="<?= $discovery['id'] ?>" ><i class="fa fa-calendar-o"></i> Change Due Date</a></li>
												<?php
												}
												if( in_array("unserve",$discovery_ACL) ) {
?>
													<!-- 3:unserve --> <li class="list-menu"><a href="javascript:" onclick="doAction('unserve','discovery','<?= $discovery['uid'] ?>');"><i class="fa fa-undo"></i> Unserve</a></li>
<?php
												}
												if( in_array("delete",$discovery_ACL) ) {
?>
													<!-- 3:delete --> <li class="list-menu"><a href="javascript:" onclick="doAction('delete','discovery','<?= $discovery['uid'] ?>');"><i class="fa fa-trash"></i> Delete</a></li>
<?php
												}
?>
											</ul>
										</div>
									</td>
								</tr>
<?php
								$totalChilds += sizeof($responses);

								//if( !empty($responses) ) {
									foreach( $responses ?: [] as $response_data ) {
										$response_creator_id	= $response_data['created_by'];
										$isserved				= $response_data['isserved'];
										$response_id			= $response_data['id'];

										$mc = $meetConferModel->findByResponseId($response_id, false);

										$ResponsePDF_FileName = "makepdf.php?id=".$discovery['uid']."&view=<?= Discovery::VIEW_RESPONDING ?>&response_id=".$response_id;
										if( !$isserved && checkSides($response_creator_id) != Side::SAME_SIDE ) {
											$totalChildsNotIncludes++; continue;
										}

										/**
										* SET UP ACL FOR RESPONSES
										**/
										$response_ACL = [];

										$response_ACL[] = "response-pdf"; // always allow PDFing
										if ( !in_array( $loggedin_email, $respondingParty )) {
											$response_ACL[] = 'meet-confer';
										}

										if( $isserved ) {
											if( checkSides($response_creator_id) == Side::SAME_SIDE ) {
												$response_ACL[] = "supp-amend";
											}
											if( $loggedin_email == 'jeff@jeffschwartzlaw.com' ) {
												$response_ACL[] = "unserve";
											}
											$response_ACL[] = "view";
										}
										else {
											if( checkSides($response_creator_id) == Side::SAME_SIDE ) {
												$response_ACL[]	= "edit";
												$response_ACL[]	= "delete";
											}
										}
?>
										<tr class="group_<?= $id ?>" style="display:none">
											<!-- 4 ("external" responses) -->
											<td>
												<?php if($mc && $mc['served']): ?>
													<a  title="Meet & Confer Letter"  href="#meet-and-confer/<?= $response_id ?>" class="meet-confer-button" data-response-id="<?= $response_id ?>"><i class="fa fa-comments-o"></i></a>
												<?php endif; ?>
											</td>
											<td> <?= ($_ENV['APP_ENV'] == 'local' ? $response_data['id'].":" : "") .$responsesModel->getTitle($response_data) ?> </td>
											<td> <?= getClientName($discovery['propounding'])                              	                                  ?> </td>
											<td> <?= getClientName($discovery['responding'])                               	                                  ?> </td>
											<td> <?= $isserved ? dateformat($response_data['servedate']) : ""              	                                  ?> </td>
											<td> <?= $isserved ? "-" : dateformat($discovery['due'])                       	                                  ?> </td>
											<td> <?= getUserName($response_data['created_by'])                             	                                  ?> </td>
                                        	<td> <?= ( $discoveryType == Discovery::TYPE_EXTERNAL ) ? "AI4Discovery" : "Other" 	                                  
?> </td>

										<td align="center">
											<div class="dropdown">
												<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action
													<span class="caret"></span>
												</button>
												<ul class="dropdown-menu dropdown-menu-right" role="menu">
<?php
													if( in_array("view",$response_ACL) ) {
?>
														<!-- 4:view --> <li class="list-menu"><a href="javascript:" onclick="selecttab('49_tab','view.php?pid=<?= $case_id ?>&id=<?= $discovery['uid'] ?>&view=<?= Discovery::VIEW_RESPONDING ?>&response_id=<?= $response_id ?>','49');"><i class="fa fa-eye"></i> View</a></li>
<?php
													}
													if( in_array("edit",$response_ACL) ) {
?>
														<!-- 4:edit --> <li class="list-menu"><a href="javascript:" onclick="selecttab('49_tab','discoverydetails.php?pid=<?= $case_id ?>&id=<?= $discovery['uid'] ?>&view=<?= Discovery::VIEW_RESPONDING ?>&respond=<?= Discovery::TYPE_EXTERNAL ?>&response_id=<?= $response_id ?>','49');"><i class="fa fa-edit"></i> Edit</a></li>
<?php
													}
													if( in_array("supp-amend",$response_ACL) ) {
?>
                                                    	<!-- 4:supp --> <li class="list-menu"><a href="javascript:" onclick="selecttab('49_tab','discoverydetails.php?pid=<?= $case_id ?>&id=<?= $discovery['uid'] ?>&supp=1&view=<?= Discovery::VIEW_RESPONDING ?>&respond=<?= Discovery::TYPE_EXTERNAL ?>&response_id=<?= $response_id ?>','49');"><i class="fa fa-refresh"></i> Supp/Amend</a></li>
<?php
													}

													if( in_array("request-pdf",$response_ACL) ) {
?>
														<!-- 4:pdf --> <li class="list-menu"><a href="<?= $RequestPDF_FileName ?>"   ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
<?php
													}
													if( in_array("response-pdf",$response_ACL) ) {
?>
														<!-- 4:pdf/response --> <li class="list-menu"><a href="<?= $ResponsePDF_FileName ?>" target="_blank" ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
<?php
													}
													if( in_array("unserve",$response_ACL) ) {
?>
														<!-- 4:unserve --> <li class="list-menu"><a href="javascript:" onclick="doAction('unserve','response','<?= $response_id ?>');"><i class="fa fa-undo"></i> Unserve</a></li>
<?php
													}
													if( in_array("delete",$response_ACL) ) {
?>
														<!-- 4:delete --> <li class="list-menu"><a href="javascript:" onclick="doAction('delete','response','<?= $response_id ?>');"><i class="fa fa-trash"></i> Delete</a></li>
<?php
													}
													if( in_array("meet-confer",$response_ACL) ) {
?>
														<!-- 4:m&c --> <li class="list-menu"><a href="#meet-and-confer/<?= $response_id ?>" class="meet-confer-button"  data-response-id="<?= $response_id ?>"><i class="fa fa-comments-o"></i> Meet & Confer</a></li>
<?php
													}
?>
												</ul>
											</div>
										</td>
										</tr>
<?php
									}
								//}
								if( $id ) {
								/*******************************************
								* 		Supplement Amending Discoveries
								*******************************************/
									$supp_discoveries = $discoveriesModel->getSuppAmended($id);
Side::legacyTranslateCaseData($case_id, $supp_discoveries);

									$totalChilds += sizeof($supp_discoveries);

									foreach( $supp_discoveries as $suppdiscovery ) {
										$supp_id			= $suppdiscovery['id'];
										$supp_creator_id	= $suppdiscovery['creator_id'];
										//$supp_is_submitted	= $suppdiscovery['is_submitted'];
										$supp_is_served		= $suppdiscovery['is_served'];
										$supp_discoveryType	= $suppdiscovery['type']; // Discovery::TYPE_EXTERNAL/TYPE_INTERNAL

										$RequestPDF_FileName = "makepdf.php?id=".$suppdiscovery['uid']."&view=". Discovery::VIEW_PROPOUNDING;

										if( $supp_discoveryType == Discovery::TYPE_INTERNAL) {
											$totalChildsNotIncludes++; continue;
										}

										if( checkSides($supp_creator_id) != Side::SAME_SIDE ) {
											if( !$supp_is_served ) {
												$totalChildsNotIncludes++; continue;
											}
										}
										/**
										* Check to see login user is responding party attorney or not
										* If he is the attorney of responding party then we give him option to respond in external discovery.
										**/
										$supp_respondingParty = partyEmailAddrs( $suppdiscovery['responding'] );

										/**
										* GET RESPONSES OF SUPP/EMEND EXTERNAL DISCOVERY
										**/
										$supp_Responses = $responsesModel->getByDiscovery($supp_id);
										$showDueDateSupp = !$responsesModel->isAnyServed($supp_Responses);

										/**
										* SET UP ACL FOR SUPP DISCOVERIES
										**/
										$supp_discovery_ACL	= [];
										$supp_discovery_ACL[] = "request-pdf"; // always allow PDFing

										if( $supp_is_served ) {
											$supp_discovery_ACL[] = "view";

											if( checkSides($supp_creator_id) == Side::SAME_SIDE ) {
												$supp_discovery_ACL[] = "supp-amend";
												$supp_discovery_ACL[] = "change-due-date";
											}
											if( $loggedin_email == 'jeff@jeffschwartzlaw.com' ) {
												$supp_discovery_ACL[] = "unserve";
											}
											if( in_array($loggedin_email,$supp_respondingParty) && !sizeof($supp_Responses) ) { //!! TODO if-same-side
												$supp_discovery_ACL[] = "respond";
											}
										}
										else {
											$supp_discovery_ACL[] = "edit";
											$supp_discovery_ACL[] = "delete";
										}
?>
										<tr class="group_<?= $id ?>" style="display:none">
											<!-- 5 (supp) -->
											<td></td>
											<td><?= ($_ENV['APP_ENV'] == 'local' ? $suppdiscovery['id'].":" : "") .$discoveriesModel->getTitle($suppdiscovery) ?> </td>
											<td><?= getClientName($suppdiscovery['propounding'])                                                               ?> </td>
											<td><?= getClientName($suppdiscovery['responding'])                                                                ?> </td>
											<td><?= dateformat($suppdiscovery['served'])                                                                       ?> </td>
											<td><?= $showDueDateSupp ? dateformat($suppdiscovery['due']) : "-"                                                 ?> </td>
                                            <td><?= $suppdiscovery['creator']                                                                                  ?> </td>
                                            <td><?= $discoveryType == Discovery::TYPE_EXTERNAL ? "AI4Discovery" : "Other"                                          
?> </td>
											<td align="center">
												<div class="dropdown">
													<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action
														<span class="caret"></span>
													</button>
													<ul class="dropdown-menu dropdown-menu-right" role="menu">
<?php
														if( in_array("view",$supp_discovery_ACL) ) {
?>
															<!-- 5:view --> <li class="list-menu"><a href="javascript:" onclick="selecttab('49_tab','view.php?pid=<?= $case_id ?>&id=<?= $suppdiscovery['uid'] ?>&view=<?= Discovery::VIEW_PROPOUNDING ?>&response_id=0','49');"><i class="fa fa-eye"></i> View</a></li>
<?php
														}
														if( in_array("edit",$supp_discovery_ACL) ) {
?>
															<!-- 5:view --> <li class="list-menu"><a href="javascript:"  onclick="selecttab('47_tab','discovery.php?pid=<?= $case_id ?>&id=<?= $suppdiscovery['id'] ?>&supp=0','47');"><i class="fa fa-edit"></i> Edit</a></li>
<?php
														}
														if( in_array("respond",$supp_discovery_ACL) )
														{
?>
															<!-- 5:respond --> <li class="list-menu"><a href="javascript:" onclick="selecttab('49_tab','discoverydetails.php?pid=<?= $case_id ?>&id=<?= $suppdiscovery['uid'] ?>&view=<?= Discovery::VIEW_RESPONDING ?>&respond=<?= Discovery::TYPE_EXTERNAL ?>&response_id=0','49');"><i class="fa fa-edit"></i> Respond</a></li>
<?php
														}
														if( in_array("supp-amend",$supp_discovery_ACL) ) {
?>
															<!-- 5:supp --> <li class="list-menu"><a href="javascript:"  onclick="selecttab('47_tab','discovery.php?pid=<?= $case_id ?>&id=<?= $suppdiscovery['id'] ?>&supp=1','47');"><i class="fa fa-refresh"></i> Supp/Amend</a></li>
<?php
														}
														if( in_array("response-pdf",$supp_discovery_ACL) ) {
?>
															<!-- 5:pdf/response --> <li class="list-menu"><a href="<?= $ResponsePDF_FileName; ?>" target="_blank" ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
<?php
														}
														if( in_array("request-pdf",$supp_discovery_ACL) ) {
?>
															<!-- 5:pdf --> <li class="list-menu"><a href="<?= $RequestPDF_FileName ?>" target="_blank"   ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
<?php
														}
														if( in_array("change-due-date",$supp_discovery_ACL) ) {
														?>
															<!-- 5:redate --> <li class="list-menu"><a href="javascript:" class="discovery-change-due-date" data-discovery-id="<?= $suppdiscovery['id'] ?>" ><i class="fa fa-calendar-o"></i>Change Due Date</a></li>
														<?php
														}
														if( in_array("unserve",$supp_discovery_ACL) ) {
?>
															<!-- 5:unserve --> <li class="list-menu"><a href="javascript:" onclick="doAction('unserve','discovery','<?= $suppdiscovery['uid'] ?>');"><i class="fa fa-undo"></i> Unserve</a></li>
<?php
														}
														if( in_array("delete",$supp_discovery_ACL) ) {
?>
															<!-- 5:delete --> <li class="list-menu"><a href="javascript:" onclick="doAction('delete','discovery','<?= $suppdiscovery['uid'] ?>');"><i class="fa fa-trash"></i> Delete</a></li>
<?php
														}
?>
													</ul>
												</div>
											</td>
										</tr>
<?php
										$totalChilds += sizeof($supp_Responses);
										//if( !empty($supp_Responses) ) {
											foreach($supp_Responses ?: [] as $response_data) {
												$response_id				= $response_data['id'];
												$supp_response_creator_id	= $response_data['created_by'];
												$supp_isserved				= $response_data['isserved'];

												$mc = $meetConferModel->findByResponseId($response_id, false);

												$ResponsePDF_FileName	= "makepdf.php?id=".$suppdiscovery['uid']."&". Discovery::VIEW_RESPONDING ."&response_id=".$response_id;

												if( $supp_isserved && checkSides($supp_response_creator_id) == Side::SAME_SIDE ) {
													$totalChildsNotIncludes++; continue;
												}

												/**
												* SET UP ACL FOR SUPP RESPONSES
												**/
												$supp_response_ACL = [];
												$supp_response_ACL[] = "response-pdf"; // always allow PDFing

												if ( !in_array( $loggedin_email, $respondingParty )) {
													$supp_response_ACL[] = 'meet-confer';
												}

												if( $supp_isserved ) {
													if( checkSides($supp_response_creator_id) == Side::SAME_SIDE ) {
														$supp_response_ACL[] = "supp-amend";
													}
													if( $loggedin_email == 'jeff@jeffschwartzlaw.com' ) {
														$supp_response_ACL[] = "unserve";
													}
													$supp_response_ACL[] = "view";
												}
												else {
													if( checkSides($supp_response_creator_id) == Side::SAME_SIDE ) {
														$supp_response_ACL[] = "edit";
														$supp_response_ACL[] = "delete";
													}
												}
?>
											<tr class="group_<?= $id ?>" style="display:none">
												<!-- 6 (supp/amended responses) -->
												<td>
													<?php if($mc && $mc['served']): ?>
														<a  title="Meet & Confer Letter" href="#meet-and-confer/<?= $response_id ?>" class="meet-confer-button" data-response-id="<?= $response_id ?>"><i class="fa fa-comments-o"></i></a>
													<?php endif; ?>
												</td>
												<td> <?= ($_ENV['APP_ENV'] == 'local' ? $response_data['id'].":" : "") .$responsesModel->getTitle($response_data) ?> </td>
												<td> <?= getClientName($suppdiscovery['propounding'])                                                             ?> </td>
												<td> <?= getClientName($suppdiscovery['responding'])                                                              ?> </td>
												<td> <?= $supp_isserved ? dateformat($response_data['servedate']) : ""                                            ?> </td>
                                                <td> <?= !$supp_isserved ? dateformat($suppdiscovery['due']) : "-"                                                ?> </td>
												<td> <?=  getUserName($response_data['created_by'])                                                               ?> </td>
												<td> <?= ( $discoveryType == 
Discovery::TYPE_EXTERNAL ) ? "AI4Discovery" : "Other"                                    ?> </td>

												<td align="center">
													<div class="dropdown">
														<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Action
															<span class="caret"></span>
														</button>
														<ul class="dropdown-menu dropdown-menu-right" role="menu">
<?php

															if( in_array("view",$supp_response_ACL) ) {
?>
																<!-- 6:view --> <li class="list-menu"><a href="javascript:" onclick="selecttab('49_tab','view.php?pid=<?= $case_id ?>&id=<?= $suppdiscovery['uid'] ?>&view=<?= Discovery::VIEW_RESPONDING ?>&response_id=<?= $response_id ?>','49');"><i class="fa fa-eye"></i> View</a></li>
<?php
															}
															if( in_array("edit",$supp_response_ACL) ) {
?>
																<!-- 6:edit --> <li class="list-menu"><a href="javascript:" onclick="selecttab('49_tab','discoverydetails.php?pid=<?= $case_id ?>&id=<?= $suppdiscovery['uid'] ?>&view=<?= Discovery::VIEW_RESPONDING ?>&respond=<?= Discovery::TYPE_EXTERNAL ?>&response_id=<?= $response_id ?>','49');"><i class="fa fa-edit"></i> Edit</a></li>
<?php
															}
															if( in_array("supp-amend",$supp_response_ACL) ) {
?>
                                                            	<!-- 6:supp --> <li class="list-menu"><a href="javascript:" onclick="selecttab('49_tab','discoverydetails.php?pid=<?= $case_id ?>&id=<?= $suppdiscovery['uid'] ?>&supp=1&view=<?= Discovery::VIEW_RESPONDING ?>&respond=<?= Discovery::TYPE_EXTERNAL ?>&response_id=<?= $response_id ?>','49');"><i class="fa fa-refresh"></i> Supp/Amend</a></li>
<?php
															}

															if(in_array("response-pdf",$supp_response_ACL)) {
?>
																<!-- 6:pdf/response --> <li class="list-menu"><a href="<?= $ResponsePDF_FileName; ?>" target="_blank" ><i class="fa fa-file-pdf-o"></i> PDF</a></li>
<?php
															}

															if(in_array("unserve",$supp_response_ACL)) {
?>
																<!-- 6:unserve --> <li class="list-menu"><a href="javascript:" onclick="doAction('unserve','response','<?= $response_id ?>');"><i class="fa fa-undo"></i> Unserve</a></li>
<?php
															}
															if( in_array("delete",$supp_response_ACL) ) {
?>
																<!-- 6:delete --> <li class="list-menu"><a href="javascript:" onclick="doAction('delete','response','<?= $response_id ?>');"><i class="fa fa-trash"></i> Delete</a></li>
<?php
															}
															if( in_array("meet-confer",$supp_response_ACL) ) {
?>
																<!-- 6:m&c --> <li class="list-menu"><a href="#meet-and-confer/<?= $response_id ?>" class="meet-confer-button" data-response-id="<?= $response_id ?>"><i class="fa fa-comments-o"></i> Meet & Confer</a></li>
<?php
															}
?>
														</ul>
													</div>
												</td>
												</tr>
<?php
											}
										//}
									}
								}

								$totalChilds -= $totalChildsNotIncludes;
								if( $totalChilds <= 0 ) {
?>
								<script>
									$("#plusBtn<?= $id ?>").hide();
									$("#minusBtn<?= $id ?>").hide();
								</script>
<?php
								}
							}
						}/*foreach*/
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

<div id="discovery-due-date-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header" style="padding: 15px;">
        <h5 class="modal-title" style="font-size: 22px;">Change Due Date</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Due Date</label>
          <input type="text" id="discovery-due-date-modal-input" class="form-control datepicker" required/>
					<input type="hidden" id="discovery-due-date-modal-id-input" />
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" id="discovery-due-date-modal-btn">Save</button>
				<button class="btn btn-danger"  type="button"  data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
      </div>
    </div>
  </div>
</div>

<script>
<?php
	if( sizeof($discoveries) ) {
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
</script>
<script src="<?= VENDOR_URL ?>sweetalert/lib/sweet-alert.min.js"></script>
<!--script src="<?= ASSETS_URL ?>custom.js"></script-->
<script>

function doAction( action = 'delete', type = 'discovery', id ) {
	console.assert( action in {'delete':1,'unserve':1}, 'Invalid `action` type specified' )
	console.assert( type   in {'discovery':1,'response':1}, 'Invalid discovery `type` specified`' )
	swal( {
		title: `Are you sure you want to ${action} this ${type}?`,
		text:  `You will not be able to undo this action!`,
		icon:  "warning",
		buttons: true,
		dangerMode: true,
	} )
	.then( willDelete => {
		if( willDelete ) {
			const data = type == `discovery`
					? { discovery_uid: id }
					: { response_id: id }
			$.post( `${action}${type}.php`, data )
				.done( case_id => {
					selecttab('45_tab','discoveries.php?pid=<?= $case_id ?>','45')
				} );
		}
	} );
	$(".swal-button-container:first").css("float","right");
}
function showHide( action, discovery_id ) {
	console.assert( action in {'show':1,'hide':1}, 'Wrong `action` specified' )
	if(action == 'hide') {
		$(".group_"+discovery_id).hide();
		$("#plusBtn"+discovery_id).show();
		$("#minusBtn"+discovery_id).hide();
	}
	else {
		$(".group_"+discovery_id).show();
		$("#plusBtn"+discovery_id).hide();
		$("#minusBtn"+discovery_id).show();
	}
}

// Change Due Date Action
$('.datepicker').datepicker({
	format: 'yyyy-mm-dd',
	startDate: "-5y",
	autoclose: true,
});
$('.discovery-change-due-date').on('click', function() {
	const discoveryId = $(this).data('discoveryId');
	getDiscovery( discoveryId,
		(response) => {
			$('#discovery-due-date-modal-input').val(response.due)
			$('#discovery-due-date-modal-id-input').val(discoveryId)
			$('#discovery-due-date-modal').modal('show');
		},
		(error) => showResponseMessage(error)
	)
});

$('#discovery-due-date-modal-btn').on('click', _ => {
	const dueDate =	$('#discovery-due-date-modal-input').val()
	const discoveryId = $('#discovery-due-date-modal-id-input').val();

	updateDiscovery(discoveryId, {due: dueDate},
		(response) => {
			$('#discovery-due-date-modal')
				.off('hidden.bs.modal')
				.on('hidden.bs.modal', () => {
					toastr.success('Due date updated successfully!');
					selecttab('45_tab','discoveries.php?pid=<?= $case_id ?>','45');
				});
			$('#discovery-due-date-modal').modal('hide');
		},
		(error)	=> showResponseMessage(error)
	)
});

$('a.meet-confer-button').on('click', function() {
	const responseId = $(this).data('response-id');
	selecttab(`meet-confer-${responseId}`,`meet-confer.php?response_id=${responseId}`, `meet-confer`);
})

</script>
