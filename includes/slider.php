<?php
	global $woo_options, $wp_query, $post;
	$exclude = array();
?>

<div id="slides">
    <?php $woo_slider_tags = $woo_options['woo_slider_tags']; if ( ($woo_slider_tags != '') && (isset($woo_slider_tags)) ) { ?>
    <?php 
    	unset($tag_array); 
		$slide_tags_array = explode(',',$woo_options['woo_slider_tags']); // Tags to be shown
        foreach ($slide_tags_array as $tags){ 
			$tag = get_term_by( 'name', trim($tags), 'post_tag', 'ARRAY_A' );
			if ( $tag['term_id'] > 0 )
				$tag_array[] = $tag['term_id'];
		}
		if ( ! empty( $tag_array ) ) {
    ?>
	
	<?php $saved = $wp_query; query_posts( array( 'tag__in' => $tag_array, 'showposts' => $woo_options['woo_slider_entries'], 'post_type' => 'post' ) ); ?>
	<?php if ( have_posts() ) { $count = 0; ?>
    <div class="slides_container">
        
            <?php while ( have_posts() ) { the_post(); $exclude[] = $post->ID; ?>    
            <?php $count++; ?>
            <?php
				$slide_content = 'image';
				$slide_transition = 'scrollHorz';
				$has_embed = false;
				
				$custom_meta = get_post_custom( $post->ID );
				
				if ( isset( $custom_meta['_slide_transition_type'] ) && $custom_meta['_slide_transition_type'][0] != '' ) {
					$slide_transition = $custom_meta['_slide_transition_type'][0];
				}
				
				if ( isset( $custom_meta['_slide_content'] ) && $custom_meta['_slide_content'][0] != '' ) {
					$slide_content = $custom_meta['_slide_content'][0];
				}
				
				if ( isset( $custom_meta['embed'] ) && $custom_meta['embed'][0] != '' ) {
					$has_embed = true;
				}
			?>
            
            <div id="slide-<?php echo $count; ?>" class="slide slide-id-<?php the_ID(); ?>">
        		<?php
        			if ( ( $has_embed == true ) && ( $slide_content == 'video' ) ) {
        				echo woo_embed( 'key=embed&width=934&class=slide-video' ); // Minus 6px off the width to cater for the 3px border.
        			} else {
        				woo_image( 'key=image&width=940&noheight=true&class=slide-image&link=img' );
        			}
        		?>
            	
            	<div class="slide-content post">
            		
            		<div class="inner-wrap">
            			<?php
            				if ( ( $has_embed == true ) && ( $slide_content == 'video' ) ) {
            					echo '<a href="#" class="btn_close hide"><span>' . __( 'Close', 'woothemes' ) . '</span></a><!--/.btn_close-->' . "\n";
            				}
            			?>
            			<?php woo_post_meta(); ?>
            	
       		     		<h2 class="title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
       		     		
       		     		<div class="entry">
           		    	 	<?php the_excerpt(); ?>
						</div>
					
					</div>     	
       		     	       		 		
       		 	</div><!-- /.slide-content -->
       		     	
       		    <div class="fix"></div>
            </div><!--/.slide-->
            
		<?php } // End WHILE Loop ?> 
		
    </div><!-- /.container -->
    
    <a class="prev hidden" href="#"><?php _e( 'Previous', 'woothemes' ); ?></a><a class="next hidden" href="#"><?php _e( 'Next', 'woothemes' ); ?></a>
    <span class="prev-text hidden"></span><span class="next-text hidden"></span>
    
    <?php } $wp_query = $saved; ?> 

	<?php } else { ?>
	<?php echo do_shortcode( '[box type="info"]No posts with your specified tag(s) were found[/box]' ); ?>
	<?php } // End IF Statement ?>  
    <?php } else { ?>
	<?php echo do_shortcode( '[box type="info"]Please setup tag(s) in your options panel that are used in posts.[/box]' ); ?>
     <?php } ?>   
</div><!-- /#slides -->

<?php if ( get_option( 'woo_exclude' ) != $exclude ) { update_option( 'woo_exclude', $exclude ); } ?>