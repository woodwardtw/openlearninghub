<?php
/*
Template Name: Magic Box Form

Provides functionality for the RSS Feed Finder
*/

// no feedback for now.
$feedback_msg = '';

// verify that a form was submitted and it passes the nonce check
if ( isset( $_POST['tv_form_add_feedfinder_submitted'] ) && wp_verify_nonce( $_POST['tv_form_add_feedfinder_submitted'], 'tv_form_add_feedfinder' ) ) {
 
	// grab the url from the form
	
	$blogurl = 	esc_url( trim($_POST['blogurl']), array('http', 'https') ); 
 	// $blogurl =  trim( $_POST['blogurl'] );
 	
 	// let's do some validation, store an error message for each problem found
 	$errors = array();
 		
 	if ( empty( $blogurl ) ) {
 		// missing value for url
 		$errors[] = '<strong>Blog URL Missing or Invalid</strong> - please enter the full URL for your blog.';
 	} elseif ( strpos( $blogurl, '/feed' )  ) {
 		$errors[] = '<strong>Is That Your Blog URL?</strong> It looks like you entered the address of an RSS Feed in the box. Please enter the web address where you content shows up, the URL that shows your site in a browser.';
 	 	
 	} else { 

		// see if we can find a feed
		$blogrss = feedSearch( $blogurl );
 	
 		// feed not found by the function
 		if ($blogrss == -1) $errors[] = '<strong>Blog Feed cound not be found</strong>. Make sure the URL that you typed is the one that dispalys your web site in a browser. Please check, edit, and try again, or get <a href="http://thoughtvectors.net/help/">assistance</a>';
 	}
 	
 	if ( count($errors) > 0 ) {
 			// form errors, build feedback string to display the errors
 			$feedback_msg = 'Sorry but the Magic Box cannot process your request: <ul>';
 			
 			// Hah, each one is an oops, get it? 
 			foreach ($errors as $oops) {
 				$feedback_msg .= '<li>' . $oops . '</li>';
 			}
 			
 			$feedback_msg .= '</ul>';
 			
	} else {
	
		$feedback_msg = '<div id="rsslink">&nbsp;<a href="' . $blogrss . '" target="_blank">' . $blogrss . '</a></div> <p><br />You should first <a href="' . $blogrss . '">verify the URL</a> (opens in a new window) -- it will show you something like may look like gibberish, but it represents the raw  source of information from your blog. It should show you something like <a href="http://www.scottgu.com/blogposts/rssreader/step11.jpg" target="_blank">this</a> or maybe <a href="http://misc.dailysplice.com/images/blog/screenshot_cbc_firefox_RSSpage.png"  target="_blank">this</a> (depending on your browser). <br /><br />
Make sure you copy this URL to a place where you can find it again. Proceed now to the next step in the journey.<br /><br /><a class="button" style="font-size:1.3em" href="/syndicate-here/">ONWARD! CONNECT ME TO THOUGHTVECTORS.NET</a></p>';  
	
	}
}
 		
?>
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
	                	
	                	<form action="#magixboxform" id="magixboxform" class="" method="post">
	                	
	                	<p><strong>Enter the web address for your blog, the one that displays your site.</strong><br />
	                	<input type="text" id="blogurl" name="blogurl" style="width:400px" value="<?php echo $blogurl?>" />
	                	
	                	
	                	<?php wp_nonce_field( 'tv_form_add_feedfinder', 'tv_form_add_feedfinder_submitted' ); ?>
	                	
	                	<input type="submit" class="btn btn-primary" value="Find My Blog's RSS Feed"  id="submitblogurl" name="submitblogurl" tabindex="15">
	                	
	                	</form>
	                	
	                	<?php if ( !empty( $feedback_msg ) and !(count($errors)) ) echo do_shortcode( '[box type="info" style="rounded"]Success! We have found an RSS Feed for you.[/box]'  );?>
	                	
	                	
	                	<?php echo $feedback_msg?>
	                	
	                	
	                	
	                	
	                	<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>
                	</div><!--/.column-->
                	<div class="fix"></div>
               	</div><!-- /.entry -->

				<?php edit_post_link( __( '{ Edit }', 'woothemes' ), '<span class="small">', '</span>' ); ?>
                
            </div><!-- /.post -->
                                           
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