<?php
require_once 'idiorm.php';
ORM::configure('mysql:host=localhost;dbname=databaseNameGoesHERE');
ORM::configure('username', 'myUsername');
ORM::configure('password', 'myDBpassword');

define('_table_users','users');

function hashPassword($password)
{
	$salt = substr(md5($password[0]),0,10 ) . "s0d!um cl0r1d3";
	return md5( $password . $salt );
}

function generatePassword($length=8)
{
	$chars = explode(",", "A,B,C,D,E,F,G,H,J,K,L,M,N,P,Q,R,S,T,U,V,W,X,Y,Z,2,2,3,3,4,4,5,5,6,6,7,6,8,8,9,9");	
	$pw = "";
	for($i=0; $i<$length; $i++)
	{
		$pw.= $chars[ rand(0, count($chars) -1)];
	}
	return $pw;
}

function resetPassword($user,$length=8)
{
	if( is_numeric($user) )
	{
		$user = ORM::for_table( _table_users)->find_one($user);
	}
	elseif (!is_object($user) )
	{
		return false;
	}
	
	$new_password = generatePassword($length);	
	$httphost = $_SERVER['HTTP_HOST'];
	
	$body = "Hello ". $user->name .",\n";
	$body.= "Your password for ".$httphost ." has been reset. \n";
	$body.= "Your new password is ".$new_password ."\n\n";
	$body.= "Thank You. \n";
	$body.= "\n----------\n";
	$body.= "This is an automated message.  Please do not reply.  The request to reset your password came from ". $_SERVER['REMOTE_ADDR'];
	
	if( mail($user->email, $httphost." password reset", $body, "FROM:no-reply@".$httphost) )
	{
		$user->password = hashPassword($new_password);
		$user->save();
		return true;
	}
	else
	{
		return false;
	}
	
}

?>