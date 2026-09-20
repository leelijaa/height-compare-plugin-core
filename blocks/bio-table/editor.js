( function () {
	const el            = wp.element.createElement;
	const { registerBlockType } = wp.blocks;
	const { useBlockProps, PlainText } = wp.blockEditor;
	const { Button } = wp.components;

	registerBlockType( 'height-compare/bio-table', {

		edit( { attributes, setAttributes } ) {
			const { groups } = attributes;
			const blockProps = useBlockProps( { className: 'hc-bio-info-table hc-bio-table-edit' } );

			function setGroups( next ) {
				setAttributes( { groups: next } );
			}

			function setGroupLabel( gi, val ) {
				setGroups( groups.map( ( g, i ) => i === gi ? { ...g, label: val } : g ) );
			}

			function setRow( gi, ri, key, val ) {
				setGroups( groups.map( ( g, i ) => {
					if ( i !== gi ) return g;
					return { ...g, rows: g.rows.map( ( r, j ) => j === ri ? { ...r, [ key ]: val } : r ) };
				} ) );
			}

			function addRow( gi ) {
				setGroups( groups.map( ( g, i ) =>
					i === gi ? { ...g, rows: [ ...g.rows, { label: 'Label', value: '' } ] } : g
				) );
			}

			function removeRow( gi, ri ) {
				setGroups( groups.map( ( g, i ) =>
					i === gi ? { ...g, rows: g.rows.filter( ( _, j ) => j !== ri ) } : g
				) );
			}

			function addGroup() {
				setGroups( [ ...groups, { label: 'New Section', rows: [ { label: 'Label', value: '' } ] } ] );
			}

			function removeGroup( gi ) {
				setGroups( groups.filter( ( _, i ) => i !== gi ) );
			}

			return el( 'div', blockProps,

				groups.map( ( group, gi ) =>
					el( 'div', { key: gi, className: 'hc-bio-info-group' },

						/* ── Section header row ── */
						el( 'div', { className: 'hc-bio-edit-group-head' },
							el( PlainText, {
								className: 'hc-bio-info-group__head',
								value: group.label,
								onChange: ( v ) => setGroupLabel( gi, v ),
								placeholder: 'Section name…',
							} ),
							el( Button, {
								variant: 'tertiary',
								isSmall: true,
								isDestructive: true,
								onClick: () => removeGroup( gi ),
								title: 'Remove section',
							}, '✕' )
						),

						/* ── Data rows ── */
						group.rows.map( ( row, ri ) =>
							el( 'div', { key: ri, className: 'hc-bio-info-row' },
								el( PlainText, {
									className: 'hc-bio-info-row__label',
									value: row.label,
									onChange: ( v ) => setRow( gi, ri, 'label', v ),
									placeholder: 'Label',
								} ),
								el( PlainText, {
									className: 'hc-bio-info-row__value',
									value: row.value,
									onChange: ( v ) => setRow( gi, ri, 'value', v ),
									placeholder: 'Enter value…',
								} ),
								el( Button, {
									variant: 'tertiary',
									isSmall: true,
									isDestructive: true,
									onClick: () => removeRow( gi, ri ),
									className: 'hc-bio-row-del',
									title: 'Remove row',
								}, '✕' )
							)
						),

						/* ── Add row ── */
						el( 'div', { className: 'hc-bio-edit-action hc-bio-edit-action--row' },
							el( Button, {
								variant: 'secondary',
								isSmall: true,
								onClick: () => addRow( gi ),
							}, '+ Add Row' )
						)
					)
				),

				/* ── Add section ── */
				el( 'div', { className: 'hc-bio-edit-action hc-bio-edit-action--section' },
					el( Button, {
						variant: 'primary',
						isSmall: true,
						onClick: addGroup,
					}, '+ Add Section' )
				)
			);
		},

		save() {
			return null; // server-side rendered via render.php
		},
	} );
} )();
