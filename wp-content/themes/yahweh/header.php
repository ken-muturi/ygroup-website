<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width initial-scale=1.0" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!-- Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. -->
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<meta name="author" content="Ygroup.us" />
<link rel="shortcut icon" href="../favicon.ico">
<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/style.css" />
<script src="<?php echo get_template_directory_uri(); ?>/js/modernizr.custom.js"></script>

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div class="container">
	<header class="clearfix">
		<span>blog</span>
		<h1>Yahweh Worshipers </h1>
		<nav>
			<a href="<?php echo bloginfo('url'); ?>" class="icon-drop" data-info="Home">Home</a>
			<a href="http://ygroup.us/website/" class="icon-images" data-info="My Portfolio">portfolio</a>
		</nav>
	</header>
	<div class="main">	
