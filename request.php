<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'].'/db.php';

header("Expires: Sat, 07 Apr 1979 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if( isset($_REQUEST['json']) )
{
	header("Content-Type: application/json");
}

switch( $_REQUEST['xhr'] )
{
	case 'reset_password' :
		if(!isset($_GET['email']) || !filter_var($_GET['email'], FILTER_VALIDATE_EMAIL) || !preg_match('/@.+\./', $_GET['email']) )
		{
			exit("Missing or Invalid E-mail Address");
		}
		$user = ORM::for_table( _table_users)->where('email',$_GET['email'])->find_one();
		if(!$user){
			exit("E-mail Address does not exisits.  Please user the registration form to create a new account.");
		}
		
		if(resetPassword($user) )
		{
			echo "success";
		}
		else
		{
			echo "Error. Could not reset password";
		}
		
	break;
	
	default :
		echo "Invalid Request";
		
	break;
}
?>