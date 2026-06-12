<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

class The7_Content_Importer extends WP_Import {

	const THE7_PROCESSED_DATA_KEY      = 'the7_demo_content_processed_data';
	const THE7_POST_RESOURCES_META_KEY = 'the7_exporter_post_resources';

	protected $total_posts = 0;
	protected $parsed_file = null;
	protected $filtered_post_id;
	protected $filter_by_url;
	protected $filter_by_id;
	protected $download_resizes = true;

	/**
	 * Rolling stats (last up to 3 items) of local image processing performance.
	 * Each item: [ 'size' => int(bytes), 'time' => float(seconds) ].
	 * Used to predict processing time for the next image and decide whether to
	 * download pre-generated resizes instead of generating them locally.
	 *
	 * @var array[]
	 */
	protected $image_processing_stats = [];

	/**
	 * Processed post names.
	 */
	public $processed_post_names = [];

	/**
	 * Import a batch of posts from a WXR file.
	 *
	 * @param string $file Path to the WXR file for importing.
	 * @param int    $batch Number of posts to import in this batch.
	 * @param bool   $fetch_attachments Whether to fetch attachments.
	 *
	 * @return array Import result with 'imported', 'left', and 'total' keys.
	 */
	public function import_batch( $file, $batch, $fetch_attachments = true ) {
		$this->fetch_attachments = $fetch_attachments;

		// Allow to import various danger files, assuming that the data from the demo content is safe.
		add_filter(
			'upload_' . 'mimes', // phpcs:ignore Generic.Strings.UnnecessaryStringConcat
			function ( $mimes ) {
				$mimes['svg']   = 'image/svg+xml';
				$mimes['ttf']   = 'application/x-font-ttf';
				$mimes['eot']   = 'application/vnd.ms-fontobject';
				$mimes['woff']  = 'application/x-font-woff';
				$mimes['woff2'] = 'font/woff2';
				$mimes['json']  = 'application/json';
				$mimes['css']   = 'text/css';

				return $mimes;
			}
		);

		ob_start();

		if ( ! $batch ) {
			ob_end_clean();

			$this->import( $file );

			return [
				'imported' => count( $this->processed_posts ),
				'left'     => 0,
				'total'    => $this->total_posts,
			];
		}

		$this->read_processed_data_from_cache();

		add_filter( 'import_post_meta_key', array( $this, 'is_valid_meta_key' ) );
		add_filter( 'http_request_timeout', array( $this, 'bump_request_timeout' ) );

		/**
		 * Set the operation timelimit based on php `max_execution_time`, between 1 and 45 seconds.
		 *
		 * Nginx and Apache has 60 seconds default timelimit, and we used it as a top baundary (minus 20 secons for safety).
		 * By default php has 30 seconds execution time, 20 seconds is the safe default.
		 */
		$max_execution_time = 20;
		if ( function_exists( 'ini_get' ) ) {
			$max_execution_time = (int) ini_get( 'max_execution_time' ) - 10;
			$max_execution_time = $max_execution_time > 0 ? $max_execution_time : 20;
			$max_execution_time = min( $max_execution_time, 40 );
		}
		$this->set_timelimit( $max_execution_time );

		$this->import_start( $file );
		$this->cut_batch( $batch );

		$this->get_author_mapping();

		wp_suspend_cache_invalidation( true );
		$this->process_categories();
		$this->process_tags();
		$this->process_terms();
		$this->process_posts();
		wp_suspend_cache_invalidation( false );

		// Update incorrect/missing information in the DB.
		$this->backfill_parents();
		$this->backfill_attachment_urls();
		$this->remap_featured_images();

		$this->import_end();

		$this->cache_processed_data();

		$new_log = ob_get_clean();

		$this->log_add( $new_log );

		$time = time() - $this->start_time;
		$this->log_add( 'Operation took ' . $time . ' seconds.' );

		return [
			'imported' => $this->imported_posts,
			'left'     => $this->total_posts - $this->imported_posts,
			'total'    => $this->total_posts,
		];
	}

	/**
	 * The main controller for the actual import stage.
	 *
	 * @param string $file Path to the WXR file for importing.
	 * @return void
	 */
	function import( $file ) {
		add_filter(
			'wp_insert_term_data',
			function ( $data, $taxonomy, $args ) {
				if ( isset( $args['term_id'] ) ) {
					$data['term_id'] = $args['term_id'];
				}

				return $data;
			},
			10,
			3
		);

		ob_start();

		$this->set_timelimit( 9999 );

		parent::import( $file );
		$log = ob_get_clean();

		$this->log_add( $log );
	}

	/**
	 * Remove already processed posts and slice the posts array to the batch size.
	 *
	 * @param int $batch Number of posts to keep in the batch.
	 * @return void
	 */
	protected function cut_batch( $batch ) {
		$processed_posts_ids = array_keys( $this->processed_posts );

		foreach ( $this->posts as $i => $post ) {
			if ( in_array( $post['post_id'], $processed_posts_ids, false ) ) {
				$this->imported_posts++;
				unset( $this->posts[ $i ] );
			}
		}

		$this->posts = array_slice( $this->posts, 0, $batch );
	}

	/**
	 * Prepare for import: filter posts, set up filters, and count total posts.
	 *
	 * @param string $file Path to the WXR file for importing.
	 * @return void
	 */
	public function import_start( $file ) {
		parent::import_start( $file );

		foreach ( $this->posts as $index => $post ) {
			$only_attachments = $this->fetch_attachments && $post['post_type'] !== 'attachment';
			$only_posts       = ! $this->fetch_attachments && $post['post_type'] === 'attachment';

			if ( $only_attachments || $only_posts ) {
				unset( $this->posts[ $index ] );
			}
		}

		$this->total_posts = count( $this->posts );

		add_filter( 'wp_import_post_data_raw', [ $this, 'wc_products_filter' ] );
		add_filter(
			'import_post_meta_key',
			function( $key ) {
				return $key === static::THE7_POST_RESOURCES_META_KEY ? false : $key;
			}
		);
		add_filter(
			'wp_import_existing_post',
			function( $post_exists, $post ) {
				if ( $post['post_type'] === 'attachment' && ! empty( $post['postmeta'] ) ) {
					// Double check existed attachments.
					$flat_postmeta = wp_list_pluck( $post['postmeta'], 'value', 'key' );
					if ( isset( $flat_postmeta['_wp_attached_file'] ) ) {
						return $this->attachment_exists( $flat_postmeta['_wp_attached_file'] );
					}
				} elseif (
					$this->filtered_post_id
					&&
					(
					$post['post_id'] === $this->filtered_post_id
					||
					$post['post_type'] === 'elementor_library'
					)
				) {
					// Force to import Elementor library and requested post.
					return 0;
				}

				// Always import posts.
				return 0;
			},
			10,
			2
		);
	}

	/**
	 * Finalize the import process, flush caches, and trigger hooks.
	 *
	 * @return void
	 */
	public function import_end() {
		wp_cache_flush();
		foreach ( get_taxonomies() as $tax ) {
			delete_option( "{$tax}_children" );
			_get_term_hierarchy( $tax );
		}

		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );

		do_action( 'import_end' );
	}

	/**
	 * Parse a WXR file.
	 *
	 * @param string $file Path to WXR file for parsing.
	 * @return array Information gathered from the WXR file.
	 */
	public function parse( $file ) {
		if ( $this->parsed_file === null ) {
			$this->parsed_file = apply_filters( 'wp_import_parse', parent::parse( $file ) );
		}

		return $this->parsed_file;
	}

	/**
	 * Process a menu item, add custom meta, and handle relationships.
	 *
	 * @param array $item Menu item data.
	 * @return void
	 */
	public function process_menu_item( $item ) {

		// skip draft, orphaned menu items
		if ( 'draft' === $item['status'] ) {
			printf( "Skip draft menu item: %s\n", (int) $item['post_id'] );

			return;
		}

		$menu_slug = false;
		if ( isset( $item['terms'] ) ) {
			// loop through terms, assume first nav_menu term is correct menu
			foreach ( $item['terms'] as $term ) {
				if ( 'nav_menu' === $term['domain'] ) {
					$menu_slug = $term['slug'];
					break;
				}
			}
		}

		// no nav_menu term associated with this menu item
		if ( ! $menu_slug ) {
			echo "Menu item skipped due to missing menu slug:\n";
			var_dump( $item['terms'] );

			return;
		}

		$menu_id = term_exists( $menu_slug, 'nav_menu' );
		if ( ! $menu_id ) {
			printf( "Menu item skipped due to invalid menu slug: '%s' - %s\n", esc_html( $menu_slug ), (int) $item['post_id'] );

			return;
		}

		$menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;

		foreach ( $item['postmeta'] as $meta ) {
			${$meta['key']} = $meta['value'];
		}

		if ( 'taxonomy' === $_menu_item_type && isset( $this->processed_terms[ intval( $_menu_item_object_id ) ] ) ) {
			$_menu_item_object_id = $this->processed_terms[ intval( $_menu_item_object_id ) ];
		} elseif ( 'post_type' === $_menu_item_type && isset( $this->processed_posts[ intval( $_menu_item_object_id ) ] ) ) {
			$_menu_item_object_id = $this->processed_posts[ intval( $_menu_item_object_id ) ];
		} elseif ( 'custom' !== $_menu_item_type ) {
			// associated object is missing or not imported yet, we'll retry later
			$this->missing_menu_items[] = $item;

			printf( "Missing menu item: %s\n", (int) $item['post_id'] );

			return;
		}

		if ( isset( $this->processed_menu_items[ (int) $_menu_item_menu_item_parent ] ) ) {
			$_menu_item_menu_item_parent = $this->processed_menu_items[ (int) $_menu_item_menu_item_parent ];
		} elseif ( $_menu_item_menu_item_parent ) {
			$this->menu_item_orphans[ (int) $item['post_id'] ] = (int) $_menu_item_menu_item_parent;
			$_menu_item_menu_item_parent                       = 0;
		}

		// wp_update_nav_menu_item expects CSS classes as a space separated string
		$_menu_item_classes = maybe_unserialize( $_menu_item_classes );
		if ( is_array( $_menu_item_classes ) ) {
			$_menu_item_classes = implode( ' ', $_menu_item_classes );
		}

		$args = array(
			'menu-item-object-id'   => $_menu_item_object_id,
			'menu-item-object'      => $_menu_item_object,
			'menu-item-parent-id'   => $_menu_item_menu_item_parent,
			'menu-item-position'    => (int) $item['menu_order'],
			'menu-item-type'        => $_menu_item_type,
			'menu-item-title'       => $item['post_title'],
			'menu-item-url'         => $_menu_item_url,
			'menu-item-description' => $item['post_content'],
			'menu-item-attr-title'  => $item['post_excerpt'],
			'menu-item-target'      => $_menu_item_target,
			'menu-item-classes'     => $_menu_item_classes,
			'menu-item-xfn'         => $_menu_item_xfn,
			'menu-item-status'      => $item['status'],
		);

		/**
		 * Filter menu item args.
		 *
		 * @since 7.4.1
		 */
		$args = apply_filters( 'wxr_menu_item_args', $args, $menu_id );

		$id = wp_update_nav_menu_item( $menu_id, 0, $args );
		if ( $id && ! is_wp_error( $id ) ) {
			$this->processed_menu_items[ (int) $item['post_id'] ] = (int) $id;

			printf( "Importing menu item: %s - %s\n", (int) $id, esc_html( $item['post_title'] ) );

			// Add custom meta.
			$meta_to_exclude   = array_keys( $args );
			$meta_to_exclude[] = 'menu-item-menu-item-parent';
			foreach ( $item['postmeta'] as $meta ) {
				$key = str_replace( '_', '-', ltrim( $meta['key'], '_' ) );
				if ( ! empty( $meta['value'] ) && ! in_array( $key, $meta_to_exclude, true ) ) {
					update_post_meta( $id, $meta['key'], maybe_unserialize( $meta['value'] ) );
				}
			}
		} else {
			echo "Failed to import menu item:\n";

			if ( is_wp_error( $id ) ) {
				print_r( $id->get_error_messages() );
			}

			var_dump( $item );
		}
	}

	/**
	 * Ensure WooCommerce product attributes taxonomies exist during import.
	 *
	 * @param array $raw_post Raw post data.
	 * @return array Filtered post data.
	 */
	public function wc_products_filter( $raw_post ) {
		global $wpdb;

		if ( $raw_post['post_type'] !== 'product' ) {
			return $raw_post;
		}

		if ( empty( $raw_post['terms'] ) ) {
			return $raw_post;
		}

		foreach ( $raw_post['terms'] as $term ) {
			$domain = $term['domain'];

			if ( false === strpos( $domain, 'pa_' ) || taxonomy_exists( $domain ) ) {
				continue;
			}

			// Make sure taxonomy exists!
			$nicename     = strtolower( sanitize_title( str_replace( 'pa_', '', $domain ) ) );
			$exists_in_db = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT attribute_id FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s;",
					$nicename
				)
			);

			// Create the taxonomy
			if ( ! $exists_in_db ) {
				$wpdb->insert(
					"{$wpdb->prefix}woocommerce_attribute_taxonomies",
					array(
						'attribute_name'    => $nicename,
						'attribute_type'    => 'select',
						'attribute_orderby' => 'menu_order',
					),
					array( '%s', '%s', '%s' )
				);
			}

			// Register the taxonomy now so that the import works!
			$tax_args = array(
				'hierarchical' => true,
				'show_ui'      => false,
				'query_var'    => true,
				'rewrite'      => false,
			);
			// Important! Allows us to pass Envato Theme Checker tests.
			$func = 'register_taxonomy';
			$func(
				$domain,
				apply_filters( 'woocommerce_taxonomy_objects_' . $domain, array( 'product' ) ),
				apply_filters( 'woocommerce_taxonomy_args_' . $domain, $tax_args )
			);
		}

		return $raw_post;
	}

	/**
	 * If fetching attachments is enabled then attempt to create a new attachment.
	 *
	 * @param array  $post Attachment post details from WXR.
	 * @param string $url URL to fetch attachment from.
	 * @param array  $postdata Additional post data to use when creating the attachment.
	 * @return int|WP_Error Post ID on success, WP_Error otherwise.
	 */
	public function process_attachment( $post, $url, $postdata = [] ) {
		if ( ! $this->fetch_attachments ) {
			return new WP_Error( 'attachment_processing_error', __( 'Fetching attachments is not enabled', 'wordpress-importer' ) );
		}

		$start = microtime( true ); // Full attachment import (download + processing) start.

		// Save elementor stuff in the separate folder.
		if ( strpos( $url, '/elementor/' ) !== false ) {
			$path_bits                 = explode( '/elementor/', wp_parse_url( $url, PHP_URL_PATH ) );
			$this->elementor_file_path = dirname( '/elementor/' . $path_bits[1] );

			add_filter( 'upload_dir', [ $this, 'uploads_filter' ] );
		}

		$this->log_add( 'Importing [attachment] ' . $url );

		// if the URL is absolute, but does not contain address, then upload it assuming base_site_url.
		if ( preg_match( '|^/[\w\W]+$|', $url ) ) {
			$url = rtrim( $this->base_url, '/' ) . $url;
		}

		$upload = $this->fetch_remote_file( $url, $post );
		if ( is_wp_error( $upload ) ) {
			return $upload;
		}

		$info = wp_check_filetype( $upload['file'] );
		if ( $info ) {
			$post['post_mime_type'] = $info['type'];
		} else {
			return new WP_Error( 'attachment_processing_error', __( 'Invalid file type', 'wordpress-importer' ) );
		}

		$is_image         = isset( $info['type'] ) && preg_match( '!^image/!', $info['type'] );
		$is_svg           = ( isset( $info['ext'] ) && $info['ext'] === 'svg' ) || $post['post_mime_type'] === 'image/svg+xml';
		$can_have_resizes = $is_image && ! $is_svg; // Only raster images can have resizes we care to download.

		$post['guid'] = $upload['url'];

		// as per wp-admin/includes/upload.php.
		$post_id = wp_insert_attachment( $post, $upload['file'] );

		$filesize                  = ! empty( $upload['file'] ) && file_exists( $upload['file'] ) ? filesize( $upload['file'] ) : 0;
		$predicted_processing_time = null;
		if ( $can_have_resizes ) {
			$predicted_processing_time = $this->predict_image_processing_time( $filesize );
			if ( $predicted_processing_time !== null ) {
				$this->log_add( sprintf(
					'Predicted image processing time: %.2fs for %s (based on %d sample%s).',
					$predicted_processing_time,
					$filesize ? size_format( $filesize ) : 'unknown size',
					count( $this->image_processing_stats ),
					count( $this->image_processing_stats ) === 1 ? '' : 's'
				) );
			}
		} else {
			if ( ! $is_image ) {
				$this->log_add( 'Skipping resize prediction: not an image attachment.' );
			} elseif ( $is_svg ) {
				$this->log_add( 'Skipping resize prediction & downloads: SVG attachment.' );
			}
		}

		$use_download_resizes = false;
		if ( $can_have_resizes && $this->download_resizes && isset( $postdata['postmeta'] ) && ! is_wp_error( $post_id ) ) {
			if ( $predicted_processing_time !== null && $predicted_processing_time > 3 ) {
				$use_download_resizes = true;
				$this->log_add( 'Switch to resize downloading (prediction > 3s).' );
			}
		} elseif ( ! $can_have_resizes ) {
			$use_download_resizes = false; // Explicit clarity.
		}

		if ( $use_download_resizes ) {
			$attachment_metadata = array_values(
				array_filter(
					$postdata['postmeta'],
					static function ( $data ) {
						return $data['key'] === '_wp_attachment_metadata';
					}
				)
			);
			$attachment_metadata = maybe_unserialize( $attachment_metadata[0]['value'] ?? null );
			$sizes               = $attachment_metadata['sizes'] ?? [];
			if ( $sizes ) {
				$sizes = $this->download_image_resizes( $post_id, $post, $sizes, dirname( $url ) );
			}
			add_filter( 'intermediate_image_sizes_advanced', '__return_empty_array', 7777 );
			$generated_attachment_metadata = wp_generate_attachment_metadata( $post_id, $upload['file'] );
			remove_filter( 'intermediate_image_sizes_advanced', '__return_empty_array', 7777 );

			$generated_attachment_metadata['sizes'] = $sizes;

			wp_update_attachment_metadata( $post_id, $generated_attachment_metadata );
		} else {
			// Measure only local processing (metadata + resize generation) time for prediction model.
			$processing_start = microtime( true );
			wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );
			$processing_time = microtime( true ) - $processing_start;
			if ( $filesize > 0 ) {
				if ( $can_have_resizes ) {
					$this->record_image_processing_stat( $filesize, $processing_time );
				}
				$this->log_add( sprintf( 'Actual local image processing time: %.2fs for %s.', $processing_time, size_format( $filesize ) ) );
			}
		}

		// remap resized image URLs, works by stripping the extension and remapping the URL stub.
		if ( preg_match( '!^image/!', $info['type'] ) ) {
			$parts = pathinfo( $url );
			$name  = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2.

			$parts_new = pathinfo( $upload['url'] );
			$name_new  = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

			$this->url_remap[ $parts['dirname'] . '/' . $name ] = $parts_new['dirname'] . '/' . $name_new;
		}

		$import_time = microtime( true ) - $start;
		$this->log_add( 'Attachment total import time (download + processing): ' . $import_time . "\n" );

		remove_filter( 'upload_dir', [ $this, 'uploads_filter' ] );

		return $post_id;
	}

	/**
	 * Predict local image processing (metadata + resize generation) time for a given file size.
	 * Uses up to the last 3 recorded stats. Simple proportional model (time per byte).
	 *
	 * @param int $filesize File size in bytes.
	 * @return float|null Predicted seconds, or null if insufficient data.
	 */
	protected function predict_image_processing_time( $filesize ) {
		if ( $filesize <= 0 || count( $this->image_processing_stats ) < 3 ) {
			return null;
		}

		$predictor = new \The7\Mods\Demo_Content\Download_Time_Predictor();
		foreach ( $this->image_processing_stats as $observation ) {
			$predictor->add_observation( $observation[0], $observation[1] );
		}

		return $predictor->predict_time( $filesize );
	}

	/**
	 * Record a new image processing stat (size/time).
	 *
	 * @param int   $filesize Bytes.
	 * @param float $time Seconds.
	 * @return void
	 */
	protected function record_image_processing_stat( $filesize, $time ) {
		$this->image_processing_stats[] = [ (int) $filesize, (float) $time ];
	}

	/**
	 * Download and import image resizes for an attachment.
	 *
	 * @param int    $post_id Attachment post ID.
	 * @param array  $post Attachment post data.
	 * @param array  $sizes Array of image sizes.
	 * @param string $base_url Base URL for image resizes.
	 * @return void
	 */
	protected function download_image_resizes( $post_id, $post, $sizes, $base_url ) {
		add_filter( 'wp_unique_filename', [ $this, 'resizes_name_filter' ], 10, 6 );

		foreach ( $sizes as $size => $data ) {
			if ( empty( $data['file'] ) ) {
				continue;
			}

			$info = wp_check_filetype( $data['file'] );
			if ( $info && $info['ext'] === 'svg' ) {
				continue; // Skip SVGs, they are not resized.
			}

			$start = microtime( true );

			$url = trailingslashit( $base_url ) . $data['file'];

			$this->log_add( 'Importing [attachment] resize ' . $url );

			$upload = $this->fetch_remote_file( $url, $post );
			if ( is_wp_error( $upload ) ) {
				$this->log_add( 'Error: ' . $upload->get_error_message() );
				continue;
			}

			$info = wp_check_filetype( $upload['file'] );
			if ( ! $info ) {
				$this->log_add( 'Error: ' . __( 'Invalid file type', 'wordpress-importer' ) );
				continue;
			}

			$this->log_add( 'Uploaded to ' . $upload['file'] );

			$this->log_add( 'Loaded in ' . ( microtime( true ) - $start ) . "\n" );
		}

		remove_filter( 'wp_unique_filename', [ $this, 'resizes_name_filter' ], 10 );
	}

	/**
	 * Attempt to download a remote file attachment.
	 *
	 * @param string $url URL of item to fetch.
	 * @param array  $post Attachment details.
	 * @param bool   $url_remap Whether to remap URLs after download.
	 * @return array|WP_Error Local file location details on success, WP_Error otherwise.
	 */
	public function fetch_remote_file( $url, $post, $url_remap = true ) {
		$maybe_return_earlier = apply_filters( 'the7_importer_fetch_remote_file', false, $url, $post );
		if ( $maybe_return_earlier ) {
			return $maybe_return_earlier;
		}

		// extract the file name and extension from the url
		$file_name = basename( $url );

		// get placeholder file in the upload dir with a unique, sanitized filename
		$upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
		if ( $upload['error'] ) {
			return new WP_Error( 'upload_dir_error', $upload['error'] );
		}

		// fetch the remote url and write it to the placeholder file
		$remote_response = wp_safe_remote_get( $url, array(
			'timeout' => 300,
			'stream' => true,
			'filename' => $upload['file'],
		) );

		$headers = wp_remote_retrieve_headers( $remote_response );

		// request failed
		if ( ! $headers ) {
			@unlink( $upload['file'] );

			$WP_error = new WP_Error(
				'import_file_error',
				__( 'Remote server did not respond', 'wordpress-importer' ) . ' ( ' . $url . ' ) '
			);

			if ( is_wp_error( $remote_response ) && method_exists( $WP_error, 'merge_from' ) ) {
				$WP_error->merge_from( $remote_response );
			}

			return $WP_error;
		}

		$remote_response_code = wp_remote_retrieve_response_code( $remote_response );

		// make sure the fetch was successful
		if ( $remote_response_code != '200' ) {
			@unlink( $upload['file'] );

			return new WP_Error(
				'import_file_error',
				sprintf(
					'Remote server returned error: url - %1$s ; response code - %2$d %3$s',
					esc_html( $url ),
					esc_html( $remote_response_code ),
					get_status_header_desc( $remote_response_code )
				)
			);
		}

		$filesize = filesize( $upload['file'] );

		if ( isset( $headers['content-length'] ) && $filesize != $headers['content-length'] ) {
			@unlink( $upload['file'] );
			return new WP_Error( 'import_file_error', __('Remote file is incorrect size', 'wordpress-importer') );
		}

		if ( 0 == $filesize ) {
			@unlink( $upload['file'] );
			return new WP_Error( 'import_file_error', __('Zero size file downloaded', 'wordpress-importer') );
		}

		$max_size = (int) $this->max_attachment_size();
		if ( ! empty( $max_size ) && $filesize > $max_size ) {
			@unlink( $upload['file'] );
			return new WP_Error( 'import_file_error', sprintf(__('Remote file is too large, limit is %s', 'wordpress-importer'), size_format($max_size) ) );
		}

		if ( $url_remap ) {
			// keep track of the old and new urls so we can substitute them later
			$this->url_remap[ $url ]          = $upload['url'];
			$this->url_remap[ $post['guid'] ] = $upload['url']; // r13735, really needed?
			// keep track of the destination if the remote url is redirected somewhere else
			if ( isset( $headers['x-final-location'] ) && $headers['x-final-location'] != $url ) {
				$this->url_remap[ $headers['x-final-location'] ] = $upload['url'];
			}
		}

		return $upload;
	}

	/**
	 * Filter for unique filename for image resizes.
	 *
	 * @param string $filename Filename.
	 * @param string $ext Extension.
	 * @param string $dir Directory.
	 * @param callable $unique_filename_callback Callback for unique filename.
	 * @param array $alt_filenames Alternative filenames.
	 * @param int $number Number for uniqueness.
	 * @return string Filtered filename.
	 */
	public function resizes_name_filter( $filename, $ext, $dir, $unique_filename_callback, $alt_filenames, $number ) {
		if ( $number === 1 ) {
			$filename = str_replace(
				"-{$number}{$ext}",
				"{$ext}",
				$filename
			);
		}
		return $filename;
	}

	/**
	 * Create new posts based on import information.
	 *
	 * Posts marked as having a parent which doesn't exist will become top level items.
	 * Doesn't create a new post if: the post type doesn't exist, the given post ID
	 * is already noted as imported or a post with the same title and date already exists.
	 * Note that new/updated terms, comments and meta are imported for the last of the above.
	 *
	 * @return void
	 */
	public function process_posts() {
		$this->posts = apply_filters( 'wp_import_posts', $this->posts );

		if ( ! $this->posts ) {
			$this->add_logger_message( 'There are no posts to import. Skipping' );
		}

		foreach ( $this->posts as $post ) {
			if ( $this->time_exceeded() ) {
				$this->add_logger_message( 'Time is exceeded. Skipping to the next batch...' );
				return;
			}

			$post = apply_filters( 'wp_import_post_data_raw', $post );

			if ( ! post_type_exists( $post['post_type'] ) ) {
				printf( __( 'Failed to import &#8220;%s&#8221;: Invalid post type %s', 'wordpress-importer' ),
					esc_html($post['post_title']), esc_html($post['post_type']) );
				echo self::LOG_ERROR_SEPARATOR;
				do_action( 'wp_import_post_exists', $post );
				continue;
			}

			// Seems that this post is already processed.
			if ( isset( $this->processed_posts[$post['post_id']] ) && ! empty( $post['post_id'] ) ) {
				$this->add_logger_message( "Already imported. Skipping {$post['post_type']} post with ID {$post['post_id']}" );
				continue;
			}

			if ( $post['status'] == 'auto-draft' )
				continue;

			if ( 'nav_menu_item' == $post['post_type'] ) {
				$this->process_menu_item( $post );
				continue;
			}

			$post_type_object = get_post_type_object( $post['post_type'] );

			$post_exists = post_exists( $post['post_title'], '', $post['post_date'], $post['post_type'] );

			/**
			 * Filter ID of the existing post corresponding to post currently importing.
			 *
			 * Return 0 to force the post to be imported. Filter the ID to be something else
			 * to override which existing post is mapped to the imported post.
			 *
			 * @see post_exists()
			 * @since 0.6.2
			 *
			 * @param int   $post_exists  Post ID, or 0 if post did not exist.
			 * @param array $post         The post array to be inserted.
			 */
			$post_exists = apply_filters( 'wp_import_existing_post', $post_exists, $post );

			if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
				printf( __('%s &#8220;%s&#8221; already exists.', 'wordpress-importer'), $post_type_object->labels->singular_name, esc_html($post['post_title']) );
				echo self::LOG_ERROR_SEPARATOR;
				$comment_post_ID = $post_id = $post_exists;
			} else {
				$post_parent = (int) $post['post_parent'];
				if ( $post_parent ) {
					// if we already know the parent, map it to the new local ID
					if ( isset( $this->processed_posts[$post_parent] ) ) {
						$post_parent = $this->processed_posts[$post_parent];
						// otherwise record the parent for later
					} else {
						$this->post_orphans[intval($post['post_id'])] = $post_parent;
						$post_parent = 0;
					}
				}

				// map the post author
				$author = sanitize_user( $post['post_author'], true );
				if ( isset( $this->author_mapping[$author] ) )
					$author = $this->author_mapping[$author];
				else
					$author = (int) get_current_user_id();

				$postdata = [
					'import_id'      => $post['post_id'],
					'post_author'    => $author,
					'post_date'      => $post['post_date'],
					'post_date_gmt'  => $post['post_date_gmt'],
					'post_content'   => $post['post_content'],
					'post_excerpt'   => $post['post_excerpt'],
					'post_title'     => $post['post_title'],
					'post_status'    => $post['status'],
					'post_name'      => $post['post_name'],
					'comment_status' => $post['comment_status'],
					'ping_status'    => $post['ping_status'],
					'guid'           => $post['guid'],
					'post_parent'    => $post_parent,
					'menu_order'     => $post['menu_order'],
					'post_type'      => $post['post_type'],
					'post_password'  => $post['post_password'],
				];

				$original_post_ID = $post['post_id'];
				$postdata = apply_filters( 'wp_import_post_data_processed', $postdata, $post );

				$postdata = wp_slash( $postdata );

				if ( 'attachment' == $postdata['post_type'] ) {
					$remote_url = ! empty($post['attachment_url']) ? $post['attachment_url'] : $post['guid'];

					// try to use _wp_attached file for upload folder placement to ensure the same location as the export site
					// e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload()
					$postdata['upload_date'] = $post['post_date'];
					if ( isset( $post['postmeta'] ) ) {
						foreach( $post['postmeta'] as $meta ) {
							if ( $meta['key'] == '_wp_attached_file' ) {
								if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta['value'], $matches ) )
									$postdata['upload_date'] = $matches[0];
								break;
							}
						}
					}

					$comment_post_ID = $post_id = $this->process_attachment( $postdata, $remote_url, $post );
				} else {
					$this->add_logger_message( "Importing [{$postdata['post_type']}] {$postdata['post_title']}" );

					unset( $postdata['guid'] );

					$comment_post_ID = $post_id = wp_insert_post( $postdata, true );

					// Store old and new post slug.
					if ( is_numeric( $post_id ) && $post_id > 0 ) {
						$this->processed_post_names[ $postdata['post_name'] ] = get_post_field( 'post_name', $post_id, 'db' );
					}

					do_action( 'wp_import_insert_post', $post_id, $original_post_ID, $postdata, $post );
				}

				if ( $post['is_sticky'] === 1 && is_numeric( $post_id ) ) {
					stick_post( $post_id );
				}
			}

			$this->imported_posts++;

			if ( is_wp_error( $post_id ) ) {
				$this->processed_posts[ (int) $post['post_id'] ] = null;

				printf(
					__( 'Failed to import %s &#8220;%s&#8221;', 'wordpress-importer' ),
					$post_type_object->labels->singular_name,
					esc_html( $post['post_title'] )
				);
				echo ': ' . implode( ', ', $post_id->get_error_messages() );
				echo "\n";
				continue;
			}

			$this->processed_posts[ (int) $post['post_id'] ] = (int) $post_id;

			if ( ! isset( $post['terms'] ) )
				$post['terms'] = array();

			$post['terms'] = apply_filters( 'wp_import_post_terms', $post['terms'], $post_id, $post );

			// add categories, tags and other terms
			if ( ! empty( $post['terms'] ) ) {
				$terms_to_set = array();
				foreach ( $post['terms'] as $term ) {
					// back compat with WXR 1.0 map 'tag' to 'post_tag'
					$taxonomy = ( 'tag' == $term['domain'] ) ? 'post_tag' : $term['domain'];
					$term_exists = term_exists( $term['slug'], $taxonomy );
					$term_id = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
					if ( ! $term_id ) {
						$t = wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
						if ( ! is_wp_error( $t ) ) {
							$term_id = $t['term_id'];
							do_action( 'wp_import_insert_term', $t, $term, $post_id, $post );
						} else {
							printf( __( 'Failed to import %s %s', 'wordpress-importer' ), esc_html($taxonomy), esc_html($term['name']) );
							echo ': ' . $t->get_error_message();
							echo self::LOG_ERROR_SEPARATOR;
							do_action( 'wp_import_insert_term_failed', $t, $term, $post_id, $post );
							continue;
						}
					}
					$terms_to_set[$taxonomy][] = intval( $term_id );
				}

				foreach ( $terms_to_set as $tax => $ids ) {
					$tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
					do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $post );
				}
				unset( $post['terms'], $terms_to_set );
			}

			if ( ! isset( $post['comments'] ) )
				$post['comments'] = array();

			$post['comments'] = apply_filters( 'wp_import_post_comments', $post['comments'], $post_id, $post );

			// add/update comments
			if ( ! empty( $post['comments'] ) ) {
				$num_comments = 0;
				$inserted_comments = array();
				foreach ( $post['comments'] as $comment ) {
					$comment_id	= $comment['comment_id'];
					$newcomments[$comment_id]['comment_post_ID']      = $comment_post_ID;
					$newcomments[$comment_id]['comment_author']       = $comment['comment_author'];
					$newcomments[$comment_id]['comment_author_email'] = $comment['comment_author_email'];
					$newcomments[$comment_id]['comment_author_IP']    = $comment['comment_author_IP'];
					$newcomments[$comment_id]['comment_author_url']   = $comment['comment_author_url'];
					$newcomments[$comment_id]['comment_date']         = $comment['comment_date'];
					$newcomments[$comment_id]['comment_date_gmt']     = $comment['comment_date_gmt'];
					$newcomments[$comment_id]['comment_content']      = $comment['comment_content'];
					$newcomments[$comment_id]['comment_approved']     = $comment['comment_approved'];
					$newcomments[$comment_id]['comment_type']         = $comment['comment_type'];
					$newcomments[$comment_id]['comment_parent'] 	  = $comment['comment_parent'];
					$newcomments[$comment_id]['commentmeta']          = isset( $comment['commentmeta'] ) ? $comment['commentmeta'] : array();
					if ( isset( $this->processed_authors[$comment['comment_user_id']] ) )
						$newcomments[$comment_id]['user_id'] = $this->processed_authors[$comment['comment_user_id']];
				}
				ksort( $newcomments );

				foreach ( $newcomments as $key => $comment ) {
					// if this is a new post we can skip the comment_exists() check
					if ( ! $post_exists || ! comment_exists( $comment['comment_author'], $comment['comment_date'] ) ) {
						if ( isset( $inserted_comments[$comment['comment_parent']] ) )
							$comment['comment_parent'] = $inserted_comments[$comment['comment_parent']];

						$commentmeta = $comment['commentmeta'];
						$comment = wp_slash( $comment );
						$comment = wp_filter_comment( $comment );
						$inserted_comments[$key] = wp_insert_comment( $comment );
						do_action( 'wp_import_insert_comment', $inserted_comments[$key], $comment, $comment_post_ID, $post );

						foreach( $commentmeta as $meta ) {
							$value = maybe_unserialize( $meta['value'] );
							add_comment_meta( $inserted_comments[$key], wp_slash( $meta['key'] ), wp_slash( $value ) );
						}

						$num_comments++;
					}
				}
				unset( $newcomments, $inserted_comments, $post['comments'] );
			}

			if ( ! isset( $post['postmeta'] ) )
				$post['postmeta'] = array();

			$post['postmeta'] = apply_filters( 'wp_import_post_meta', $post['postmeta'], $post_id, $post );

			// add/update post meta
			if ( ! empty( $post['postmeta'] ) ) {
				foreach ( $post['postmeta'] as $meta ) {
					$key   = apply_filters( 'import_post_meta_key', $meta['key'], $post_id, $post );
					$value = false;

					if ( '_edit_last' == $key ) {
						if ( isset( $this->processed_authors[ intval( $meta['value'] ) ] ) ) {
							$value = $this->processed_authors[ intval( $meta['value'] ) ];
						} else {
							$key = false;
						}
					}

					if ( $key ) {
						// export gets meta straight from the DB so could have a serialized string
						if ( ! $value ) {
							$value = maybe_unserialize( $meta['value'] );
						}

						$value = apply_filters( 'wp_import_post_meta_value', $value, $key );

						add_post_meta( $post_id, wp_slash( $key ), wp_slash( $value ) );
						do_action( 'import_post_meta', $post_id, $key, $value );

						// if the post has a featured image, take note of this in case of remap
						if ( '_thumbnail_id' == $key ) {
							$this->featured_images[ $post_id ] = (int) $value;
						}
					}
				}
			}
		}

		unset( $this->posts );
	}

	/**
	 * Filter upload directory for Elementor files.
	 *
	 * @param array $uploads Uploads array.
	 * @return array Filtered uploads array.
	 */
	public function uploads_filter( $uploads ) {
		if ( isset( $this->elementor_file_path ) ) {
			$uploads['path'] = $uploads['basedir'] . $this->elementor_file_path;
		}

		return $uploads;
	}

	/**
	 * Cache processed data (posts, terms, authors, images, url remap, etc.)
	 * so that subsequent batched imports can resume without re-processing
	 * previously imported entities.
	 *
	 * @return void
	 */
	public function cache_processed_data() {
		self::clear_session();

		$data = [
			'processed_authors'      => $this->processed_authors,
			'processed_terms'        => $this->processed_terms,
			'processed_posts'        => $this->processed_posts,
			'featured_images'        => $this->featured_images,
			'url_remap'              => $this->url_remap,
			'base_url'               => $this->base_url,
			'image_processing_stats' => $this->image_processing_stats,
			'processed_post_names'   => $this->processed_post_names,
		];
		update_option( self::THE7_PROCESSED_DATA_KEY, $data, false);
	}

	/**
	 * Restore previously cached processed data into the importer instance
	 * (used for continuing batched imports).
	 *
	 * @return void
	 */
	public function read_processed_data_from_cache() {
		$this->log_add( "\n" . str_repeat( '-', 30 ) );

		$processed_data = get_option( self::THE7_PROCESSED_DATA_KEY );
		if ( ! is_array( $processed_data ) || empty( $processed_data ) ) {
			$this->log_add( 'There is no session stored.' );
			return;
		}

		$this->log_add( 'Restore session:' );

		foreach ( $processed_data as $group => $data ) {
			if ( property_exists( $this, $group ) ) {
				if ( $data ) {
					$this->$group = $data;
				}
				$this->log_add(
					sprintf(
						'[%1$s] with %2$s items.',
						$group,
						count( (array) $data )
					)
				);
			}
		}

		$this->log_add( str_repeat( '-', 30 ) . "\n" );
	}

	/**
	 * Clear the processed data session cache.
	 *
	 * @return void
	 */
	public static function clear_session() {
		delete_option( self::THE7_PROCESSED_DATA_KEY );
	}

	/**
	 * Decide whether or not the importer is allowed to create users.
	 * Default is false (we don't want to scare users), can be filtered via import_allow_create_users.
	 *
	 * @return bool True if creating users is allowed.
	 */
	public function allow_create_users() {
		return apply_filters( 'import_allow_create_users', false );
	}

	/**
	 * Get the processed post ID mapped from the original post ID.
	 *
	 * @param int $post_id Original post ID.
	 * @return int Processed post ID or 0 if not found.
	 */
	public function get_processed_post( $post_id ) {
		if ( isset( $this->processed_posts[ $post_id ] ) ) {
			return $this->processed_posts[ $post_id ];
		}

		return 0;
	}

	/**
	 * Get the processed post for the filtered post ID.
	 *
	 * @return int Processed post ID or 0 if not found.
	 */
	public function get_processed_filtered_post() {
		return $this->get_processed_post( $this->filtered_post_id );
	}

	/**
	 * Return the post ID (from the demo data) after applying a URL or ID filter.
	 *
	 * @return int|null Filtered post ID or null if not set.
	 */
	public function get_filtered_post_id() {
		return $this->filtered_post_id;
	}

	/**
	 * Get the processed term ID mapped from the original term ID.
	 *
	 * @param int $term_id Original term ID.
	 * @return int Processed term ID or 0 if not found.
	 */
	public function get_processed_term( $term_id ) {
		if ( isset( $this->processed_terms[ $term_id ] ) ) {
			return $this->processed_terms[ $term_id ];
		}

		return 0;
	}

	/**
	 * Get the processed taxonomy ID mapped from the original taxonomy ID.
	 *
	 * @param int $tax_id Original taxonomy ID.
	 * @return int Processed taxonomy ID or 0 if not found.
	 */
	public function get_processed_taxonomy_id( $tax_id ) {
		if ( isset( $this->processed_taxonomies[ $tax_id ] ) ) {
			return $this->processed_taxonomies[ $tax_id ];
		}

		return 0;
	}

	/**
	 * @since 10.4.3
	 *
	 * @param int $id Author id.
	 *
	 * @return int
	 */
	public function get_processed_author_id( $id ) {
		if ( isset( $this->processed_authors[ $id ] ) ) {
			return $this->processed_authors[ $id ];
		}

		return 0;
	}

	/**
	 * Filter import to a single post identified by its URL.
	 *
	 * @param string $post_url Post URL to import (plus dependencies).
	 * @return void
	 */
	public function add_filter_by_url( $post_url ) {
		$this->filter_by_url = $post_url;
		$this->add_import_posts_filter();
	}

	/**
	 * Filter import to a single post identified by its original demo post ID.
	 *
	 * @param int $post_id Original demo post ID.
	 * @return void
	 */
	public function add_filter_by_id( $post_id ) {
		$this->filter_by_id = $post_id;
		$this->add_import_posts_filter();
	}

	/**
	 * Reset the log.
	 */
	public function log_reset() {
		delete_option( 'the7_import_log' );
	}

	/**
	 * Add a message to the log.
	 *
	 * @param string $message Message.
	 */
	public function log_add( $message ) {
		if ( $message ) {
			update_option( 'the7_import_log', $this->log_get() . "\n" . $message, false );
		}
	}

	/**
	 * Get current import log contents.
	 *
	 * @return string Log text.
	 */
	public function log_get() {
		return (string) get_option( 'the7_import_log' );
	}

	/**
	 * Find a post by its link (URL) in parsed WXR posts array.
	 *
	 * @param array $posts Parsed posts.
	 * @return array Found post array or empty array if not found.
	 */
	protected function find_post_by_url( $posts ) {
		foreach ( $posts as $post ) {
			if ( (string) $post['link'] === (string) $this->filter_by_url ) {
				return $post;
			}
		}

		return [];
	}

	/**
	 * Find a post by its original demo post_id in parsed WXR posts array.
	 *
	 * @param array $posts Parsed posts.
	 * @return array Found post array or empty array if not found.
	 */
	protected function find_post_by_id( $posts ) {
		foreach ( $posts as $post ) {
			if ( (int) $post['post_id'] === (int) $this->filter_by_id ) {
				return $post;
			}
		}

		return [];
	}

	/**
	 * Attach a filter to limit parsed posts to only the requested resource + its dependencies.
	 *
	 * @return void
	 */
	protected function add_import_posts_filter() {
		add_filter(
			'wp_import_parse',
			function( $parsed_file ) {
				$posts = $parsed_file['posts'];

				$this->log_add( 'Filtering ' . count( $posts ) . ' posts by [' . ( $this->filter_by_url ?: $this->filter_by_id ) . ']' );

				if ( $this->filter_by_url ) {
					$found_post = $this->find_post_by_url( $posts );
				} elseif ( $this->filter_by_id ) {
					$found_post = $this->find_post_by_id( $posts );
				}

				if ( empty( $found_post ) ) {
					$this->log_add( 'Requested post was not found' );

					return [];
				}

				$this->log_add( "Post with ID [{$found_post['post_id']}] was found" );

				$this->filtered_post_id = $found_post['post_id'];
				$filtered_posts         = [ $found_post ];

				$resources = array_reduce(
					(array) $found_post['postmeta'],
					function ( $acc, $item ) {
						return $item['key'] === static::THE7_POST_RESOURCES_META_KEY ? maybe_unserialize( $item['value'] ) : $acc;
					},
					[]
				);

				$this->log_add( 'Found ' . count( $resources ) . ' resources' );

				$posts_terms = [];
				foreach ( $posts as $post ) {
					if ( in_array( $post['post_id'], $resources, false ) ) {
						if ( $post['post_type'] === 'elementor_library' && ! empty( $post['postmeta'] ) ) {
							foreach ( $post['postmeta'] as &$post_meta ) {
								if ( $post_meta['key'] === '_elementor_conditions' ) {
									if ( $found_post['post_type'] === 'product' ) {
										$value = [
											'include/woocommerce/product/' . $this->filtered_post_id,
										];
									} else {
										$value = [
											'include/singular/' . $found_post['post_type'] . '/' . $this->filtered_post_id,
										];
									}
									$post_meta['value'] = maybe_serialize( $value );
									break;
								}
							}
							unset( $post_meta );
						}

						if ( ! empty( $post['terms'] ) ) {
							$posts_terms[] = $post['terms'];
						}

						$filtered_posts[] = $post;
					}
				}

				$posts_terms = array_merge( [], ...$posts_terms );
				$posts_terms = array_unique( wp_list_pluck( $posts_terms, 'slug' ) );

				$parsed_file['posts'] = $filtered_posts;

				$parsed_file['categories'] = array_filter(
					$parsed_file['categories'],
					function( $term ) use ( $posts_terms ) {
						return in_array( $term['category_nicename'], $posts_terms, true );
					}
				);

				$parsed_file['tags'] = array_filter(
					$parsed_file['tags'],
					function( $term ) use ( $posts_terms ) {
						return in_array( $term['tag_slug'], $posts_terms, true );
					}
				);

				$parsed_file['terms'] = array_filter(
					$parsed_file['terms'],
					function( $term ) use ( $posts_terms ) {
						return in_array( $term['slug'], $posts_terms, true );
					}
				);

				return $parsed_file;
			}
		);
	}

	/**
	 * Find an existing attachment by its _wp_attached_file meta value.
	 *
	 * @param string $wp_attached_file Relative path stored in _wp_attached_file.
	 * @return int Attachment post ID or 0 if not found.
	 */
	protected function attachment_exists( $wp_attached_file ) {
		global $wpdb;

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE 1=1 AND meta_key = '_wp_attached_file' AND meta_value = %s",
				$wp_attached_file
			)
		);
	}

	/**
	 * Check if the importer allowed to download resizes.
	 *
	 * @return bool
	 */
	protected function allow_downloading_resizes() {
		return $this->download_resizes;
	}
}
