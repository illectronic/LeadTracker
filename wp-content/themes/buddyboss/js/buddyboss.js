/**
 * BuddyBoss JavaScript functionality
 *
 * @since    3.0
 * @package  buddyboss
 */

var BuddyBoss = ( function( $, window, undefined ) {

	/**
	 * Globals/Options
	 */
	var _l = {
		window: $(window)
	};

	// Controller
	var App = {};

	// Custom Events
	var Vent = $({});

	// Responsive
	var Responsive = {};

	// BuddyPress Legacy
	var BP_Legacy = {};


	/** --------------------------------------------------------------- */

	/**
	 * Application
	 */

	// Initialize, runs when script is procesed/loaded
	App.init = function() {
		$(document).ready( App.domReady );

		BP_Legacy.init();
		Responsive.init();
	}

	// When the DOM is ready (page laoded)
	App.domReady = function() {
		_l.body = $('body');
		_l.$buddypress = $('#buddypress');
	}


	/** --------------------------------------------------------------- */

	/**
	 * BuddyPress Responsive Help
	 */
	Responsive.init = function() {

	}


	/** --------------------------------------------------------------- */

	/**
	 * BuddyPress Legacy Support
	 */

	// Initialize
	BP_Legacy.init = function() {
		BP_Legacy.injected = false;
		$(document).ready( BP_Legacy.domReady );
	}

	// On dom ready we'll check if we need legacy BP support
	BP_Legacy.domReady = function() {
		BP_Legacy.check();
	}

	// Check for legacy support
	BP_Legacy.check = function() {
		if ( ! BP_Legacy.injected && _l.body.hasClass('buddypress') && _l.$buddypress.length == 0 ) {
			BP_Legacy.inject();
		}
		// _l.$buddypress.animate({opacity:1});
	}

	// Inject the right code depending on what kind of legacy support
	// we deduce we need
	BP_Legacy.inject = function() {
		BP_Legacy.injected = true;

		var $secondary  = $('#secondary'),
				do_legacy = false;

		var $content  = $('#content'),
				$padder   = $content.find('.padder').first(),
				do_legacy = false;

		var $article = $content.children('article').first();

		var $legacy_page_title,
				$legacy_item_header;

		// Check if we're using the #secondary widget area and add .bp-legacy inside that
		if ( $secondary.length ) {
			$secondary.prop( 'id', 'secondary' ).addClass('bp-legacy');

			do_legacy = true;
		}
		
		// Check if the plugin is using the #content wrapper and add #buddypress inside that
		if ( $padder.length ) {
			$padder.prop( 'id', 'buddypress' ).addClass('bp-legacy entry-content');

			do_legacy = true;

			// console.log( 'Buddypress.js #buddypress fix: Adding #buddypress to .padder' );
		}
		else if ( $content.length ) {
			$content.wrapInner( '<div class="bp-legacy entry-content" id="buddypress"/>' );

			do_legacy = true;

			// console.log( 'Buddypress.js #buddypress fix: Dynamically wrapping with #buddypresss' );
		}

		// Apply legacy styles if needed
		if ( do_legacy ) {

			_l.$buddypress = $('#buddypress');

			$legacy_page_title = $('.buddyboss-bp-legacy.page-title');
			$legacy_item_header = $('.buddyboss-bp-legacy.item-header');

			// Article Element
			if ( $article.length === 0 ) {
				$content.wrapInner('<article/>');
				$article = $( $content.find('article').first() );
			}

			// Page Title
			if ( $content.find('.entry-header').length === 0 || $content.find('.entry-title').length === 0 ) {
				$legacy_page_title.prependTo( $article ).show();
				$legacy_page_title.children().unwrap();
			}

			// Item Header
			if ( $content.find('#item-header-avatar').length === 0 && _l.$buddypress.find('#item-header').length ) {
				$legacy_item_header.prependTo( _l.$buddypress.find('#item-header') ).show();
				$legacy_item_header.children().unwrap();
			}
		}
	}

	// Boot er' up
	App.init();

	// Expose events within an object literal,
	// the BuddyBoss global can contain properties
	// about the app state like "is_mobile"
	return {
		Events: Vent
	};

}( jQuery, window ) );