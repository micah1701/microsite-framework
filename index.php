<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/db.php';


$uri_parts = (isset($_GET['uri']) ) ? explode("/",$_GET['uri']) : array('home');
$view = $uri_parts[0];
$pages = array(  "home" 	=> array("title"=>"Homepage","required_role"=>false)
			  );

if(!array_key_exists($view,$pages) )
{
	header("HTTP/1.0 404 Not Found");
	exit("<h1>404 - File Not Found</h1>");	
}
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $pages[$view]['title'] ?></title>
    <link type="text/css" href="/assets/css/screenstyles.css" rel="stylesheet" media="screen, projection" />
</head>
<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
<body>

<div id="pageContainer">
	<div id="header">
    
    	<img src="/assets/images/logo.png" width="152" height="37" alt="LOGO_TEXT" style="margin: 20px 0 18px 20px;">
    	<br><span id="headertext">Some text about this site</span>
    
    </div>

	<?php include_once $_SERVER['DOCUMENT_ROOT'].'/pages/'.$view.'.php'; ?>


</div><!-- end "pageContainer" -->

</body>
</html>