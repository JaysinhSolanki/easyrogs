<?php
@session_start();
require_once __DIR__ . '/../bootstrap.php';
include_once("../library/classes/functions.php");

$olddocuments = $_SESSION['documents'];

// $logger->debug( $_SESSION['documents'] );

$id		= $_POST['id'];
$rp_uid = $_POST['rp_uid'];

if( sizeof($olddocuments) ) {
	foreach( $olddocuments as $values ) {
		foreach( $values ?: [] as $key => $data) {
			$doc_purpose	=	$data['doc_purpose'];
			$doc_name		=	$data['doc_name'];
			$doc_path		=	$data['doc_path'];
			$status			=	$data['status'];
			if( $id == $key ) {
				unlink(SYSTEMPATH.$doc_path);
			}
			else {
				$documents[$rp_uid][] = [ 	"doc_name" => $doc_name,
											"doc_purpose" => $doc_purpose,
											"doc_path" => $doc_path,
											"status" => $status ];
			}
		}
	}
}
else {
	$documents[$rp_uid][] = [];
}

$_SESSION['documents'] = $documents;
