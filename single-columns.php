<?php
	get_header();
	global $woo_options, $post;
	
	// Get the metadata for the current post.
	$post_meta = get_post_custom( $post->ID );
	
	$title_before = '<div class="post-title-wrap">' . "\n" . '<h1 class="title">' . '<a href="' . get_permalink( get_the_ID() ) . '" rel="bookmark" title="' . the_title_attribute( array( 'echo' => 0 ) ) . '">';
	$title_after = '</a>' . '</h1>' . "\n" . '</div><!-- /.post-title-wrap -->';

	$is_full_width = false;
	if ( 'template-fullwidth.php' == get_post_meta( get_the_ID(), '_wp_page_template', true ) ) {
		$is_full_width = true;
	}
?> 

	<?php if ( $woo_options[ 'woo_breadcrumbs_show' ] == 'true' ) { ?>
	    <div id="breadcrumbs">
	    	<?php woo_breadcrumbs(); ?>
	    </div><!--/#breadcrumbs -->
	<?php } ?>

    <div id="content" <?php woo_section_class( 'content', 'col-full special-single' ); ?>>
		
		<?php if ( have_posts() ) { $count = 0; ?>
        <?php while ( have_posts() ) { the_post(); $count++; ?>
        	
        	<div <?php post_class(); ?>>
        	
        		<div <?php woo_section_class( 'title-media-block', '' ); ?>>		
					<?php
						woo_post_title_before();
						woo_post_meta();
						the_title( $title_before, $title_after );
						woo_post_title_after();
					?>
					<div class="fix"></div>
					<?php
						// Dynamically generated intro paragraph.
						echo woo_post_intro_text();
					?>
				</div><!-- /.title-media-block -->
            	
                <div class="entry">
                            
                	<div class="column column-01">
	                	<?php the_content(); ?>
	                	<?php the_tags( '<p class="tags">'.__( 'Tags: ', 'woothemes' ), ', ', '</p>' ); ?>
	                	<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>
	                	<?php edit_post_link( __( '{ Edit }', 'woothemes' ), '<span class="small">', '</span>' ); ?>
                	</div><!--/.column-->
                	<div class="fix"></div>
				</div>
                                
            </div><!-- .post -->
            
            <div id="post-entries">
	            <div class="nav-prev fl"><?php previous_post_link( '%link', '%title' ); ?></div>
	            <div class="nav-next fr"><?php next_post_link( '%link', '%title' ); ?></div>
	            <div class="fix"></div>
	        </div><!-- #post-entries -->
                                                
		<?php
				} // End WHILE Loop
			} else {
		?>
			<div <?php post_class(); ?>>
            	<p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
			</div><!-- .post -->             
       	<?php } // End IF Statement ?>
       	
		<div id="main" class="col-left<?php if ( true == $is_full_width ) { echo ' fullwidth'; } ?>">
		
				<?php if ( $woo_options['woo_post_author'] == 'true' && ( get_post_type() == 'post' ) ) { ?>
				<div id="post-author">
					<div class="profile-header">
						<h3><?php printf( esc_attr__( 'About %s', 'woothemes' ), get_the_author() ); ?></h3>
						<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
							<?php printf( __( 'View all posts by %s', 'woothemes' ), get_the_author() ); ?>
						</a>
						<div class="fix"></div>
					</div><!-- /.profile-header -->
					<div class="profile-content">
							
						<div class="profile-image"><?php echo get_avatar( get_the_author_meta( 'ID' ), '70' ); ?></div>
						<?php the_author_meta( 'description' ); ?>
					</div><!-- .profile-content -->
					<div class="fix"></div>
				</div><!-- #post-author -->
				<?php } ?>

				<?php woo_subscribe_connect(); ?>
	        
	        <?php if ( $woo_options['woo_ad_single'] == 'true' && ( get_post_type() == 'post' ) ) { ?>
        	<div id="single-ad">
				<?php if ( $woo_options['woo_ad_single_adsense'] != '' ) { echo stripslashes( $woo_options['woo_ad_single_adsense'] );  } else { ?>
					<a href="<?php echo $woo_options[ 'woo_ad_single_url' ]; ?>"><img src="<?php echo $woo_options['woo_ad_single_image']; ?>" width="468" height="60" alt="advert" /></a>
				<?php } ?>		   	
			</div><!-- /#single-ad -->
        	<?php } ?>
            
            <?php
            	$comm = $woo_options['woo_comments'];
            	if ( ($comm == get_post_type() || $comm == 'both' ) ) {
            		comments_template();
            	}
            ?>
        
		</div><!-- #main -->
		<?php if ( ! $is_full_width ) { get_sidebar(); } ?>

    </div><!-- #content -->
		
<?php get_footer(); ?>