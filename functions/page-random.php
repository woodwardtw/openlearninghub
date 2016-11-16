<?php
/*
Template Name: Random Syndicated Content Redirect

*/


// check for the passed parameter value, set to blank if not fo
$group =  (isset($wp_query->query_vars['group'])) ? $wp_query->query_vars['group'] : '';

// 21 day filter function (in functions.php)
// add_filter( 'posts_where', 'filter_where_21' );

// set arguments for WP_Query()
$args = array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 10,
    'orderby' => 'rand',
    'cat' => 25,
);

//--------- set up db query
global 	$wpdb;

$now = time();

// custom query to get subscribed blogs from the links table
$custom_query =  
	"
	SELECT wp_posts.ID 
			FROM wp_posts  
			WHERE 
				1=1
				AND wp_posts.post_type = 'post' 
				AND wp_posts.post_status = 'publish' 
			ORDER BY RAND() LIMIT 0, 5
	";
  

// run run run that query
$my_random_post = $wpdb->get_results( $custom_query );


/*
if ($group != '') {
	// add parameter to search for category
	$args['cat'] = 9;
} 
*/

echo '<pre>';
var_dump($my_random_post);
echo '</pre>';


exit;



// clean up after ourselves
// remove_filter( 'posts_where', 'filter_where_21' );


// process the database request through WP_Query
while ( $my_random_post->have_posts () ) {
  $my_random_post->the_post ();
  
  // redirect the user to the random post
  wp_redirect ( get_permalink () );
  exit;
}
?>