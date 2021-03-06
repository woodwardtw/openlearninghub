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
/* You can add custom functions below THANKS DUDE! */ 
/*-----------------------------------------------------------------------------------*/

/* -----set syndicated blog category ID 
		hopefully you made one named "Syndicated"										*/  
define( DEFCATID, get_cat_ID('Syndicated'));

/* ----  mobile menu script  ------ */
/* ----- enque scripts for Google+ */

function add_theme_scripts() {
  
  wp_enqueue_script( 'toms-extras', get_template_directory_uri() . '/functions/js/toms-extras.js', array ( 'jquery' ), 1.1, true);
  wp_enqueue_script( 'openlearnhub_google_plus', 'https://apis.google.com/js/plusone.js' );

}
add_action( 'wp_enqueue_scripts', 'add_theme_scripts' );


/* ----- return a specific character count for any content (e.g. excerpts based on chars) */
function get_character_excerpt( $content, $chars=500 ) {
	if ( strlen( $content ) > $chars ) {
		return ( strip_tags( substr( $content, 0, $chars ) ) . '...' );
	} else {

	return ($content); 
	}
}

/* ----- add allowable url parameter for urls */


add_filter('query_vars', 'openlearnhub_parameter_queryvars' );

function openlearnhub_parameter_queryvars( $qvars )

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
					wpl.link_notes LIKE '%%{category#" . DEFCATID . "}%%'
		";
		
	// run run run that query
	$feedblogs = $wpdb->get_results( $custom_query );
	
	return (count ($feedblogs ) );
}

add_shortcode("feedroll", "openlearnhub_feedroll");  

function openlearnhub_feedroll( $atts ) {  
	global $wpdb;
	global $cat; // so we can get the current category
	
	/*  $category => either passed or assumed from being on an archive page 
			default is the parent category for all feeds
	    $orderby => 'random' for a selection of blogs in category, 'name' to list in 
	    	by blog name, or 'newest' for order by most recent
		$count = number of blogs to show, use 'all' for... you guessed it
		$authorlink = true to append a link to author archive
		$blogslug = link for page with all blogs listed
	*/
 	
 	extract( shortcode_atts( array( "category" => DEFCATID, "orderby" => "random", "count" => 10, "authorlink" => "no", "blogslug" => 'all-blogs'), $atts ) );  

 	
 	if ( is_category() ) {
 		// set to correct id if we are a widget on a category archive (e.g. general category widget)
 		
 		$catid = $cat;
 	} else {
		// get category ID if not passed as a value (we can haz string names for a param)
		$catid = ( is_numeric ( $category ) ) ? $category : get_cat_ID( $category );
 	}	

 	// keep a reference for the current category so we can fetch stuff about it (name,etc)
 	$mycat = get_category($catid);

 	// if the sort order is newest, reverse sort by link ID
 	
 	if ( $orderby == "newest" ) {
 		// list in reverse order of ID (newest added first)
 		$order_sql = "wpl.link_id  DESC";
 		
 	} elseif ( $orderby == "random" ) {
 		// random order, usually used for widgets
 		$order_sql = "RAND()";
 		
 	} else {
 		// default, alpha order of title
 		$order_sql = "wpl.link_name ASC";
 	}

 	// for limited number of responses
 	$limit_sql = ( $count != 'all' ) ? "LIMIT 0, $count" : '';
 	 		
 	// custom mySQL query to get subscribed blogs from the links table
 	// because FeedWordpress stores stuff in the link notes
 	// the linked WHERE condition makes sure there is at least one syndicated post
 	// from a given feed 
 	$custom_query = "
		SELECT DISTINCT      
					wpl.link_name, wpl.link_url, wpl.link_notes
		FROM        $wpdb->links wpl,  $wpdb->postmeta wpm
		WHERE       wpm.meta_key='syndication_feed_id' AND 
					wpm.meta_value = wpl.link_id AND 
					wpl.link_notes LIKE '%%{category#" . $catid . "}%%'
		ORDER BY    $order_sql $limit_sql
		";
	
 	// run run run that query
	$feedblogs = $wpdb->get_results( $custom_query );	
	
	// bail if we got nothing
	if (count($feedblogs) == 0 ) {
		$content =  "No blogs found for '"  . $mycat->name . "'";
		
	// we got feeds!
	} else {
	
	
		if ( $orderby == "newest" ) {
		
			$suffix = ' from <strong>' .  get_feed_count()  . '</strong> total blogs syndicated into this site, listed below in the order of most recently added: ';
			
	
		} elseif ($orderby == "random") {
			// for a random subset we want to reference a count of all blogs on the site; 

			$suffix = ' from <strong>' .  get_feed_count()  . '</strong> total blogs syndicated into this site, including the random sites listed below: ';
		
		} else {
		
			// let's be grammatically correct for only one blog
			$plural = ( (count($feedblogs) == 1 ) ) ? '' : 's';
			
			$suffix = ' from <strong>' .  count($feedblogs) . '</strong> blog' . $plural  . '. ';
		}
		
		// link to page for all blogs
		if ($count != 'all') {
			$footer = '<br /><br/><a href="' . site_url() . '/' . $blogslug .'" class="button">See All Blogs...</a></p>';
		}

	
		// Yikes, we use the "Count Posts in a Category, Tag, or Custom Taxonomy" plugin to get a total post count
		$content = '<p>This site includes <strong>' .  do_shortcode('[cat_count slug ="'. $mycat->slug . '"]') . '</strong> total post(s) syndicated for <strong>' . $mycat->name . '</strong>' .  $suffix . '</p>';
		

		
		//start the output
		$content .= "<ol style=\"padding:1.5em;\">\n";
		
		if ( $authorlink == "yes") {
			// output with author link (e.g. page) 
					
			foreach ( $feedblogs as $item ) {
			
				// Alan's hack to deal with search strings with \n in them
				$author_notes = str_replace( '\n', 'XxxX', $item->link_notes);
				
				// fish through the link notes for the author info
				preg_match ( '/map authors: nameXxxX(.*)XxxX([0-9]+)/' , $author_notes , $matches);
				
				// look for the authors info in the notes
				if ( count( $matches ) ) {
				
					// build output link
					$authorlink =  ' (see all syndicated posts by <a href="' . get_site_url() .'/author/' . sanitize_title( get_the_author_meta('user_login', $matches[2]) ) . '">' . get_the_author_meta('display_name', $matches[2]). '</a>)';
				} else {
					$authorlink = '';
				}
				
				$content  .=  '<li><a href="' . htmlspecialchars($item->link_url)   . '">' . htmlspecialchars_decode($item->link_name)  . '</a>' . $authorlink . '</li>' . "\n";              
			}
		
		} else {
			// output without author link (e.g. widget) 
			
			foreach ( $feedblogs as $item ) {
				$content  .=  '<li><a href="' . htmlspecialchars($item->link_url)   . '">' . htmlspecialchars_decode($item->link_name)  . '</a></li>' . "\n";              
			}
		}
		// clean up after your lists
		$content .= '</ol>' . $footer .  '<p>Give some comment love to <a href="' . get_site_url() . '/random/?group=' . $catid . '" target="_blank">a random post from "' . $mycat->name . '"</a></p>'; 
		
		
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
	
	// skip this function if they opted for help; in this case just
	// send the info via email
	
	if ( $entry[16] == 'help')  {
	
		// Get the link category we will use for pending feeds
		// NOTE: There needs to be at least one link in there to find the category

		/*
		$fwp_link_category = get_terms( 'link_category', array(
			'name__like'    => 'Pending',
		 ) );
		 
		 */
		 
		 $contrib_category = 120;
	
	} else {
		// Get the link category Feed Wordpress uses to store feed data
	
		/*
		$fwp_link_category = get_terms( 'link_category', array(
			'name__like'    => 'Contributors',
		 ) );
		 */
		 
		 $contrib_category = 3;
	}
	

	// set filters aside for input
	remove_filter('pre_link_rss', 'wp_filter_kses');
	remove_filter('pre_link_url', 'wp_filter_kses');

	// we will use twitter handle as a user name, clean it up.
	$twittername = trim( $entry[2] ); 

	// just in case they put an @ in front, take it out
	if ( $twittername[0] == '@' ) $twittername = substr($twittername, 1);
		
	// check if email address is already in use, if it does, we get a user id back
	$user_id = email_exists($entry[3]);

	// or check is username exists
	if ( !$user_id ) $user_id = username_exists( $twittername );

	// create account if it does not exist
	if ( !$user_id ) {
		// set up array to create a new WP account; password is randomized- user never needs
		// to log in, shhhhhhh it's a secret
		$userdata = array(
			'user_login'    => $twittername,
			'user_pass'		=> wp_generate_password( $length=12, $include_standard_special_chars=false ),
			'user_email'	=> $entry[3],
			'display_name'	=> '@' . $twittername,
			'first_name' 	=> $entry['1.3'],
			'last_name' 	=> $entry['1.6']
		);

		$user_id = wp_insert_user( $userdata );
	
	}

	// error check and bomb out, should only be if username exists
	if ( !is_wp_error( $user_id ) ) {
		
		if ( $entry[16] == 'yes' ) {
			// going for the whole blog option
			$blogurl = trim( $entry[17] );

			// RSS Feed
			$feedurl = trim( $entry[18] );
			
		} elseif ( $entry[16] == 'no' ) {
			// going for the category / tag option
			$blogurl = trim( $entry[19] );

			// RSS Feed
			$feedurl = trim( $entry[20] );
		} else {
			// the TBA option, edit later in links table
			$blogurl = trim( $entry[27] );

			// RSS Feed
			$feedurl = trim( $entry[27] );

		
		}
		
				
		// start with an array to hold categories for the Links note field
		// in the fun quirky format FWP uses
		$catids[] = '{category#' . DEFCATID . '}'; // add Syndicated category to all feeds

		// Add Category for affiliation (category id from gform)		
		$catids[] = "{category#" . $entry[23] . "}";				

		//build the link notes with the user name created for this blog
		$link_notes = 'map authors: name\n*\n'. $user_id . "\n";

		// add categories to the link notes
		$link_notes .=  'cats: ' . implode( '\n' , $catids) . "\n";
	
		// flag to add tags and categories
		$link_notes .= 	'add/category: yes' . "\n";

		// link data, we can use url for name, FWP will update it once syndication happens
		$new_link = array(
				'link_name' => $blogurl,
				'link_url' => $blogurl,
				'link_category' => $contrib_category,
				'link_rss' => $feedurl
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

		// a most useless error message for user creation probs, danger danger
		$error_string = $user_id->get_error_message();
		die ('ERROR CONDITION RED: ' .  $error_string);
	}

}


if(!function_exists('load_my_script')){
    function load_my_script() {
        global $post;
        $deps = array('jquery');
        $version= '1.0'; 
        $in_footer = true;
        wp_enqueue_script('my-script', get_stylesheet_directory_uri() . '/functions/js/toms-extras.js', $deps, $version, $in_footer);
        wp_localize_script('my-script', 'my_script_vars', array(
                'postID' => $post->ID
            )
        );
    }
}
add_action('wp_enqueue_scripts', 'load_my_script');

/*-----------------------------------------------------------------------------------*/
/* Don't add any code below here or the sky will fall down */
/*-----------------------------------------------------------------------------------*/
?>