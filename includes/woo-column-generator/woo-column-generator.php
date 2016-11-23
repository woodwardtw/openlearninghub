<?php
/*-----------------------------------------------------------------------------------

CLASS INFORMATION

Description: WooThemes column generator.
Date Created: 2011-05-31.
Author: Matty.
Since: 1.0.0


TABLE OF CONTENTS

- var $template_url
- var $column_counter
- var $count_column
- var $total_columns
- var $used_numbers
- var $current_column

- Constructor Function
- function init()
- function filter_mce_buttons()
- function filter_mce_external_plugins()
- function create_columns()
- function create_columns_callback()
- function tag_unautop()

INSTANTIATE CLASS

-----------------------------------------------------------------------------------*/

class WooThemes_Column_Generator {

/*-----------------------------------------------------------------------------------
  Class Variables
  
  * Setup of variable placeholders, to be populated when the constructor runs.
-----------------------------------------------------------------------------------*/

	var $template_url;
	var $column_counter;
	var $count_column;
	var $total_columns;
	var $used_numbers;
	var $current_column;

/*-----------------------------------------------------------------------------------
  Class Constructor
  
  * Constructor function. Sets up the class and registers variable action hooks.
-----------------------------------------------------------------------------------*/

	function WooThemes_Column_Generator () {
	
		$this->template_url = get_template_directory_uri();
		$this->column_counter = 2;
		$this->count_column = true;
		$this->total_columns = 1;
		$this->current_column = 2;
		$this->used_numbers = array();
	
		// Register the necessary actions on `admin_init`.
		add_action( 'admin_init', array( &$this, 'init' ) );
		
		// Create columns in the content.
		add_filter( 'the_content', array( &$this, 'tag_unautop' ) );
		add_filter( 'the_content', array( &$this, 'create_columns' ) );
	
	} // End WooThemes_Shortcode_Generator()

/*-----------------------------------------------------------------------------------
  init()
  
  * This guy runs the show. Rocket boosters... engage!
-----------------------------------------------------------------------------------*/

	function init() {
	
		if ( ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) && get_user_option( 'rich_editing') == 'true' )  {
		  	
		  	// Add the tinyMCE buttons and plugins.
			add_filter( 'mce_buttons', array( &$this, 'filter_mce_buttons' ) );
			add_filter( 'mce_external_plugins', array( &$this, 'filter_mce_external_plugins' ) );
			
		} // End IF Statement
	
	} // End init()

/*-----------------------------------------------------------------------------------
  filter_mce_buttons()
  
  * Add our new button to the tinyMCE editor.
-----------------------------------------------------------------------------------*/
	
	function filter_mce_buttons( $buttons ) {
		
		array_push( $buttons, '|', 'WooThemesColumnGenerator' );
		
		return $buttons;
		
	} // End filter_mce_buttons()

/*-----------------------------------------------------------------------------------
  filter_mce_external_plugins()
  
  * Add functionality to the tinyMCE editor as an external plugin.
-----------------------------------------------------------------------------------*/
	
	function filter_mce_external_plugins( $plugins ) {
		
        $plugins['WooThemesColumnGenerator'] = $this->template_url . '/includes/woo-column-generator/js/editor_plugin.js';
        
        return $plugins;
        
	} // End filter_mce_external_plugins()
	
/*-----------------------------------------------------------------------------------
  create_columns()
  
  * Creates columns in the content by replacing <!--column--> tags.
-----------------------------------------------------------------------------------*/

	function create_columns ( $content ) {
		
		if ( in_the_loop() ) {
		
			global $post;
		
			$custom_meta = get_post_custom( $post->ID );
		
			$columns_in_layout = 2;
			
			if ( ( array_key_exists( '_column_layout', (array) $custom_meta ) ) && 
				 ( $custom_meta['_column_layout'][0] == 'layout-3col' ) ) {
				$columns_in_layout = 3;
			}
			
			$this->columns_in_layout = $columns_in_layout;
		
			$pattern = '/<!--column(.*?)?-->/';
			
			preg_match_all( $pattern, $content, $matches );
			
			if ( is_array( $matches ) && is_array( $matches[0] ) ) {
				$this->total_columns = count( $matches[0] );
			}
			
			if ( preg_match( $pattern, $content, $matches ) ) {
				$content = preg_replace_callback( $pattern, array( &$this, 'create_columns_callback' ), $content );
			}
		
		}
		
		return $content;
	
	} // End create_columns()
	
/*-----------------------------------------------------------------------------------
  create_columns_callback()
  
  * Creates columns in the content by replacing <!--column--> tags.
-----------------------------------------------------------------------------------*/

	function create_columns_callback ( $matches ) {
		
		$column_number = $this->current_column;
		
		if ( $column_number < 10 ) { $column_number = '0' . $column_number; }
		
		$fix = '';
		$css_class = '';
		
		if ( ( ( $this->current_column - 1 ) % $this->columns_in_layout == 0 ) && ( $this->current_column > 1 ) ) {
			$fix = '<div class="fix column-clear"></div><!--/.fix column-clear-->' . "\n";
		}
		
		if ( ( ( $this->current_column ) % $this->columns_in_layout == 0 ) && ( $this->current_column > 1 ) ) {
			$css_class = ' last';
		}
		
		$pattern = '/<!--column(.*?)?-->/';
		$replacement = '</div>';
		
		$replacement .= $fix;
		
		$replacement .= '<div class="column column-' . $column_number . $css_class . '">';

		$content = $replacement;
		
		$this->current_column++;
		
		return $content;
	
	} // End create_columns_callback()

/*-----------------------------------------------------------------------------------
  tag_unautop()
  
  * Attempts to prevent <p><!--column--></p>.
  
  * @since 2.9.0
  *
  * @param string $content The content.
  * @return string The filtered content.
-----------------------------------------------------------------------------------*/
	
	/**
	 * Don't auto-p wrap shortcodes that stand alone
	 *
	 * Ensures that shortcodes are not wrapped in <<p>>...<</p>>.
	 *
	 * @since 2.9.0
	 *
	 * @param string $pee The content.
	 * @return string The filtered content.
	 */
	function tag_unautop ( $content ) {
		global $shortcode_tags;
	
		if ( !empty($shortcode_tags) && is_array($shortcode_tags) ) {
			$tagnames = array_keys($shortcode_tags);
			$tagregexp = join( '|', array_map('preg_quote', $tagnames) );
			$content = preg_replace('/<p>\\s*?(<!--column-->)\\s*<\\/p>/s', '$1', $content );
		}
	
		return $content;
		
	} // End tag_unautop()

} // End Class

/*-----------------------------------------------------------------------------------
  INSTANTIATE CLASS
-----------------------------------------------------------------------------------*/

$woothemes_column_generator = new WooThemes_Column_Generator();
?>