<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Guj_Theme_Customizer' ) ) {

	/**
	 * Handles customisations options for theme
	 *
	 */
	class Guj_Theme_Customizer {

		private static $theme_options = [];

		/**
		 * Constructor
		 *
		 * @mvc Controller
		 */
		public function __construct() {
			add_filter( 'wp_nav_menu_items', __CLASS__ . '::guj_loginout_menu_link', 10, 2 );
			add_action('after_setup_theme', __CLASS__ . '::guj_remove_admin_bar');
		}

		/* Logout link redirect callback */
		public static function guj_loginout_menu_link( $items, $args ) {
		   if ($args->theme_location == 'primary') {
		      if (is_user_logged_in()) {
		         $items .= '<li><a href="'. wp_logout_url(home_url()) .'">'. __("Log Out") .'</a></li>';
		      } else {
		         $items .= '<li class="right"><a href="'. home_url().'/login">'. __("Log In") .'</a></li>';
		      }
		   }
		   return $items;
		}

		/* Logout link redirect callback */
		public static function guj_remove_admin_bar() {
		  // if (!current_user_can('administrator') && !is_admin()) {
		    show_admin_bar(false);
		  // }
		}

	} // end Guj_Theme_Customizer

	new Guj_Theme_Customizer();
}
