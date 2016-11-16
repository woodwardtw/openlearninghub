<?php
	get_header();
	global $woo_options;
	
	// Set how many items to display in a single row.
	$per_row = 3;
	$main_css = 'fullwidth';
	
	
	query_posts( 'cat=27&posts_per_page=12' );
?>
    
    <div id="content" class="col-full">
    	
    	<?php if ( $woo_options[ 'woo_breadcrumbs_show' ] == 'true' ) { ?>
			<div id="breadcrumbs">
				<?php woo_breadcrumbs(); ?>
			</div><!--/#breadcrumbs -->
		<?php } ?>
    
		<div id="main" class="fullwidth">
		
		<?php if ( have_posts() ) { $count = 0; ?>
        
        	<span class="archive_header">
        		<span class="fl cat"><?php _e( '#thoughtvector tweets', 'woothemes' ); ?></span> 
        		<span class="fr cat"><a href="/twitter-explorer/">Tweet Explorer</a></span>
        	</span>        
        
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
        	    
				<?php echo wp_oembed_get( get_permalink() ); ?>
				
				<span class="read-more"><?php edit_post_link(esc_html__('Edit', 'woothemes'));?></span>
	
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



    </div><!-- /#content -->
		
<?php get_footer(); ?>