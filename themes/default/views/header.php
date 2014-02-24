<?php defined('BASE_PATH') or die('No direct script access.');?>
<!doctype html>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<head>
	<meta charset="<?php echo CHARSET;?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	
	<title><?php echo $meta_title;?></title>
	<meta name="keywords" content="<?php echo $meta_keywords;?>" />
    <meta name="description" content="<?php echo $meta_description;?>" />
	<meta name="author"	content="jaf-php-<?php echo VERSION?>">
	<link rel ="author" href="humans.txt" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link rel="shortcut icon" href="<?php echo View::template_url()?>images/favicon.ico">
	<link rel="apple-touch-icon" href="<?php echo View::template_url()?>images/apple-touch-icon.png">
	<?php View::css_return();?>
	<?php View::js_return('header');?>
</head>
<body>
<p class="info">Your project is almost ready! Please check the <a href="<?php echo SITE_URL?>/home/readme/">readme list</a>.</p>
<div id="header-container">
	<header class="wrapper">
		<h1 id="title"><?php echo $meta_title;?></h1>
		<nav>
			<ul>
				<li><a href="<?php echo SITE_URL?>">Home</a></li>
				<li><a href="<?php echo SITE_URL?>/home/readme/">Readme</a></li>
				<li><a href="<?php echo SITE_URL?>/example/guide/">Examples</a></li>
			</ul>
		</nav>
	</header>
</div>