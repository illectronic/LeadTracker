/*------------------------------------------------------------------------------------------------------
This file contains the JavaScript Code for Mobile Devices
--------------------------------------------------------------------------------------------------------
>>> TABLE OF CONTENTS:
--------------------------------------------------------------------------------------------------------

1.0 - Set Mobile Layout & Carousel
	1.1 - Set Mobile Layout
	1.2 - Set Mobile Carousel
	1.3 - Device Specific
2.0 - Sliding Panels
	2.1 - Header Buttons
	2.2 - Sliding Functionality
	2.3 - Menu Touch Swipe Functionality
	2.4 - Prevent Hover Event + Add Link Open Delay
	2.5 - Notifications Area
3.0 - Content
	3.1 - Members (Group Admin)
	3.2 - Search Input Field
	3.3 - Hide Profile and Group Buttons Area, when there are no buttons (ex: Add Friend, Join Group etc...)
	3.4 - Move the Messages Checkbox, below the Avatar
	3.5 - Repopulate dropdown text
	3.6 - Make Video Embeds Responsive - Fitvids.js
4.0 - Misc Functions
	4.1 - Check if on a Touch Device - isTouchDevice()
	4.2 - Force Touch Scrolling on Div - touchScroll(id)
	4.3 - Check if Element Exists - doesExist()
	4.4 - Better CSS support for jQuery
	4.5 - Detect Android OS Version

--------------------------------------------------------------------------------------------------------*/


var BuddyBossMobileJS = function( $, window, undefined ) {

	// GLOBALS *
	// ---------
		window.BuddyBoss = window.BuddyBoss || {};

		window.BuddyBoss.is_mobile = null;
		window.BuddyBoss.mobile_main_nav = 'closed';
		window.BuddyBoss.mobile_user_nav = 'closed';

		var
			$document         = $(document),
			$window           = $(window),
			$html             = $('html'),
			$body             = $('body'),
			bodyEl            = $body[0],
			$mobile_check     = $('#mobile-check').css({position:'absolute',top:0,left:0,width:'100%',height:1,zIndex:1}),
			mobile_width      = 0,
			is_mobile         = false,
			has_item_nav      = false,
			mobile_modified   = false,
			bb_swiper         = false,
			$main             = $('#main-wrap'),
			$inner            = $('#inner-wrap'),
			$buddypress       = $('#buddypress'),
			$item_nav         = $buddypress.find('#item-nav'),
			$selects          = $('select#activity-filter-by, select#whats-new-post-in, select#groups-order-by, select#members-friends, select#members-order-by, #buddypress #filter select'),
			$mobile_nav_wrap,
			$mobile_item_wrap,
			$mobile_item_nav,
			bb_mobile_overrides_interval,
			bb_mobile_overrides_function,
			bb_mobile_overrides_max = 5,
			bb_mobile_overrides_current = 0;

		/*------------------------------------------------------------------------------------------------------
		1.0 - Set Mobile Layout & Carousel
		--------------------------------------------------------------------------------------------------------*/

			if ( $mobile_check.is(':visible') ) {
				is_mobile = window.BuddyBoss.is_mobile = true;
			}

			// We need to manually trigger window scroll events
			$inner.scroll(function(e){
				$window.trigger('scroll', e);
			});

			function bb_check_is_mobile() {
				$mobile_check.remove().appendTo( $body );
				is_mobile = BuddyBoss.is_mobile = $mobile_check.is(':visible') || (mobile_width > 0 && $window.width() <= mobile_width);

				if ( is_mobile ) {
					mobile_width = $window.width();
				}
				// console.log( 'function bb_check_is_mobile()', is_mobile );
			}
			function bb_check_has_item_nav() {
				if ( $item_nav.doesExist() ) {
					has_item_nav = true;
				}
			}
			function bb_get_mobile_wrap_height() {
			  return $window.height() - 45;
			}
			bb_mobile_overrides_function = function() {

				// Sometimes the first repaint doesn't make #inner-wrap
				if ( bodyEl.className.indexOf('ps-active') === -1 && $inner.height() < bb_get_mobile_wrap_height() ) {
					// console.log( 'if ( $inner.height() < bb_get_mobile_wrap_height() )' );
				  bb_trigger_mobile_repaint();
				}

				// console.log( '' );
				// console.log( 'function bb_mobile_overrides_function()' );
				// console.log( '$inner.height()', $inner.height() );
				// console.log( '' );
				// console.log( 'bb_get_mobile_wrap_height()', bb_get_mobile_wrap_height() );
				// console.log( '' );
				// console.log( '====================================' );

				bb_mobile_overrides_current++;

				if ( bb_mobile_overrides_current < bb_mobile_overrides_max ) {
					// Set another timeout so we check every 250ms
					bb_mobile_overrides_interval = setTimeout( bb_mobile_overrides_function, 250 );
					// console.log( 'Mobile Override: Set Timeout for 250ms' );
				}
				else {
					// Set another timeout so we check every 1200ms because we've looped a
					// bunch of times already at 250ms. This will ensure we catch any
					// additional bugs and override them but not use as much CPU
					bb_mobile_overrides_interval = setTimeout( bb_mobile_overrides_function, 1200 );
					// console.log( 'Mobile Override: Resetting Timeout 1200ms' );
				}
			}
			function bb_trigger_mobile_repaint()
			{
				// console.log( 'function bb_trigger_mobile_repaint()' );
				bb_check_is_mobile();
				bb_check_has_item_nav();
				bb_set_mobile_layout();
				bb_set_mobile_carousel();
				// setTimeout( bb_set_mobile_layout(), 10 );
				// setTimeout( bb_set_mobile_carousel(), 10 );

				// Make sure we don't end up setting a bunch of timeouts
				clearTimeout( bb_mobile_overrides_interval );

				// Reset the number of overrides
				bb_mobile_overrides_current = 0;

				// If we're on mobile, let's add a 100ms timeout to override anything
				// that other plugin's end up hijacking
				//
				// TODO: Move this to buddypress.js, some of the overrides we need to do
				// regardlress of mobile or not
				// if ( is_mobile ) {
					bb_mobile_overrides_interval = setTimeout( bb_mobile_overrides_function, 100 );
				// }
			}

			$window.bind("load", function() {
				bb_trigger_mobile_repaint();
			});

			$window.resize(function() {
				bb_trigger_mobile_repaint();
			});

			$window.on('reset_carousel', function(){
				// console.log( 'Resetting carousel' );
				$('.mobile-item-nav-scroll-container').remove();
				mobile_modified = false;
				bb_swiper = false;
				bb_set_mobile_layout();
				bb_set_mobile_carousel();
			});


		/*------------------------------------------------------------------------------------------------------
		1.1 - Set Mobile Layout
		--------------------------------------------------------------------------------------------------------*/

			function bb_populate_select_label() {
				if ( is_mobile ) {
					$selects.each( function( idx, val ) {
						var $select = $(this);
						$select.prev('label').text( $select.find('option:selected').text() );
					});
				}
				else {
					$selects.each( function( idx, val ) {
						var $select = $(this);

						// Use the post in label for the post form select box
						if ( $select.attr('id') === 'whats-new-post-in' ) {
							$select.prev('label').text( BuddyBossOptions.post_in_label );
						}
						// Otherwise use the default select filter label "Show:"
						else {
							$select.prev('label').text( BuddyBossOptions.select_label );
						}
					});

				}
			}

			function bb_set_mobile_layout() {
				var
					window_height = $window.height(), // window height - 60px (Header height) - carousel_nav_height (Carousel Navigation space)
					carousel_width = ($item_nav.find('li').length * 94);

				// First run, mobile setup for scroller div
				if ( is_mobile && BuddyBoss.mobile_user_nav !== 'open' && BuddyBoss.mobile_user_nav !== 'open' ) {
					setTimeout( function() {
						navigationPanel( 'open' );
						navigationPanel( 'close' );
						$main.css('-webkit-transform', 'translate3D(1, 0, 0)').css('-moz-transform', 'translate3D(1, 0, 0)').css('transform', 'translate3D(1, 0, 0)');
						$main.css('-webkit-transform', 'translate3D(0, 0, 0)').css('-moz-transform', 'translate3D(0, 0, 0)').css('transform', 'translate3D(0, 0, 0)');

						$inner.css({
							'overflow-y': 'scroll',
							'overflow-x': 'hidden',
							'-webkit-overflow-scrolling': 'touch'
						});
					}, 30 );
				}
				// Reset overflow on desktop
				if ( !is_mobile ) {
					setTimeout( function() {
						$inner.css('overflow-y', 'hidden');
					}, 30 );
				}

				// First run, mobile setup
				if ( is_mobile && has_item_nav && ! mobile_modified ) {
					// console.log( 'Setting up mobile for first time' );
					mobile_modified = true;
					$mobile_nav_wrap  = $('<div id="mobile-item-nav-wrap" class="mobile-item-nav-container mobile-item-nav-scroll-container">');
					$mobile_item_wrap = $('<div class="mobile-item-nav-wrapper">').appendTo( $mobile_nav_wrap );
					$mobile_item_nav  = $('<div id="mobile-item-nav" class="mobile-item-nav">').appendTo( $mobile_item_wrap );
					$mobile_item_nav.append( $item_nav.html() );

					$mobile_item_nav.css( 'width', ($item_nav.find('li').length * 94) );
					$mobile_nav_wrap.insertBefore( $item_nav ).show();
					$('#mobile-item-nav-wrap, .mobile-item-nav-scroll-container, .mobile-item-nav-container').addClass('fixed');
					$item_nav.css({display:'none'});
				}
				// Resized to non-mobile resolution
				else if ( ! is_mobile && has_item_nav && mobile_modified ) {
					$mobile_nav_wrap.css({display:'none'});
					$item_nav.css({display:'block'});
					userMenu('close');
					navigationPanel('close');
				}
				// Resized back to mobile resolution
				else if ( is_mobile && has_item_nav && mobile_modified ) {
					$mobile_nav_wrap.css({
						display:'block',
						width: carousel_width
					});

					$mobile_item_nav.css({
						width: carousel_width
					});

					$item_nav.css({display:'none'});
				}


				setTimeout(function(){
					$inner.css( 'height', bb_get_mobile_wrap_height() );
				}, 10);

				// Update select drop-downs
				bb_populate_select_label();
			}


		/*------------------------------------------------------------------------------------------------------
		1.2 - Set Mobile Carousel
		--------------------------------------------------------------------------------------------------------*/

			function bb_set_mobile_carousel() {
				if ( is_mobile && has_item_nav && ! bb_swiper ) {
					// console.log( 'Setting up mobile nav swiper' );
					bb_swiper = $('.mobile-item-nav-scroll-container').swiper({
						scrollContainer : true,
						slideElement : 'div',
						slideClass : 'mobile-item-nav',
						wrapperClass : 'mobile-item-nav-wrapper'
					});
				}
			}

		/*------------------------------------------------------------------------------------------------------
		1.3 - Device Specific Fixes
		--------------------------------------------------------------------------------------------------------*/

			// Android 2.x - JS Driven Scrolling
			androidversion = getAndroidOsVersion();

			if ( androidversion > 1 && androidversion < 3 ) {
				touchScroll('inner-wrap');
				touchScroll('wpadminbar');
				touchScroll('masthead');
			}


		/*------------------------------------------------------------------------------------------------------
		2.0 - Sliding Panels
		--------------------------------------------------------------------------------------------------------*/

		/*------------------------------------------------------------------------------------------------------
		2.1 - Header Buttons
		--------------------------------------------------------------------------------------------------------*/

			$('a#user-nav').click(function(event) { // User Menu
				// console.log( 'userMenu link clicked' );
				if($('a#user-nav').hasClass('closed')){
					// Open User Menu
					userMenu('open');
				}
				else{
					// Close User Menu
					userMenu('close');
				}
				event.preventDefault();
				return false;
			});

			$('a#main-nav').click(function(event) { // Navigation Menu
				// console.log( 'navigationPanel link clicked' );
				if($('a#main-nav').hasClass('closed')){
					// Open Navigation Menu
					navigationPanel('open');
				}
				else{
					// Close Navigation Menu
					navigationPanel('close');
				}
				event.preventDefault();
				return false;
			});

		/*------------------------------------------------------------------------------------------------------
		2.2 - Sliding Functionality
		--------------------------------------------------------------------------------------------------------*/

			// User Menu
			// -------------------

			function userMenu( action ) {

				// console.log( 'userMenu action: ' + action, BuddyBoss.mobile_user_nav );

				menu_link = $('a#user-nav'); // Header User Nav Button

				if ( action == 'open' && BuddyBoss.mobile_user_nav !== 'open' ) {

					BuddyBoss.mobile_user_nav = 'open';

					admin_bar_width = $('#wpadminbar').css('width');

					// Open User Menu Sliding Panel
					$main.css('-webkit-transform', 'translate3D( ' + admin_bar_width + ', 0, 0)').css('-moz-transform', 'translate3D( ' + admin_bar_width + ', 0, 0)').css('transform', 'translate3D( ' + admin_bar_width + ', 0, 0)');
					$inner.css('overflow', 'hidden');
					$('#wpadminbar').removeClass('hide').addClass('show');
					$('div#swipe-area').show();

					// Add class of "open" to User Nav Button
					menu_link.removeClass('closed open');
					menu_link.addClass('open');

				} else if ( BuddyBoss.mobile_user_nav !== 'closed' ) {

					BuddyBoss.mobile_user_nav = 'closed';

					// Close User Menu Sliding Panel
					$('div#swipe-area').hide();
					$main.css('-webkit-transform', 'translate3D(0, 0, 0)').css('-moz-transform', 'translate3D(0, 0, 0)').css('transform', 'translate3D(0, 0, 0)');
					$('#wpadminbar').removeClass('show').addClass('hide');
					$inner.css({
						'overflow-y': 'scroll',
						'overflow-x': 'hidden',
						'-webkit-overflow-scrolling': 'touch'
					});

					// Add class of "closed" to User Nav Button
					menu_link.removeClass('closed open');
					menu_link.addClass('closed');
				}
			}

			// Main Navigation
			// -------------------

			function navigationPanel ( action ) {

				menu_link = $('a#main-nav');

				// console.log( 'navigationPanel:', action, BuddyBoss.mobile_main_nav );

				if( action == 'open' && BuddyBoss.mobile_main_nav !== 'open' ) {

					BuddyBoss.mobile_main_nav = 'open';

					main_nav_width = $('header#masthead').css('width');
					main_nav_width = '-' + main_nav_width;
					// Open Main Navigation Sliding Panel
					$main.css('-webkit-transform', 'translate3D(' + main_nav_width + ', 0, 0)').css('-moz-transform', 'translate3D(' + main_nav_width + ', 0, 0)').css('transform', 'translate3D(' + main_nav_width + ', 0, 0)');
					$('header#masthead').removeClass('hide').addClass('show');
					$inner.css('overflow', 'hidden');
					$('div#swipe-area').show();

					// Add Class of "open" to Main Nav Button
					menu_link.removeClass('closed open');
					menu_link.addClass('open');

				} else if ( BuddyBoss.mobile_main_nav !== 'closed') {

					BuddyBoss.mobile_main_nav = 'closed';

					// Close Main Navigation Sliding Panel
					$('div#swipe-area').hide();
					$main.css('-webkit-transform', 'translate3D(0, 0, 0)').css('-moz-transform', 'translate3D(0, 0, 0)').css('transform', 'translate3D(0, 0, 0)');
					$('header#masthead').removeClass('show').addClass('hide');
					$inner.css({
						'overflow-y': 'scroll',
						'overflow-x': 'hidden',
						'-webkit-overflow-scrolling': 'touch'
					});

					// Add Class of "closed" to Main Nav Button
					menu_link.removeClass('closed open');
					menu_link.addClass('closed');
				}
			}


		/*------------------------------------------------------------------------------------------------------
		2.3 - Menu Touch Swipe Functionality
			  Apply the Swipe functionality to the #swipe-area, when the menu is open
			  Only use the closing functionality here. - Swipe Using touchSwipe.min.js
		--------------------------------------------------------------------------------------------------------*/

		$("div#swipe-area").swipe({
		  swipe: function(event, direction, distance, duration, fingerCount) {
				if (direction == 'left'){
					if ($('a#user-nav').hasClass('open')){
						userMenu('close');
					}
				}
				else if (direction == 'right'){
					if ($('a#main-nav').hasClass('open')){
						navigationPanel('close');
					}
				}
		  }
		});

		/*------------------------------------------------------------------------------------------------------
		2.4 - Prevent Hover Event + Add Link Open Delay
		--------------------------------------------------------------------------------------------------------*/

		// Handle WP Admin Bar link clicks
		if ( is_mobile ) {

			$('#wpadminbar > div.quicklinks ul#wp-admin-bar-user-actions > li:not(#bb-custom-notification-area) > a, \
		     #wpadminbar div.quicklinks ul li.menupop div.ab-sub-wrapper ul > li.menupop > div.ab-sub-wrapper li a, \
				 div.menu-primary-menu-container ul li:not(.parent) a, \
				 div.menu-primary-menu-container ul > li > ul.sub-menu li a'
			).on('click', function(event) {

				event.preventDefault();

				// Close Sliding Panel
				userMenu('close');

				// Close Navigation Menu
				navigationPanel('close');

				// Get Link Info
				var el = $(this);
				var link = el.attr('href');

				// Open Link with a delay, on mobile this prevents some bugs
				setTimeout(function(){
					window.location = link;
				},300);

			});
		}

		/*------------------------------------------------------------------------------------------------------
		2.5 - Notifications Area
			- Add Notifications Area, if there are notifications to show
		--------------------------------------------------------------------------------------------------------*/

		if ( is_mobile && $(window).width() < 600 ) {

			if ($('#wp-admin-bar-bp-notifications').length != 0){

				// Clone and Move the Notifications Count to the Header
				$('li#wp-admin-bar-bp-notifications a.ab-item > span#ab-pending-notifications').clone().appendTo('#user-nav');

			}
		}

		// Disable Notifications Link in WP Admin Bar
		if ( is_mobile ) {
			$('#wpadminbar div.quicklinks ul li.menupop div.ab-sub-wrapper ul > li.menupop > a, li#bb-custom-notification-area > a').on('click', function(event) {
				event.preventDefault();
			});
		}

		/*------------------------------------------------------------------------------------------------------
		3.0 - Content
		--------------------------------------------------------------------------------------------------------*/
		/*------------------------------------------------------------------------------------------------------
		3.1 - Members (Group Admin)
		--------------------------------------------------------------------------------------------------------*/

		// Hide/Reveal action buttons
		$('a.show-options').click(function(event){
			event.preventDefault;

			parent_li = $(this).parent('li');
			if ($(parent_li).children('ul#members-list span.small').hasClass('inactive')){
				$(this).removeClass('inactive').addClass('active');
				$(parent_li).children('ul#members-list span.small').removeClass('inactive').addClass('active');
			}
			else{
				$(this).removeClass('active').addClass('inactive');
				$(parent_li).children('ul#members-list span.small').removeClass('active').addClass('inactive');
			}

		});


		/*------------------------------------------------------------------------------------------------------
		3.2 - Search Input Field
		--------------------------------------------------------------------------------------------------------*/
		$('#buddypress div.dir-search form, #buddypress div.message-search form, div.bbp-search-form form, form#bbp-search-form').append('<a href="#" id="clear-input"> </a>');
		$('a#clear-input').click(function(){
			jQuery("#buddypress div.dir-search form input[type=text], #buddypress div.message-search form input[type=text], div.bbp-search-form form input[type=text], form#bbp-search-form input[type=text]").val("");
		});


		/*------------------------------------------------------------------------------------------------------
		3.3 - Hide Profile and Group Buttons Area, when there are no buttons (ex: Add Friend, Join Group etc...)
		--------------------------------------------------------------------------------------------------------*/

		if (!$('#buddypress div#item-header #item-buttons div.generic-button').doesExist()){
		  $('#buddypress div#item-header #item-buttons').hide();
		  $('#buddypress div#item-header').addClass('no-buttons');
		}

		/*------------------------------------------------------------------------------------------------------
		3.4 - Move the Messages Checkbox, below the Avatar
		--------------------------------------------------------------------------------------------------------*/

		$('#message-threads.messages-notices ul li.thread-options span.checkbox').each(function (){
			move_to_spot = $(this).parent().siblings('.thread-avatar');
			$(this).appendTo(move_to_spot);
		});

		/*------------------------------------------------------------------------------------------------------
		3.5 - Repopulate dropdown text
		--------------------------------------------------------------------------------------------------------*/

		$selects.on( 'change', function( e ) {
			bb_populate_select_label();
		});

		/*------------------------------------------------------------------------------------------------------
		3.6 - Make Video Embeds Responsive - Fitvids.js
		--------------------------------------------------------------------------------------------------------*/

		$('#content').fitVids();

} // End BuddyBossMobileJS() Mega Function

		// We need to make sure the document is ready
		jQuery( document ).ready( function( $ ) {
			BuddyBossMobileJS( $, window );
		});

		/*------------------------------------------------------------------------------------------------------
		4.0 - Misc Functions
		--------------------------------------------------------------------------------------------------------*/

		/*------------------------------------------------------------------------------------------------------
		4.1 - Check if on a Touch Device - isTouchDevice()
		--------------------------------------------------------------------------------------------------------*/

		function isTouchDevice(){
			try{
				document.createEvent("TouchEvent");
				return true;
			}catch(e){
				return false;
			}
		}


		/*------------------------------------------------------------------------------------------------------
		4.2 - Force Touch Scrolling on Div - touchScroll(id)
		--------------------------------------------------------------------------------------------------------*/

		function touchScroll(id){
			// console.log( 'Forcing touch scrolling for ' + id );
			if(isTouchDevice()){ //if touch events exist...
				var el=document.getElementById(id);
				var scrollStartPos=0;

				document.getElementById(id).addEventListener("touchstart", function(event) {
					scrollStartPos=this.scrollTop+event.touches[0].pageY;
					/* event.preventDefault(); /* Commenting this allows for clicks not to be
											     considered as an attempt to scroll */
				},false);

				document.getElementById(id).addEventListener("touchmove", function(event) {
					this.scrollTop=scrollStartPos-event.touches[0].pageY;
					event.preventDefault();
				},false);
			}
		}

/*------------------------------------------------------------------------------------------------------
4.3 - Check if Element Exists - doesExist()
--------------------------------------------------------------------------------------------------------*/

$.fn.doesExist = function(){
    return jQuery(this).length > 0;
};

/*------------------------------------------------------------------------------------------------------
4.4 - Better CSS support for jQuery
--------------------------------------------------------------------------------------------------------*/
// For those who need them (< IE 9), add support for CSS functions
var isStyleFuncSupported = CSSStyleDeclaration.prototype.getPropertyValue != null;
if (!isStyleFuncSupported) {
    CSSStyleDeclaration.prototype.getPropertyValue = function(a) {
        return this.getAttribute(a);
    };
    CSSStyleDeclaration.prototype.setProperty = function(styleName, value, priority) {
        this.setAttribute(styleName,value);
        var priority = typeof priority != 'undefined' ? priority : '';
        if (priority != '') {
            // Add priority manually
            var rule = new RegExp(RegExp.escape(styleName) + '\\s*:\\s*' + RegExp.escape(value) + '(\\s*;)?', 'gmi');
            this.cssText = this.cssText.replace(rule, styleName + ': ' + value + ' !' + priority + ';');
        }
    }
    CSSStyleDeclaration.prototype.removeProperty = function(a) {
        return this.removeAttribute(a);
    }
    CSSStyleDeclaration.prototype.getPropertyPriority = function(styleName) {
        var rule = new RegExp(RegExp.escape(styleName) + '\\s*:\\s*[^\\s]*\\s*!important(\\s*;)?', 'gmi');
        return rule.test(this.cssText) ? 'important' : '';
    }
}

// Escape regex chars with \
RegExp.escape = function(text) {
    return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
}

// The style function
jQuery.fn.style = function(styleName, value, priority) {
    // DOM node
    var node = this.get(0);
    // Ensure we have a DOM node
    if (typeof node == 'undefined') {
        return;
    }
    // CSSStyleDeclaration
    var style = this.get(0).style;
    // Getter/Setter
    if (typeof styleName != 'undefined') {
        if (typeof value != 'undefined') {
            // Set style property
            var priority = typeof priority != 'undefined' ? priority : '';
            style.setProperty(styleName, value, priority);
        } else {
            // Get style property
            return style.getPropertyValue(styleName);
        }
    } else {
        // Get CSSStyleDeclaration
        return style;
    }
}

/*------------------------------------------------------------------------------------------------------
4.5 - JS Driven Touch Scrolling
--------------------------------------------------------------------------------------------------------*/

function getAndroidOsVersion() {

	var ua = navigator.userAgent,
	    retval;

	if( ua.indexOf("Android") >= 0 )
	{
	  retval = parseFloat(ua.slice(ua.indexOf("Android")+8));
	}
	else  {
		retval = 0;
	}

	return retval;
}
