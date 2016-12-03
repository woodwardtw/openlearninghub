<?php
	
// Do not delete these lines

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ( 'Please do not load this page directly. Thanks!' );

if ( post_password_required() ) { ?>
	<p class="nocomments"><?php _e( 'This post is password protected. Enter the password to view comments.', 'woothemes' ); ?></p>
<?php return; } ?>
<?php $comments_by_type = &separate_comments( $comments ); ?>    

<!-- You can start editing here. -->

<div id="comments">

<?php if ( have_comments() ) { ?>
	<?php if ( ! empty( $comments_by_type['comment'] ) ) { ?>
		<h3>
			<?php comments_number(__( 'No Responses', 'woothemes' ), __( 'One Response', 'woothemes' ), __( '% Responses', 'woothemes' ) );?> <?php _e( 'to', 'woothemes' ); ?> &#8220;<?php the_title(); ?>&#8221;
			<?php post_comments_feed_link( $link_text = 'Subscribe' ); ?>	
		</h3>
		<ol class="commentlist">
	
			<?php wp_list_comments( 'avatar_size=40&callback=custom_comment&type=comment' ); ?>
		
		</ol>    
		<div class="navigation">
			<div class="fl"><?php previous_comments_link(); ?></div>
			<div class="fr"><?php next_comments_link(); ?></div>
			<div class="fix"></div>
		</div><!-- /.navigation -->
	<?php } ?>	    
	<?php if ( ! empty( $comments_by_type['pings'] ) ) { ?>
    		
        <h3 id="pings"><?php _e( 'Trackbacks/Pingbacks', 'woothemes' ); ?></h3>
    
        <ol class="pinglist">
            <?php wp_list_comments( 'type=pings&callback=list_pings' ); ?>
        </ol>
    	
	<?php } ?>	
<?php } else { // this is displayed if there are no comments so far ?>

		<?php if ( 'open' == $post->comment_status) { ?>
			<!-- If comments are open, but there are no comments. -->
			<p class="nocomments"><?php _e( 'There are comments yet... maybe you can be the first?', 'woothemes' ); ?></p>

		<?php } ?>

<?php } // End IF Statement ?>
</div> <!-- /#comments_wrap -->
<?php if ( 'open' == $post->comment_status) { ?>
<div id="respond">
	
	<div class="cancel-comment-reply">
		<small><?php cancel_comment_reply_link(); ?></small>
	</div><!-- /.cancel-comment-reply -->
	
	<h3><?php comment_form_title( __( 'Leave a Reply', 'woothemes' ), __( 'Leave a Reply to %s', 'woothemes' ) ); ?></h3>	

	<?php if ( get_option( 'comment_registration' ) && !$user_ID ) { //If registration required & not logged in. ?>

		<p><?php _e( 'You must be', 'woothemes' ); ?> <a href="<?php echo get_option( 'siteurl' ); ?>/wp-login.php?redirect_to=<?php echo urlencode( get_permalink() ); ?>"><?php _e( 'logged in', 'woothemes' ); ?></a> <?php _e( 'to post a comment.', 'woothemes' ); ?></p>

	<?php } else { //No registration required ?>
	
		<form action="<?php echo get_option( 'siteurl' ); ?>/wp-comments-post.php" method="post" id="commentform">
		
		<div class="col-left">
			<textarea name="comment" id="comment" rows="10" cols="50" tabindex="4"></textarea>
		</div>

		<?php if ( $user_ID ) { //If user is logged in ?>
		
			<div class="col-right">

				<p><?php _e( 'Logged in as', 'woothemes' ); ?> <a href="<?php echo get_option( 'siteurl' ); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(); ?>" title="<?php esc_attr_e( 'Log out of this account', 'woothemes' ); ?>"><?php _e( 'Logout', 'woothemes' ); ?> &raquo;</a></p>

		<?php } else { //If user is not logged in ?>
			<div class="col-right">

				<p>
					<input type="text" name="author" class="txt" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
					<label for="author" class="name-label"><?php _e( 'Name', 'woothemes' ); ?></label>
				</p>
				
				<p>
					<input type="text" name="email" class="txt" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
					<label for="email" class="email-label"><?php _e( 'E-mail', 'woothemes' ); ?></label>
				</p>
				
				<p>
					<input type="text" name="url" class="txt" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
					<label for="url" class="url-label"><?php _e( 'Website', 'woothemes' ); ?></label>
				</p>
				
				<p>
					<input type="text" name="twitter" class="txt" id="twitter" value="" size="22" tabindex="3" />
					<label for="twitter" class="twitter-label"><?php _e( 'Twitter Username', 'woothemes' ); ?></label>
				</p>
				
				<p>
					<input type="text" name="facebook" class="txt" id="facebook" value="" size="22" tabindex="3" />
					<label for="facebook" class="facebook-label"><?php _e( 'Facebook URL', 'woothemes' ); ?></label>
				</p>

		<?php } // End if logged in ?>

			<p>
				<input name="submit" type="submit" id="submit" class="button" tabindex="5" value="<?php esc_attr_e( 'Submit Comment', 'woothemes' ); ?>" />
				<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
			</p>
				
			</div><!-- /.col-right -->
			
		<?php comment_id_fields(); ?>
		<?php do_action( 'comment_form', $post->ID); ?>

		</form><!-- /#commentform -->

	<?php } // If registration required ?>

	<div class="fix"></div>

</div><!-- /#respond -->

<?php } // if you delete this the sky will fall on your head ?>