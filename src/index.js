/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { SVG, Path } from '@wordpress/components';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';
import metadata from './block.json';
import './style.scss';

const icon = (
	<SVG xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
		<Path d="m19.25 27.4 1.8-5.75-4.75-3.7h5.8L24 12l1.85 5.95h5.85l-4.75 3.7 1.75 5.75-4.7-3.55ZM12.2 46V30.8q-2.25-2.35-3.225-5.15Q8 22.85 8 20q0-6.8 4.6-11.4Q17.2 4 24 4q6.8 0 11.4 4.6Q40 13.2 40 20q0 2.85-.975 5.65-.975 2.8-3.225 5.15V46L24 42.05ZM24 33q5.45 0 9.225-3.775Q37 25.45 37 20q0-5.45-3.775-9.225Q29.45 7 24 7q-5.45 0-9.225 3.775Q11 14.55 11 20q0 5.45 3.775 9.225Q18.55 33 24 33Zm-8.8 8.8 8.8-2.75 8.8 2.75v-8.55q-2 1.45-4.3 2.1-2.3.65-4.5.65t-4.5-.65q-2.3-.65-4.3-2.1Zm8.8-4.3Z"/>
	</SVG>
);

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType( metadata.name, {
	icon,
	edit,
	save,
	merge( attributes, attributesToMerge ) {
		return {
			...attributes,
			content: attributes.content + attributesToMerge.content,
		};
	},
} );
