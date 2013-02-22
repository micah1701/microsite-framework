<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/db.php';

if( isset($_GET['logout']) )
{
	$_SESSION['roles'] = NULL;
	$_SESSION['user_id'] = NULL;
	unset($_SESSION);
	header("Location: /admin/login");
	return;
}

$uri_parts = (isset($_GET['uri']) ) ? explode("/",$_GET['uri']) : array('home');
$view = $uri_parts[0];
$pages = array(  "home" 	=> array("title"=>"CMS Homepage","required_role"=>"admin"),
				 "login"	=> array("title"=>"Log In","required_role"=>false),
				 "register"	=> array("title"=>"Register for Access","required_role"=>false),
				 "myprofile"=> array("title"=>"My Profile","required_role"=>"admin")
			  );

if(!array_key_exists($view,$pages) )
{
	header("HTTP/1.0 404 Not Found");
	exit("<h1>404 - File Not Found</h1>");	
}

if( $pages[$view]['required_role'] !== false )
{
	if( !isset($_SESSION['roles'])  )
	{
		header("Location: /admin/login");
		exit();
	}
	elseif( !in_array($pages[$view]['required_role'],$_SESSION['roles'])) 
	{
		header("HTTP/1.0 403 Forbidden");
		exit("<h1>403 - Forbidden</h1>You do not have permission to view this page");	
	}	
}

/* set header from within a view
 *
 */
function setHeader($header,$exit=false)
{
	header($header);
	if( $exit ){
		exit();
	}
}

?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $pages[$view]['title'] ?></title>
    <link type="text/css" href="/assets/css/admin.css" rel="stylesheet" media="screen, projection" />
    <link type="text/css" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" rel="stylesheet" media="screen, projection" />
</head>

<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
<script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>


<body>

<div id="pageContainer">
	<div id="header">
    
    <? if ($view != "register" && $view != "login" ) { ?>
    	<div id="utilitynav">
            <a href="/admin"><?=(in_array('admin',$_SESSION['roles'])) ? "Homepage " : "My Presentations" ?></a> | 
       		<a href="/admin/myprofile">My Profile</a> | 
            <a href="/admin?logout">Log Out</a>
        </div>
    <? } ?>

		<img src="/assets/images/logo.png" width="152" height="37" alt="LOGO_TEXT" style="margin: 20px 0 18px 20px;">
    	<br><span id="headertext">Some text about this site</span>

    
    </div>

	<?php include_once $_SERVER['DOCUMENT_ROOT'].'/admin/pages/'.$view.'.php'; ?>
</div><!-- end "pageContainer" -->

</body>
</html>