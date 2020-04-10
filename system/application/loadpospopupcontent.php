<?php
@session_start();
require_once("adminsecurity.php");
include_once($_SESSION['library_path']."helper.php");
//dump($_POST);
$discovery_id					=	$_POST['id']; 
$respond						=	$_POST['respond']; 
$response_id					=	$_POST['response_id'];  
if($discovery_id != "")
{
	$discoveryDetails	=	$AdminDAO->getrows('discoveries d,cases c,system_addressbook a,forms f',
												'c.case_title 	as case_title,
												c.plaintiff,
												c.defendant,
												c.case_number 	as case_number,
												c.jurisdiction 	as jurisdiction,
												c.judge_name 	as judge_name,
												c.county_name 	as county_name,
												c.court_address as court_address,
												c.department 	as department, 
												c.uid 	as case_uid, 
												d.case_id 		as case_id,
												d.id 			as discovery_id, 
												d.uid,
												d.type,
												d.send_date,
												d.propounding,
												d.responding,
												d.served,
												d.discovery_name,
												d.propounding_uid,
												d.responding_uid,
												d.attorney_id as attr_id,
												d.form_id 		as form_id,
												d.set_number 	as set_number,
												d.discovery_introduction as introduction,
												f.form_name	 	as form_name,
												f.short_form_name as short_form_name,
												a.firstname 	as atorny_fname,
												a.lastname 		as atorny_lname,
												a.address		as atorny_address,
												a.cityname,
												a.street,
												a.companyname	as atorny_firm,
												d.attorney_id	as attorney_id,
												a.email,
												a.phone,
												a.attorney_info,
												(CASE WHEN (form_id = 1 OR form_id = 2) 
												 THEN
													  f.form_instructions 
												 ELSE
													  d.discovery_instrunctions 
												 END)
												 as instructions 
												',
												"d.id 			= :id AND 
												d.case_id 		= c.id AND  
												d.form_id		= f.id AND
												d.attorney_id 	= a.pkaddressbookid",
												array(":id"=>$id)
											);
	
	//$AdminDAO->displayquery=0;
	
	//dump($discoveryDetails);
	//exit;
	$discovery_data		=	$discoveryDetails[0];
	$uid				=	$discovery_data['uid'];
	$case_uid			=	$discovery_data['case_uid'];
	$discovery_name		=	$discovery_data['discovery_name'];
	$discovery_type		=	$discovery_data['type'];
	$case_id			=	$discovery_data['case_id'];
	$case_title			=	$discovery_data['case_title'];
	$case_number		=	$discovery_data['case_number'];
	$county_name		=	$discovery_data['county_name'];
	$is_send			=	$discovery_data['is_send'];
	$set_number			=	$discovery_data['set_number'];
	$form_name			=	$discovery_data['form_name'];
	$propounding		=	$discovery_data['propounding'];
	$responding			=	$discovery_data['responding'];
	$discovery_id		=	$discovery_data['discovery_id'];
	$attr_id			=	$discovery_data['attr_id'];
	
}
/*else
{
	$form_id						=	$_POST['form_id'];
	$discovery_name					=	$_POST['discovery_name'];
	$set_number						=	$_POST['set_number'];
	$propounding					=	$_POST['propounding'];
	$responding						=	$_POST['responding'];
	$question_number_start_from		=	$_POST['question_number_start_from'];
	$incidenttext					=	$_POST['incidenttext'];
	$case_id						=	$_POST['case_id'];
	
		
	$discoveryDetails				=	$AdminDAO->getrows('cases c',
													'c.case_title 	as case_title,
													c.plaintiff,
													c.defendant,
													c.case_number 	as case_number,
													c.jurisdiction 	as jurisdiction,
													c.judge_name 	as judge_name,
													c.county_name 	as county_name,
													c.court_address as court_address,
													c.department 	as department, 
													c.uid 	as case_uid
													',
													"c.id 			= :id",
													array(":id"=>$case_id)
												);
		
	
	$discovery_data		=	$discoveryDetails[0];
	$case_title			=	$discovery_data['case_title'];
	$case_number		=	$discovery_data['case_number'];
	$county_name		=	$discovery_data['county_name'];
	$case_uid			=	$discovery_data['case_uid'];
	$attr_id			=	$_SESSION['addressbookid'];
}*/

//Responding Party
$respondingdetails		=	$AdminDAO->getrows("clients","*","id = :id",array(":id"=>$responding));
$responding_name		=	$respondingdetails[0]['client_name'];
$responding_email		=	$respondingdetails[0]['client_email'];
$responding_type		=	$respondingdetails[0]['client_type'];
$responding_role		=	$respondingdetails[0]['client_role'];

//Sender Details
$attr_id 			= 	$_SESSION['addressbookid'];
$senderDetails		=	$AdminDAO->getrows("system_addressbook","*","pkaddressbookid = :id",array(":id"=>$attr_id));

$senderDetail		=	$senderDetails[0];
$senderEmail		=	$senderDetail['email'];
$senderPhone		=	$senderDetail['phone'];
$senderName			=	$senderDetail['firstname']." ".$senderDetail['lastname'];	
$senderAddress		=	makeaddress($attr_id);//;$senderDetail['address'].", ".$senderDetail['cityname'].", ".$senderDetail['statecode']." ".$senderDetail['zip'];
$getstate		=	getstate($attr_id);

//$servicelists	=	$AdminDAO->getrows("attorney","*", "case_id = :case_id  AND attorney_type = :attorney_type", array(":case_id"=>$case_id,":attorney_type"=>2));
$loggedin_email		=	$_SESSION['loggedin_email'];
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
													
											","attorney.*,clients.client_name", "attorney.case_id = :case_id AND attorney.attorney_type = :attorney_type", array(":case_id"=>$case_id,":attorney_type"=>2), "attorney.attorney_name", "ASC");
//$AdminDAO->displayquery=0;
//dump($servicelists);
//dump($attorneys);
//exit;


 

?>
<style>

.tabela
	{
		width:100% !important;
	}
   .tabela tbody tr th{
   background: #999;
   color: white;
   font-weight: bold;
   font-size: 13pt;
   text-align:center !important
   }

   .tabela {
   border: 1px solid #A2A9B1;
   border-collapse: collapse;
   line-height:25px; 
   }
td, th {
    padding: 5px;
}
   .tabela tbody tr td, .tabela tbody tr th {
   border: 1px solid #A2A9B1;
   border-collapse: collapse;
    line-height:25px; 
   }
</style>
<div class="row">
	<div class="col-md-12" id="poshtml">
    	<table class="tabela1" style="border:none !important">
          <tbody>
            <tr>
                <td align="center">
                     <h4>
                    <?php echo $case_title ?><br />
                    <?php echo "Case no. ".$case_number; ?>
                    </h4>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <h4>
                         <?php echo strtoupper ("STATE OF CALIFORNIA, COUNTY OF ".$county_name); ?>
                    </h4>
                </td>
            </tr>
            <tr>
                <td align="justify">
                 I am over the age of 18 years and not a party to the within action. My business address is <br /><input type="text" name="pos_address" id="pos_address" placeholder="Enter your address" value="<?php echo $senderAddress; ?>" size="50"/>. My electronic service address is <?php echo $senderEmail ?>.
                 <br />
                 <br />
                 On <?php echo date('F j, Y'); ?>, I electronically served <?php echo str_replace(["set", "For", "Of"], ["Set", "for", "of"], ucwords(strtolower($discovery_name." [Set ".numberTowords( $set_number )."]")) ); ?> upon the following:
                </td>
            </tr>
          </tbody>
		</table>
        <br />
        <table class="tabela" style="1px solid #ddd">
          <tbody>
          	<tr>
                <th align="center">Person Served</th>
                <th align="center">Party Served</th>
                <th align="center">E-service Address</th>
<!--                 <th align="center">EasyRogs</th> -->
            </tr>
            <?php
			foreach($servicelists as $list)
			{
/*
				$isEasyRogsMember	=	$AdminDAO->getrows("system_addressbook","*","email = :email", array(":email"=>$list['attorney_email']));
				//dump($isEasyRogsMember);
				if(sizeof($isEasyRogsMember) > 0)
				{
					$ismember	=	'<img src="'.ASSETS_URL.'images/greensquare.png">';
				}
				else
				{
					$ismember	=	"";
				}
*/
				
			?>
             <tr>
                <td align="left"><?php echo $list['attorney_name']; ?></td>
                <td align="left"><?php echo $list['client_name']; ?></td>
                <td align="left"><?php echo $list['attorney_email']; ?></td>
                <!--<td align="center"><?php echo $ismember; ?></td>-->
            </tr>
            <?php
			}
			?>
          </tbody>
		</table>
        <table class="tabela1" style="border:none !important">
          <tbody>
            <tr>
                <td align="justify">
                <br />
                 <br />
                I declare under penalty of perjury under the laws of the State of California that the above is true and correct. Executed on <?php echo date('F j, Y') ?> at <span id="citystate"><input type="text" name="pos_city" id="pos_city" placeholder="Enter your city..." value="<?php echo $senderDetail['cityname']; ?>"/>, <input type="text" name="pos_state" id="pos_state" value="<?php echo $getstate; ?>" /></span>. <span style='display:none' id='signtime'></span>
                 <br />
                 <br />
                </td>
            </tr>
          </tbody>
		</table>
        <table style="border:none !important" width="100%">
          <tbody>
            <tr>
                <td align="left"><?php echo date('F j, Y'); ?></td>
                <td align="right">By: <?php echo $senderName; ?><br /> Signed electronically,<br />
                <img src="<?php echo ASSETS_URL; ?>images/court.png" style="width: 18px;padding-right: 3px;">Cal. Rules of Court, rule 2.257</td>
            </tr>
          </tbody>
		</table>
        <br />
        <br />
    </div>
</div>

<form name="formPOS" id="formPOS">
<input type="hidden" name="discovery_id" value="<?php echo $discovery_id ?>" />
<input type="hidden" name="discovery_type" value="<?php echo $discovery_type ?>" />
<input type="hidden" name="response_id" value="<?php echo $response_id ?>" />
<input type="hidden" name="pos_text" id="pos_text" value="" />
<input type="hidden" name="posstate" id="posstate" value="" />
<input type="hidden" name="posaddress" id="posaddress" value="" />

<input type="hidden" name="poscity" id="poscity" value="" />
<input type="hidden" name="respond" value="<?php echo $respond ?>" />
<?php /*?><div class="form-group">
    <label for="recipient-name" class="col-form-label"></label>  
    <textarea class="form-control" rows="100" name="pos_text" id="pos_text"><?php //echo $postext; ?> </textarea> 
</div><?php */?> 
<div class="row">
<div class="col-md-12" style="text-align:right">
	<i id="POS_msgdiv" class="POS_msgdiv" style="color:red"></i>
	<button type="button" class="btn btn-purple" onclick="servePOS()"><i class="fa fa-share"></i> Serve</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
</div>
</div>
</form>


<script>
$(document).ready(function()
{
	//CKEDITOR.replace('pos_text', { height: 500});
});

</script>
