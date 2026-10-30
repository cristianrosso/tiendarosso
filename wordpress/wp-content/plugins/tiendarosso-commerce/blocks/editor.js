( function ( blocks, element, components, blockEditor, serverSideRender ) {
	'use strict';
	var el = element.createElement;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = components.PanelBody;
	var TextControl = components.TextControl;
	var SelectControl = components.SelectControl;
	var ServerSideRender = serverSideRender;

	blocks.registerBlockType( 'tiendarosso/plans-comparison', {
		edit: function () { return el( ServerSideRender, { block: 'tiendarosso/plans-comparison' } ); },
		save: function () { return null; }
	} );

	blocks.registerBlockType( 'tiendarosso/service-request-form', {
		edit: function ( props ) {
			return [
				el( InspectorControls, { key: 'controls' }, el( PanelBody, { title: 'Formulario' }, el( TextControl, { label: 'Plan preseleccionado', value: props.attributes.selectedPlan || '', onChange: function ( value ) { props.setAttributes( { selectedPlan: value } ); } } ) ) ),
				el( ServerSideRender, { key: 'preview', block: 'tiendarosso/service-request-form', attributes: props.attributes } )
			];
		},
		save: function () { return null; }
	} );

	blocks.registerBlockType( 'tiendarosso/whatsapp-cta', {
		edit: function ( props ) {
			return [
				el( InspectorControls, { key: 'controls' }, el( PanelBody, { title: 'WhatsApp' },
					el( SelectControl, { label: 'Responsable', value: props.attributes.context || 'general', options: [ { label: 'General', value: 'general' }, { label: 'Planes', value: 'plans' }, { label: 'Marketing', value: 'marketing' } ], onChange: function ( value ) { props.setAttributes( { context: value } ); } } ),
					el( TextControl, { label: 'Texto del botón', value: props.attributes.label || '', onChange: function ( value ) { props.setAttributes( { label: value } ); } } ),
					el( TextControl, { label: 'Contexto del mensaje', value: props.attributes.plan || '', onChange: function ( value ) { props.setAttributes( { plan: value } ); } } )
				) ),
				el( ServerSideRender, { key: 'preview', block: 'tiendarosso/whatsapp-cta', attributes: props.attributes } )
			];
		},
		save: function () { return null; }
	} );
} )( window.wp.blocks, window.wp.element, window.wp.components, window.wp.blockEditor, window.wp.serverSideRender );
