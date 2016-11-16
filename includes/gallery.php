<?php
global $woo_options, $post;

/*-----------------------------------------------------------------------------------*/
/* Variables and general gallery setup. */
/*-----------------------------------------------------------------------------------*/

$html = '';
$container_class = 'slides_container';
$post_meta = get_post_custom( $post->ID );

$settings = array();

$settings['use_timthumb'] = false; 		// Set to false to disable for this section of theme. Images will be downsized instead of resized to 640px width
$settings['limit'] = 20; 				// Number of maximum attachments to get 
$settings['photo_size'] = 'large';		// The WP "size" to use for the large image
$settings['width'] = 606;				// Default width
$settings['height'] = 0;				// Default height
$settings['use_height'] = false;		// Use height value
$settings['post_id'] = get_the_ID();	// Post ID to get the attachments for
$settings['embed'] = '';				// Determine whether or not the post has an embedded video
$settings['use_embed'] = false;			// Determine whether to display the embed code, if one exists, in place of the post gallery/image.

$dimensions = woo_post_gallery_dimensions();

if ( is_array( $dimensions ) && isset( $dimensions['width'] ) ) {
	$settings['width'] = $dimensions['width'];
}
if ( is_array( $dimensions ) && isset( $dimensions['height'] ) ) {
	$settings['height'] = $dimensions['height'];
}
if ( $settings['height'] > 0 ) {
	$settings['use_height'] = true;
}

if ( isset( $post_meta['_enable_video'] ) && ( $post_meta['_enable_video'][0] == 'true' ) ) {
	$settings['use_embed'] = true;
}

$embed_args = 'width=' . ( $settings['width'] - 6 ); // Cater for the 3px border.

// Look for a video embed code.
$embed = woo_embed( $embed_args );
if ( ( $embed != '' ) && ( $settings['use_embed'] == true ) ) {
	$settings['width'] = $settings['width'] - 6; // Cater for the 3px border.
	$settings['embed'] = $embed;
	$container_class = 'video_container'; // Change the container class to be specific to videos.
}

// Toggle usage of TimThumb.
if ( get_option( 'woo_resize' ) == 'true' ) { $settings['use_timthumb'] = true; }

// Allow child themes and plugins to filter these settings on a per-post basis.
$settings = apply_filters( 'woo_post_gallery_settings', $settings, $settings['post_id'] );

/*-----------------------------------------------------------------------------------*/
/* Process code - Setup the query arguments and get the attachmented images. */
/*-----------------------------------------------------------------------------------*/

$images = array(); // Default value, to prevent images from displaying if we have an embedded video.
if ( ( $settings['embed'] != '' ) && ( $settings['use_embed'] == true ) ) {
	
	$html = $settings['embed'];
	
} else {

	$query_args = array(
						'post_parent' => $settings['post_id'], 
						'numberposts' => $settings['limit'], 
						'post_type' => 'attachment', 
						'post_mime_type' => 'image', 
						'order' => 'DESC', 
						'orderby' => 'menu_order date'
						);
						
	$query_args = apply_filters( 'woo_post_gallery_query_args', $query_args );
	
	$images = get_children( $query_args );

} // End IF Statement

/*-----------------------------------------------------------------------------------*/
/* Generate the HTML to be outputted, if applicable. */
/*-----------------------------------------------------------------------------------*/

if ( ! empty( $images ) ) {

	$counter = 0;
	
	$main_css_class = '';
	
	$slide_container_class = 'slide';
	
	if ( count( $images ) == 1 ) {
		$slide_container_class = 'image';
		$main_css_class = ' single-image';
	}
	
	foreach ( $images as $k => $img ) {
		$counter++;
		
		$caption = '';
		$title = '';
		$src = '';
		$img_url = '';
		$img_atts = ' class="single-photo"';
		
		// Setup the caption text, with a filter.
		if ( $img->post_excerpt != '' ) {
			$caption = apply_filters( 'woo_post_gallery_image_caption', '<span class="photo-caption">' . $img->post_excerpt . '</span>', $img->ID );
			
			$img_atts .= ' alt="' . strip_tags( $caption ) . '"';
			$title = ' title="' . strip_tags( $caption ) . '"';
		}
		
		// Setup the image source, with a filter.
		$src = wp_get_attachment_image_src( $img->ID, $settings['photo_size'], true );
		
		// Setup "template" for displaying each slide.
		$before = '<div class="' . $slide_container_class . '"><a href="'. $src[0] .'" rel="lightbox-group" class="thickbox"' . $title . '>';
		$after = '</a>' . "\n" . $caption . '</div><!--/.slide-->' . "\n";
		
		if ( $settings['use_timthumb'] == 'true' ) {
		
			// Setup arguments to be used with TimThumb, with a filter.
			$timthumb_args = 'src=' . $src[0] . '&width=' . $settings['width'] . '&q=90&zc=1';
			if ( $settings['use_height'] == true ) { $timthumb_args .= '&height=' . $settings['height']; }
			
			$timthumb_args = apply_filters( 'woo_post_gallery_timthumb_args', $timthumb_args, $img->ID );
		
			// Retrieve image crop alignment value.
			$crop_align = get_post_meta( $post->ID, '_image_alignment', true );
			if ( in_array( $crop_align, array( 'c', 't', 'b', 'l', 'r' ) ) ) { $crop_align = '&alignment=' . $crop_align; } else { $crop_align = ''; }
		
			$html .= woo_image( $timthumb_args . '&return=true&link=img' . $crop_align );
		
		} else {
		
			$img_url = $src[0];
			$img_atts .= ' width="' . $settings['width'] . '"';
			if ( $settings['use_height'] == true ) {
				$img_atts .= ' height="' . $settings['height'] . '"';
			}
			
			// Add the HTML to our main HTML to be outputted.
			$html .= $before . '<img src="' . $img_url . '"' . $img_atts . ' />' . $after;
		
		} // End IF Statement

	}

} // End IF Statement

if ( $html != '' ) {
?>
	<!-- Start Photo Slider -->
	<div id="post-gallery" class="gallery<?php echo $main_css_class; ?>">
	    <div class="<?php echo $container_class; ?>">
	    	<?php echo $html; // This will show the large photo in the slider ?>
	    </div>
	<div class="fix"></div>
	</div>
	<!-- End Photo Slider -->
<?php
} // End IF Statement
?>
<?php if ( $counter > 1 ) { ?>
<script type="text/javascript">
jQuery(window).load(function(){
    jQuery( '#post-gallery' ).slides({
		generateNextPrev: true,
		generatePagination: false, 
		slidesLoaded: function () {
			var sliderHeight = parseInt( jQuery( '#post-gallery .slide:first' ).height() );
			if ( sliderHeight == 0 ) { sliderHeight = 300; }
			jQuery( '#post-gallery .slides_container' ).animate({ height: sliderHeight }, 500, function () {
				jQuery( this ).fadeIn( 'fast', function () {
					woo_adjust_column_margins();
				});
			});
		}
  	});
});
</script>
<?php } else { ?>
<script type="text/javascript">
jQuery(window).load(function(){
	jQuery( '#post-gallery .slides_container' ).fadeIn( 'fast', function () {
		woo_adjust_column_margins();
	});
});
</script>
<?php } // End IF Statement ?>