<?php
/**
 * This file handles a class of helper methods.
 *
 * @author     Nicola Palermo
 * @since      1.0.0
 * @package    Wubtitle
 */

namespace Wubtitle;

/**
 * This class handles some helper methods used throughout the plugin.
 */
class Helpers {

	/**
	 * Check if gutenberg is active.
	 *
	 * @return bool
	 */
	public function is_gutenberg_active() {
		// Gutenberg plugin is installed and activated.
		// @phpstan-ignore-next-line. False positive, does not recognize gutenberg_init as a callback.
		$gutenberg = ! ( false === has_filter( 'replace_editor', 'gutenberg_init' ) );

		$block_editor = false;
		// Block editor since 5.0.
		if ( isset( $GLOBALS['wp_version'] ) ) {
			$block_editor = version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' );
		}

		if ( ! $gutenberg && ! $block_editor ) {
			return false;
		}

		if ( $this->is_classic_editor_active() ) {
			$editor_option       = get_option( 'classic-editor-replace' );
			$block_editor_active = array( 'no-replace', 'block' );

			return in_array( $editor_option, $block_editor_active, true );
		}

		return true;
	}

	/**
	 * Check if classic editor is active.
	 *
	 * @return bool
	 */
	public function is_classic_editor_active() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return is_plugin_active( 'classic-editor/classic-editor.php' );
	}

}
