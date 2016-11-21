<?php
// Fist full of comments
if (!function_exists( "custom_comment")) {
	function custom_comment($comment, $args, $depth) {
	   $GLOBALS['comment'] = $comment; ?>
	                 
		<li <?php comment_class(); ?>>
	    
	    	<a name="comment-<?php comment_ID() ?>"></a>
	      	
	      	<div id="li-comment-<?php comment_ID() ?>" class="comment-container">
	      	
				<div class="comment-head">
				
					<div class="fl">
		      	            
	                	<span class="name"><?php the_commenter_link() ?></span>           
	                	<span class="date"><?php echo get_comment_date(get_option( 'date_format' )) ?> <?php _e( 'at', 'woothemes' ); ?> <?php echo get_comment_time(get_option( 'time_format' )); ?></span>
	                	<span class="perma"><a href="<?php echo get_comment_link(); ?>" title="<?php esc_attr_e( 'Direct link to this comment', 'woothemes' ); ?>">#</a></span>
	                	<span class="edit"><?php edit_comment_link(__( 'Edit', 'woothemes' ), '', '' ); ?></span>
	                
	                </div>
	                
	                <?php if(get_comment_type() == "comment"){ ?>
	            	    <div class="avatar"><?php the_commenter_avatar($args); ?></div>
	            	<?php
	            		$social_urls = array(
	            							'twitter' => get_comment_meta( get_comment_ID(), '_twitter', true ), 
	            							'facebook' => get_comment_meta( get_comment_ID(), '_facebook', true )
	            							);
	            		if ( $social_urls['twitter'] == '' && $social_urls['facebook'] == '' ) {} else {
	            	?>
	            	    <div class="comment-social">
	                		<ul>
	                			<?php
	                				$html = '';
	                				
	                				foreach ( $social_urls as $k => $v ) {
	                					if ( $social_urls[$k] != '' ) {
	                						
	                						$url = $v;
	                						if ( $k == 'twitter' ) {
	                							$url = 'http://' . $k . '.com/' . $v;
	                						}
	                					
	                						$html .= '<li><a href="' . $url . '" title="' . ucwords( $k ) . '"><img src="' . get_template_directory_uri() . '/images/ico-comments-' . $k . '.png" at="' . ucwords( $k ) . '" /></a></li>' . "\n";
	                					}
	                				}
	                				
	                				echo $html;
	                			?>
	                		</ul>
	               		</div>
	            	   
	            	<?php
	            		}
	            	}
	            	?>
	            	
	            	<div class="fix"></div>
		        		          	
				</div><!-- /.comment-head -->
		      
		   		<div class="comment-entry"  id="comment-<?php comment_ID(); ?>">
				
				<?php comment_text() ?>
		            
				<?php if ($comment->comment_approved == '0') { ?>
	                <p class='unapproved'><?php _e( 'Your comment is awaiting moderation.', 'woothemes' ); ?></p>
	            <?php } ?>
						
	                <div class="reply">
	                    <?php comment_reply_link(array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
	                </div><!-- /.reply -->                       
			
				</div><!-- /comment-entry -->
	
			</div><!-- /.comment-container -->
			
	<?php 
	}
}

// PINGBACK / TRACKBACK OUTPUT
if (!function_exists( "list_pings")) {
	function list_pings($comment, $args, $depth) {
	
		$GLOBALS['comment'] = $comment; ?>
		
		<li id="comment-<?php comment_ID(); ?>">
			<span class="author"><?php comment_author_link(); ?></span> - 
			<span class="date"><?php echo get_comment_date(get_option( 'date_format' )) ?></span>
			<span class="pingcontent"><?php comment_text() ?></span>
	
	<?php 
	} 
}
		
if (!function_exists( "the_commenter_link")) {
	function the_commenter_link() {
	    $commenter = get_comment_author_link();
	    if ( ereg( ']* class=[^>]+>', $commenter ) ) {$commenter = ereg_replace( '(]* class=[\'"]?)', '\\1url ' , $commenter );
	    } else { $commenter = ereg_replace( '(<a )/', '\\1class="url "' , $commenter );}
	    echo $commenter ;
	}
}

if (!function_exists( "the_commenter_avatar")) {
	function the_commenter_avatar($args) {
	    $email = get_comment_author_email();
	    $avatar = str_replace( "class='avatar", "class='photo avatar", get_avatar( "$email",  $args['avatar_size']) );
	    echo $avatar;
	}
}

?>