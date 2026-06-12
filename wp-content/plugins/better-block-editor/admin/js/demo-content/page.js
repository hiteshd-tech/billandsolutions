( function ( $ ) {
	'use strict';

	$( function () {
		// Localized feedback strings moved from PHP to JS using wp.i18n __() only (plugin text domain: better-block-editor).
		const { __ } = window.wp.i18n;
		// Keep only dynamic progress feedback keys; confirmation & button texts are inlined.
		const feedbackStrings = {
			setup_rewrite_rules: __( 'Setting up rewrite rules...', 'better-block-editor' ),
			download_package: __( 'Downloading package...', 'better-block-editor' ),
			turn_on_design_system: __( 'Enabling design system...', 'better-block-editor' ),
			import_post_types: __( 'Importing content...', 'better-block-editor' ),
			import_attachments: __( 'Importing attachments...', 'better-block-editor' ),
			import_site_logo: __( 'Importing site logo...', 'better-block-editor' ),
			process_block_theme_data: __( 'Processing imported data...', 'better-block-editor' ),
			cleanup: __( 'Cleaning up...', 'better-block-editor' ),
			install_bb_theme: __( 'Installing Better Block Theme...', 'better-block-editor' ),
			download_fse_fonts: __( 'Downloading fonts...', 'better-block-editor' ),
			clear_importer_session: __( 'Clearing importer session...', 'better-block-editor' ),
		};

		function buildGoBackLink() {
			const linkText = __( 'Back to Site Templates', 'better-block-editor' );
			const url = ( typeof wpbbeContentImportData !== 'undefined' && wpbbeContentImportData.admin_url ) ? wpbbeContentImportData.admin_url : '#';
			return '<a href="' + url + '">' + linkText + '</a>';
		}
		const $globalFeedbackContainer = $( '.bbe-import-feedback' );
		const customProcesses = {
			import_attachments: addAttachmentsImportProcess,
			install_bb_theme ( xhr, data, contentData ) {
				return addProcess( xhr, {
					action: 'wpbbe_install_bb_theme',
					_wpnonce: wpbbeContentImportData.nonces.install_bb_theme,
					sub_action: 'install_bb_theme',
				} ).
				done( function(response) {
					// If activation URL is provided, make a request to activate the theme
					if ( response.data.activateUrl ) {
						// Make request to activate theme
						$.ajax( {
							url: response.data.activateUrl,
							type: 'GET'
						} );
					}
				} );
			},
			download_fse_fonts( xhr, data, contentData ) {
				return xhr.then( function () {
					const deferred = $.Deferred();

					// If downloader is unavailable, no-op.
					if ( typeof window.wpbbeDownloadFseFonts === 'undefined' ) {
						return deferred.resolve().promise();
					}

					const actionName = getFeedbackText( data );
					const $feedback = createFeedbackElement( actionName, $globalFeedbackContainer );

					// Ask server which fonts are missing, then trigger downloader.
					wp.apiRequest( {
						path: '/wpbbe/v1/fse-fonts',
						method: 'GET',
					} )
						.then( function ( fonts ) {
							// fonts is an array of filenames returned by the server (missing fonts).
							if ( ! fonts || ! Array.isArray( fonts ) || fonts.length === 0 ) {
								$feedback.replaceWith(
									$( '<p class="bbe-updated">' + actionName + '</p>' )
								);
								return $.Deferred().resolve().promise();
							}

							return window.wpbbeDownloadFseFonts( fonts );
						} )
						.always( function () {
							$feedback.replaceWith(
								$( '<p class="bbe-updated">' + actionName + '</p>' )
							);
							deferred.resolve();
						} );

					return deferred.promise();
				} );
			},
		};
		let attachmentsLastFeedbackText = '';

		const ImportActionsObject = function ( actions ) {
			this.actions = actions;
			this.retryAttempts = 0;
		};

		ImportActionsObject.prototype.get = function () {
			return this.actions;
		};

		ImportActionsObject.prototype.done = function ( actionDone ) {
			this.actions = this.actions.filter( function ( action ) {
				return action !== actionDone;
			} );
			this.dropRetryAttempts();
		};

		ImportActionsObject.prototype.getRetries = function () {
			return this.retryAttempts;
		};

		ImportActionsObject.prototype.attemptRetry = function () {
			this.retryAttempts += 1;
			if ( this.canRetry() && this.isActionRetriable() ) {
				return this;
			}

			return null;
		};

		ImportActionsObject.prototype.isActionRetriable = function () {
			const retriableActions = [ 'import_attachments' ];

			return this.actions && retriableActions.includes( this.actions[ 0 ] );
		};

		ImportActionsObject.prototype.canRetry = function () {
			return this.retryAttempts <= 3;
		};

		ImportActionsObject.prototype.dropRetryAttempts = function () {
			this.retryAttempts = 0;
		};

		// Run the import.
		if ( typeof wpbbeImportData !== 'undefined' ) {
			wpbbeImportData.importActions = new ImportActionsObject( wpbbeImportData.actions );
			wpbbeImportData.onDone = showFinalLinks;

			importDemo(
				wpbbeImportData.importActions,
				{
					action: 'wpbbe_import_demo',
					content_part_id: wpbbeImportData.demo_id,
					user_map: wpbbeImportData.users,
					_wpnonce: wpbbeContentImportData.nonces.import
				},
				wpbbeImportData
			);
		}

		/**
		 * Errors.
		 *
		 * @param $container
		 * @class
		 */
		function ErrorsContainer( $container ) {
			this.container = $container;
		}

		ErrorsContainer.prototype.addText = function ( text ) {
			this.add( '<p>' + text + '</p>' );
		};

		ErrorsContainer.prototype.add = function ( text ) {
			const $msg = $(
				'<div class="dt-dummy-inline-msg hide-if-js inline error">' + text + '</div>'
			);
			this.container.append( $msg );
			$msg.slideDown();
		};

		ErrorsContainer.prototype.clear = function () {
			this.container.slideUp().html( '' );
		};

		function addProcess( xhr, data, importConfig, $feedback ) {
			return xhr.then( function () {
				const actionName = getFeedbackText( data );

				if ( typeof $feedback === 'undefined' ) {
					$feedback = createFeedbackElement( actionName, $globalFeedbackContainer );
				}

				return $.post( ajaxurl, data )
					.then( filterAjaxResponse )
					.done( function ( response ) {
						if ( importConfig && importConfig.importActions ) {
							importConfig.importActions.done( data.sub_action );
						}
						$feedback.replaceWith(
							$( '<p class="bbe-updated">' + actionName + '</p>' )
						);
					} )
					.fail( function () {
						$feedback.replaceWith(
							$( '<p class="bbe-error">' + actionName + '</p>' )
						);
					} );
			} );
		}

		function addAttachmentsImportProcess( xhr, data, importConfig ) {
			return xhr.then( function () {
				let feedbackText = getFeedbackText( data );

				// Save the first feedback text.
				if ( ! attachmentsLastFeedbackText ) {
					attachmentsLastFeedbackText = feedbackText;
				}

				if ( importConfig.importActions ) {
					const retries = importConfig.importActions.getRetries();
					if ( retries ) {
						feedbackText = attachmentsLastFeedbackText + ' Retry... ' + retries;
					}
				}

				return importNextAttachmentsBatch(
					data,
					importConfig,
					createFeedbackElement( feedbackText, $globalFeedbackContainer )
				);
			} );
		}

		function importDemo( actions, data, importConfig ) {
			if ( ! actions || ! actions.get() ) {
				return;
			}

			let xhr = $.when();

			actions.get().forEach( function ( action ) {
				const importData = $.extend( {}, data, { sub_action: action } );

				if ( customProcesses.hasOwnProperty( action ) ) {
					xhr = customProcesses[ action ]( xhr, importData, importConfig );
				} else {
					xhr = addProcess( xhr, importData, importConfig );
				}
			} );

			xhr.fail( function ( response ) {
				// Retry first.
				if ( actions.attemptRetry() ) {
					importDemo( actions, data, importConfig );
					return;
				}

				const $errorsContainer = new ErrorsContainer( $globalFeedbackContainer );

				if (
					typeof response.data !== 'undefined' &&
					typeof response.data.error_msg !== 'undefined' &&
					response.data.error_msg
				) {
					$errorsContainer.addText( response.data.error_msg );
				} else {
					$errorsContainer.addText( __( 'Import failed', 'better-block-editor' ) );
				}

				$( '.bbe-go-back-link' ).html( buildGoBackLink() );
				showFinalLinks();
			} );

			if ( importConfig.onDone ) {
				xhr.done( importConfig.onDone );
			}

			return xhr;
		}

		function importNextAttachmentsBatch( data, importConfig, $feedback ) {
			return $.post( ajaxurl, data )
				.then( filterAjaxResponse )
				.then( function ( response ) {
					attachmentsLastFeedbackText =
						getFeedbackText( { sub_action: 'import_attachments' } ) +
						' (' +
						response.data.imported +
						'/' +
						response.data.total +
						')';

					// Throw an error if nothing changed since the last import attempt.
					if ( attachmentsLastFeedbackText === $feedback.text().trim() ) {
						return filterAjaxResponse( null );
					}

					if ( response.data.left != 0 ) {
						importConfig.importActions &&
							importConfig.importActions.dropRetryAttempts();
						return importNextAttachmentsBatch(
							data,
							importConfig,
							replaceFeedbackElementWith(
								$feedback,
								createFeedbackElement( attachmentsLastFeedbackText )
							)
						);
					}

					importConfig.importActions &&
						importConfig.importActions.done( data.sub_action );
					$feedback.replaceWith(
						$( '<p class="bbe-updated">' + getFeedbackText( data ) + '</p>' )
					);
				} )
				.fail( function () {
					if ( importConfig.importActions && importConfig.importActions.canRetry() ) {
						$feedback.remove();
						return;
					}

					$feedback.replaceWith(
						$( '<p class="bbe-error">' + getFeedbackText( data ) + '</p>' )
					);
				} );
		}

		function createFeedbackElement( text, $feedbackContainer ) {
			const spinnerHTML =
				'<span class="spinner is-active" style="float: none; margin: 0"></span> ';
			const $feedbackElement = $( '<p>' + spinnerHTML + text + '</p>' );

			if ( typeof $feedbackContainer !== 'undefined' ) {
				$feedbackContainer.append( $feedbackElement );
			}

			return $feedbackElement;
		}

		function getFeedbackText( data ) {
			let actionName = data.sub_action;
			if ( typeof feedbackStrings[ data.sub_action ] !== 'undefined' ) {
				actionName = feedbackStrings[ data.sub_action ];
			}

			return actionName;
		}

		function replaceFeedbackElementWith( $origin, $new ) {
			$origin.replaceWith( $new );

			return $new;
		}

		function sanitizeUrl( url ) {
			return (
				'https://' +
				url
					.replace( /[\?#].*/, '' )
					.replace( /\/?$/, '/' )
					.replace( 'https://', '' )
			);
		}

		function normalizeUrl( url ) {
			let normUrl = sanitizeUrl( url ).replace( 'https://', '' );
			normUrl = normUrl
				.split( '/' )
				.slice( 0, 2 )
				.filter( function ( el ) {
					return el !== '';
				} )
				.join( '/' );
			return normUrl + '/';
		}

		function showFinalLinks() {
			$( '.bbe-go-back-link' ).removeClass( 'hide-if-js' );
		}

		function filterAjaxResponse( response ) {
			const filter = $.Deferred();

			if ( response.success ) {
				filter.resolve( response );
			} else {
				filter.reject( response );
			}

			return filter.promise();
		}

		function xhrFailWithError( errorMsg ) {
			return $.Deferred()
				.reject( { data: { error_msg: errorMsg || '' } } )
				.promise();
		}

		// Filter functionality
		$( '.wp-filter.websites-filter .filter-links a' ).on( 'click', function ( e ) {
			e.preventDefault();

			// Update active filter
			$( '.wp-filter.websites-filter .filter-links a' ).removeClass( 'current' );
			$( this ).addClass( 'current' );

			const filter = $( this ).data( 'filter' ) || 'all';
			const $items = $( '.websites-item' );
			let visibleCount = 0;

			if ( filter === 'all' ) {
				$items.show();
				visibleCount = $items.length;
			} else {
				$items.each( function () {
					const $item = $( this );
					const itemTags = $item.data( 'tags' ) || '';

					if ( filter === 'imported' ) {
						if ( $item.find( '.notice-success' ).length > 0 ) {
							$item.show();
							visibleCount++;
						} else {
							$item.hide();
						}
					} else if ( itemTags.toLowerCase().indexOf( filter ) !== -1 ) {
						$item.show();
						visibleCount++;
					} else {
						$item.hide();
					}
				} );
			}

			// Update count
			$( '.wp-filter.websites-filter .filter-count .count' ).text( visibleCount );
		} );

		// Search functionality
		$( '#wp-filter-search-input' ).on( 'input', function () {
			const searchTerm = $( this ).val().toLowerCase();
			const $items = $( '.websites-item' );
			let visibleCount = 0;

			if ( searchTerm === '' ) {
				// Reset filter to "All" when search is empty
				$( '.wp-filter.websites-filter .filter-links a[data-filter="all"]' ).trigger( 'click' );
				return;
			}

			$items.each( function () {
				const $item = $( this );
				const title = $item.find( 'h2' ).text().toLowerCase();

				if ( title.indexOf( searchTerm ) !== -1 ) {
					$item.show();
					visibleCount++;
				} else {
					$item.hide();
				}
			} );

			// Update count
			$( '.wp-filter.websites-filter .filter-count .count' ).text( visibleCount );

			// Reset active filter when searching
			$( '.wp-filter.websites-filter .filter-links a' ).removeClass( 'current' );
		} );

		// Demo actions
		$( '.websites-actions .button' ).on( 'click', function ( e ) {
			const $button = $( this );
			const $item = $button.closest( '.websites-item' );
			const action = $button.data( 'action' ) || $button.text().toLowerCase();
			const demoTitle = '"' + ($button.data( 'demo' ) || $item.find( 'h2' ).text()) + '"';

			if ($button.data('force-import')) {
				return;
			}

			// Check if button is inside a form
			const $form = $button.closest( 'form' );
			let shouldSubmitForm = false;

			// Localized confirmation messages using %s placeholder replacement.
			const formatConfirm = ( template, value ) => ( template || '' ).replace( /%s/g, value );
			const confirmMessages = {
				// translators: %s is the website template (demo) title.
				import: formatConfirm( __( 'Do you want to import %s website template? This will install the Better Block Theme and add all template content to your site.', 'better-block-editor' ), demoTitle ),
				// translators: %s is the website template (demo) title.
				remove: formatConfirm( __( 'Do you want to remove %s website template? This will delete all related template content.', 'better-block-editor' ), demoTitle ),
				// translators: %s is the website template (demo) title.
				keep: formatConfirm( __( 'Do you want to keep %s website template? It will no longer be removable in bulk.', 'better-block-editor' ), demoTitle ),
			};

			// Check each action type. For 'import' we now skip confirm and rely on popup/theme install flow.
			const actions = [ 'import', 'remove', 'keep' ];
			for ( const actionType of actions ) {
				if ( action === actionType || action.indexOf( actionType ) !== -1 ) {
					if ( confirm( confirmMessages[ actionType ] ) ) {
						shouldSubmitForm = true;
					}
					break;
				}
			}

			// AJAX remove handling.
			if ( shouldSubmitForm && action === 'remove' ) {
				// Prevent native submit; do AJAX.
				e.preventDefault();
				// Get demo id from hidden input inside the form.
				const demoId = $form.find('input[name="demo_id"]').val();
				if ( ! demoId ) {
					return; // Nothing to do.
				}
				const originalText = $button.text();
				$button.prop('disabled', true).text( __( 'Removing content...', 'better-block-editor' ) );
				$.post( ajaxurl, {
					action: 'wpbbe_remove_content',
					_ajax_nonce: wpbbeContentImportData.nonces.remove,
					demo: demoId,
				} ).done( function( response ) {
					if ( response && response.success ) {
						$item.find( '.notice-success' ).remove(); // Remove existing success notice if any.
						window.location.reload(); // Reload to reflect changes.
						return;
					}
					$button.prop('disabled', false).text( originalText );
				}).fail( function() {
					$button.prop('disabled', false).text( originalText );
					alert( 'Failed to remove template content.' );
				});
				return;
			}

			// Ajax keep handling.
			if ( shouldSubmitForm && action === 'keep' ) {
				// Prevent native submit; do AJAX.
				e.preventDefault();
				// Get demo id from hidden input inside the form.
				const demoId = $form.find('input[name="demo_id"]').val();
				if ( ! demoId ) {
					return; // Nothing to do.
				}
				const originalText = $button.text();
				$button.prop('disabled', true).text( __( 'Keeping content...', 'better-block-editor' ) );
				$.post( ajaxurl, {
					action: 'wpbbe_keep_content',
					_ajax_nonce: wpbbeContentImportData.nonces.keep,
					demo_id: demoId,
				} ).done( function( response ) {
					if ( response && response.success ) {
						window.location.reload(); // Reload to reflect changes.
						return;
					}
					$button.prop('disabled', false).text( originalText );
				}).fail( function() {
					$button.prop('disabled', false).text( originalText );
					alert( 'Failed to keep template content.' );
				});
				return;
			}

			// Submit form if user confirmed and form exists, otherwise prevent submission
			if ( ! shouldSubmitForm || $form.length === 0 ) {
				e.preventDefault();
			}
		} );

		// Initialize count
		const totalItems = $( '.websites-item' ).length;
		$( '.wp-filter.websites-filter .filter-count .count' ).text( totalItems );
	} );
} )( jQuery );
