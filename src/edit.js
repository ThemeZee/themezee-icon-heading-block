/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

export default function Edit() {
	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: [ 'themezee/icon', 'core/heading' ],
		template: [ 
			[ 'themezee/icon', {
				iconName: "cover",
				iconLibrary: "wordpress",
				iconSVG: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.7 3H5.3C4 3 3 4 3 5.3v13.4C3 20 4 21 5.3 21h13.4c1.3 0 2.3-1 2.3-2.3V5.3C21 4 20 3 18.7 3zm.8 15.7c0 .4-.4.8-.8.8H5.3c-.4 0-.8-.4-.8-.8V5.3c0-.4.4-.8.8-.8h6.2v8.9l2.5-3.1 2.5 3.1V4.5h2.2c.4 0 .8.4.8.8v13.4z"></path></svg>',
				iconWidth: "36px",
				iconHeight: "36px",
			} ],
			[ 'core/heading', {
				placeholder: __( 'Icon Heading' ),
			} ] 
		],
		renderAppender: false,
	} );

	return (
		<>
			<div { ...innerBlocksProps }>
				{ innerBlocksProps.children }
			</div>
		</>
	);
}
