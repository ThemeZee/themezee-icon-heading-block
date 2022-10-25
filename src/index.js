/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockVariation } from '@wordpress/blocks';
import { SVG, Path } from '@wordpress/components';

const icon = (
	<SVG xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
		<Path d="m19.25 27.4 1.8-5.75-4.75-3.7h5.8L24 12l1.85 5.95h5.85l-4.75 3.7 1.75 5.75-4.7-3.55ZM12.2 46V30.8q-2.25-2.35-3.225-5.15Q8 22.85 8 20q0-6.8 4.6-11.4Q17.2 4 24 4q6.8 0 11.4 4.6Q40 13.2 40 20q0 2.85-.975 5.65-.975 2.8-3.225 5.15V46L24 42.05ZM24 33q5.45 0 9.225-3.775Q37 25.45 37 20q0-5.45-3.775-9.225Q29.45 7 24 7q-5.45 0-9.225 3.775Q11 14.55 11 20q0 5.45 3.775 9.225Q18.55 33 24 33Zm-8.8 8.8 8.8-2.75 8.8 2.75v-8.55q-2 1.45-4.3 2.1-2.3.65-4.5.65t-4.5-.65q-2.3-.65-4.3-2.1Zm8.8-4.3Z"/>
	</SVG>
);

registerBlockVariation(
	'core/group', {
		name: 'themezee/icon-heading',
		title: __( 'Icon Heading' ),
		icon,
		description: __( 'Arrange icon and heading horizontally.' ),
		category: 'design',
		attributes: {
			layout: { type: 'flex', flexWrap: 'nowrap' },
			style: { spacing: { blockGap: '12px' } },
		},
		scope: [ 'inserter' ],
		isActive: ( blockAttributes ) =>
			blockAttributes.layout?.type === 'flex' &&
			( ! blockAttributes.layout?.orientation ||
				blockAttributes.layout?.orientation === 'horizontal' ),
		innerBlocks: [
			[ 'themezee/icon', {
				iconName: "cover",
				iconLibrary: "wordpress",
				iconSVG: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.7 3H5.3C4 3 3 4 3 5.3v13.4C3 20 4 21 5.3 21h13.4c1.3 0 2.3-1 2.3-2.3V5.3C21 4 20 3 18.7 3zm.8 15.7c0 .4-.4.8-.8.8H5.3c-.4 0-.8-.4-.8-.8V5.3c0-.4.4-.8.8-.8h6.2v8.9l2.5-3.1 2.5 3.1V4.5h2.2c.4 0 .8.4.8.8v13.4z"></path></svg>',
				iconWidth: "36px",
				iconHeight: "36px",
			}],
			[ 'core/heading', {
				placeholder: __( 'Icon Heading' ),
			} ],
		],
		example: {
			attributes: {
				style: {
					color: {
						text: '#000000',
						background: '#ffffff',
					},
				},
			},
			innerBlocks: [
				{
					name: 'themezee/icon',
					attributes: {
						iconName: "cover",
						iconLibrary: "wordpress",
						iconSVG: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.7 3H5.3C4 3 3 4 3 5.3v13.4C3 20 4 21 5.3 21h13.4c1.3 0 2.3-1 2.3-2.3V5.3C21 4 20 3 18.7 3zm.8 15.7c0 .4-.4.8-.8.8H5.3c-.4 0-.8-.4-.8-.8V5.3c0-.4.4-.8.8-.8h6.2v8.9l2.5-3.1 2.5 3.1V4.5h2.2c.4 0 .8.4.8.8v13.4z"></path></svg>',
						iconWidth: "36px",
						iconHeight: "36px",
					},
				},
				{
					name: 'core/heading',
					attributes: {
						content: __( 'Icon Heading' ),
					},
				},
			],
		},
	}
);
