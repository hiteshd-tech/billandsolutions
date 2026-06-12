<?php
/**
 * Full Site Editing (FSE) importer utilities for remapping IDs, URLs and related
 * attributes inside block markup after demo content import.
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent\Importers;

use BetterBlockEditor\Modules\DemoContent\FontDownloader;
use BetterBlockEditor\Modules\DemoContent\Trackers\ContentTracker;

defined( 'ABSPATH' ) || exit;

/**
 * Handles post‑processing of imported content (ID remapping, global styles merging,
 * template/theme adoption and URL adjustments).
 */
class FSEImporter {

	/**
	 * Tracks original / existing content pieces for potential rollback or merge logic.
	 *
	 * @var ContentTracker
	 */
	private $content_tracker;

	/**
	 * Underlying content importer that performs the raw WXR import.
	 *
	 * @var ContentImporter
	 */
	private $importer;

	/**
	 * FSEImporter constructor.
	 *
	 * @param ContentImporter $importer        Content importer performing the initial import.
	 * @param ContentTracker  $content_tracker Tracks stored content snapshots.
	 */
	public function __construct( $importer, $content_tracker ) {
		$this->content_tracker = $content_tracker;
		$this->importer        = $importer;
	}

	/**
	 * Remap post IDs and related URLs inside block markup after import.
	 *
	 * Processes only imported published posts with non‑empty content. Also updates
	 * block attributes that reference attachments / template parts and normalises
	 * global styles & font face posts.
	 *
	 * @return void
	 */
	public function remap_post_ids_and_urls_in_blocks() {
		global $wpdb;

		$processed_post_ids = array_map( 'intval', array_values( $this->importer->processed_posts ) );
		if ( empty( $processed_post_ids ) ) {
			return;
		}

		$this->importer->log_add( 'Remap post ids and urls in blocks...' );

		// Build placeholders string (safe, contains only '%d' tokens) then prepare.
		$placeholders = implode( ',', array_fill( 0, count( $processed_post_ids ), '%d' ) );
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Dynamic list of IDs converted to placeholder tokens then prepared; single run, low impact.
		$posts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->posts} WHERE post_content != '' AND post_status = %s AND ID IN ($placeholders)", array_merge( array( 'publish' ), $processed_post_ids ) ) );

		$home_url = home_url();

		// Fix uploads url. With trailing slash.
		$uploads_url_to_replace_regexp     = '|' . preg_quote( str_replace( '/', '\/', $this->importer->base_url ), '|' ) . '\\\/wp-content\\\/uploads\\\/(sites\\\/\d*\\\/)?|';
		$local_uploads_url_for_replacement = str_replace( '/', '\/', trailingslashit( wp_get_upload_dir()['baseurl'] ) );

		add_filter( 'wp_revisions_to_keep', '__return_zero', 99 );

		foreach ( $posts as $post ) {
			$updated      = array();
			$post_content = $post->post_content;

			// Custom logic for global styles and font face.
			if ( in_array( $post->post_type, array( 'wp_global_styles', 'wp_font_face' ), true ) ) {

				if ( $post->post_type === 'wp_global_styles' ) {
					$this->importer->log_add( 'Process global styles: ' . $post->ID );

					// Store fse fonts to download later.
					FontDownloader::instance()->get_fonts_from_json( $post_content );
				} else {
					$this->importer->log_add( 'Process font face: ' . $post->ID );
				}

				$post_content = preg_replace( $uploads_url_to_replace_regexp, $local_uploads_url_for_replacement, $post_content );

				wp_update_post(
					array(
						'ID'           => $post->ID,
						'post_content' => wp_slash( $post_content ),
					)
				);

				continue;
			}

			$urls = self::get_non_attachment_urls( $post_content );

			foreach ( $urls as $url ) {
				$new_url      = str_replace( $this->importer->base_url, $home_url, $url );
				$post_content = str_replace( $url, $new_url, $post_content );
			}

			$blocks        = parse_blocks( $post_content );
			$edited_blocks = self::iterate_blocks(
				$blocks,
				function ( $block ) use ( &$updated ) {
					if ( empty( $block['attrs'] ) ) {
						return $block;
					}

					$attrs = $block['attrs'];

					if ( isset( $attrs['style']['background']['backgroundImage']['id'] ) ) {
						$id = $attrs['style']['background']['backgroundImage']['id'];
						if ( ! empty( $this->importer->processed_posts[ $id ] ) ) {
							$processed_post_id = $this->importer->processed_posts[ $id ];

							$attrs['style']['background']['backgroundImage']['id']  = $processed_post_id;
							$attrs['style']['background']['backgroundImage']['url'] = wp_get_attachment_url( $processed_post_id );

							$updated[] = array(
								'block'  => $block['blockName'],
								'old_id' => $id,
								'new_id' => $processed_post_id,
							);
						}
					}

					if ( isset( $attrs['id'] ) ) {
						$id = $attrs['id'];
						if ( ! empty( $this->importer->processed_posts[ $id ] ) ) {
							$attrs['id'] = $this->importer->processed_posts[ $id ];
							$updated[]   = array(
								'block'  => $block['blockName'],
								'old_id' => $id,
								'new_id' => $attrs['id'],
							);
						}
					}

					if ( isset( $attrs['ref'] ) ) {
						$ref = $attrs['ref'];
						if ( ! empty( $this->importer->processed_posts[ $ref ] ) ) {
							$attrs['ref'] = $this->importer->processed_posts[ $ref ];
							$updated[]    = array(
								'block'   => $block['blockName'],
								'old_ref' => $ref,
								'new_ref' => $attrs['ref'],
							);
						}
					}

					// Handle wp:query block.
					if ( isset( $block['blockName'] ) && $block['blockName'] === 'core/query' ) {
						$old_attrs = $attrs;

						unset( $attrs['query']['author'] );
						unset( $attrs['query']['exclude'] );

						if ( isset( $attrs['query']['taxQuery'] ) ) {
							$tax_query = $attrs['query']['taxQuery'];

							if ( isset( $tax_query['post_tag'] ) && is_array( $tax_query['post_tag'] ) ) {
								$tax_query['post_tag'] = array_filter(
									array_map(
										function ( $tag_id ) {
											return ! empty( $this->importer->processed_terms[ $tag_id ] ) ? $this->importer->processed_terms[ $tag_id ] : null;
										},
										$tax_query['post_tag']
									)
								);
							}
							if ( isset( $tax_query['category'] ) && is_array( $tax_query['category'] ) ) {
								$tax_query['category'] = array_filter(
									array_map(
										function ( $cat_id ) {
											return ! empty( $this->importer->processed_terms[ $cat_id ] ) ? $this->importer->processed_terms[ $cat_id ] : null;
										},
										$tax_query['category']
									)
								);
							}
							$attrs['query']['taxQuery'] = $tax_query;
						}

						$updated[] = array(
							'block'     => $block['blockName'],
							'old_attrs' => $old_attrs,
							'new_attrs' => $attrs,
						);
					}

					// Handle template parts.
					if ( isset( $block['blockName'] ) && $block['blockName'] === 'core/template-part' ) {
						if ( isset( $attrs['slug'] ) ) {
							$attrs['slug'] = $this->importer->processed_post_names[ $attrs['slug'] ] ?? $attrs['slug'];
						}

						// Adopt for activated theme.
						if ( isset( $attrs['theme'] ) ) {
							$attrs['theme'] = get_stylesheet();
						}
					}

					$block['attrs'] = $attrs;

					return $block;
				}
			);

			$this->importer->log_add( 'Process post: ' . $post->ID );

			wp_update_post(
				array(
					'ID'           => $post->ID,
					'post_content' => wp_slash( serialize_blocks( $edited_blocks ) ),
				)
			);

			// Handle post meta _wp_page_template.
			$template = get_post_meta( $post->ID, '_wp_page_template', true );
			if ( $template && isset( $this->importer->processed_post_names[ $template ] ) ) {
				update_post_meta( $post->ID, '_wp_page_template', $this->importer->processed_post_names[ $template ] );
				$this->importer->log_add( ' - Update page template: ' . $template . ' -> ' . $this->importer->processed_post_names[ $template ] );
			}
		}

		remove_filter( 'wp_revisions_to_keep', '__return_zero', 99 );

		$this->importer->log_add( 'Done remapping.' );
	}

	/**
	 * Import plugin settings.
	 *
	 * @param array $site_meta Site meta data.
	 *
	 * @return bool
	 */
	public function import_block_editor_settings( array $site_meta ) {
		if ( empty( $site_meta['wpbbe_settings'] ) || ! is_array( $site_meta['wpbbe_settings'] ) ) {
			$this->importer->log_add( 'No Better Block Editor settings to import.' );

			return false;
		}

		$this->importer->log_add( 'Importing Block Editor settings...' );

		foreach ( $site_meta['wpbbe_settings'] as $name => $value ) {
			if ( strpos( $name, WPBBE_PLUGIN_ID ) === 0 ) {
				update_option( $name, maybe_unserialize( $value ) );
				$this->importer->log_add( 'Update ' . esc_html( $name ) );
			} else {
				$this->importer->log_add( 'Skip ' . esc_html( $name ) );
			}
		}

		$this->importer->log_add( 'Done' );

		return true;
	}

	/**
	 * Process global styles post. Intended to be used with wp_import_post_data_raw filter.
	 *
	 * @param array $post_array Post array.
	 */
	public function process_global_styles_filter( $post_array ) {
		if ( ! isset( $post_array['post_type'] ) || $post_array['post_type'] !== 'wp_global_styles' ) {
			return $post_array;
		}

		$existing_post_id = \WP_Theme_JSON_Resolver::get_user_global_styles_post_id();
		if ( ! $existing_post_id ) {
			return $post_array;
		}

		// Store the global styles content in the content tracker.
		if ( $this->content_tracker ) {
			$this->content_tracker->add( 'wp_global_styles', get_post_field( 'post_content', $existing_post_id, 'raw' ) );
		}

		// Update existing global styles post instead of creating a new one.
		$result = wp_update_post(
			array(
				'ID'           => $existing_post_id,
				'post_content' => wp_slash( $post_array['post_content'] ),
			),
			true
		);

		if ( is_wp_error( $result ) ) {
			$this->importer->log_add( 'Error updating global styles post: ' . $result->get_error_message() );
			return $post_array;
		}

		$this->importer->log_add( 'Global styles post updated: ' . $existing_post_id );

		$this->importer->processed_posts[ $post_array['post_id'] ] = $existing_post_id;

		return $post_array;
	}

	/**
	 * Process template terms. Intended to be used with wp_import_post_terms filter.
	 *
	 * At the moment, this function only updates the term slugs for the 'wp_theme' domain adopting to the active theme.
	 *
	 * @param array $terms   Terms array.
	 * @param int   $post_id Post ID.
	 * @param array $post    Post array.
	 *
	 * @return array
	 */
	public function process_template_terms( $terms, $post_id, $post ) {
		if ( in_array( $post['post_type'], array( 'wp_template', 'wp_template_part' ), true ) ) {
			foreach ( $terms as $key => $term ) {
				if ( isset( $term['domain'], $term['slug'] ) && $term['domain'] === 'wp_theme' ) {
					$terms[ $key ]['slug'] = get_stylesheet();
				}
			}
		}

		return $terms;
	}

	/**
	 * Run before importing full content.
	 */
	public function do_before_importing_content() {
		add_filter( 'wp_import_post_data_raw', array( $this, 'process_global_styles_filter' ) );
		add_filter( 'wp_import_post_terms', array( $this, 'process_template_terms' ), 10, 3 );
	}

	/**
	 * Iterate through blocks and apply a callback function to each block.
	 *
	 * @param array    $blocks  Parsed blocks.
	 * @param callable $callback Callback function to apply to each block.
	 *
	 * @return array
	 */
	public static function iterate_blocks( $blocks, $callback ) {
		foreach ( $blocks as $i => $block ) {
			if ( ! empty( $block['innerBlocks'] ) ) {
				$block['innerBlocks'] = self::iterate_blocks( $block['innerBlocks'], $callback );
			}

			$blocks[ $i ] = $callback( $block );
		}

		return $blocks;
	}

	/**
	 * Get all non-attachment URLs from the content.
	 *
	 * @param string $content Content to search for URLs.
	 *
	 * @return array
	 */
	public static function get_non_attachment_urls( $content ) {
		$regex = '~
    \bhttps?://                     # Start with http:// or https://
    [^\s"\'<>]+                     # Match all non-space, non-quote, non-bracket characters
    (?=["\'\s<>]|$)                 # Stop at quote, space, bracket, or end of string
    ~xi';

		preg_match_all( $regex, $content, $matches );

		$urls = $matches[0];

		// Filter out attachment urls.
		$urls = array_filter(
			$urls,
			function ( $url ) {
				return strpos( $url, 'wp-content/uploads' ) === false;
			}
		);

		// Remove any potential trailing commas or quotes.
		$clean_urls = array_map(
			function ( $url ) {
				return rtrim( $url, '",' );
			},
			$urls
		);

		return array_unique( $clean_urls );
	}
}
