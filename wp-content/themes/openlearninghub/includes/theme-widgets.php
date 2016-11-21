<?php

/*---------------------------------------------------------------------------------*/
/* Loads all the .php files found in /includes/widgets/ directory */
/*---------------------------------------------------------------------------------*/

include( get_template_directory() . '/includes/widgets/widget-woo-tabs.php' );
include( get_template_directory() . '/includes/widgets/widget-woo-adspace.php' );
include( get_template_directory() . '/includes/widgets/widget-woo-blogauthor.php' );
include( get_template_directory() . '/includes/widgets/widget-woo-embed.php' );
include( get_template_directory() . '/includes/widgets/widget-woo-flickr.php' );
include( get_template_directory() . '/includes/widgets/widget-woo-search.php' );
include( get_template_directory() . '/includes/widgets/widget-woo-twitter.php' );
include( get_template_directory() . '/includes/widgets/widget-woo-subscribe.php' );
include( get_template_directory() . '/includes/widgets/widget-woo-gallery.php' );
include( get_template_directory() . '/includes/widgets/widget-woo-ad125.php' );

/*---------------------------------------------------------------------------------*/
/* Deregister Default Widgets */
/*---------------------------------------------------------------------------------*/
if (!function_exists( 'woo_deregister_widgets')) {
	function woo_deregister_widgets(){
	    unregister_widget( 'WP_Widget_Search' );         
	}
}
add_action( 'widgets_init', 'woo_deregister_widgets' );  


?>