<?php
/*
Template Name: Latest Posts as Text

*/

$default_cat = 23; //default category, for this site "All Blogs"
$posts_to_grab = 20; // last n posts
 
// The Query
$the_query = new WP_Query("cat=$default_cat&posts_per_page=$posts_to_grab");

$count = 0;
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
		$count++;
		
		echo "<br />($count)<br /><br />";
		
		//echo sanitize_post_field( 'post_content', $post->post_content $post->ID, 'display' );
		echo strip_tags( get_the_content() );
		?>
<?php endwhile; else: ?>
	
	<?php echo 'Got no posts for ya, chief';?>

<?php endif; ?>

</body>
</html>