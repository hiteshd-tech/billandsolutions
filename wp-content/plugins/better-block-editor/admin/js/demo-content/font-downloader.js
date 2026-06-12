( function ( $ ) {
	$( function () {
		window.wpbbeDownloadFseFonts = function ( fonts ) {
			if ( ! fonts || fonts.length === 0 ) {
				console.error( 'No fonts to download' );
				return $.Deferred().resolve().promise();
			}

			const deferred = $.Deferred();

			wp.apiRequest( {
				path: '/wp/v2/font-collections',
				method: 'GET',
			} )
				.done( async function ( response ) {
					try {
						const googleFonts = response.find( function ( fontCollection ) {
							return fontCollection.slug === 'google-fonts';
						} );

						const themeFonts = fonts.map( function ( font ) {
							font.fontFamily = font.fontFamily.replace( /\"/g, '' );
							return font;
						} );

						const themeUniqueFontFamilies = themeFonts
							.map( function ( font ) {
								return font.fontFamily;
							} )
							.filter( function ( fontFamily, index, self ) {
								return self.indexOf( fontFamily ) === index;
							} );

						const fontFamilies = googleFonts.font_families
							.filter( function ( fontFamily ) {
								return themeUniqueFontFamilies.includes(
									fontFamily.font_family_settings.name
								);
							} )
							.reduce( function ( acc, fontFamily ) {
								acc.push( ...fontFamily.font_family_settings.fontFace );

								return acc;
							}, [] )
							.reduce( function ( acc, fontFace ) {
								const key = [
									fontFace.fontFamily,
									fontFace.fontStyle,
									fontFace.fontWeight,
								].join( '-' );
								acc[ key ] = fontFace.src;

								return acc;
							}, {} );

						for ( const fontFace of themeFonts ) {
							const key = [
								fontFace.fontFamily,
								fontFace.fontStyle,
								fontFace.fontWeight,
							].join( '-' );
							if ( fontFamilies.hasOwnProperty( key ) ) {
								try {
									const response = await fetch( fontFamilies[ key ], {
										mode: 'cors',
									} );
									const blob = await response.blob();
									const fileName = fontFamilies[ key ].split( '/' ).pop();
									const formData = new FormData();
									formData.append( 'font', blob, fileName );

									// Upload font to server via REST API.
									await wp.apiRequest( {
										path: '/wpbbe/v1/fse-font',
										method: 'POST',
										data: formData,
										contentType: false,
										processData: false,
									} );
								} catch ( error ) {
									console.error( 'Error uploading font:', error );
								}
							}
						}

						deferred.resolve();
					} catch ( error ) {
						console.error( 'Error processing fonts:', error );
						deferred.resolve();
					}
				} )
				.fail( function ( response ) {
					console.log( 'Cannot get font collections', response );
					deferred.resolve();
				} );

			return deferred.promise();
		};
	} );
} )( jQuery );
