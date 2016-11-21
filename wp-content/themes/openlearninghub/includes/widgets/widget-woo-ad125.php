<?php
/*---------------------------------------------------------------------------------*/
/* Adspace 125x125 Widget */
/*---------------------------------------------------------------------------------*/

class Woo_Ad125Widget extends WP_Widget {

	function Woo_Ad125Widget() {
		$widget_ops = array('description' => 'Use this widget to add 125x125 Ads as a widget.' );
		parent::__construct(false, __('Woo - Ads 125x125', 'woothemes'),$widget_ops);      
	}

	function widget($args, $instance) {  
		 
		$number = $instance['number']; if ($number == 0) $number = 1;
		if ($number == 0) $number = 1;
		$img_url = array();
		$dest_url = array();
		
		$numbers = range(1,$number); 
		$counter = 0;
		
		if (get_option('woo_ads_rotate') == "true") {
			shuffle($numbers);
		}
		?>
		<div class="ads-125 widget">
		<?php
			foreach ($numbers as $number) {	
				$counter++;
				$img_url[$counter] = get_option('woo_ad_image_'.$number);
				$dest_url[$counter] = get_option('woo_ad_url_'.$number);
			
		?>
		        <a href="<?php echo "$dest_url[$counter]"; ?>"><img src="<?php echo "$img_url[$counter]"; ?>" alt="Ad" /></a>
		<?php } ?>
		</div>
		<!--/ads -->
		<?php
	}

	function update($new_instance, $old_instance) {                
		return $new_instance;
	}

	function form($instance) {        
		$number = esc_attr($instance['number']);
		?>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of ads (1-6):','woothemes'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('number'); ?>" value="<?php echo $number; ?>" class="widefat" id="<?php echo $this->get_field_id('number'); ?>" />
        </p>
        <?php
	}
} 

register_widget('Woo_Ad125Widget');
?>