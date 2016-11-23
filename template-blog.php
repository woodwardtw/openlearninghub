<?php
/*
Template Name: Blog
*/
?>
<?php
	get_header();
	global $woo_options;
	
	// Set how many items to display in a single row.
	$per_row = 3;
	$main_css = 'fullwidth';
	
	if ( woo_active_sidebar( 'primary' ) ) {
		$per_row = 2;
		$main_css = 'col-left';
	}
?>

    <!-- #content Starts -->
	<?php woo_content_before(); ?>
	
	<?php if ( $woo_options[ 'woo_breadcrumbs_show' ] == 'true' ) { ?>
	    <div id="breadcrumbs">
	    	<?php woo_breadcrumbs(); ?>
	    </div><!--/#breadcrumbs -->
	<?php } ?>
	
    <div id="content" class="col-full">
    
        <!-- #main Starts -->
        <div id="main" class="<?php echo $main_css; ?>">
        
        <div class="archive-layout">    

        <?php
        	if ( get_query_var( 'paged') ) { $paged = get_query_var( 'paged' ); } elseif ( get_query_var( 'page') ) { $paged = get_query_var( 'page' ); } else { $paged = 1; }
        	
        	query_posts( "post_type=post&paged=$paged" );
        	
        	if ( have_posts() ) { $count = 0;
        
	        	while ( have_posts() ) { the_post(); $count++;
	        	
	        	$class = '';
	        	if ( ( $count % $per_row == 0 ) && ( $count > 1 ) ) { $class = 'last'; }
        ?>
                                                                    
            <!-- Post Starts -->
             <div <?php post_class( $class ); ?>>
				
        	    <?php woo_post_meta(); ?>
        	    
        	    <h2 class="title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
        	    
        	    <?php if ( $woo_options['woo_post_content'] != 'content' ) { woo_image( 'width='.$woo_options['woo_thumb_w'].'&height='.$woo_options['woo_thumb_h'].'&class=thumbnail '.$woo_options['woo_thumb_align'] ); } ?>
        	    
        	    <div class="entry">
        	        <?php if ( $woo_options['woo_post_content'] == 'content' ) { the_content( __( 'Continue Reading &rarr;', 'woothemes' ) ); } else { the_excerpt(); } ?>
        	    </div>
        	    
        	    <div class="post-more">      
        	    	<?php if ( $woo_options['woo_post_content'] == 'excerpt' ) { ?>
        	    	<span class="read-more"><a href="<?php the_permalink(); ?>" title="<?php esc_attr_e( 'Read More', 'woothemes' ); ?>"><?php _e( 'Read More', 'woothemes' ); ?></a></span>
			    	<span class="comments"><?php comments_popup_link( __( '0 Comments', 'woothemes' ), __( '1 Comment', 'woothemes' ), __( '% Comments', 'woothemes' ) ); ?></span>
        	        <div class="fix"></div>
        	        <?php } ?>
        	        
        	    </div>
        	                         
        	</div><!-- /.post -->
            
            <?php
		    	if ( $count % $per_row == 0 ) {
		    		echo '<div class="fix"></div>' . "\n";
		    	}
		    ?>
                                                
        <?php } // End WHILE Loop ?>
        
        <div class="fix"></div>
        
        </div><!-- /.archive-layout -->
        
        <?php } else { ?>
            <div <?php post_class(); ?>>
                <p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
            </div><!-- /.post -->
        <?php } ?>
    
            <?php woo_pagenav(); ?>
			<?php wp_reset_query(); ?>                

        </div><!-- /#main -->
            
		<?php get_sidebar(); ?>

    </div><!-- /#content -->    
		
<?php get_footer(); ?>