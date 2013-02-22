<?php

// if user is already logged in, just forward them back to the admin homepage
if(is_array($_SESSION['roles']) )
{	
	setHeader("Location: /admin");
	return;	
}
?>
<h1>Register for Access</h1>

<form action="" method="post">
<?php 

// check for submitted form data
if ( isset($_POST['submit']) && isset( $_POST['name']) && isset($_SESSION['registration_password_confirmed']) )
{
	// validate posted data
	if( trim($_POST['name']) == "" || strlen($_POST['name']) < 5) // 5 assumes at least 2 letters each for first and last name plus a space between them
	{
		$err[] = "Please enter your full name";
	}
	
	if( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || !preg_match('/@.+\./', $_POST['email']) )
	{ 
		$err[] = "Please enter a valid e-mail address";
	}
	
	if( trim($_POST['password']) == "") 
	{
		$err[] = "Please create a password"; 
	}
	if( $_POST['password'] != $_POST['passwordconfirm'])
	{
		$err[] = "Password Mismatch: Please enter the same password in the password confirmation field";
	}
	if( !in_array($_POST['region'],$regions) )
	{
		$err[] = "Please select your region from the list of valid regions";	
	}
	
	// if no errors, register the user
	if( !isset($err[0]) )
	{
		$check = ORM::for_table( _table_users)->where('email',$_POST['email'])->find_one();
		if( $check )
		{
			$err[] = "There is already a user account associated with this e-mail address";
		}
		else
		{			
			$user = ORM::for_table( _table_users)->create();
			$user->name = $_POST['name'];
			$user->email = $_POST['email'];
			$user->password = hashPassword($_POST['password']);
			$user->roles = 'sales';
			$user->region = $_POST['region'];
			$user->last_login = date("Y-m-d H:i:s");
			$user->total_logins = 1;
			$user->save();
		
			$_SESSION['roles'] = array('sales');  // log the user in and forward to index page
			$_SESSION['user_id'] = $user->id;
			setHeader("Location: /admin",true);	  // forward user off this page
			return;	
		}
	}
	
}

if( isset($err) )
{
?>	
<ul class="message">
<?php	foreach($err as $msg)
{
?>
	<li><?=$msg ?></li>
<?php 	}
?>
</ul>
<?php 
}
?>

<div class="form_row">
 <div class="form_label"><label for="fullname">Full Name</label></div>
 <div class="form_field"><input type="text" name="name" id="fullname" value="<?=(isset($_POST['name']))?htmlspecialchars($_POST['name']):'' ?>" /></div>
</div>

<div class="form_row">
 <div class="form_label"><label for="email">E-mail Address</label></div>
 <div class="form_field"><input type="text" name="email" id="email" value="<?=(isset($_POST['email']))?htmlspecialchars($_POST['email']):'' ?>" /></div>
</div>

<div class="form_row">
 <div class="form_label">
   <label for="password">Create a Password</label></div>
 <div class="form_field"><input type="password" name="password" id="password" value="<?=(isset($_POST['password']))?htmlspecialchars($_POST['password']):'' ?>" /></div>
</div>

<div class="form_row">
 <div class="form_label"><label for="passwordconfirm">Confirm Password</label></div>
 <div class="form_field"><input type="password" name="passwordconfirm" id="passwordconfirm" value="<?=(isset($_POST['passwordconfirm']))?htmlspecialchars($_POST['passwordconfirm']):'' ?>" /></div>
</div>


<div class="form_row">
 <div class="form_label">&nbsp;</div>
 <div class="form_field"><input type="submit" name="submit" value="Register for Access" /></div>
</div>
</form>