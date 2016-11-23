<?php
/*
Template Name: Thought Vectors Guide

*/
?>


<?php get_header('tvguide'); ?>
<?php global $woo_options; ?>
    
    <?php if ( $woo_options[ 'woo_breadcrumbs_show' ] == 'true' ) { ?>
	    <div id="breadcrumbs">
	    	<?php woo_breadcrumbs(); ?>
	    </div><!--/#breadcrumbs -->
	<?php } ?>
    
    <div id="content" <?php woo_section_class( 'content', 'col-full special-single' ); ?>>
    
		<div id="main" class="fullwidth">
	<table id="course-table" class="table table-striped">

	<thead>
		<tr>
			<th>twitter</th>
			<th>#thoughtvectors tweets</th>
			<th>Blog</th>
			<th>Role</th>
			<th>Section</th>
		</tr>
	</thead>
	<tbody>

	</tbody>
	<tfoot>
		<tr>
			<th>twitter</th>
			<th>#thoughtvectors tweets</th>
			<th>Blog</th>
			<th>Role</th>
			<th>Section</th>
		</tr>
		</tfoot>
	</table>	           

        
		</div><!-- /#main -->


    </div><!-- /#content -->
		
<?php get_footer(); ?>