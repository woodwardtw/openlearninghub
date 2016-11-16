<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">

<title><?php woo_title(); ?></title>
<?php woo_meta(); ?>
<?php global $woo_options; ?>

<link rel="stylesheet" type="text/css" href="<?php bloginfo( 'stylesheet_url' ); ?>" media="screen" />
<link href="http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT:regular,italic" rel="stylesheet" type="text/css" />
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php if ( $woo_options[ 'woo_feed_url' ] ) { echo $woo_options[ 'woo_feed_url' ]; } else { echo get_bloginfo_rss( 'rss2_url' ); } ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
      
<?php wp_head(); ?>
<?php woo_head(); ?>

</head>

<body <?php body_class(); ?>>
<?php woo_top(); ?>

<div id="wrapper">

	<?php if ( function_exists( 'has_nav_menu') && has_nav_menu( 'top-menu' ) ) { ?>
	
	<div id="top" <?php if ($woo_options['woo_header_align'] == 'alignleft' ) { ?>class="left"<?php } ?>>
		<div class="col-full">
			<?php wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav', 'menu_class' => 'nav fl', 'theme_location' => 'top-menu' ) ); ?>
		</div>
	</div><!-- /#top -->
	
    <?php } ?>
           
    <?php
	    $header_align = $woo_options['woo_header_align'];
	    $header_left_layout = $woo_options['woo_header_left_layout'];
	?>
           
	<div id="header" class="col-full <?php if ($header_align == 'alignleft' ) {  echo "left"; } if($header_left_layout == "headlines") { echo " headlines-layout"; } ?>">
 		
 		<?php if((($header_align == 'alignleft') && ($header_left_layout == 'search-subscribe')) || ($header_align == 'aligncenter') ) { ?>
 		
 		<div class="rss">
            <a class="button" href="<?php if ( $woo_options[ 'woo_feed_url' ] ) { echo $woo_options[ 'woo_feed_url' ]; } else { echo get_bloginfo_rss( 'rss2_url' ); } ?>"><span><?php _e( 'Subscribe to RSS', 'woothemes' ); ?></span></a>
        </div><!-- /.rss -->
        
        <?php } ?>
 		 
		<div id="logo">
	       
		<?php if ($woo_options['woo_texttitle'] != 'true' ) { $logo = $woo_options['woo_logo']; ?>
			<a href="<?php echo home_url( '/' ); ?>" title="<?php bloginfo( 'description' ); ?>">
				<img src="<?php if ($logo) echo $logo; else { echo get_template_directory_uri(); ?>/images/logo<?php if ( $woo_options['woo_header_align'] == 'alignleft' ) { ?>-left<?php } ?>.png<?php } ?>" alt="<?php bloginfo( 'name' ); ?>" />
			</a>
        <?php } ?> 
        
        <?php if( is_singular() && ! is_front_page() ) { ?>
			<span class="site-title"><a href="<?php echo home_url( '/' ); ?>"><?php bloginfo( 'name' ); ?></a></span>
        <?php } else { ?>
			<h1 class="site-title"><a href="<?php echo home_url( '/' ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
        <?php } ?>
			<span class="site-description"><?php bloginfo( 'description' ); ?></span>
	      	
		</div><!-- /#logo -->
		
		<?php if( ( ( $header_align == 'alignleft' ) && ( $header_left_layout == 'search-subscribe' ) ) || ( $header_align == 'aligncenter' ) ) { ?>
		
		<div class="header-search">
		
			<form method="get" class="searchform" action="<?php echo home_url( '/' ); ?>" >
    		    <input type="text" class="field s" name="s" value="<?php esc_attr_e( 'Search Keywords...', 'woothemes' ); ?>" onfocus="if (this.value == '<?php esc_attr_e( 'Search Keywords...', 'woothemes' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php esc_attr_e( 'Search Keywords...', 'woothemes' ); ?>';}" />
    		    <input type="image" src="<?php echo get_template_directory_uri(); ?>/images/ico-search.png" class="search-submit" name="submit" value="<?php esc_attr_e( 'Go', 'woothemes' ); ?>" />
    		</form>    
    		<div class="fix"></div>
				
		</div><!-- /.header-search -->
		
		<?php } ?>
		<?php
			if ( ( $header_align == 'alignleft' ) && ( $header_left_layout == 'headlines' ) ) {
				get_template_part( 'includes/header-headlines' );
			}
		?>
 
	</div><!-- /#header -->
    
	<div id="navigation" class="col-full <?php if ($woo_options['woo_header_align'] == 'alignleft' ) { ?>left<?php } ?>">
		<?php
		if ( function_exists( 'has_nav_menu') && has_nav_menu( 'primary-menu') ) {
			wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'main-nav', 'menu_class' => 'nav fl', 'theme_location' => 'primary-menu' ) );
		} else {
		?>
        <ul id="main-nav" class="nav fl">
			<?php 
        	if ( isset($woo_options[ 'woo_custom_nav_menu' ]) AND $woo_options[ 'woo_custom_nav_menu' ] == 'true' ) {
        		if ( function_exists( 'woo_custom_navigation_output') )
					woo_custom_navigation_output();
			} else { ?>
	            <?php if ( is_page() ) $highlight = "page_item"; else $highlight = "page_item current_page_item"; ?>
	            <li class="<?php echo $highlight; ?>"><a href="<?php echo home_url( '/' ); ?>"><?php _e( 'Home', 'woothemes' ); ?></a></li>
	            <?php 
	    			wp_list_pages( 'sort_column=menu_order&depth=6&title_li=&exclude=' ); 
			}
			?>
        </ul><!-- /#nav -->
        <?php } ?>
        
	</div><!-- /#navigation -->