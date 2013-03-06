<h1>Update My Profile</h1>
<?PHP

$user = ORM::for_table( _table_users)->find_one($_SESSION['user_id']);

// check for submitted form data
if ( isset($_POST['submit']) && isset( $_POST['name']) )
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
	
	
	if( trim($_POST['password']) != "" && $_POST['password'] != $_POST['passwordconfirm'])
	{
		$err[] = "Password Mismatch: Please enter the same password in the password confirmation field";
	}
	
	
	if( $_POST['email'] != $user->email) // if user is changing their e-mail address
	{
		$check = ORM::for_table( _table_users)->where('email',$_POST['email'])->find_one();
		if( $check )
		{
			$err[] = "There is already a user account associated with this e-mail address";
		}	
		
	}
	
	// if no errors, update the user
	if( !isset($err[0]) )
	{
		$user->name = $_POST['name'];
		$user->email = $_POST['email'];
		
		if(trim($_POST['password']) != "" && $_POST['password'] == $_POST['passwordconfirm'])
		{
			$user->password = hashPassword($_POST['password']);
		}
		
		$user->save();
	
		setHeader("Location: /admin",true);	  // forward user off this page
		return;	
	}
	
}

function value($field,$userObj)
{
	$value = (isset($_POST[$field])) ? $_POST[$field] : $userObj->$field;
	return trim(htmlspecialchars($value));
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
<form method="post">

<div class="form_row">
 <div class="form_label"><label for="fullname">Full Name</label></div>
 <div class="form_field"><input type="text" name="name" id="fullname" value="<?=value('name',$user) ?>" /></div>
</div>

<div class="form_row">
 <div class="form_label"><label for="email">E-mail Address</label></div>
 <div class="form_field"><input type="text" name="email" id="email" value="<?=value('email',$user) ?>" /></div>
</div>

<div class="form_row" style="padding: 8px 8px 0 8px; border: 1px #7090a5 solid; width: 600px;">    
    <div class="form_row">
     <div class="form_label"><label for="password">Password</label></div>
     <div class="form_field"><input type="password" name="password" id="password" value="" autocomplete="off" /></div>
    </div>
    
    <div class="form_row">
     <div class="form_label"><label for="passwordconfirm">Confirm Password</label></div>
     <div class="form_field">
     	<input type="password" name="passwordconfirm" id="passwordconfirm" value="" autocomplete="off"  />
     	<br>Leave blank to keep exisiting password
     </div>
    </div>
</div>

<div class="form_row">
 <div class="form_label">&nbsp;</div>
 <div class="form_field"><input type="submit" name="submit" value="Update Profile" /></div>
</div>
</form>

<br>
<div class="form_row">
 <div class="form_label">Last Login Date:</div>
 <? $logtime = ($user->previous_login != "0000-00-00 00:00:00") ? $user->previous_login : $user->last_login; ?>
 <div class="form_field"><?=date("F d, Y g:i:sa", strtotime($logtime)) ?></div>
</div>
<div class="form_row">
 <div class="form_label">Total Logins:</div>
 <div class="form_field"><?=$user->total_logins ?> times</div>
</div>
<div class="form_row">
 <div class="form_label">Current IP Address:</div>
 <div class="form_field"><?=$_SERVER['REMOTE_ADDR'] ?></div>
</div>