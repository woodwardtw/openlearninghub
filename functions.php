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


/* ----- enque scripts for Google+ */

wp_register_script( 'tv_google_plus', 'https://apis.google.com/js/plusone.js' );
wp_enqueue_script( 'tv_google_plus' );


/* ----- return a specific character count for any content (e.g. excerpts based on chars) */
function get_character_excerpt( $content, $chars=500 ) {
	if ( strlen( $content ) > $chars ) {
		return ( strip_tags( substr( $content, 0, $chars ) ) . '...' );
	} else {

	return ($content); 
	}
}


/* ----- add custom user profile fields */
add_filter('user_contactmethods', 'tv_modify_profile');

function tv_modify_profile( $profile_fields ) {

	// Add new fields
	$profile_fields['blog'] = 'Thought Vectors Blog';
	$profile_fields['blogfeed'] = 'Thought Vectors Blog Feed';
	$profile_fields['geolocation'] = 'Geographical Location';
	$profile_fields['twitter'] = 'Twitter User Name';
	$profile_fields['org'] = 'Organizational Affiliation';
	
	return $profile_fields;
}


/* ----- add allowable url parameter for urls */


add_filter('query_vars', 'tv_parameter_queryvars' );

function tv_parameter_queryvars( $qvars )

// allow  tag parameter to be passed in query string
{
	$qvars[] = 'group';
	$qvars[] = 'blogurl';
	return $qvars;
}     
               
/* ----- find an RSS feed for a given URL */
function feedSearch( $url ) {

	// via Martin Hawksey via from Alan Levine (@cogdog)
    if ( $html = @DOMDocument::loadHTML( file_get_contents( $url ) ) ) {
  
        $xpath = new DOMXPath($html);
        $results = array();
                 
    	// find RSS 2.0 feeds
        $feeds = $xpath->query("//head/link[@href][@type='application/rss+xml']/@href");
        foreach($feeds as $feed) {
            $results[] = $feed->nodeValue;
        }
 
         // find Atom feeds
        $feeds = $xpath->query("//head/link[@href][@type='application/atom+xml']/@href");
        foreach($feeds as $feed) {
            $results[] = $feed->nodeValue;
        }
        
        // we have somethng
        if ( count( $results ) ) {
        
        	// we assume (badly) that the first result is the blog feed
        	return $results[0];
        } else {
        
        	//oops return an error
        	return -1;
        }
    } else {
    	return -1;
    }	
}


/* ----- shortcode to generate a Feed Wordpress feedroll for a given tag */

function get_feed_count() {

	global $wpdb;
	
 	$custom_query = "
		SELECT DISTINCT      
					wpl.link_name
		FROM        $wpdb->links wpl,  $wpdb->postmeta wpm
		WHERE       wpm.meta_key='syndication_feed_id' AND 
					wpm.meta_value = wpl.link_id AND
					wpl.link_notes LIKE '%%{category#23}%%'
		";
		
	// run run run that query
	$feedblogs = $wpdb->get_results( $custom_query );
	
	return (count ($feedblogs ) );
}

add_shortcode("feedroll", "tv_feedroll");  

function tv_feedroll( $atts ) {  
	global $wpdb;
	global $cat; // so we can get the current category
	
	// Get the value of any passed attributes to our function
	// $category is either passed or assumed from being on an archive page 
	//		default is the parent category for all feeds
	// $show defaults to listing all feeds for display in a sidebar; if the list is long
	//		pass a value of "random" to list a subset chosen randomly; "all" to list all on a PAGE_LAYOUT_ONE_COLUMN
	// $limit = how many to list on a random subset
	
 	extract( shortcode_atts( array( "category" => "23", "limit" => "10", "show" => "sidebar" ), $atts));  
 	
 	if ( is_category() ) {
 		// set to correct id if we are on a category archive (e.g. general category widget)
 		
 		$catid = $cat;
 	} else {
		// get category ID if not passed as a value (we can haz string names for a param)
		$catid = ( is_numeric ( $category ) ) ? $category : get_cat_ID( $category );
 	}	
 	
 	// A bit of gymnastics- for a sidebar where we do not want them all (default category)
 	// override for the random subset
 	if ( $catid == 23 and $show!= "all" ) $show = "random";

 	// keep a reference for the current category for an archive page
 	$mycat = get_category($catid);
 
 	
 	if ($show == "random") {
 		// setup for displaying a random subset of feeds
 		$orderby = 'RAND()'; // mySQL order to pick random feeds
 		$limit = "LIMIT 0, $limit"; // limit to set amount
 		
 		// uh oh,  hard wired link to see the full list. Make the link to a page that 
 		// uses the shortcode to list all (or empty this string out)
 		$footer = '<br /><br/><a href="/all-blogs/">See All Thought Vector Blogs...</a></p>';
 		
 	} else {
 	
 		// normal mode to list all blogs in a category
 		$orderby = 'wpl.link_name ASC';
 		$limit = '';
 		$footer = '';
 		$suffix = ' from the following blogs:';
	} 
	
 	// custom mySQL query to get subscribed blogs from the links table
 	// because FeedWordpress stores stuff in the link notes
 	// the linked WHERE condition makes sure there is at least one syndicated post
 	// from a given feed 
 	$custom_query = "
		SELECT DISTINCT      
					wpl.link_name, wpl.link_url, wpl.link_description
		FROM        $wpdb->links wpl,  $wpdb->postmeta wpm
		WHERE       wpm.meta_key='syndication_feed_id' AND 
					wpm.meta_value = wpl.link_id AND 
					wpl.link_notes LIKE '%%{category#" . $catid . "}%%'
		ORDER BY    $orderby
		$limit
		";
		
 	// run run run that query
	$feedblogs = $wpdb->get_results( $custom_query );	
	
	// bail if we got nothing
	if (count($feedblogs) == 0 ) {
		$content =  "No blogs found for '"  . $mycat->name . "'";
		
	// we got feeds!
	} else {
	
		if ($show == "random") {
			// for a random subset we want to reference a count of all blogs on the site; 
			// in this site we have 2 feeds that are not blogs, so pass that as a parameter
			// (more hard coding specific to this site, sigh)
			$suffix = ' from <strong>' .  get_feed_count()  . '</strong> total blogs syndicated into this site. Ten random ones are listed below: ';
		} else {
		
			// let's be grammatically correct for only one blog
			$plural = ( (count($feedblogs) == 1 ) ) ? '' : 's';
			
			
			$suffix = ' from <strong>' .  count($feedblogs) . '</strong> blog' . $plural  . '. ';
		}
	
		// Yikes, we use the "Count Posts in a Category, Tag, or Custom Taxonomy" plugin to get a total post count
		$content = '<p>Thought Vectors includes <strong>' .  do_shortcode('[cat_count slug ="'. $mycat->slug . '"]') . '</strong> total post(s) syndicated for <strong>' . $mycat->name . '</strong>' .  $suffix . '</p>';
		
		//start the output
		$content .= "<ol>\n";
		
		if ($show == 'all') {
			// output each item as a list item, title of blog linked to URL, and a description
			foreach ( $feedblogs as $item ) {
				$content  .=  '<li><strong><a href="' . htmlspecialchars($item->link_url)   . '">' . htmlspecialchars_decode($item->link_name)  . '</a></strong> <em>' . htmlspecialchars_decode($item->link_description) . '</em>  (' . htmlspecialchars($item->link_url)  . ')</li>' . "\n";              
			}
		
		} else {		
			// output each item as a list item, title of blog linked to URL 
			foreach ( $feedblogs as $item ) {
			
				$content  .=  '<li><a href="' . htmlspecialchars($item->link_url)   . '">' . htmlspecialchars_decode($item->link_name)  . '</a></li>' . "\n";              
			}
		}
		
		// clean up after your lists
		$content .= '</ol>' . $footer .  '<hr /><p>Give some comment love to <a href="' . get_site_url() . '/random/?group=' . $catid . '">a random post in the last 2 weeks from ' . $mycat->name . '</a></p>'; 
		
		
	}		
	
	// here comes the output  
    return $content;  
}


/* ----- response for gravity form submission to add new blog to Feed wordpress 

	$entry array corresponds to the fields of a specific gravity form, so they will
	need to be adjusted for another site. Ditto for the hook used to call the workhorse
	function

*/

add_action("gform_after_submission_1", "add_blog_to_fwp", 10, 2);

function add_blog_to_fwp( $entry, $form ) {
	
	global $wpdb; // cause we need to go to the database directly
	
	// set filters aside for input
	remove_filter('pre_link_rss', 'wp_filter_kses');
	remove_filter('pre_link_url', 'wp_filter_kses');
	
	// Get the link category Feed Wordpress uses to store feed data
	$fwp_link_category = get_terms('link_category', 'name__like=Contributors');
	
	// get the correct term id for the link category
	$contrib_category = intval($fwp_link_category[0]->term_id);
	
	// we will use twitter handle as a user name
	$twittername = trim( $entry[4] ); 
	
	// just in case they put an @ in front, take it out
	if ( $twittername[0] == '@' ) $twittername = substr($twittername, 1);
		
	// set up array to create a new WP account; password is randomized- user never needs
	// to log in, shhhhhhh it's a secret
	$userdata = array(
		'user_login'    => $twittername,
		'user_pass'		=> wp_generate_password( $length=12, $include_standard_special_chars=false ),
		'user_email'	=> $entry[2],
		'display_name'	=> '@' . $twittername,
		'first_name' 	=> $entry['1.3'],
		'last_name' 	=> $entry['1.6'],
		
	);
	
	// create account
	$user_id = wp_insert_user( $userdata );
	
	// error check and bomb out, should only be feeif username exists
	if ( !is_wp_error( $user_id ) ) {
	
		// Safari browsers convert RSS feeds to feed://, so replace it if its in the string
		$feedurl = str_replace("feed://", "http://", trim( $entry[5] ));
		
		// update our custom meta data fields, these are ones created specifically
		// for this site, your mileage and use should vary
		update_user_meta( $user_id, 'blog', trim( $entry[3] ) );
		update_user_meta( $user_id, 'blogfeed', $feedurl );
		update_user_meta( $user_id, 'geolocation', trim( $entry[6] ) );
		update_user_meta( $user_id, 'twitter', trim( $twittername ) );
	
		// start with an array to hold categories for the Links note field
		// in the fun quirky format FWP uses
		$catids[] = "{category#23}"; // add Blog category to all feeds
	
		// Walk through the affiliations
		if  ( $entry[7] == 'open' ) {
		
			// open participants?
			$catids[] = "{category#24}"; // add category for Open Participants
			update_usermeta( $user_id, 'org', trim($entry[8]) );
	
		} else {
			// VCU Participants
			$catids[] = "{category#70}"; // category for VCU Participants
			update_usermeta( $user_id, 'org', 'VCU' );
		
			if  ( $entry[7] == 'vcu' ) {
				$catids[] = "{category#33}"; // category for VCU Faculty, staff
			
			} else {
					$catids[] = "{category#26}"; // category for UNIV Student
					
					// for UNIV students, we add a category, the code is in the Gravity
					// form because I was lazy on writing a swtich STATEMENT_TRACE
					$catids[] = $entry[10];
			} // end if for VCU participants
		} // end if for VCU
				
	
		//build the link notes with the user name created for this blog
		$link_notes = 'map authors: name\n*\n'. $user_id . "\n";
	
		// add a tag to all syndicated blogs
		$link_notes .= 'tags: blogged' . "\n";
	
		// add categories to the link notes
		$link_notes .=  'cats: ' . implode( '\n' , $catids) . "\n";
		
		// flag to add tags and categories
		$link_notes .= 	'add/post_tag: yes' . "\n" . 'add/category: yes' . "\n";
	
		$new_link = array(
				'link_name' => trim($entry[3]),
				'link_url' => trim($entry[3]),
				'link_category' => $contrib_cat,
				'link_rss' => trim($entry[5])
				);
		if( !function_exists( 'wp_insert_link' ) )
			include_once( ABSPATH . '/wp-admin/includes/bookmark.php' );	

		// add the new link
		$linkid = wp_insert_link($new_link);
	
		// clean the link notes
		$esc_link_notes = $wpdb->escape($link_notes);
	
		// update the notes in the links
		$result = $wpdb->query("
				UPDATE $wpdb->links
				SET link_notes = \"" . $esc_link_notes . "\" 
				WHERE link_id='$linkid'
		");
	} else {
	
		// a most useless error message for user creation
	   	$error_string = $user_id->get_error_message();
   		die ('ERROR CONDITION RED: ' .  $error_string);
	}
}

/*-----------------------------------------------------------------------------------*/
/* Don't add any code below here or the sky will fall down */
/*-----------------------------------------------------------------------------------*/
?>