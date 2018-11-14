/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import EventCategory from './block';
import { Categories } from '@moderntribe/events/icons';

/**
 * Module Code
 */
export default {
	id: 'event-category',
	title: __( 'Event Categories', 'events-gutenberg' ),
	description: __(
		'Show assigned event categories as links to their respective archives.',
		'events-gutenberg'
	),
	icon: <Categories/>,
	category: 'tribe-events',
	keywords: [ 'event', 'events-gutenberg', 'tribe' ],

	supports: {
		html: false,
	},

	attributes: {},

	edit: EventCategory,
	save( props ) {
		return null;
	},
};
