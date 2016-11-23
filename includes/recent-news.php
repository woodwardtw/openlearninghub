<?php
	global $woo_options;
	
	// Setup RSS URL.
	$rss_url = get_bloginfo_rss( 'rss2_url' );
	if ( isset( $woo_options[ 'woo_feed_url' ] ) && ( $woo_options[ 'woo_feed_url' ] != '' ) ) { $rss_url = esc_url( $woo_options[ 'woo_feed_url' ] ); }
	
	$exclude = get_option( 'woo_exclude' );
	if ( ! is_array( $exclude ) ) { $exclude = array(); }
	
	$total_posts = get_option( 'woo_homepage_recentnews_totalposts' );
	if ( $total_posts == '' ) { $total_posts = get_option( 'posts_per_page' ); }
	
	$recent_news_categories = get_option( 'woo_homepage_recentnews_categories' );
	$categories_data = array();
	
	
	if ( $recent_news_categories != '' ) {
		$cats = explode( ',', $recent_news_categories );
		if ( is_array( $cats ) ) {
			foreach ( $cats as $k => $v ) {
				$cats[$k] = trim( $v );
				
				// Get the information for each of the categories.
				$data = get_term_by( 'id', $cats[$k], 'category' );
				
				if ( $data ) { $categories_data[] = $data; }
			}
		}
	}
	
	// Setup an array of post IDs to be excluded from the "More News" section, only on the homepage.
	$exclude_from_morenews = array();
	
	// Set how many items to display in a single row.
	$per_row = 3;
	
	if ( woo_active_sidebar( 'primary' ) ) {
		$per_row = 2;
	}
?>

<h2 class="section-title"><?php _e( 'Recently Opened', 'woothemes' ); ?> <a class="subscribe" href="<?php echo $rss_url; ?>" title="<?php esc_attr_e( 'Subscribe to RSS', 'woothemes' ); ?>"><?php _e( 'Subscribe', 'woothemes' ); ?></a></h2>

<div id="recent-news-filter">

	<span><?php _e( 'Categories', 'woothemes' ); ?>:</span>
	<?php
		// Determine the category of posts to display.
		$woo_current_category = 0;
		
		if ( isset( $_GET['current_category'] ) ) {
			$woo_current_category = (int) $_GET['current_category'];
		}
		
		// Generate categories used for sorting the recent news posts.
		$html = '';
		
		if ( is_array( $categories_data ) && ( count( $categories_data ) > 0 ) ) {
		
			$css_class = ' class="active"';
		
			if ( $woo_current_category > 0 ) { $css_class = ''; }
		
			$html .= '<ul>' . "\n";
			
				// Cater for if the system uses a static front page and a posts page.
				$front_page_url = site_url( '/' );
				if ( 'page' == get_option( 'show_on_front' ) && '' != get_option( 'page_for_posts' ) ) {
					$front_page_url = get_permalink( get_option( 'page_for_posts' ) );
				}
			
				$html .= '<li id="latest" class="category"><a href="' . $front_page_url . '" title="' . __( 'Latest', 'woothemes' ) . '"' . $css_class . '>' . __( 'Latest', 'woothemes' ) . '</a></li>' . "\n";
				
			
			for ( $i = 0; $i < count( $categories_data ); $i++ ) {
				$c = $categories_data[$i];
				
				$css_class = '';
				if ( $woo_current_category == $c->term_id ) { $css_class = ' class="active"'; }
				
				$html .= '<li id="category-' . $c->term_id . '" class="category category-' . $c->slug . '"><a href="' . get_term_link( $c->slug, 'category' ) . '" title="' . esc_attr( $c->name ) . '"' . $css_class . '>' . $c->name . '</a></li>' . "\n";
			}
			
			$html .= '</ul>' . "\n";
		
		}
		
		echo $html;
	?>

</div><!-- /#recent-news-filter -->

<div id="recent-news" class="archive-layout">

	<?php $paged = ( get_query_var( 'paged') ) ? get_query_var( 'paged' ) : 1; ?>
	<?php
		$args = array( 
						'post_type' => 'post',
						'paged' => $paged,  
						'posts_per_page' => $total_posts
					);
		
		// If we've got the slider enabled, exclude the slider posts.
		if ( $woo_options['woo_slider'] == 'true' && isset( $woo_options['woo_slider_exclude'] ) && ( $woo_options['woo_slider_exclude'] == 'true' ) ) {
			$args['post__not_in'] = $exclude;
		}
		
		$args['ignore_sticky_posts'] = 0;
			
		if ( $woo_current_category > 0 ) {
			$args['cat'] = $woo_current_category;
		} else {
			// If we have specified to only display posts from our specified categories under "latest", filter this appropriately.
			if ( isset( $woo_options['woo_recentnews_specificcats'] ) && ( $woo_options['woo_recentnews_specificcats'] == 'true' ) && ( $recent_news_categories != '' ) ) {
				// $args['cat'] = $recent_news_categories;
				$args['tax_query'] = array(
										array(
												'taxonomy' => 'category', 
												'field' => 'id', 
												'terms' => explode( ',', $recent_news_categories )
											)
									);
			}
		}
		
		$query_saved = $wp_query;
		
		$query = new WP_Query( $args );

		if ( $query->have_posts() ) { $count = 0;
			while ( $query->have_posts() ) { $query->the_post(); $count++;
				$exclude_from_morenews[] = get_the_ID();
				
				$css_class = '';
				if ( $count % $per_row == 0 ) { $css_class = 'last'; }
				
				$author_twname = get_the_author_meta( 'user_login' ); 
	?>
	                                                            
	    <div <?php post_class( $css_class ); ?>>
	
	        <?php woo_post_meta(); ?>
	        
	        <h2 class="title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
	        
	        <p class="fwp-source">by <a href="http://twitter.com/<?php echo $author_twname?>" class="twitter">@<?php echo $author_twname?></a></p>
	        
	        <?php if ( isset( $woo_options['woo_post_content'] ) && $woo_options['woo_post_content'] != 'content' ) woo_image( 'width=' . $woo_options['woo_thumb_w'] . '&height=' . $woo_options['woo_thumb_h'] . '&class=thumbnail ' . $woo_options['woo_thumb_align'] ); ?>
	        
	        <div class="entry">
	            <?php if ( isset( $woo_options['woo_post_content'] ) && $woo_options['woo_post_content'] == 'content' ) { the_content( __( 'Continue Reading &rarr;', 'woothemes' ) ); } else { echo get_character_excerpt( get_the_excerpt(), 700 ); } ?>
	        </div>
	        
	        <div class="post-more">      
	        	<?php if ( isset( $woo_options['woo_post_content'] ) && $woo_options['woo_post_content'] == 'excerpt' ) { ?>
	        	
	        	<?php 
	        	
	        	// set link for read more text to blog source if syndicated
	        	if ( is_syndicated() ) {
	        		echo '<span class="fwp-source">via: <em><a href="' . get_post_meta($post->ID, 'syndication_source_uri', $single = true)  . '">' . get_post_meta($post->ID, 'syndication_source', $single = true) . '</a></em><br />See <a href="/author/' . $author_twname . '">all posts by ' . $author_twname .  '</a></span>'; 
	        	
	        	}
			?>

	        	
	        	<span class="read-more"><a href="<?php the_permalink(); ?>" title="<?php esc_attr_e( 'More...', 'woothemes' ); ?>"><?php _e( 'More...', 'woothemes' ); ?></a>  <?php edit_post_link(esc_html__('Edit', 'woothemes'));?></span>
	        	
	            <div class="fix"></div>
	            <?php } ?>
	        </div>
	                             
	    </div><!-- /.post -->
	    <?php
	    	if ( $count % $per_row == 0 ) {
	    		echo '<div class="fix"></div>' . "\n";
	    	}
	    ?>                                    
	<?php
			} // End WHILE Loop
			
			// If we've got the slider enabled, exclude the slider posts.
			if ( isset( $woo_options['woo_slider'] ) && $woo_options['woo_slider'] == 'true' ) {
				$exclude_from_morenews = array_merge( $exclude_from_morenews, $exclude ); // Make sure the slider posts don't display in "More News" either.
			}
			
			$GLOBALS['exclude_from_morenews'] = $exclude_from_morenews; // Make the "posts to exclude" available to the "more news" section.
			
		} else {
	?>
	
	    <div <?php post_class(); ?>>
	        <p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
	    </div><!-- /.post -->
	
	<?php } // End IF Statement ?>  
	
	<div class="fix"></div>
</div><!-- /#recent-news -->