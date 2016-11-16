<?php 
	// Don't display sidebar if full width
	global $woo_options;
	if ( $woo_options['woo_layout'] != 'layout-full' ) {
		if ( woo_active_sidebar( 'primary' ) ) {
?>
		<div id="sidebar" class="col-right">
		
    		<div class="primary">
				<?php woo_sidebar( 'primary' ); ?>		           
			</div>
			
		</div><!-- /#sidebar -->
<?php
		}
	}
?>