<?php
/**
 * Post content processor that applies registered block migrations.
 */

namespace The7\Mods\Theme_Update\Base\Block_Editor;

defined( 'ABSPATH' ) || exit;

class Post_Content_Wp_Block_Processor {

	/**
	 * @var Wp_Block_Migration_Base[]
	 */
	protected $migrations = [];

	/**
	 * @param string[] $migration_classes List of fully qualified class names.
	 */
	public function __construct( array $migration_classes ) {
		foreach ( $migration_classes as $class ) {
			if ( is_a( $class, Wp_Block_Migration_Base::class, true ) ) {
				$this->migrations[] = new $class();
			}
		}
	}

	public function process( string $post_content ): string {
		$blocks = parse_blocks( $post_content );

		if ( count( $blocks ) === 1 && empty( $blocks[0]['blockName'] ) && empty( $blocks[0]['innerBlocks'] ) ) {
			return $post_content;
		}

		foreach ( $this->migrations as $migration ) {
			$blocks = $this->process_blocks_recursive( $blocks, $migration );
		}

		return serialize_blocks( $blocks );
	}

	protected function process_blocks_recursive( array $blocks, Wp_Block_Migration_Base $migration ): array {
		foreach ( $blocks as &$block ) {
			if ( ! is_array( $block ) ) {
				continue;
			}

			if ( $migration->need_to_process_block( $block ) ) {
				$block = $migration->process_block( $block );
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$block['innerBlocks'] = $this->process_blocks_recursive( $block['innerBlocks'], $migration );
			}
		}

		return $blocks;
	}
}
