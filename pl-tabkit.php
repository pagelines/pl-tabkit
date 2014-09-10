<?php
/*
Plugin Name: TabKit
Plugin URI: http://www.pagelines.com
Description: TabKit is a self-contained, custom post type section. TabKit will display your categories across the top and list your posts below.   
Author: PageLines
PageLines: true
Version: 1.0
Section: true
Class Name: PL_TabKit
Demo: http://www.pagelines.com
*/

// maybe load section code...
if( class_exists( 'PageLinesSectionFactory' ) )
	include( 'class.section.php');

include_once( 'class.tabkit.php' );
