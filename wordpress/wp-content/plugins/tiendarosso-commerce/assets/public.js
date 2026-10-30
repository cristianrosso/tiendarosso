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
} )();
