<?php
/*
Template Name: Latest Posts as Text

*/

$default_cat = 23; //default category, for this site "All Blogs"
$posts_to_grab = 100; // last n posts
 
// The Query
$the_query = new WP_Query("cat=$default_cat&posts_per_page=$posts_to_grab");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title><?php echo get_bloginfo( 'name', 'display' );?> Blabber</title>
</head>
<body>


<?php if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post();?>

	<?php 	
		//echo sanitize_post_field( 'post_content', $post->post_content $post->ID, 'display' );
		//echo strip_tags( get_the_content() ) . ' ';
		$content = apply_filters( 'the_content', get_the_content() );
		echo strip_tags( $content ) . ' ';
		?>
<?php endwhile; else: ?>
	
	<?php echo 'Got no posts for ya, chief';?>

<?php endif; ?>

</body>
</html>