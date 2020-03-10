<?php
@session_start();
/* *********************************** */
/* User Security and User rights class */
/***************************************/
class userSecurity
{
	function getRights($screen)
	{
		$fieldslabels['fields']		=	$_SESSION['screens'][$screen]['fields'];
		$fieldslabels['labels']		=	$_SESSION['screens'][$screen]['labels'];
		$fieldslabels['actions']	=	$_SESSION['screens'][$screen]['actions'];
		return $fieldslabels;
	}
}//end class