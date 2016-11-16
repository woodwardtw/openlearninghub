/*-------------------------------------------------------------------------------------

FILE INFORMATION

Description: Theme-specific JavaScript calls.
Date Created: 2011-06-14.
Author: Cobus, Matty.
Since: 1.0.0


TABLE OF CONTENTS

- Slider Setup on window.load

- Logic for the "close" button on video slide captions
- "Recent News" Category Switcher Using AJAX
- "Recent News" Category Switcher
- Center aligned menu's - Drop down positioning
- Archive layouts - add '.last' for margin removal
- Add <span> to tags to apply backgropund image for hiding/fading effect
- Hide/Show labels on commentform
- Add alt-row styling to tables
- Superfish navigation dropdown

- function getSlideTitle()
-- Add "Next" and "Previous" slide title and category next to appropriate button
- function woo_show_loader()
-- Add a loading DIV in the appropriate element.
- function woo_remove_loader()
-- Remove a specified loading DIV.
- function woo_adjust_column_margins()
-- Adjust column margins to vertically align correctly.

-------------------------------------------------------------------------------------*/

jQuery(document).ready(function(){

/*-----------------------------------------------------------------------------------*/
/* Logic for the "close" button on video slide captions */
/*-----------------------------------------------------------------------------------*/

	if ( jQuery( '#slides .btn_close' ).length ) {
		jQuery( '#slides .btn_close' ).removeClass( 'hide' );
		jQuery( '#slides .btn_close' ).click( function ( e ) {
			jQuery( this ).parents( '.slide-content' ).slideUp( 200 );
			return false;
		});
	}

/*-----------------------------------------------------------------------------------*/
/* "Recent News" Category Switcher Using AJAX */
/*-----------------------------------------------------------------------------------*/

	if ( jQuery( '#recent-news-filter ul li' ).length ) {
		jQuery( '#recent-news-filter ul a' ).live( 'click', function ( e ) {
			jQuery( '#recent-news-filter a' ).removeClass( 'noclick' );
			
			if ( jQuery( this ).hasClass( 'noclick' ) ) { return false; }
			
			jQuery( '#recent-news-filter a' ).addClass( 'noclick' );
			
			var activeCategory = jQuery( '.active' ).parent( 'li' ).attr( 'id' );
		
			var url = jQuery( '#recent-news-filter ul li#latest a' ).attr( 'href' );
			var category_id = jQuery( this ).parent( 'li' ).attr( 'id' );
			
			if ( category_id == 'latest' ) {} else { category_id = category_id.replace( 'category-', '' ); }

			// Determine the delimiter so we don't break our URLs.
			var delimiter = '?';			
			if ( url.indexOf( '?' ) > 0 ) { delimiter = '&'; }
			
			url = url + delimiter + 'current_category=' + category_id;

			var sectionToLoad = '#recent-news';
		
			if ( jQuery( '.woo-loader' ).length > 0 ) { return false; }
		
			if ( url ) {
			
				jQuery( this ).parents( 'ul' ).find( '.active' ).removeClass( 'active' );
				jQuery( this ).addClass( 'active' );
			
				jQuery( sectionToLoad ).fadeTo( 'slow', 0, function () {
					
					woo_show_loader( 'woo-loader' );
					
					jQuery( '.woo-loader' ).css( 'width', jQuery( '#recent-news' ).css( 'width' ) );
					
					jQuery.ajax({
						url: url, 
						success: function ( data ) {
							var content = jQuery( data ).find( sectionToLoad ).html();
							if ( content.length ) {
								jQuery( sectionToLoad ).html( content ).fadeTo( 'slow', 1 );
							}
							jQuery( '#recent-news-filter a' ).removeClass( 'noclick' );
						}, 
						error: function ( jqXHR, textStatus, errorThrown ) {}, 
						complete: function ( jqXHR, textStatus ) { jQuery( '#recent-news-filter a' ).removeClass( 'noclick' ); woo_remove_loader( 'woo-loader' ); }
					});
				
				});
			
			}
			return false;
		});
	}

/*-----------------------------------------------------------------------------------*/
/* Center aligned menu's - Drop down positioning */
/*-----------------------------------------------------------------------------------*/

	jQuery( '#top:not(.left) .nav li').each(function(){
	
		li_width = jQuery(this).width();
		ul_width = jQuery(this).children('ul').width()
		li_width = li_width + 10;
		li_width = ul_width - li_width;
		li_width = li_width / 2;
		
		jQuery(this).children('ul').css('margin-left', -li_width);
	
	});
	jQuery( '#navigation:not(.left) .nav li').each(function(){
	
		li_width = jQuery(this).width();
		li_width = 190 -3 - li_width;
		li_width = li_width / 2;
		
		jQuery(this).children('ul').css('margin-left', -li_width);
	
	});


/*-----------------------------------------------------------------------------------*/
/* Archive layouts - add '.last' for margin removal */
/*-----------------------------------------------------------------------------------*/

	jQuery( 'body.single .col-left .archive-layout .post:nth-child(3n + 2), body.single .fullwidth .archive-layout .post:nth-child(4n + 3), body.page:not(.page-template-template-blog-php) .col-left .archive-layout .post:nth-child(3n + 2), body.page .fullwidth .archive-layout .post:nth-child(4n + 3)' ).addClass('last');

/*-----------------------------------------------------------------------------------*/
/* Single page columns. Negative margin trickery. */
/*-----------------------------------------------------------------------------------*/
	
	woo_adjust_column_margins();

/*-----------------------------------------------------------------------------------*/
/* Hide/Show labels on commentform */
/*-----------------------------------------------------------------------------------*/

	jQuery('#commentform input.txt').each(function(){
		
		var input_value = jQuery(this).val();
		
		if (input_value != '') {
			jQuery(this).next('label').hide();
		}
	
	});

	jQuery('#commentform input.txt').focus(function(){
		
		var input_value = jQuery(this).val();
		
		if (input_value == '') {
			jQuery(this).next('label').hide();
		}
	
	});
	
	jQuery('#commentform input.txt').blur(function(){
		
		var input_value = jQuery(this).val();
		
		if (input_value == '') {
			jQuery(this).next('label').show();
		}
	
	});


/*-----------------------------------------------------------------------------------*/
/* Add alt-row styling to tables */
/*-----------------------------------------------------------------------------------*/

	jQuery( '.entry table tr:odd').addClass( 'alt-table-row' );

/*-----------------------------------------------------------------------------------*/
/* Superfish navigation dropdown */
/*-----------------------------------------------------------------------------------*/

if(jQuery().superfish) {
		jQuery( 'ul.nav').superfish({
			delay: 200,
			animation: {opacity:'show', height:'show'},
			speed: 'fast',
			dropShadows: false
		});
}

	jQuery('.column > p:first-child').each ( function () {
		if ( jQuery( this ).html() == '' ) {
			jQuery( this ).remove();
		}
	});

}); // End jQuery()

/*-----------------------------------------------------------------------------------*/
/* - function getSlideTitle() */
/*-----------------------------------------------------------------------------------*/

function getSlideTitle ( slideNumber ) {
		
		var currentIndex = slideNumber;
		var nextIndex = slideNumber + 1;
		var prevIndex = slideNumber - 1;
		var totalSlides = jQuery( '#slides .slide' ).length;
		
		if ( nextIndex > totalSlides ) { nextIndex = 1; } // If there's no next slide, use the first one.
		if ( prevIndex <= 0 ) { prevIndex = totalSlides; } // If there's no previous slide, use the last one.
		
		var currentSlideObj = jQuery( '#slides .slide#slide-' + slideNumber );
		var nextSlideObj = jQuery( '#slides #slide-' + nextIndex );
		var prevSlideObj = jQuery( '#slides #slide-' + prevIndex );
		
		var nextSlideTitle = nextSlideObj.find( '.title' ).html();
		
		var prevSlideTitle = prevSlideObj.find( '.title' ).html();
		
		jQuery( '#slides span.prev-text' ).html( '<span class="title">' + prevSlideTitle + '</span>' );
		jQuery( '#slides span.next-text' ).html( '<span class="title">' + nextSlideTitle + '</span>' );
		
} // End getSlideTitle()

/*-----------------------------------------------------------------------------------
  - function woo_show_loader()
-----------------------------------------------------------------------------------*/

function woo_show_loader( loaderId ) {

	var loadingDiv = jQuery( '<div></div>' ).attr( 'id', loaderId ).addClass( 'loading' ).addClass( loaderId ).html( '<span>Loading</span>' ).hide();

	jQuery( '#main' ).before( loadingDiv );
	jQuery( loadingDiv ).fadeTo( 'slow', 1 );

} // End woo_show_loader()

/*-----------------------------------------------------------------------------------
  - function woo_remove_loader()
-----------------------------------------------------------------------------------*/

function woo_remove_loader( loaderId ) {

	jQuery( '#' + loaderId ).fadeTo( 'slow', 0, function () {
		jQuery( this ).remove();
	});

} // End woo_show_loader()

/*-----------------------------------------------------------------------------------
  - function woo_adjust_column_margins()
-----------------------------------------------------------------------------------*/

function woo_adjust_column_margins () {
	var title_height = jQuery('.special-single .title-media-block').height();

	if(jQuery('.special-single .title-media-block .intro-paragraph').length > 0)  {
		title_height += 30;
	}
	
	
	if(jQuery('#content.special-single.layout-3col').length > 0 && jQuery('.special-single .title-media-block.span1').length > 0 )  { 
		jQuery('.column-02, .column-03').css('margin-top', -title_height);
		
	} else if (jQuery('#content.special-single.layout-3col').length > 0 && jQuery('.special-single .title-media-block.span2').length > 0 )  {
		jQuery('.column-03').css('margin-top', -title_height);
	}
	
	if(jQuery('#content.special-single.layout-2colA').length > 0 && jQuery('.special-single .title-media-block.span1').length > 0 )  { 
		jQuery('.column-02').css('margin-top', -title_height);
	}
	
	if(jQuery('#content.special-single.layout-2colB').length > 0 && jQuery('.special-single .title-media-block.span1').length > 0 )  { 
		jQuery('.column-02').css('margin-top', -title_height);
	}
	
	if(jQuery('#content.special-single.layout-2colC').length > 0 && jQuery('.special-single .title-media-block.span1').length > 0 )  { 
		jQuery('.column-02').css('margin-top', -title_height);
	}
} // End woo_adjust_column_margins()