<?php
require_once("adminsecurity.php");
$id		=	$_POST['id'];
$data	=	$AdminDAO->getrows(
											"
											attorney 
												LEFT JOIN client_attorney 
												ON 		
												attorney.case_id			=	client_attorney.case_id AND
												client_attorney.attorney_id	=	attorney.id
												LEFT JOIN clients 
												ON 		
												clients.id		=	client_attorney.client_id
													
											","attorney.*,clients.client_name,clients.id as client_id", "attorney.id = :id", array(":id"=>$id));
echo json_encode($data[0]);


