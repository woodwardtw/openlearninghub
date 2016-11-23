<?php

if (!function_exists( 'woo_options')) {
function woo_options() {
	
// THEME VARIABLES
$themename = "Editorial";
$themeslug = "editorial";

// STANDARD VARIABLES. DO NOT TOUCH!
$shortname = "woo";
$manualurl = 'http://www.woothemes.com/support/theme-documentation/'.$themeslug.'/';

//Access the WordPress Categories via an Array
$woo_categories = array();  
$woo_categories_obj = get_categories( 'hide_empty=0' );
foreach ($woo_categories_obj as $woo_cat) {
    $woo_categories[$woo_cat->cat_ID] = $woo_cat->cat_name;}
$categories_tmp = array_unshift($woo_categories, "Select a category:" );    
       
//Access the WordPress Pages via an Array
$woo_pages = array();
$woo_pages_obj = get_pages( 'sort_column=post_parent,menu_order' );    
foreach ($woo_pages_obj as $woo_page) {
    $woo_pages[$woo_page->ID] = $woo_page->post_name; }
$woo_pages_tmp = array_unshift($woo_pages, "Select a page:" );       

//Stylesheets Reader
$alt_stylesheet_path = get_template_directory() . '/styles/';
$alt_stylesheets = array();
if ( is_dir($alt_stylesheet_path) ) {
    if ($alt_stylesheet_dir = opendir($alt_stylesheet_path) ) { 
        while ( ($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false ) {
            if(stristr($alt_stylesheet_file, ".css") !== false) {
                $alt_stylesheets[] = $alt_stylesheet_file;
            }
        }    
    }
}

//More Options
$other_entries = array( "Select a number:","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19" );

// Categories array (no empty categories).
$categories_obj = get_categories();
$categories = array();

if ( $categories_obj ) {
	foreach ( $categories_obj as $c ) {
		$categories[$c->term_id] = $c->name;
	}
}

// Total recent posts to display.
// Change these numbers depending on whether or not a sidebar is present.

$recentnews_postnumbers = array( 0 => __( 'Select a Number:', 'woothemes' ) );
$multiples_of = 3;
$total_limit = 21;

if ( woo_active_sidebar( 'primary' ) ) {
	$multiples_of = 2;
	$total_limit = 20;
}

for ( $i = 1; $i <= $total_limit; $i++ ) {
	$recentnews_postnumbers[] = $i;
	// if ( $i % $multiples_of == 0 && $i > 0 ) { $recentnews_postnumbers[] = $i; }
}

// THIS IS THE DIFFERENT FIELDS
$options = array();
  
// General

$options[] = array( "name" => "General Settings",
					"type" => "heading",
					"icon" => "general" );

$options[] = array( 'name' => __( 'Quick Start', 'woothemes' ),
    				'type' => 'subheading' );

$options[] = array( "name" => "Theme Stylesheet",
					"desc" => "Select your themes alternative color scheme.",
					"id" => $shortname."_alt_stylesheet",
					"std" => "default.css",
					"type" => "select",
					"options" => $alt_stylesheets);

$options[] = array( "name" => "Custom Logo",
					"desc" => "Upload a logo for your theme, or specify an image URL directly.",
					"id" => $shortname."_logo",
					"std" => "",
					"type" => "upload" );    
                                                                                     
$options[] = array( "name" => "Text Title",
					"desc" => "Enable text-based Site Title and Tagline. Setup title & tagline in <a href='".home_url()."/wp-admin/options-general.php'>General Settings</a>.",
					"id" => $shortname."_texttitle",
					"std" => "false",
					"class" => "collapsed",
					"type" => "checkbox" );

$options[] = array( "name" => "Site Title",
					"desc" => "Change the site title typography.",
					"id" => $shortname."_font_site_title",
					"std" => array( 'size' => '50','unit' => 'px','face' => 'Droid Serif','style' => 'bold','color' => '#333333'),
					"class" => "hidden",
					"type" => "typography" );  

$options[] = array( "name" => "Site Description",
					"desc" => "Enable the site description/tagline under site title.",
					"id" => $shortname."_tagline",
					"class" => "hidden",
					"std" => "false",
					"type" => "checkbox" );

$options[] = array( "name" => "Site Description",
					"desc" => "Change the site description typography.",
					"id" => $shortname."_font_tagline",
					"std" => array( 'size' => '12','unit' => 'px','face' => 'Droid Sans','style' => '','color' => '#999999'),
					"class" => "hidden last",
					"type" => "typography" );  
					          
$options[] = array( "name" => "Custom Favicon",
					"desc" => "Upload a 16px x 16px <a href='http://www.faviconr.com/'>ico image</a> that will represent your website's favicon.",
					"id" => $shortname."_custom_favicon",
					"std" => "",
					"type" => "upload" ); 
                                               
$options[] = array( "name" => "Tracking Code",
					"desc" => "Paste your Google Analytics (or other) tracking code here. This will be added into the footer template of your theme.",
					"id" => $shortname."_google_analytics",
					"std" => "",
					"type" => "textarea" );        

$options[] = array( 'name' => __( 'Subscription Settings', 'woothemes' ),
    				'type' => 'subheading' );

$options[] = array( "name" => "RSS URL",
					"desc" => "Enter your preferred RSS URL. (Feedburner or other)",
					"id" => $shortname."_feed_url",
					"std" => "",
					"type" => "text" );
                    
$options[] = array( "name" => "E-Mail Subscription URL",
					"desc" => "Enter your preferred E-mail subscription URL. (Feedburner or other)",
					"id" => $shortname."_subscribe_email",
					"std" => "",
					"type" => "text" );

$options[] = array( 'name' => __( 'Display Options', 'woothemes' ),
    				'type' => 'subheading' );

$options[] = array( "name" => "Contact Form E-Mail",
					"desc" => "Enter your E-mail address to use on the Contact Form Page Template. Add the contact form by adding a new page and selecting 'Contact Form' as page template.",
					"id" => $shortname."_contactform_email",
					"std" => "",
					"type" => "text" );

$options[] = array( "name" => "Custom CSS",
                    "desc" => "Quickly add some CSS to your theme by adding it to this block.",
                    "id" => $shortname."_custom_css",
                    "std" => "",
                    "type" => "textarea" );

$options[] = array( "name" => "Post/Page Comments",
					"desc" => "Select if you want to enable/disable comments on posts and/or pages. ",
					"id" => $shortname."_comments",
					"type" => "select2",
					"options" => array( "post" => "Posts Only", "page" => "Pages Only", "both" => "Pages / Posts", "none" => "None") );                                                          
    
$options[] = array( "name" => "Post Content",
					"desc" => "Select if you want to show the full content or the excerpt on posts. ",
					"id" => $shortname."_post_content",
					"type" => "select2",
					"options" => array( "excerpt" => "The Excerpt", "content" => "Full Content" ) );                                                          

$options[] = array( "name" => "Post Author Box",
					"desc" => "This will enable the post author box on the single posts page. Edit description in <a href='".home_url()."/wp-admin/profile.php'>Profile</a>.",
					"id" => $shortname."_post_author",
					"std" => "true",
					"type" => "checkbox" );
					
$options[] = array( "name" => "Display Breadcrumbs",
					"desc" => "Display dynamic breadcrumbs on each page of your website.",
					"id" => $shortname."_breadcrumbs_show",
					"std" => "false",
					"type" => "checkbox" );
				
$options[] = array( "name" => "Pagination Style",
					"desc" => "Select the style of pagination you would like to use on the blog.",
					"id" => $shortname."_pagination_type",
					"type" => "select2",
					"options" => array( "paginated_links" => "Numbers", "simple" => "Next/Previous" ) );
// Styling 

$options[] = array( "name" => "Styling",
					"type" => "heading",
					"icon" => "styling" );   

$options[] = array( 'name' => __( 'Background', 'woothemes' ),
    				'type' => 'subheading' );
					
$options[] = array( "name" =>  "Body Background Color",
					"desc" => "Pick a custom color for background color of the theme e.g. #697e09",
					"id" => "woo_body_color",
					"std" => "",
					"type" => "color" );
					
$options[] = array( "name" => "Body background image",
					"desc" => "Upload an image for the theme's background",
					"id" => $shortname."_body_img",
					"std" => "",
					"type" => "upload" );
					
$options[] = array( "name" => "Background image repeat",
                    "desc" => "Select how you would like to repeat the background-image",
                    "id" => $shortname."_body_repeat",
                    "std" => "no-repeat",
                    "type" => "select",
                    "options" => array( "no-repeat","repeat-x","repeat-y","repeat"));

$options[] = array( "name" => "Background image position",
                    "desc" => "Select how you would like to position the background",
                    "id" => $shortname."_body_pos",
                    "std" => "top",
                    "type" => "select",
                    "options" => array( "top left","top center","top right","center left","center center","center right","bottom left","bottom center","bottom right"));

$options[] = array( 'name' => __( 'Links', 'woothemes' ),
    				'type' => 'subheading' );

$options[] = array( "name" =>  "Link Color",
					"desc" => "Pick a custom color for links or add a hex color code e.g. #697e09",
					"id" => "woo_link_color",
					"std" => "",
					"type" => "color" );   

$options[] = array( "name" =>  "Link Hover Color",
					"desc" => "Pick a custom color for links hover or add a hex color code e.g. #697e09",
					"id" => "woo_link_hover_color",
					"std" => "",
					"type" => "color" );                    

$options[] = array( "name" =>  "Button Color",
					"desc" => "Pick a custom color for buttons or add a hex color code e.g. #697e09",
					"id" => "woo_button_color",
					"std" => "",
					"type" => "color" );          

/* Typography */	
				
$options[] = array( "name" => "Typography",
					"type" => "heading",
					"icon" => "typography" );   

$options[] = array( "name" => "Add a dropcap to each post/page",
					"desc" => "Add a dropcap to the first letter of each post/page when on the single post/page screen.",
					"id" => $shortname."_add_dropcap",
					"std" => "true",
					"type" => "checkbox" );

$options[] = array( "name" => "Enable Custom Typography",
					"desc" => "Enable the use of custom typography for your site. Custom styling will be output in your sites HEAD.",
					"id" => $shortname."_typography",
					"std" => "false",
					"type" => "checkbox" ); 									   

$options[] = array( "name" => "General Typography",
					"desc" => "Change the general font.",
					"id" => $shortname."_font_body",
					"std" => array( 'size' => '12','unit' => 'px','face' => 'Arial','style' => '','color' => '#555555'),
					"type" => "typography" );  

$options[] = array( "name" => "Navigation",
					"desc" => "Change the navigation font.",
					"id" => $shortname."_font_nav",
					"std" => array( 'size' => '14','unit' => 'px','face' => 'Arial','style' => '','color' => '#555555'),
					"type" => "typography" );  

$options[] = array( "name" => "Post Title",
					"desc" => "Change the post title.",
					"id" => $shortname."_font_post_title",
					"std" => array( 'size' => '24','unit' => 'px','face' => 'Arial','style' => 'bold','color' => '#222222'),
					"type" => "typography" );  

$options[] = array( "name" => "Post Meta",
					"desc" => "Change the post meta.",
					"id" => $shortname."_font_post_meta",
					"std" => array( 'size' => '12','unit' => 'px','face' => 'Arial','style' => '','color' => '#999999'),
					"type" => "typography" );  
					          
$options[] = array( "name" => "Post Entry",
					"desc" => "Change the post entry.",
					"id" => $shortname."_font_post_entry",
					"std" => array( 'size' => '14','unit' => 'px','face' => 'Arial','style' => '','color' => '#555555'),
					"type" => "typography" );  

$options[] = array( "name" => "Widget Titles",
					"desc" => "Change the widget titles.",
					"id" => $shortname."_font_widget_titles",
					"std" => array( 'size' => '16','unit' => 'px','face' => 'Arial','style' => 'bold','color' => '#555555'),
					"type" => "typography" );

/* Layout */ 

$options[] = array( "name" => "Layout Options",
					"type" => "heading",
					"icon" => "layout" );   
					
$options[] = array( "name" => "Header &amp; navigation alignment",
					"desc" => "Select how to align your header &amp; navigation",
					"id" => $shortname."_header_align",
					"std" => "aligncenter",
					"type" => "radio",
					"class" => "collapsed", 
					"options" => array( "alignleft" => "Left","aligncenter" => "Center")); 

$options[] = array( "name" => "Left aligned header options",
					"desc" => "These options apply if you are using the left aligned header.",
					"id" => $shortname."_header_left_layout",
					"std" => "headlines",
					"type" => "radio",
					"class" => "hidden", 
					"options" => array( "headlines" => "Display Headline Entries","search-subscribe" => "Display Search & Subscribe"));
					
$options[] = array( "name" => "Left aligned header - Headlines Category",
					"desc" => "Specify the category slug you would like to use for Headline posts",
					"id" => $shortname."_header_left_headlines_tag",
					"std" => "",
					"class" => "hidden", 
					"type" => "text" );
					
$options[] = array( "name" => "Left aligned header - Number of Headlines",
					"desc" => "Please specify the number of Headlines to display in the left aligned header",
					"id" => $shortname."_header_left_headlines_number",
					"std" => "4",
					"type" => "select",
					"class" => "hidden last", 
					"options" => array( "1", "2", "3", "4"));
					 					                   
$url =  get_template_directory_uri() . '/functions/images/';
$options[] = array( "name" => "Main Layout",
					"desc" => "Select which layout you want for your site.",
					"id" => $shortname."_site_layout",
					"std" => "layout-left-content",
					"type" => "images",
					"options" => array(
						'layout-left-content' => $url . '2cl.png',
						'layout-right-content' => $url . '2cr.png')
					);

/* Homepage */

$options[] = array( "name" => "Homepage",
					"icon" => "homepage",
					"type" => "heading");

$options[] = array( "name" => "'Recent News' Total Posts",
					"desc" => "Select how many posts you would like to display in the 'Recent News' section of the homepage.",
					"id" => $shortname."_homepage_recentnews_totalposts",
					"std" => ( $multiples_of * 2 ),
					"type" => "select2",
					"options" => $recentnews_postnumbers );

$options[] = array( "name" => "'Recent News' Categories",
					"desc" => "Specify the IDs of the categories you'd like to display in the 'Recent News' section of the homepage. Please use comma-separated values here (eg: 1,2,3,4). To find a category's ID, please see <a hre='http://www.wprecipes.com/how-to-find-wordpress-category-id'>this tutorial</a>.	",
					"id" => $shortname."_homepage_recentnews_categories",
					"std" => "1",
					"type" => "text");

$options[] = array( "name" => "'Latest' Displays Posts From 'Recent News Categories' Only",
					"desc" => "Determine wether to display posts from all categories or just those specified under 'Recent News Categories' under the 'Latest' tab..",
					"id" => $shortname."_recentnews_specificcats",
					"std" => "false", 
					"type" => "checkbox" );

$options[] = array( "name" => "Enable 'More News' Section",
					"desc" => "Determine wether or not to display the 'More News' section.",
					"id" => $shortname."_morenews_enable",
					"std" => "true", 
					"class" => "collapsed",
					"type" => "checkbox" );
					
$options[] = array( "name" => "'More News' Post Tag(s)",
                    "desc" => "Add a comma-separated list of the tags you would like to use in the 'more news' section of the homepage. For example, if you add 'tag1, tag3' here, then all posts tagged with either 'tag1' or 'tag3' will be shown.<br /><br /><strong>Note:</strong> Leaving this field blank will display your most recent posts.",
                    "id" => $shortname."_homepage_tags",
                    "std" => "",
                    "class" => "hidden",
                    "type" => "text");

$options[] = array( "name" => "'More News' Total Posts",
					"desc" => "Select how many posts you would like to display in the 'More News' section of the homepage.",
					"id" => $shortname."_homepage_morenews_totalposts",
					"std" => 6,
					"type" => "select2",
					"class" => "hidden last",
					"options" => $recentnews_postnumbers );

$options[] = array( "name" => "Display Thumbnails in 'More News' Section",
					"desc" => "Determine wether or not to display an image alongside each post in the 'More News' section.",
					"id" => $shortname."_morenews_display_image",
					"std" => "true", 
					"class" => "collapsed",
					"type" => "checkbox" );    

$options[] = array( "name" => "'More News' Thumbnail Image Dimensions",
					"desc" => "Enter an integer value i.e. 40 for the desired size which will be used when dynamically creating the images in the 'More News' section.",
					"id" => $shortname."_morenews_image_dimensions",
					"std" => "",
					"class" => "hidden",
					"type" => array( 
									array(  'id' => $shortname. '_morenews_thumb_w',
											'type' => 'text',
											'std' => 40,
											'meta' => 'Width'),
									array(  'id' => $shortname. '_morenews_thumb_h',
											'type' => 'text',
											'std' => 40,
											'meta' => 'Height')
								  ));
                                                                                                
$options[] = array( "name" => "'More News' Thumbnail Image alignment",
					"desc" => "Select how to align your thumbnails with posts in the 'More News' section.",
					"id" => $shortname."_morenews_thumb_align",
					"std" => "alignleft",
					"type" => "radio",
					"class" => "hidden last",
					"options" => array( "alignleft" => 'Left',"alignright" => 'Right',"aligncenter" => 'Center', "alignnone" => 'None' )); 

/* Homepage Slider */
					
$options[] = array( "name" => "Homepage Slider",
					"icon" => "slider",
					"type" => "heading");
					
$options[] = array( "name" => "Enable Slider",
                    "desc" => "Enable the slider on the homepage.",
                    "id" => $shortname."_slider",
                    "std" => "false",
                    "type" => "checkbox");
                                            
$options[] = array( "name" => "Slider Category",
                    "desc" => "Enter the slug name of the category you would like to use to feature content on the front slider",
                    "id" => $shortname."_slider_tags",
                    "std" => "",
                    "type" => "text");

$options[] = array(    "name" => "Slider Entries",
                    "desc" => "Select the number of entries that should appear in the home page slider.",
                    "id" => $shortname."_slider_entries",
                    "std" => "3",
                    "type" => "select",
                    "options" => $other_entries);
                    
$options[] = array(    "name" => "Exclude from Latest News",
                    "desc" => "Exclude the slider posts from the latest news section.",
                    "id" => $shortname."_slider_exclude",
                    "std" => "true",
                    "type" => "checkbox");   

$options[] = array( "name" => "Slider Effect",
					"desc" => "Select the slider effect you'd like to use for the homepage featured slider.",
					"id" => $shortname."_slider_effect",
					"std" => "slide",
					"type" => "select2",
					"class" => "",
					"options" => array( "slide" => 'Slide',"fade" => 'Fade' )); 

$options[] = array(    "name" => "Animation Speed",
                    "desc" => "The time in <b>seconds</b> the animation between frames will take.",
                    "id" => $shortname."_slider_speed",
                    "std" => "0.6",
					"type" => "select",
					"options" => array( '0.0', '0.1', '0.2', '0.3', '0.4', '0.5', '0.6', '0.7', '0.8', '0.9', '1.0', '1.1', '1.2', '1.3', '1.4', '1.5', '1.6', '1.7', '1.8', '1.9', '2.0' ) );

$options[] = array(    "name" => "Auto Start",
                    "desc" => "Set the slider to start sliding automatically.",
                    "id" => $shortname."_slider_auto",
                    "std" => "false",
                    "type" => "checkbox");   
                    
$options[] = array(    "name" => "Auto Slide Interval",
                    "desc" => "The time in <b>seconds</b> each slide pauses for, before sliding to the next.",
                    "id" => $shortname."_slider_interval",
					"std" => "4",
					"type" => "select",
					"options" => array( '1', '2', '3', '4', '5', '6', '7', '8', '9', '10' ) );

$options[] = array( "name" => "Enable Slider AutoHeight",
                    "desc" => "Enable autoHeight functionality the slider on the homepage.",
                    "id" => $shortname."_slider_autoheight",
                    "std" => "true",
                    "type" => "checkbox");

$options[] = array(    "name" => "Slider Height",
                    "desc" => "Set the height of the slider image in pixels e.g 290 (works only if autoHeight is off).",
                    "id" => $shortname."_slider_height",
                    "std" => "290",
                    "type" => "text");	   

/* Dynamic Images */
$options[] = array( "name" => "Dynamic Images",
					"type" => "heading",
					"icon" => "image" );    
				    				   
$options[] = array( "name" => "WP Post Thumbnail",
					"desc" => "Use WordPress post thumbnail to assign a post thumbnail.",
					"id" => $shortname."_post_image_support",
					"std" => "true",
					"class" => "collapsed",
					"type" => "checkbox" ); 

$options[] = array( "name" => "WP Post Thumbnail - Dynamically Resize",
					"desc" => "The post thumbnail will be dynamically resized using native WP resize functionality. <em>(Requires PHP 5.2+)</em>",
					"id" => $shortname."_pis_resize",
					"std" => "true",
					"class" => "hidden",
					"type" => "checkbox" ); 									   
					
$options[] = array( "name" => "WP Post Thumbnail - Hard Crop",
					"desc" => "The image will be cropped to match the target aspect ratio.",
					"id" => $shortname."_pis_hard_crop",
					"std" => "true",
					"class" => "hidden last",
					"type" => "checkbox" ); 									   

$options[] = array( "name" => "Enable Dynamic Image Resizer",
					"desc" => "This will enable the thumb.php script which dynamically resizes images added through post custom field.",
					"id" => $shortname."_resize",
					"std" => "true",
					"type" => "checkbox" );    
                    
$options[] = array( "name" => "Automatic Image Thumbs",
					"desc" => "If no image is specified in the 'image' custom field or WP post thumbnail then the first uploaded post image is used.",
					"id" => $shortname."_auto_img",
					"std" => "false",
					"type" => "checkbox" );    

$options[] = array( "name" => "Thumbnail Image Dimensions",
					"desc" => "Enter an integer value i.e. 250 for the desired size which will be used when dynamically creating the images.",
					"id" => $shortname."_image_dimensions",
					"std" => "",
					"type" => array( 
									array(  'id' => $shortname. '_thumb_w',
											'type' => 'text',
											'std' => 291,
											'meta' => 'Width'),
									array(  'id' => $shortname. '_thumb_h',
											'type' => 'text',
											'std' => 120,
											'meta' => 'Height')
								  ));
                                                                                                
$options[] = array( "name" => "Thumbnail Image alignment",
					"desc" => "Select how to align your thumbnails with posts.",
					"id" => $shortname."_thumb_align",
					"std" => "alignleft",
					"type" => "radio",
					"options" => array( "alignleft" => "Left","alignright" => "Right","aligncenter" => "Center")); 
/*
$options[] = array( "name" => "Show thumbnail in Single Posts",
					"desc" => "Show the attached image in the single post page.",
					"id" => $shortname."_thumb_single",
					"class" => "collapsed",
					"std" => "false",
					"type" => "checkbox" );    

$options[] = array( "name" => "Single Image Dimensions",
					"desc" => "Enter an integer value i.e. 250 for the image size. Max width is 576.",
					"id" => $shortname."_image_dimensions",
					"std" => "",
					"class" => "hidden last",
					"type" => array( 
									array(  'id' => $shortname. '_single_w',
											'type' => 'text',
											'std' => 200,
											'meta' => 'Width'),
									array(  'id' => $shortname. '_single_h',
											'type' => 'text',
											'std' => 200,
											'meta' => 'Height')
								  ));

$options[] = array( "name" => "Single Post Image alignment",
					"desc" => "Select how to align your thumbnail with single posts.",
					"id" => $shortname."_thumb_single_align",
					"std" => "alignright",
					"type" => "radio",
					"class" => "hidden",
					"options" => array( "alignleft" => "Left","alignright" => "Right","aligncenter" => "Center")); 
*/
$options[] = array( "name" => "Add thumbnail to RSS feed",
					"desc" => "Add the the image uploaded via your Custom Settings to your RSS feed",
					"id" => $shortname."_rss_thumb",
					"std" => "false",
					"type" => "checkbox" );  
					
/* Footer */
$options[] = array( "name" => "Footer Customization",
					"type" => "heading",
					"icon" => "footer" );    
					

$url =  get_template_directory_uri() . '/functions/images/';
$options[] = array( "name" => "Footer Widget Areas",
					"desc" => "Select how many footer widget areas you want to display.",
					"id" => $shortname."_footer_sidebars",
					"std" => "4",
					"type" => "images",
					"options" => array(
						'0' => $url . 'layout-off.png',
						'1' => $url . 'footer-widgets-1.png',
						'2' => $url . 'footer-widgets-2.png',
						'3' => $url . 'footer-widgets-3.png',
						'4' => $url . 'footer-widgets-4.png')
					); 		   
										
$options[] = array( "name" => "Custom Affiliate Link",
					"desc" => "Add an affiliate link to the WooThemes logo in the footer of the theme.",
					"id" => $shortname."_footer_aff_link",
					"std" => "",
					"type" => "text" );	
									
$options[] = array( "name" => "Enable Custom Footer (Left)",
					"desc" => "Activate to add the custom text below to the theme footer.",
					"id" => $shortname."_footer_left",
					"std" => "false",
					"type" => "checkbox" );    

$options[] = array( "name" => "Custom Text (Left)",
					"desc" => "Custom HTML and Text that will appear in the footer of your theme.",
					"id" => $shortname."_footer_left_text",
					"std" => "",
					"type" => "textarea" );
						
$options[] = array( "name" => "Enable Custom Footer (Right)",
					"desc" => "Activate to add the custom text below to the theme footer.",
					"id" => $shortname."_footer_right",
					"std" => "false",
					"type" => "checkbox" );    

$options[] = array( "name" => "Custom Text (Right)",
					"desc" => "Custom HTML and Text that will appear in the footer of your theme.",
					"id" => $shortname."_footer_right_text",
					"std" => "",
					"type" => "textarea" );

/* Subscribe & Connect */
$options[] = array( "name" => "Subscribe & Connect",
					"type" => "heading",
					"icon" => "connect" ); 

$options[] = array( "name" => "Enable Subscribe & Connect - Single Post",
					"desc" => "Enable the subscribe & connect area on single posts. You can also add this as a <a href='".home_url()."/wp-admin/widgets.php'>widget</a> in your sidebar.",
					"id" => $shortname."_connect",
					"std" => 'false',
					"type" => "checkbox" ); 

$options[] = array( "name" => "Subscribe Title",
					"desc" => "Enter the title to show in your subscribe & connect area.",
					"id" => $shortname."_connect_title",
					"std" => '',
					"type" => "text" ); 

$options[] = array( "name" => "Text",
					"desc" => "Change the default text in this area.",
					"id" => $shortname."_connect_content",
					"std" => '',
					"type" => "textarea" ); 

$options[] = array( "name" => "Subscribe By E-mail ID (Feedburner)",
					"desc" => "Enter your <a href='http://www.woothemes.com/tutorials/how-to-find-your-feedburner-id-for-email-subscription/'>Feedburner ID</a> for the e-mail subscription form.",
					"id" => $shortname."_connect_newsletter_id",
					"std" => '',
					"type" => "text" );

$options[] = array( "name" => 'Subscribe By E-mail to MailChimp', 'woothemes',
					"desc" => 'If you have a MailChimp account you can enter the <a href="http://woochimp.heroku.com" target="_blank">MailChimp List Subscribe URL</a> to allow your users to subscribe to a MailChimp List.',
					"id" => $shortname."_connect_mailchimp_list_url",
					"std" => '',
					"type" => "text");

$options[] = array( "name" => "Enable RSS",
					"desc" => "Enable the subscribe and RSS icon.",
					"id" => $shortname."_connect_rss",
					"std" => 'true',
					"type" => "checkbox" ); 

$options[] = array( "name" => "Twitter URL",
					"desc" => "Enter your  <a href='http://www.twitter.com/'>Twitter</a> URL e.g. http://www.twitter.com/woothemes",
					"id" => $shortname."_connect_twitter",
					"std" => '',
					"type" => "text" ); 

$options[] = array( "name" => "Facebook URL",
					"desc" => "Enter your  <a href='http://www.facebook.com/'>Facebook</a> URL e.g. http://www.facebook.com/woothemes",
					"id" => $shortname."_connect_facebook",
					"std" => '',
					"type" => "text" ); 
					
$options[] = array( "name" => "YouTube URL",
					"desc" => "Enter your  <a href='http://www.youtube.com/'>YouTube</a> URL e.g. http://www.youtube.com/woothemes",
					"id" => $shortname."_connect_youtube",
					"std" => '',
					"type" => "text" ); 

$options[] = array( "name" => "Flickr URL",
					"desc" => "Enter your  <a href='http://www.flickr.com/'>Flickr</a> URL e.g. http://www.flickr.com/woothemes",
					"id" => $shortname."_connect_flickr",
					"std" => '',
					"type" => "text" ); 

$options[] = array( "name" => "LinkedIn URL",
					"desc" => "Enter your  <a href='http://www.www.linkedin.com.com/'>LinkedIn</a> URL e.g. http://www.linkedin.com/woothemes",
					"id" => $shortname."_connect_linkedin",
					"std" => '',
					"type" => "text" ); 

$options[] = array( "name" => "Delicious URL",
					"desc" => "Enter your <a href='http://www.delicious.com/'>Delicious</a> URL e.g. http://www.delicious.com/woothemes",
					"id" => $shortname."_connect_delicious",
					"std" => '',
					"type" => "text" ); 

$options[] = array( "name" => "Google+ URL",
					"desc" => "Enter your <a href='http://plus.google.com/'>Google+</a> URL e.g. https://plus.google.com/104560124403688998123/",
					"id" => $shortname."_connect_googleplus",
					"std" => '',
					"type" => "text" );

$options[] = array( "name" => "Enable Related Posts",
					"desc" => "Enable related posts in the subscribe area. Uses posts with the same <strong>tags</strong> to find related posts. Note: Will not show in the Subscribe widget.",
					"id" => $shortname."_connect_related",
					"std" => 'true',
					"type" => "checkbox" ); 
							
/* Advertising */
$options[] = array( 'name' => __( 'Advertising', 'woothemes' ),
					'icon' => 'ads', 
    				'type' => 'heading' );

$options[] = array( "name" => "Single Post (468x60px)",
					"type" => "subheading",
					"icon" => "ads" );    

$options[] = array( "name" => "Enable Ad",
					"desc" => "Enable the ad space",
					"id" => $shortname."_ad_single",
					"std" => "false",
					"type" => "checkbox" );    

$options[] = array( "name" => "Adsense code",
					"desc" => "Enter your adsense code (or other ad network code) here.",
					"id" => $shortname."_ad_single_adsense",
					"std" => "",
					"type" => "textarea" );

$options[] = array( "name" => "Image Location",
					"desc" => "Enter the URL to the banner ad image location.",
					"id" => $shortname."_ad_single_image",
					"std" => "http://www.woothemes.com/ads/468x60b.jpg",
					"type" => "upload" );
					
$options[] = array( "name" => "Destination URL",
					"desc" => "Enter the URL where this banner ad points to.",
					"id" => $shortname."_ad_single_url",
					"std" => "http://www.woothemes.com",
					"type" => "text" );  
					
//Advertising
$options[] = array(	"name" => "Widget (125x125)",
					"icon" => "ads",
					"type" => "subheading");

$options[] = array(	"name" => "Rotate banners?",
					"desc" => "Check this to randomly rotate the banner ads.",
					"id" => $shortname."_ads_rotate",
					"std" => "true",
					"type" => "checkbox");	

$options[] = array(	"name" => "Banner Ad #1 - Image Location",
					"desc" => "Enter the URL for this banner ad.",
					"id" => $shortname."_ad_image_1",
					"std" => "http://www.woothemes.com/ads/125x125b.jpg",
					"type" => "text");
						
$options[] = array(	"name" => "Banner Ad #1 - Destination",
					"desc" => "Enter the URL where this banner ad points to.",
					"id" => $shortname."_ad_url_1",
					"std" => "http://www.woothemes.com",
					"type" => "text");						

$options[] = array(	"name" => "Banner Ad #2 - Image Location",
					"desc" => "Enter the URL for this banner ad.",
					"id" => $shortname."_ad_image_2",
					"std" => "http://www.woothemes.com/ads/125x125b.jpg",
					"type" => "text");
						
$options[] = array(	"name" => "Banner Ad #2 - Destination",
					"desc" => "Enter the URL where this banner ad points to.",
					"id" => $shortname."_ad_url_2",
					"std" => "http://www.woothemes.com",
					"type" => "text");

$options[] = array(	"name" => "Banner Ad #3 - Image Location",
					"desc" => "Enter the URL for this banner ad.",
					"id" => $shortname."_ad_image_3",
					"std" => "http://www.woothemes.com/ads/125x125b.jpg",
					"type" => "text");
						
$options[] = array(	"name" => "Banner Ad #3 - Destination",
					"desc" => "Enter the URL where this banner ad points to.",
					"id" => $shortname."_ad_url_3",
					"std" => "http://www.woothemes.com",
					"type" => "text");

$options[] = array(	"name" => "Banner Ad #4 - Image Location",
					"desc" => "Enter the URL for this banner ad.",
					"id" => $shortname."_ad_image_4",
					"std" => "http://www.woothemes.com/ads/125x125b.jpg",
					"type" => "text");
						
$options[] = array(	"name" => "Banner Ad #4 - Destination",
					"desc" => "Enter the URL where this banner ad points to.",
					"id" => $shortname."_ad_url_4",
					"std" => "http://www.woothemes.com",
					"type" => "text");

$options[] = array(	"name" => "Banner Ad #5 - Image Location",
					"desc" => "Enter the URL for this banner ad.",
					"id" => $shortname."_ad_image_5",
					"std" => "http://www.woothemes.com/ads/125x125b.jpg",
					"type" => "text");
						
$options[] = array(	"name" => "Banner Ad #5 - Destination",
					"desc" => "Enter the URL where this banner ad points to.",
					"id" => $shortname."_ad_url_5",
					"std" => "http://www.woothemes.com",
					"type" => "text");

$options[] = array(	"name" => "Banner Ad #6 - Image Location",
					"desc" => "Enter the URL for this banner ad.",
					"id" => $shortname."_ad_image_6",
					"std" => "http://www.woothemes.com/ads/125x125b.jpg",
					"type" => "text");
						
$options[] = array(	"name" => "Banner Ad #6 - Destination",
					"desc" => "Enter the URL where this banner ad points to.",
					"id" => $shortname."_ad_url_6",
					"std" => "http://www.woothemes.com",
					"type" => "text");
					                      
                                              
// Add extra options through function
if ( function_exists( "woo_options_add") )
	$options = woo_options_add($options);

if ( get_option( 'woo_template') != $options) update_option( 'woo_template',$options);      
if ( get_option( 'woo_themename') != $themename) update_option( 'woo_themename',$themename);   
if ( get_option( 'woo_shortname') != $shortname) update_option( 'woo_shortname',$shortname);
if ( get_option( 'woo_manual') != $manualurl) update_option( 'woo_manual',$manualurl);

// Woo Metabox Options
// Start name with underscore to hide custom key from the user
$woo_metaboxes = array();

global $post;

// Determine numbers for slide content positioning.
$slide_width_start = 0;
$slide_width_end = 680;

$slide_height_start = 0;
$slide_height_end = 400;

$slide_width_numbers = array();
$slide_height_numbers = array();

for ( $i = $slide_width_start; $i <= $slide_width_end; $i++ ) {
	if ( ( $i % 10 == 0 ) && ( $i != $slide_width_start ) && ( $i <= $slide_width_end ) ) {
		$slide_width_numbers[] = $i;
	}
}

for ( $i = $slide_height_start; $i <= $slide_height_end; $i++ ) {
	if ( ( $i % 10 == 0 ) && ( $i != $slide_height_start ) && ( $i <= $slide_height_end ) ) {
		$slide_height_numbers[] = $i;
	}
}

if ( ( get_post_type() == 'post') || ( !get_post_type() ) ) {

	/* General Options */
	$woo_metaboxes[] = array (	"name" => "general_heading",
								"label" => "General Options",
								"type" => "info",
								"desc" => "" );

	$woo_metaboxes[] = array (	"name" => "image",
								"label" => "Image",
								"type" => "upload",
								"desc" => "Upload an image or enter an URL." );
	
	if ( get_option( 'woo_resize') == "true" ) {						
		$woo_metaboxes[] = array (	"name" => "_image_alignment",
									"std" => "Center",
									"label" => "Image Crop Alignment",
									"type" => "select2",
									"desc" => "Select crop alignment for resized image",
									"options" => array(	"c" => "Center",
														"t" => "Top",
														"b" => "Bottom",
														"l" => "Left",
														"r" => "Right"));
	}
	
	$woo_metaboxes[] = array (  "name"  => "embed",
					            "std"  => "",
					            "label" => "Embed Code",
					            "type" => "textarea",
					            "desc" => "Enter the video embed code for your video (YouTube, Vimeo or similar)" );
	
	/* Slide Options */
	$woo_metaboxes[] = array (	"name" => "slide_heading",
								"label" => "Slide Options",
								"type" => "info",
								"desc" => "" );
	
	$woo_metaboxes[] = array( "label" => "When to use these options",
						"type" => "info",
						"desc" => "If this post is to be included in the homepage slider, use these settings to customise it." );
			            
	$woo_metaboxes[] = array (	"name" => "_slide_content",
								"std" => "image",
								"label" => "Slide Content",
								"type" => "select2",
								"desc" => "Select whether to display the embedded video or the post image in the homepage slider.",
								"class" => "",
								"options" => array( 'image' => 'Image', 'video' => 'Video' ));
	
	$woo_metaboxes[] = array (	"name" => "_slide_excerpt_position_top",
								"std" => 40,
								"label" => "Slide Excerpt Position - Top",
								"type" => "select",
								"desc" => "Select the position from the top to place the slide excerpt at.",
								"class" => "",
								"options" => $slide_height_numbers );
								
	$woo_metaboxes[] = array (	"name" => "_slide_excerpt_position_left",
								"std" => 640,
								"label" => "Slide Excerpt Position - Left",
								"type" => "select",
								"desc" => "Select the position from the left to place the slide excerpt at.",
								"class" => "",
								"options" => $slide_width_numbers );
	
	/* Gallery Options */
	$woo_metaboxes[] = array (	"name" => "gallery_heading",
								"label" => "Gallery Options",
								"type" => "info",
								"desc" => "" );
	
	$woo_metaboxes[] = array ( 	"name" => "_enable_gallery",
								"label" => "Enable Gallery/Featured Image/Embedded Video", 
								"desc" => "Enable the gallery, featured image or video for this entry.",
								"std" => "true",
								"class" => "collapsed",
								"type" => "checkbox" );
							
	$woo_metaboxes[] = array ( 	"name" => "_enable_video",
								"label" => "Display embedded video instead of images", 
								"desc" => "Display embed code instead of post gallery/image, if one exists.",
								"std" => "false",
								"class" => "",
								"type" => "checkbox" );
								
	$woo_metaboxes[] = array (	"name" => "_gallery_position",
								"std" => "below",
								"label" => "Gallery Position",
								"type" => "select2",
								"desc" => "Select whether to display the post image/gallery above or below the post title.",
								"class" => "hidden last",
								"options" => array( 'above' => 'Above the Title', 'below' => 'Below the Title' ));
					            
} // End post

/* Column Options */
	$woo_metaboxes[] = array (	"name" => "column_heading",
								"label" => "Column Options",
								"type" => "info",
								"desc" => "" );

	$woo_metaboxes[] = array (	"name" => "_column_layout",
								"std" => "normal",
								"label" => "Column Layout Style",
								"type" => "images",
								"class" => "collapse",
								"desc" => "Select the post content column layout style you want on this specific post/page.",
								"options" => array(
											'layout-std-full' => get_template_directory_uri() . '/images/' . 'ico-layout-std-full.png',
											'layout-std' => get_template_directory_uri() . '/images/' . 'ico-layout-std.png',
											'layout-3col' => get_template_directory_uri() . '/images/' . 'ico-layout-3col.png', 
											'layout-2colA' => get_template_directory_uri() . '/images/' . 'ico-layout-2colA.png',
											'layout-2colB' => get_template_directory_uri() . '/images/' . 'ico-layout-2colB.png',
											'layout-2colC' => get_template_directory_uri() . '/images/' . 'ico-layout-2colC.png'
											));
	
	$woo_metaboxes[] = array( "label" => "A Note on Column Layout Selection",
						"type" => "info",
						"desc" => "When selecting a column layout option, please be sure to add only the appropriate number of column breaks into the content editor above.<br />For example, if you have selected a two-column layout, only a single column break is required to produce two columns.<br />If you have chosen a three-column layout, two column breaks are required." );
	
	$woo_metaboxes[] = array (	"name" => "_title_position",
								"std" => "span1",
								"label" => "Title Position",
								"type" => "select2",
								"desc" => "Select the position of the title, according to the column layout selected above.",
								"class" => "hidden last",
								"options" => array( 'span1' => 'Spanning only the first column', 'span2' => 'Two Thirds (applies only to the three-column layout)', 'span3' => 'Full Width (for either two or three column layouts)' ));

// Add extra metaboxes through function
if ( function_exists( "woo_metaboxes_add") )
	$woo_metaboxes = woo_metaboxes_add($woo_metaboxes);
    
if ( get_option( 'woo_custom_template' ) != $woo_metaboxes) update_option( 'woo_custom_template', $woo_metaboxes );      

} // END woo_options()
} // END function_exists()

// Add options to admin_head
add_action( 'admin_head','woo_options' );  

//Enable WooSEO on these Post types
$seo_post_types = array( 'post','page' );
define( "SEOPOSTTYPES", serialize($seo_post_types));

//Global options setup
add_action( 'init','woo_global_options' );
function woo_global_options(){
	// Populate WooThemes option in array for use in theme
	global $woo_options;
	$woo_options = get_option( 'woo_options' );
}

?>