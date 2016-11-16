<?php
/*
Template Name: Random Syndicated Content Redirect

*/


// check for the passed parameter value, set to blank if not fo
$group =  (isset($wp_query->query_vars['group'])) ? $wp_query->query_vars['group'] : '23';

// date stamp for 2 weeks ago
//$recent_str = date('%a, %e %b %Y %H:%i:%s', strtotime("-2 weeks"));
// 				wp_posts.post_date >= '$recent_str'

//--------- set up db query
global 	$wpdb;
 
// custom query to get subscribed blogs from the links table
$custom_query =  
	"
	SELECT wp_posts.guid
			FROM wp_posts
			INNER JOIN wp_term_relationships 
			ON wp_posts.ID = wp_term_relationships.object_id   
			WHERE
				wp_posts.ID >= FLOOR(1 + RAND() * (SELECT MAX(ID) FROM wp_posts))
				AND wp_term_relationships.term_taxonomy_id IN ($group)
				AND wp_posts.post_type = 'post' 
				AND wp_posts.post_status = 'publish'  
			Limit 0,1
	";
 

// run run run that query
$my_random_post = $wpdb->get_results( $custom_query );

// go browser go!
wp_redirect ( $my_random_post[0]->guid );


?>