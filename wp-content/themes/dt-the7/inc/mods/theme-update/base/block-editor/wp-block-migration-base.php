<?php
/**
 * Base class for Gutenberg block migrations within The7.
 */

namespace The7\Mods\Theme_Update\Base\Block_Editor;

defined( 'ABSPATH' ) || exit;

abstract class Wp_Block_Migration_Base {

	/**
	 * List of supported block names. Empty array means all blocks.
	 */
	protected const BLOCK_NAMES = [];

	/**
	 * Optional attribute that must exist before processing block.
	 */
	protected const ATTR_NAME = null;

	/**
	 * Optional attribute group (array) that must exist before processing block.
	 */
	protected const ATTR_GROUP_NAME = null;

	public function need_to_process_block( array $block ): bool {
		if ( ! $this->is_block_supported( $block ) ) {
			return false;
		}

		if ( null !== static::ATTR_NAME && ! isset( $block['attrs'][ static::ATTR_NAME ] ) ) {
			return false;
		}

		if ( null !== static::ATTR_GROUP_NAME && ! is_array( $block['attrs'][ static::ATTR_GROUP_NAME ] ?? null ) ) {
			return false;
		}

		return true;
	}

	abstract public function process_block( array $block ): array;

	protected function is_block_supported( array $block ): bool {
		if ( empty( $block['blockName'] ) ) {
			return false;
		}

		if ( static::BLOCK_NAMES ) {
			return in_array( $block['blockName'], static::BLOCK_NAMES, true );
		}

		return true;
	}
}
