<?php

/*-------------------------------------------------------------------------------------

TABLE OF CONTENTS

- Generate dynamic slide excerpt position CSS for each homepage slide.
- Add post image/gallery above or below the post title.
- Dynamic post intro/excerpt text.
- Use single-columns.php if a non-standard column layout option is selected
- Get post/page gallery dimensions, depending on layout option selected
- Save Twitter and Facebook links from comments
- Auto-add a dropcap to each post/page on the single post/page view
- Custom excerpt length for the "More News" section of the homepage
- Add post/page-specific content classes via a filter on woo_content_class
- woo_section_class()
- Register WP Menus
- Page navigation
- Post Meta
- Subscribe & Connect

-------------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Generate dynamic slide excerpt position CSS for each homepage slide. */
/*-----------------------------------------------------------------------------------*/
	
	add_action( 'template_redirect', 'woo_enqueue_dynamic_slides_css' );
	add_action( 'template_redirect', 'woo_load_dynamic_slides_css' );

	function woo_enqueue_dynamic_slides_css () {
	
		$url = home_url();
		$sep = '?';
		if ( ! get_option( 'permalink_structure' ) ) { $sep = '&'; }
		
		if ( is_singular() ) {
			global $post;
			
			$url = get_permalink( $post->ID );
		}
	
		wp_register_style( 'woo-slides-dynamic', trailingslashit( $url ) . $sep . 'woo-slides-css=load' );
		
		if ( is_home() ) { wp_enqueue_style( 'woo-slides-dynamic' ); }
	
	} // End woo_enqueue_dynamic_slides_css()
	
	function woo_load_dynamic_slides_css () {
	
		if ( isset( $_GET['woo-slides-css'] ) && $_GET['woo-slides-css'] == 'load' ) {
			
			header( 'Content-Type: text/css' );
			
			global $woo_options, $wp_query, $post;
			
			// Default variables.
			$default_left = 640;
			$default_top = 40;
			
			// Begin compiling CSS string.
			$css = '';
			$css .= '/* Begin Dynamic Homepage Slides CSS */' . "\n";

			$woo_slider_tags = $woo_options['woo_slider_tags'];
			
			if ( ($woo_slider_tags != '') && (isset($woo_slider_tags)) ) {
	    	
		    	unset($tag_array); 
				$slide_tags_array = explode( ',', $woo_options['woo_slider_tags'] ); // Tags to be shown
		        foreach ( $slide_tags_array as $tags ) { 
					$tag = get_term_by( 'name', trim( $tags ), 'post_tag', 'ARRAY_A' );
					if ( $tag['term_id'] > 0 )
						$tag_array[] = $tag['term_id'];
				}
			
			}
			if ( ! empty( $tag_array ) ) {
			
				$saved = $wp_query; query_posts( array( 'tag__in' => $tag_array, 'showposts' => $woo_options['woo_slider_entries'] ) );
				
				if ( have_posts() ) { $count = 0;
					while ( have_posts() ) { the_post();
						
						$post_meta = get_post_custom( $post->ID );
						
						$top = 0;
						$left = 0;
						$has_dims = false;
						
						if ( isset( $post_meta['_slide_excerpt_position_top'] ) && ( $post_meta['_slide_excerpt_position_top'] != $default_top ) ) {
							$top = $post_meta['_slide_excerpt_position_top'][0];
							$has_dims = true;
						}
						
						if ( isset( $post_meta['_slide_excerpt_position_left'] ) && ( $post_meta['_slide_excerpt_position_left'] != $default_left ) ) {
							$left = $post_meta['_slide_excerpt_position_left'][0];
							$has_dims = true;
						}
						
						if ( $has_dims == true ) {
							$css .= '#slides .slide-id-' . $post->ID . ' .slide-content { ';
								if ( $top > 0 ) { $css .= 'top: ' . $top . 'px; '; }
								if ( $left > 0 ) { $css .= 'left: ' . $left . 'px; '; }
							$css .= '}' . "\n";
						}
					}
				}
			
			} // End IF Statement
			
			echo $css;
			
			die();

		}
	
	} // End woo_load_dynamic_slides_css()

/*-----------------------------------------------------------------------------------*/
/* Add post image/gallery above or below the post title. */
/*-----------------------------------------------------------------------------------*/

	add_action( 'wp_head', 'woo_determine_gallery_position' );

	function woo_determine_gallery_position () {
	
		if ( is_singular() ) {
	
			global $post;
			
			$gallery_position = 'below';
			$enable_gallery = 'true';
			
			// Get the metadata for the current post.
			$post_meta = get_post_custom( $post->ID );
		
			if ( isset( $post_meta['_gallery_position'] ) && in_array( $post_meta['_gallery_position'][0], array( 'above', 'below' ) ) ) {
				$gallery_position = $post_meta['_gallery_position'][0];
			}
			
			if ( isset( $post_meta['_enable_gallery'] ) && in_array( $post_meta['_enable_gallery'][0], array( 'true', 'false' ) ) ) {
				$enable_gallery = $post_meta['_enable_gallery'][0];
			}
			
			if ( $enable_gallery == 'true' ) {
				
				if ( $gallery_position == 'above' ) {
					add_action( 'woo_post_title_before', 'woo_position_post_gallery' );
				} else {
					add_action( 'woo_post_title_after', 'woo_position_post_gallery' );
				}
			
			}
		
		}
	
	} // End woo_determine_gallery_position()

	function woo_position_post_gallery () {
		
		global $post;
	
		get_template_part( 'includes/gallery' );
	
	} // End woo_position_post_gallery()

/*-----------------------------------------------------------------------------------*/
/* Dynamic post intro/excerpt text. */
/*-----------------------------------------------------------------------------------*/

function woo_post_intro_text ( $post_id = 0 ) {

	global $post;
	
	if ( $post_id == 0 && isset( $post->ID ) ) { $post_id = $post->ID; }
	if ( $post_id == 0 ) { return; }

	$intro_text = '';

	// Determine whether or not to display the intro text.
	if ( has_excerpt( $post_id ) ) {
		$intro_text = '<div class="intro-paragraph">' . "\n";
			$intro_text .= get_the_excerpt( $post_id );
		$intro_text .= '</div><!--/.intro-paragraph-->' . "\n";
		
		return $intro_text;
	}

} // End woo_post_intro_text()

/*-----------------------------------------------------------------------------------*/
/* Use single-columns.php if a non-standard column layout option is selected */
/*-----------------------------------------------------------------------------------*/

	add_action( 'template_redirect', 'woo_columns_template_redirect' );

	function woo_columns_template_redirect () {
		
		global $post;
		
		if ( is_singular() && isset( $post->ID ) ) {
		
			$layout = 'layout-std';
			
			$custom_meta = get_post_custom( $post->ID );
	
			if ( array_key_exists( '_column_layout', (array) $custom_meta ) ) {
				$layout = $custom_meta['_column_layout'][0];
			}
			
			if ( $layout != 'layout-std' && $layout != '' ) {
				locate_template( array( 'single-columns.php', 'single.php', 'index.php' ), true );
				
				exit;
			}
		
		}
		
	} // End woo_columns_template_redirect()

/*-----------------------------------------------------------------------------------*/
/* Get post/page gallery dimensions, depending on layout option selected */
/*-----------------------------------------------------------------------------------*/

function woo_post_gallery_dimensions ( $post_id = 0 ) {

	global $post;
	
	if ( $post_id == 0 && isset( $post->ID ) ) { $post_id = $post->ID; }
	if ( $post_id == 0 ) { return; }
	
	$layout = 'layout-std';
	$title_position = 'span1';
	$dimensions = array( 'width' => 606, 'height' => 0 );
	
	// Setup an array of the different layout and title combinations we can expect.
	$combinations = array(
							'large' => array( 
												'layout-2colC|span1'
											), 
						 	'medium' => array(
						 						'layout-3col|span2', 
						 						'layout-2colB|span1'
						 					), 
						 	'small' => array(
						 						'layout-3col|span1', 
						 						'layout-2colA|span1'
						 					), 
						 	'standard' => array(
						 						'layout-std|span1', 
						 						'layout-std|span2'
						 					), 
						 	'full' => array(
						 						'layout-std-full|span1', 
						 						'layout-std-full|span2', 
						 						'layout-std-full|span3', 
						 						'layout-3col|span3', 
						 						'layout-2colA|span3', 
						 						'layout-2colB|span3', 
						 						'layout-2colC|span3'
						 					)
						);
						
	// Dimensions for the various sizes.
	$size_values = array(
							'large' => array( 'width' => 446, 'height' => 0 ), 
							'medium' => array( 'width' => 606, 'height' => 0 ), 
							'small' => array( 'width' => 282, 'height' => 0 ), 
							'standard' => array( 'width' => 606, 'height' => 0 ), 
							'full' => array( 'width' => 940, 'height' => 0 )
						);
	
	$custom_meta = get_post_custom( $post_id );
	
	if ( array_key_exists( '_column_layout', (array) $custom_meta ) ) {
		$layout = $custom_meta['_column_layout'][0];
	}
	
	if ( array_key_exists( '_title_position', (array) $custom_meta ) ) {
		$title_position = $custom_meta['_title_position'][0];
	}
	
	// Determine which dimensions to return.
	$combo_used = $layout . '|' . $title_position;
	
	$has_combo = false;
	foreach ( $combinations as $k => $v ) {
		if ( in_array( $combo_used, $v ) ) {
			$dimensions = $size_values[$k];
			break;
			$has_combo = true;
		}
	}

	return $dimensions;

} // End woo_post_gallery_dimensions()

/*-----------------------------------------------------------------------------------*/
/* Save Twitter and Facebook links from comments */
/*-----------------------------------------------------------------------------------*/

function woo_save_comment_meta ( $post_id ) {

	$twitter_username = $_POST['twitter'];
	$facebook_url = $_POST['facebook'];
	
	if ( $twitter_username ) {
	
		$twitter_username = sanitize_user( $twitter_username, true );
		$twitter_username = str_replace( ' ', '', $twitter_username );
		$twitter_username = str_replace( '.', '', $twitter_username );
	
	} // End IF Statement
	
	if ( $facebook_url ) {
	
		$facebook_url = esc_url( $facebook_url );
	
	} // End IF Statement
	
	if ( $twitter_username ) { add_comment_meta( $post_id, '_twitter', $twitter_username, true ); } // End IF Statement
	if ( $facebook_url ) { add_comment_meta( $post_id, '_facebook', $facebook_url, true ); } // End IF Statement

} // End matty_store_twitter_username()

add_action( 'comment_post', 'woo_save_comment_meta', 1 );

/*-----------------------------------------------------------------------------------*/
/* Auto-add a dropcap to each post/page on the single post/page view */
/*-----------------------------------------------------------------------------------*/

	add_filter( 'the_content', 'woo_auto_add_dropcap', 0 );

	function woo_auto_add_dropcap ( $content ) {
	
		global $woo_options;
	
		if ( is_singular() ) {
		
			$add_dropcap = 'true';
		
			if ( is_array( $woo_options ) && isset( $woo_options['woo_add_dropcap'] ) ) {
				$add_dropcap = $woo_options['woo_add_dropcap'];
			}
	
			if ( ( $add_dropcap == 'true' ) && strlen( $content ) > 0 && ctype_alpha( substr( $content, 0, 1 ) ) ) {
				
				$first_letter = substr( $content, 0, 1 );
				$remaining_content = substr( $content, 1, strlen( $content ) - 1 );
				
				$content = '[dropcap]' . $first_letter . '[/dropcap] ' . $remaining_content;
				
				$content = do_shortcode( $content );
				
			}
		
		}
		
		return $content;
	
	} // End woo_auto_add_dropcap()

/*-----------------------------------------------------------------------------------*/
/* Custom excerpt length for the "More News" section of the homepage */
/*-----------------------------------------------------------------------------------*/

	function woo_more_news_excerpt_length ( $length ) {
		return 15; // Measured in words, not characters.
	} // End woo_more_news_excerpt_length()

/*-----------------------------------------------------------------------------------*/
/* Add post/page-specific content classes via a filter on woo_content_class */
/*-----------------------------------------------------------------------------------*/

	add_filter( 'woo_section_class', 'woo_add_post_layout_classes', 0, 2 );
	
	function woo_add_post_layout_classes ( $content, $base_class ) {
		
		global $post;
		
		if ( is_singular() ) {
		
			$custom_meta = get_post_custom( $post->ID );
			
			// Add the content class, if working with the content wrapper.
			if ( $base_class == 'content' ) {
			
				if ( array_key_exists( '_column_layout', (array) $custom_meta ) ) {
					$content .= ' ' . $custom_meta['_column_layout'][0];
				} else {
					$content .= ' layout-std';
				}
				
			}
		
			
			// Add the title class, if working with the title.
			if ( $base_class == 'title-media-block') {
				
				if ( array_key_exists( '_title_position', (array) $custom_meta ) ) {
					$content .= ' ' . $custom_meta['_title_position'][0];
				}
				
			}
		
		}
		
		return $content;
		
	} // End woo_add_post_layout_classes()

/*-----------------------------------------------------------------------------------*/
/* woo_section_class() */
/*-----------------------------------------------------------------------------------*/

	function woo_section_class( $base, $custom_classes = '' ) {
		
		$output = '';
		
		$classes = strtolower( $base );
		
		if ( $custom_classes != '' ) { $classes .= ' ' . $custom_classes; }
		
		$classes = apply_filters( 'woo_section_class', $classes, $base );
		
		$output = ' class="' . $classes . '"';
		
		echo $output;
		
	} // End woo_section_class()

/*-----------------------------------------------------------------------------------*/
/* Register WP Menus */
/*-----------------------------------------------------------------------------------*/
if ( function_exists( 'wp_nav_menu') ) {
	add_theme_support( 'nav-menus' );
	register_nav_menus( array( 'primary-menu' => __( 'Primary Menu', 'woothemes' ) ) );
	register_nav_menus( array( 'top-menu' => __( 'Top Menu', 'woothemes' ) ) );
}


/*-----------------------------------------------------------------------------------*/
/* Page navigation */
/*-----------------------------------------------------------------------------------*/
if (!function_exists( 'woo_pagenav')) {
	function woo_pagenav() {

		global $woo_options;

		// If the user has set the option to use simple paging links, display those. By default, display the pagination.
		if ( array_key_exists( 'woo_pagination_type', $woo_options ) && $woo_options[ 'woo_pagination_type' ] == 'simple' ) {
			if ( get_next_posts_link() || get_previous_posts_link() ) {
		?>
            <div class="nav-entries">
                <?php next_posts_link( '<span class="nav-prev fl">'. __( '<span class="meta-nav">&larr;</span> Older posts', 'woothemes' ) . '</span>' ); ?>
                <?php previous_posts_link( '<span class="nav-next fr">'. __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'woothemes' ) . '</span>' ); ?>
                <div class="fix"></div>
            </div>
		<?php
			}
		} else {
			woo_pagination();

		} // End IF Statement

	} // End woo_pagenav()
} // End IF Statement

/*-----------------------------------------------------------------------------------*/
/* WooTabs - Popular Posts */
/*-----------------------------------------------------------------------------------*/
if (!function_exists( 'woo_tabs_popular')) {
	function woo_tabs_popular( $posts = 5, $size = 45 ) {
		global $post;
		$popular = get_posts( 'ignore_sticky_posts=1&orderby=comment_count&showposts='.$posts);
		foreach($popular as $post) :
			setup_postdata($post);
	?>
	<li>
		<?php if ($size <> 0) woo_image( 'height='.$size.'&width='.$size.'&class=thumbnail&single=true' ); ?>
		<a title="<?php the_title(); ?>" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
		<span class="meta"><?php the_time( get_option( 'date_format' ) ); ?></span>
		<div class="fix"></div>
	</li>
	<?php endforeach;
	}
}


/*-----------------------------------------------------------------------------------*/
/* Post Meta */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_post_meta' ) ) {
	function woo_post_meta( $section = '' ) {
	
		global $post;
	
	if ( ! defined( 'WOO_DATE_FORMAT' ) ) {
		define( 'WOO_DATE_FORMAT', get_option( 'date_format' ) );
	}
	
	$categories_obj = get_the_category( $post->ID );
	$prepared_categories = array();
	$the_category = '';
	
	if ( count( $categories_obj ) && ! is_wp_error( $categories_obj ) ) {
		foreach ( $categories_obj as $c ) {
			$prepared_categories[] = '<a href="' . get_term_link( $c->slug, 'category' ) . '">' . $c->name . '</a>';
		}
		
		/*
		if ( $section == 'more-news' ) {
			$the_category = $prepared_categories[0];
		} else {
			$the_category = join( ', ', $prepared_categories );
		}
		*/
	}
?>
<div class="post-meta">
	<span class="category"><?php echo $the_category; ?></span>
	<span class="date"><?php echo the_time( WOO_DATE_FORMAT ); ?></span>
	<div class="fix"></div>
</div>
<?php
	}
}


/*-----------------------------------------------------------------------------------*/
/* Subscribe / Connect */
/*-----------------------------------------------------------------------------------*/

if (!function_exists( 'woo_subscribe_connect')) {
	function woo_subscribe_connect($widget = 'false', $title = '', $form = '', $social = '') {

		global $woo_options;

		// Setup title
		if ( $widget != 'true' )
			$title = $woo_options[ 'woo_connect_title' ];

		// Setup related post (not in widget)
		$related_posts = '';
		if ( $woo_options[ 'woo_connect_related' ] == "true" AND $widget != "true" )
			if ( is_single() && ! is_attachment() ) { $related_posts = do_shortcode( '[related_posts limit="5"]' ); }

?>
	<?php if ( $woo_options[ 'woo_connect' ] == "true" OR $widget == 'true' ) : ?>
	<div id="connect-related">
	
		<div class="connect <?php if ( $related_posts != '' ) echo 'col-left'; ?>">
		<h3><?php if ( $title ) echo stripslashes( $title ); else _e( 'Subscribe', 'woothemes' ); ?></h3>

		<div class="connect-inner">
			<p><?php if ($woo_options[ 'woo_connect_content' ] != '') echo stripslashes($woo_options[ 'woo_connect_content' ]); else _e( 'Subscribe to our e-mail newsletter to receive updates.', 'woothemes' ); ?></p>

			<?php if ( $woo_options[ 'woo_connect_newsletter_id' ] != "" AND $form != 'on' ) : ?>
			<form class="newsletter-form<?php if ( $related_posts == '' ) echo ' fl'; ?>" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open( 'http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $woo_options[ 'woo_connect_newsletter_id' ]; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520' );return true">
				<input class="email" type="text" name="email" value="<?php esc_attr_e( 'E-mail', 'woothemes' ); ?>" onfocus="if (this.value == '<?php _e( 'E-mail', 'woothemes' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'E-mail', 'woothemes' ); ?>';}" />
				<input type="hidden" value="<?php echo $woo_options[ 'woo_connect_newsletter_id' ]; ?>" name="uri"/>
				<input type="hidden" value="<?php bloginfo( 'name' ); ?>" name="title"/>
				<input type="hidden" name="loc" value="en_US"/>
				<input class="submit" type="submit" name="submit" value="<?php _e( 'Submit', 'woothemes' ); ?>" />
			</form>
			<?php endif; ?>

			<?php if ( $woo_options['woo_connect_mailchimp_list_url'] != "" AND $form != 'on' AND $woo_options['woo_connect_newsletter_id'] == "" ) : ?>
			<!-- Begin MailChimp Signup Form -->
			<div id="mc_embed_signup">
				<form class="newsletter-form<?php if ( $related_posts == '' ) echo ' fl'; ?>" action="<?php echo $woo_options['woo_connect_mailchimp_list_url']; ?>" method="post" target="popupwindow" onsubmit="window.open('<?php echo $woo_options['woo_connect_mailchimp_list_url']; ?>', 'popupwindow', 'scrollbars=yes,width=650,height=520');return true">
					<input type="text" name="EMAIL" class="required email" value="<?php _e('E-mail','woothemes'); ?>"  id="mce-EMAIL" onfocus="if (this.value == '<?php _e('E-mail','woothemes'); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('E-mail','woothemes'); ?>';}">
					<input type="submit" value="<?php _e('Submit', 'woothemes'); ?>" name="subscribe" id="mc-embedded-subscribe" class="btn submit button">
				</form>
			</div>
			<!--End mc_embed_signup-->
			<?php endif; ?>

			<?php if ( $social != 'on' ) : ?>
			<div class="social<?php if ( $related_posts == '' AND $woo_options[ 'woo_connect_newsletter_id' ] != "" ) echo ' fr'; ?>">
		   		<?php if ( $woo_options[ 'woo_connect_rss' ] == "true" ) { ?>
		   		<a href="<?php if ( $woo_options[ 'woo_feed_url' ] ) { echo $woo_options[ 'woo_feed_url' ]; } else { echo get_bloginfo_rss( 'rss2_url' ); } ?>" class="subscribe"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-rss.png" title="<?php esc_attr_e( 'Subscribe to our RSS feed', 'woothemes' ); ?>" alt=""/></a>

		   		<?php } if ( $woo_options[ 'woo_connect_twitter' ] != "" ) { ?>
		   		<a href="<?php echo $woo_options[ 'woo_connect_twitter' ]; ?>" class="twitter"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-twitter.png" title="<?php esc_attr_e( 'Follow us on Twitter', 'woothemes' ); ?>" alt=""/></a>

		   		<?php } if ( $woo_options[ 'woo_connect_facebook' ] != "" ) { ?>
		   		<a href="<?php echo $woo_options[ 'woo_connect_facebook' ]; ?>" class="facebook"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-facebook.png" title="<?php esc_attr_e( 'Connect on Facebook', 'woothemes' ); ?>" alt=""/></a>

		   		<?php } if ( $woo_options[ 'woo_connect_youtube' ] != "" ) { ?>
		   		<a href="<?php echo $woo_options[ 'woo_connect_youtube' ]; ?>" class="youtube"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-youtube.png" title="<?php esc_attr_e( 'Watch on YouTube', 'woothemes' ); ?>" alt=""/></a>

		   		<?php } if ( $woo_options[ 'woo_connect_flickr' ] != "" ) { ?>
		   		<a href="<?php echo $woo_options[ 'woo_connect_flickr' ]; ?>" class="flickr"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-flickr.png" title="<?php esc_attr_e( 'See photos on Flickr', 'woothemes' ); ?>" alt=""/></a>

		   		<?php } if ( $woo_options[ 'woo_connect_linkedin' ] != "" ) { ?>
		   		<a href="<?php echo $woo_options[ 'woo_connect_linkedin' ]; ?>" class="linkedin"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-linkedin.png" title="<?php esc_attr_e( 'Connect on LinkedIn', 'woothemes' ); ?>" alt=""/></a>

		   		<?php } if ( $woo_options[ 'woo_connect_delicious' ] != "" ) { ?>
		   		<a href="<?php echo $woo_options[ 'woo_connect_delicious' ]; ?>" class="delicious"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-delicious.png" title="<?php esc_attr_e( 'Discover on Delicious', 'woothemes' ); ?>" alt=""/></a>
		   		
		   		<?php } if ( $woo_options[ 'woo_connect_googleplus' ] != "" ) { ?>
		   		<a href="<?php echo $woo_options[ 'woo_connect_googleplus' ]; ?>" class="google+"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-googleplus.png" title="<?php esc_attr_e( 'Discover on Google+', 'woothemes' ); ?>" alt=""/></a>

				<?php } ?>
			</div>
			<?php endif; ?>
			
			</div><!-- /.connect-inner -->

		</div><!-- col-left -->

		<?php if ( $woo_options[ 'woo_connect_related' ] == "true" AND $related_posts != '' ) : ?>
		<div class="related-posts col-right">
			<h3><?php _e( 'Related Posts', 'woothemes' ); ?></h3>
			<div class="related-inner">
				<?php echo $related_posts; ?>
			</div>
		</div><!-- col-right -->
		<?php wp_reset_query(); endif; ?>

        <div class="fix"></div>
	</div>
	<?php endif; ?>
<?php
	}
}


/*-----------------------------------------------------------------------------------*/
/* END */
/*-----------------------------------------------------------------------------------*/
?>