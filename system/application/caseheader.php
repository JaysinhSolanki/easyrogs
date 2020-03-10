<?php
$cases		=	$AdminDAO->getrows("cases c,system_addressbook a",
									"
									c.*,
									c.case_title 	as case_title,
									c.case_number 	as case_number,
									c.jurisdiction 	as jurisdiction,
									c.judge_name 	as judge_name,
									c.county_name 	as county_name,
									c.court_address as court_address,
									c.department 	as department, 
									a.firstname 	as atorny_fname,
									a.lastname 		as atorny_lname
									",
									"
									id 				= :case_id AND
									attorney_id 	= :attorney_id AND
									pkaddressbookid = attorney_id
									
									",
									array('case_id'=>$case_id,'attorney_id'=>$_SESSION['addressbookid'])
								 );
$case				=	$cases[0];
$case_title			=	$case['case_title'];
$case_number		=	$case['case_number'];
$jurisdiction		=	$case['jurisdiction'];
$judge_name			=	$case['judge_name'];
$county_name		=	$case['county_name'];
$court_address		=	$case['court_address'];
$department			=	$case['department'];
$set_number			=	$case['set_number'];
$atorny_name		=	$case['atorny_fname']." ".$case['atorny_lname'];
?>
<table class="table table-bordered table-hover table-striped">
  <tbody>
     <tr>
      <th>Case Title</th>
      <td><?php echo $case_title ?></td>
      <th>Case#</th>
      <td><?php echo $case_number ?></td>
    </tr>
     <tr>
      <th>State</th>
      <td><?php echo $jurisdiction ?></td>
      <th>County/District</th>
      <td><?php echo $county_name ?></td>
    </tr>
    <tr>
      <th>Court Address</th>
      <td><?php echo $court_address ?></td>
      <th>Attorney</th>
      <td><?php echo $atorny_name ?></td>
    </tr>
  </tbody>
</table>