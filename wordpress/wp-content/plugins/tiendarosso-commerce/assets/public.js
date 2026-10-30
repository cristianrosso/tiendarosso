( function () {
	'use strict';

	function analyticsAllowed() {
		return Boolean( window.trConsent && window.trConsent.analytics === true );
	}

	function pushEvent( name, parameters ) {
		if ( ! analyticsAllowed() ) return;
		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push( Object.assign( { event: name }, parameters || {} ) );
	}

	var emittedEventIds = new Set();
	function recordCommerceEvent( event ) {
		if ( ! event || ! event.event || event.event === 'purchase' ) return;
		var eventId = event.event_id || [ event.event, Date.now(), Math.random().toString( 16 ).slice( 2 ) ].join( '-' );
		if ( emittedEventIds.has( eventId ) ) return;
		emittedEventIds.add( eventId );
		var normalized = {
			event: event.event,
			event_id: eventId,
			timestamp: event.timestamp || new Date().toISOString(),
			data: event.data || {}
		};
		window.trCommerceEvents = window.trCommerceEvents || [];
		window.trCommerceEvents.push( normalized );
		pushEvent( normalized.event, Object.assign( { event_id: eventId }, normalized.data ) );
	}

	var commerce = window.trCommerceConfig || {};
	( commerce.events || [] ).forEach( recordCommerceEvent );
	if ( commerce.page === 'product' && commerce.item ) {
		recordCommerceEvent( { event: 'view_item', event_id: 'view-item-' + commerce.item.item_id, data: commerce.item } );
	} else if ( commerce.page === 'cart' ) {
		recordCommerceEvent( { event: 'view_cart', event_id: 'view-cart-' + Date.now(), data: { currency: commerce.currency || 'BOB' } } );
	} else if ( commerce.page === 'checkout' ) {
		recordCommerceEvent( { event: 'begin_checkout', event_id: 'begin-checkout-' + Date.now(), data: { currency: commerce.currency || 'BOB' } } );
	}

	function recordCheckoutSelections() {
		var shipping = document.querySelector( 'input[type="radio"][id*="shipping"]:checked, input[type="radio"][value*="shipping"]:checked' );
		var payment = document.querySelector( 'input[name="radio-control-wc-payment-method-options"]:checked' );
		if ( shipping ) {
			recordCommerceEvent( {
				event: 'add_shipping_info',
				event_id: 'shipping-' + shipping.value,
				data: { shipping_tier: shipping.value, currency: commerce.currency || 'BOB' }
			} );
		}
		if ( payment ) {
			recordCommerceEvent( {
				event: 'add_payment_info',
				event_id: 'payment-' + payment.value,
				data: { payment_type: payment.value, currency: commerce.currency || 'BOB' }
			} );
		}
	}
	if ( commerce.page === 'checkout' ) {
		var checkoutObserver = new MutationObserver( recordCheckoutSelections );
		checkoutObserver.observe( document.body, { childList: true, subtree: true } );
		document.addEventListener( 'change', recordCheckoutSelections );
		window.setTimeout( recordCheckoutSelections, 500 );
	}

	function syncClosedMiniCartFocus() {
		document.querySelectorAll( '.wc-block-mini-cart__drawer' ).forEach( function ( drawer ) {
			var isHidden = drawer.getAttribute( 'aria-hidden' ) === 'true';
			drawer.querySelectorAll( 'a, button, input, select, textarea, [tabindex]' ).forEach( function ( control ) {
				if ( isHidden ) {
					if ( ! control.hasAttribute( 'data-tr-tabindex' ) ) {
						control.setAttribute( 'data-tr-tabindex', control.getAttribute( 'tabindex' ) || '' );
					}
					control.setAttribute( 'tabindex', '-1' );
					if ( control.matches( 'button, input, select, textarea' ) ) control.disabled = true;
				} else if ( control.hasAttribute( 'data-tr-tabindex' ) ) {
					var previous = control.getAttribute( 'data-tr-tabindex' );
					if ( previous ) control.setAttribute( 'tabindex', previous );
					else control.removeAttribute( 'tabindex' );
					if ( control.matches( 'button, input, select, textarea' ) ) control.disabled = false;
					control.removeAttribute( 'data-tr-tabindex' );
				}
			} );
		} );
	}
	var miniCartObserver = new MutationObserver( syncClosedMiniCartFocus );
	miniCartObserver.observe( document.body, { attributes: true, attributeFilter: [ 'aria-hidden' ], childList: true, subtree: true } );
	syncClosedMiniCartFocus();

	var params = new URLSearchParams( window.location.search );
	document.querySelectorAll( '[data-tr-service-form]' ).forEach( function ( form ) {
		var fields = [ 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'campaign_ref' ];
		fields.forEach( function ( key ) {
			var field = form.elements.namedItem( key );
			if ( field ) field.value = ( params.get( key ) || '' ).slice( 0, 190 );
		} );
		var origin = form.elements.namedItem( 'origin' );
		if ( origin ) origin.value = window.location.href.split( '#' )[ 0 ];
		form.addEventListener( 'focusin', function onFirstFocus() {
			pushEvent( 'service_request_start', { form: 'seller_application' } );
			form.removeEventListener( 'focusin', onFirstFocus );
		} );
	} );

	document.querySelectorAll( '[data-tr-whatsapp]' ).forEach( function ( link ) {
		link.addEventListener( 'click', function () {
			pushEvent( 'whatsapp_click', {
				context: link.dataset.trContext || 'general',
				service: link.dataset.trWhatsapp || ''
			} );
		} );
	} );

	document.addEventListener( 'submit', function ( event ) {
		var form = event.target;
		if ( ! form.matches( '.wc-block-checkout__form, form.checkout' ) ) return;
		var button = form.querySelector( 'button[type="submit"], #place_order' );
		if ( button ) {
			button.setAttribute( 'aria-busy', 'true' );
			button.classList.add( 'is-loading' );
		}
	} );
} )();
