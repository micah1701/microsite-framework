<?php
	if(is_array($_SESSION['roles']) )
	{	// user is already logged in
		setHeader("Location: /admin");
		return;	
	}

	if(isset($_POST['submit']))
	{
		if( $_POST['username'] == "" || $_POST['password'] == "")
		{
			$err = "Please enter both your e-mail address and password";
		}
		
		$password = hashPassword( $_POST['password']);
		
		$user = ORM::for_table( _table_users)
					->where('email',$_POST['username'])
					->where('password', $password )
					->find_one();
		
		if($user)
		{
			$user->total_logins = $user->total_logins + 1;
			$user->previous_login = $user->last_login;
			$user->last_login = date("Y-m-d H:i:s");
			$user->save();
			
			$_SESSION['user_id'] = $user->id;
			$_SESSION['roles'] = explode(",",$user->roles );
			setHeader("Location: /admin");
			return;
		}
		else
		{
			$err = "Invalid e-mail address or password";
		}
		
	}
?>
<h1>Log In</h1>

<form action="" method="post">

<?php
	if(isset($err) ){
	echo "\n<ul class=\"message\"><li>".$err."</li></ul>\n";	
	}
?>
<div class="form_row"> 
<label for="username">E-Mail Address</label><br />
<input type="text" id="username" name="username" value="" />
</div>

<div class="form_row"> 
<label for="password">Password</label><br />
<input type="password" id="password" name="password" value="" />
</div>

<div class="form_row"> 
<input type="submit" name="submit" value="Sign In" />

&nbsp; &nbsp; <a href="#" onclick="$('#resetPassword').fadeToggle('fast'); return false" id="resetPasswordLink">Forgot Password?</a>
</div>

</form>

<div id="resetPassword" style="display: none">
<h2 style="margin-top: 20px">Reset Password</h2>
 <div id="resetPasswordForm">
 <p>To have your password reset, enter the e-mail address associated with your account  and click "Reset Password."  A new password will be generated and e-mailed to you.</p>
 <label for="username">Email Address</label><br />
 <input type="text" id="resetEmail" name="resetEmail" value="" />
 <input type="submit" name="submit" id="resetPasswordSumbit" value="Reset Password" />
 </div>
</div>

</div>

<script type="text/javascript">
$(document).ready(function(){
	
	$("#resetPasswordSumbit").click( function(){
		var email = $("#resetEmail").val();
		if(email == ""){ alert("Please enter your e-mail address to continue"); return; }
	
 		$(this).fadeOut('fast'); // hide submit button		
		
		$.get("/request.php",{xhr: 'reset_password', email:email}, function(data){
			if($.trim(data) != "success"){
				$("#resetPasswordSumbit").fadeIn('fast'); // display the submit button again
				alert($.trim(data));
			}else{
				$("#resetPasswordForm").html("<strong>Your password has been reset and e-mailed to "+email +".</strong>");
			}
		});
	});
	
});
</script>    