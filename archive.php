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
    
    <div id="content" class="col-full">
    	
    	<?php if ( $woo_options[ 'woo_breadcrumbs_show' ] == 'true' ) { ?>
			<div id="breadcrumbs">
				<?php woo_breadcrumbs(); ?>
			</div><!--/#breadcrumbs -->
		<?php } ?>
    
		<div id="main" class="<?php if ( ! woo_active_sidebar( 'primary' ) ) { echo 'fullwidth'; } else { echo 'col-left'; } ?>">
		
		<?php if ( have_posts() ) { $count = 0; ?>
        
            <?php if (is_category()) { ?>
        	<span class="archive_header">
        		<span class="fl cat"><?php _e( 'All Posts From', 'woothemes' ); ?>  <?php echo single_cat_title(); ?></span> 
        		<span class="fr catopml"><?php $cat_id = get_cat_ID( single_cat_title( '', false ) ); echo '<a href="' . get_site_url() . '/tv-opml.php?group=' . $cat_id  . '">' . __( " OPML ", "woothemes" ) . '</a>'; ?></span>  
        		
        		<span class="fr catrss"><?php  echo '<a href="' . get_category_feed_link( $cat_id, '' ) . '">' . __( "RSS feed", "woothemes" ) . '</a>'; ?>  </span>
        		
        		
        	</span>        
        
            <?php } elseif ( is_day() ) { ?>
            <span class="archive_header"><?php _e( 'Archive', 'woothemes' ); ?> | <?php the_time( get_option( 'date_format' ) ); ?></span>

            <?php } elseif ( is_month() ) { ?>
            <span class="archive_header"><?php _e( 'Archive', 'woothemes' ); ?> | <?php the_time( 'F, Y' ); ?></span>

            <?php } elseif ( is_year() ) { ?>
            <span class="archive_header"><?php _e( 'Archive', 'woothemes' ); ?> | <?php the_time( 'Y' ); ?></span>

            <?php } elseif ( is_author() ) { ?>
            <?php $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));?>
            <span class="archive_header"><?php _e( 'All Posts by ' . $curauth->display_name, 'woothemes' ); ?></span>

            <?php } elseif ( is_tag() ) { ?>
            <span class="archive_header"><?php _e( 'All Posts Tagged', 'woothemes' ); ?> "<?php echo single_tag_title( '', true ); ?>"</span>
            
            <?php } ?>
            <div class="fix"></div>
            
            <div class="archive-layout">
        
        <?php
        	while ( have_posts() ) { the_post(); $count++;
        	
        	$class = '';
	        if ( ( $count % $per_row == 0 ) && ( $count > 1 ) ) { $class = 'last'; }
        ?>
             
            <!-- Post Starts -->
            <div <?php post_class( $class ); ?>>
				
        	    <?php woo_post_meta(); ?>
        	           	    
        	    <?php $author_twname = get_the_author_meta( 'user_login' ); ?>
        	    
        	    <h2 class="title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
        	    
        	     <p class="fwp-source">by <a href="http://twitter.com/<?php echo $author_twname?>" class="twitter">@<?php echo $author_twname?></a></p>
        	     
        	    <?php if ( $woo_options[ 'woo_post_content' ] != 'content' ) { woo_image( 'width='.$woo_options['woo_thumb_w'].'&height='.$woo_options['woo_thumb_h'].'&class=thumbnail '.$woo_options['woo_thumb_align'] ); } ?>
        	    
        	    <div class="entry">
        	        <?php if ( $woo_options['woo_post_content'] == 'content' ) { the_content( __( 'Continue Reading &rarr;', 'woothemes' ) ); } else { echo get_character_excerpt( get_the_excerpt(), 700 ); } ?>
        	    </div>
        	    
        	    <div class="post-more">      
        	    	<?php if ( $woo_options[ 'woo_post_content' ] == 'excerpt' ) { ?>
        	    	
        	    	<?php 
					// set link for read more text to blog source if syndicated
					if ( is_syndicated() ) {
						echo '<span class="fwp-source">via: <em><a href="' . get_post_meta($post->ID, 'syndication_source_uri', $single = true)  . '">' . get_post_meta($post->ID, 'syndication_source', $single = true) . '</a></em></span>'; 
				
					}
					?>

        	    	<span class="read-more"><a href="<?php the_permalink(); ?>" title="<?php esc_attr_e( 'Read More', 'woothemes' ); ?>"><?php _e( 'Read More', 'woothemes' ); ?></a> <?php edit_post_link(esc_html__('Edit', 'woothemes'));?></span>
			    	
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
                <p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ) ?></p>
            </div><!-- /.post -->
        
        <?php } // End IF Statement ?>
    
			<?php woo_pagenav(); ?>
                
		</div><!-- /#main -->

        <?php get_sidebar(); ?>

    </div><!-- /#content -->
		
<?php get_footer(); ?>