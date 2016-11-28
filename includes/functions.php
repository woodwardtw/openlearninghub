<?php

/*-----------------------------------------------------------------------------------*/
/* Start WooThemes Functions - Please refrain from editing this section */
/*-----------------------------------------------------------------------------------*/

// Set path to WooFramework and theme specific functions
$functions_path = get_template_directory() . '/functions/';
$includes_path = get_template_directory() . '/includes/';

// Define the theme-specific key to be sent to PressTrends.
define( 'WOO_PRESSTRENDS_THEMEKEY', '1j9ve3hvnjs3wu0bg7gxep2jux9vabdbs' );

// WooFramework
require_once ($functions_path . 'admin-init.php' );			// Framework Init

// Theme specific functionality
require_once ($includes_path . 'theme-options.php' ); 		// Options panel settings and custom settings
require_once ($includes_path . 'theme-functions.php' ); 	// Custom theme functions
require_once ($includes_path . 'theme-plugins.php' );		// Theme specific plugins integrated in a theme
require_once ($includes_path . 'theme-actions.php' );		// Theme actions & user defined hooks
require_once ($includes_path . 'theme-comments.php' ); 		// Custom comments/pingback loop
require_once ($includes_path . 'theme-js.php' );			// Load javascript in wp_head
require_once ($includes_path . 'sidebar-init.php' );		// Initialize widgetized areas
require_once ($includes_path . 'theme-widgets.php' );		// Theme widgets
require_once ($includes_path . 'woo-column-generator/woo-column-generator.php' ); // Button to generate content columns

/*-----------------------------------------------------------------------------------*/
/* You can add custom functions below */
/*-----------------------------------------------------------------------------------*/


add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

function custom_excerpt_length( $length ) {
	// adjust the excerpt length to 30 words
	return 100;
}

add_filter('user_contactmethods', 'tv_modify_profile');

function tv_modify_profile( $profile_fields ) {

	// Add new fields
	$profile_fields['twitter'] = 'Twitter Username';
	$profile_fields['blog'] = 'Blog';
	$profile_fields['blogfeed'] = 'Blog Feed';
	$profile_fields['geolocation'] = 'Geographical Location';

	return $profile_fields;
}




/*-----------------------------------------------------------------------------------*/
/* Don't add any code below here or the sky will fall down */
/*-----------------------------------------------------------------------------------*/
?>