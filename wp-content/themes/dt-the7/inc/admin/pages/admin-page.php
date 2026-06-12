<?php
/**
 * Base admin page.
 *
 * @package The7\Admin\Pages
 */

namespace The7\Admin\Pages;

defined( 'ABSPATH' ) || exit;

abstract class Admin_Page {

	/**
	 * @var string
	 */
	protected $slug = '';

	/**
	 * @var string
	 */
	protected $title = '';

	/**
	 * @var string
	 */
	protected $capability = '';

	/**
	 * @var string
	 */
	protected $screen_path = '';

	/**
	 * @var string
	 */
	protected $hook_suffix = '';

	/**
	 * Get menu slug.
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Get page title.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Get required capability.
	 *
	 * @return string
	 */
	public function get_capability() {
		return $this->capability;
	}

	/**
	 * Get path to page template.
	 *
	 * @return string
	 */
	public function get_screen_path() {
		return $this->screen_path;
	}

	/**
	 * Store page hook suffix.
	 *
	 * @param  string $hook_suffix Hook suffix.
	 */
	public function set_hook_suffix( string $hook_suffix ) {
		$this->hook_suffix = $hook_suffix;
	}

	/**
	 * Get page hook suffix.
	 *
	 * @return string
	 */
	public function get_hook_suffix() {
		return $this->hook_suffix;
	}

	/**
	 * Render page content.
	 */
	public function render() {
		include $this->get_screen_path();
	}

	/**
	 * Add a top level admin menu page.
	 *
	 * @param string $menu_title Menu title. Defaults to page title.
	 * @param string $icon_url   Icon URL.
	 * @param int    $position   Menu position.
	 *
	 * @return string Hook suffix.
	 */
	public function add_menu_page( $menu_title = '', $icon_url = '', $position = null ) {
		$title = $menu_title ? $menu_title : $this->get_title();

		$hook_suffix = add_menu_page(
			$this->get_title(),
			$title,
			$this->get_capability(),
			$this->get_slug(),
			[ $this, 'render' ],
			$icon_url,
			$position
		);

		$this->set_hook_suffix( $hook_suffix );

		$this->on_after_add_menu_page( $hook_suffix );

		return $hook_suffix;
	}

	/**
	 * Add submenu page under current page.
	 *
	 * @param Admin_Page $submenu_page Submenu page instance.
	 * @param array      $args         Optional overrides for menu args.
	 *
	 * @return array {
	 *     @type string $hook_suffix Hook suffix.
	 *     @type array  $page        Final page args after filters.
	 * }
	 */
	public function add_submenu_page( Admin_Page $submenu_page, $args = [] ) {
		$page = wp_parse_args(
			$args,
			[
				'dashboard_slug' => $this->get_slug(),
				'slug'           => $submenu_page->get_slug(),
				'title'          => $submenu_page->get_title(),
				'capability'     => $submenu_page->get_capability(),
				'page_title'     => '',
				'menu_title'     => '',
				'callback'       => [ $submenu_page, 'render' ],
			]
		);

		$page = apply_filters( 'the7_subpages_filter', $page );

		$hook_suffix = add_submenu_page(
			$page['dashboard_slug'],
			$page['page_title'] ? $page['page_title'] : $page['title'],
			$page['menu_title'] ? $page['menu_title'] : $page['title'],
			$page['capability'],
			$page['slug'],
			isset( $page['callback'] ) ? $page['callback'] : [ $submenu_page, 'render' ]
		);

		$submenu_page->set_hook_suffix( $hook_suffix );

		$this->on_after_add_menu_page( $hook_suffix );

		return [
			'hook_suffix' => $hook_suffix,
			'page'        => $page,
		];
	}

	public function enqueue_styles() {
		// Override in subclass.
	}

	public function enqueue_scripts() {
		// Override in subclass.
	}

	protected function on_after_add_menu_page( $hook_suffix ) {
		// Custom assets.
		add_action(
			'admin_print_styles-' . $hook_suffix,
			[
				$this,
				'enqueue_styles',
			]
		);
		add_action(
			'admin_print_scripts-' . $hook_suffix,
			[
				$this,
				'enqueue_scripts',
			]
		);
	}
}
