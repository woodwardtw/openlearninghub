<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: A custom WooThemes Gallery widget.
Date Created: 2011-06-22.
Last Modified: 2011-06-22.
Author: Cobus and Matty.
Since: 1.0.0


TABLE OF CONTENTS

- function (constructor)
- function widget ()
- function update ()
- function form ()

- Register the widget on `widgets_init`.

-----------------------------------------------------------------------------------*/

class Woo_Widget_Gallery extends WP_Widget {

	var $defaults;

	/*----------------------------------------
	  Constructor.
	  ----------------------------------------
	  
	  * The constructor. Sets up the widget.
	----------------------------------------*/

	function Woo_Widget_Gallery () {
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_woo_gallery', 'description' => __( 'A customised gallery widget to display the featured images from a specified number of recent posts.', 'woothemes' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'woo_gallery' );

		/* Create the widget. */
		$this->__construct( 'woo_gallery', __('Woo - Gallery', 'woothemes' ), $widget_ops, $control_ops );
		
		$this->defaults = array(
						'title' => __( 'Gallery', 'woothemes' ), 
						'limit' => 5, 
						'orderby' => 'post_date', 
						'width' => 200
					);
		
	} // End Constructor

	/*----------------------------------------
	  widget()
	  ----------------------------------------
	  
	  * Displays the widget on the frontend.
	----------------------------------------*/

	function widget( $args, $instance ) {  
		
		$query_args = array(
							'post_type' => 'post', 
							'meta_key' => 'image', 
							'numberposts' => $instance['limit'], 
							'orderby' => $instance['orderby'], 
							'order' => $instance['order']
							);
		
		// Exclude the current post/page if we're on it.
		if ( is_singular() ) {
			global $post;
			
			$query_args['exclude'] = $post->ID;
		}
							
		$images = get_posts( $query_args );
		
		$html = '';
		
		/* Set up some default widget settings. */
		$defaults = $this->defaults;

		$instance = wp_parse_args( (array) $instance, $defaults );
		
		extract( $args, EXTR_SKIP );
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base );
			
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) {
		
			echo $before_title . $title . $after_title;
		
		} // End IF Statement
		
		/* Widget content. */
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_gallery_top' );
		
		// Load the main gallery HTML.
		if ( is_array( $images ) && count( $images ) > 0 ) {
		
			add_action( 'wp_footer', array( $this, 'enqueue_widget_js' ) ); // Load the widget JavaScript into the footer.
		
			$html .= '<div id="gallery" class="gallery">' . "\n";
				$html .= '<div class="slides_container">' . "\n";
				
				foreach ( $images as $k => $v ) {
					$image = woo_image( 'key=image&return=true&width=' . $instance['width'] . '&id=' . $v->ID );
					
					if ( $image != '' ) {
						$html .= '<div class="slide">' . "\n";
							$html .= $image;
						$html .= '</div><!--/.slide-->' . "\n";
					}
				}
				
				$html .= '</div><!--/.slides_container-->' . "\n";
			$html .= '</div><!--/#gallery .gallery-->' . "\n";
		
			echo $html;
		
		} // End IF Statement
		
		// Add actions for plugins/themes to hook onto.
		do_action( 'widget_woo_gallery_bottom' );

		/* After widget (defined by themes). */
		echo $after_widget;

	} // End widget()

	/*----------------------------------------
	  update()
	  ----------------------------------------
	
	* Function to update the settings from
	* the form() function.
	
	* Params:
	* - Array $new_instance
	* - Array $old_instance
	----------------------------------------*/
	
	function update ( $new_instance, $old_instance ) {
		
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		
		$instance['limit'] = intval( $new_instance['limit'] );
		if ( $instance['limit'] == 0 ) { $instance['limit'] = 5; }
		
		$instance['orderby'] = strip_tags( $new_instance['orderby'] );
		$instance['order'] = strip_tags( $new_instance['order'] );
		
		$instance['width'] = intval( $new_instance['width'] );
		if ( $instance['width'] == 0 ) { $instance['width'] = 200; }
		
		return $instance;
		
	} // End update()

   /*----------------------------------------
	 form()
	 ----------------------------------------
	  
	  * The form on the widget control in the
	  * widget administration area.
	  
	  * Make use of the get_field_id() and 
	  * get_field_name() function when creating
	  * your form elements. This handles the confusing stuff.
	  
	  * Params:
	  * - Array $instance
	----------------------------------------*/

   function form( $instance ) {       
   
       /* Set up some default widget settings. */
		$defaults = $this->defaults;

		$instance = wp_parse_args( (array) $instance, $defaults );
?>
       <!-- Widget Title: Text Input -->
       <p>
	   	   <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
       </p>
       <!-- Widget Posts Limit: Text Input -->
       <p>
	   	   <label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'How many posts to display:', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'limit' ); ?>"  value="<?php echo $instance['limit']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" />
       </p>
       <!-- Widget Posts Order By: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( 'Order Posts By:', 'woothemes' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'orderby' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'orderby' ); ?>">
				<option value="ID"<?php selected( $instance['orderby'], 'ID' ); ?>><?php _e( 'Post ID', 'woothemes' ); ?></option>
				<option value="post_title"<?php selected( $instance['orderby'], 'post_title' ); ?>><?php _e( 'Post Title', 'woothemes' ); ?></option>
				<option value="post_date"<?php selected( $instance['orderby'], 'post_date' ); ?>><?php _e( 'Post Date', 'woothemes' ); ?></option>     
			</select>
		</p>
		<!-- Widget Posts Order Direction: Select Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Order Direction:', 'woothemes' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'order' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>">
				<option value="DESC"<?php selected( $instance['order'], 'DESC' ); ?>><?php _e( 'Descending', 'woothemes' ); ?></option>
				<option value="ASC"<?php selected( $instance['order'], 'ASC' ); ?>><?php _e( 'Ascending', 'woothemes' ); ?></option>   
			</select>
		</p>
		<!-- Widget Posts Image Width: Text Input -->
       <p>
	   	   <label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Image Width:', 'woothemes' ); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name( 'width' ); ?>"  value="<?php echo $instance['width']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" />
       </p>
<?php
	} // End form()
	
	/*----------------------------------------
	 enqueue_widget_js()
	 ----------------------------------------
	  
	  * Load the widget's JavaScript.
	----------------------------------------*/
	
	function enqueue_widget_js () {
	
		$html = '';
		
		$html .= '<script type="text/javascript">
		jQuery(window).load(function(){
		  jQuery(function(){
		    jQuery("#gallery").slides({
				generateNextPrev: true,
				generatePagination: false
		  	});
		  });
		});
		</script>' . "\n";
		
		echo $html;
	
	} // End enqueue_widget_js()
	
} // End Class

/*----------------------------------------
  Register the widget on `widgets_init`.
  ----------------------------------------
  
  * Registers this widget.
----------------------------------------*/

add_action( 'widgets_init', create_function( '', 'return register_widget("Woo_Widget_Gallery");' ), 1 ); 
?>