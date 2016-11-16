<?php get_header(); ?>
<?php global $woo_options; ?>
    
    <?php if ( $woo_options[ 'woo_breadcrumbs_show' ] == 'true' ) { ?>
	    <div id="breadcrumbs">
	    	<?php woo_breadcrumbs(); ?>
	    </div><!--/#breadcrumbs -->
	<?php } ?>
    
    <div id="content" <?php woo_section_class( 'content', 'col-full special-single' ); ?>>
    
		<div id="main" class="col-left">
		           
		<?php if ( have_posts() ) { $count = 0; ?>
        <?php while ( have_posts() ) { the_post(); $count++; ?>
                                                                    
            <div <?php post_class(); ?>>

			   <div <?php woo_section_class( 'title-media-block', '' ); ?>>
        			<div class="post-title-wrap">
                		<h1 class="title"><?php the_title(); ?></h1>
                	</div><!-- /.post-title-wrap -->
					<div class="fix"></div>
				</div><!-- /.title-media-block -->

                <div class="entry">
                	<div class="column column-01">
	                	<?php the_content(); ?>
	                	<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>
                	</div><!--/.column-->
                	<div class="fix"></div>
               	</div><!-- /.entry -->

				<?php edit_post_link( __( '{ Edit }', 'woothemes' ), '<span class="small">', '</span>' ); ?>
                
            </div><!-- /.post -->
            
            <?php
            	$comm = $woo_options[ 'woo_comments' ];
            	if ( ($comm == 'page' || $comm == 'both' ) ) {
            		comments_template();
            	}
            ?>
                                                
		<?php
				} // End WHILE Loop
			} else {
		?>
			<div <?php post_class(); ?>>
            	<p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
            </div><!-- /.post -->
        <?php } // End IF Statement ?>  
        
		</div><!-- /#main -->

        <?php get_sidebar(); ?>

    </div><!-- /#content -->
		
<?php get_footer(); ?>