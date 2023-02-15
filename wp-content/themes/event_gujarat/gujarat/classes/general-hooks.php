<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Guj_General_Hooks' ) ) {

	class Guj_General_Hooks {

		/**
		 * Constructor
		 *
		 * @mvc Controller
		 */
		public function __construct() {
			add_filter( 'wp_enqueue_scripts', __CLASS__ . '::enqueue_resources' );
			add_filter( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_resources' );
		}

		/**
		 * Enqueue Scripts and Styles to theme front
		 */
		public static function enqueue_resources() {

			wp_enqueue_style(
				'guj-fontawesome',
				'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
				[],
				GUJ_THEME_VERSION
			);
			
			wp_enqueue_style( 'guj-child-style', GUJ_THEME_URL . '/assets/css/child-style.css', [], time() );
			wp_enqueue_style( 'guj-jquery-ui-style', GUJ_THEME_URL . '/assets/css/jquery-ui.css', [], time() );
			wp_enqueue_style( 'guj-bootstrap-style', GUJ_THEME_URL . '/assets/css/bootstrap.min.css', [], time() );
			wp_enqueue_style( 'guj-bootstrap-style', GUJ_THEME_URL . '/assets/css/bootstrap.min.css', [], time() );
			wp_enqueue_style( 'guj-select2-style', GUJ_THEME_URL . '/assets/css/select2.min.css', [], time() );
			wp_enqueue_style( 'guj-table-style', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.9.0/bootstrap-table.min.css', [], time() );
			wp_enqueue_style( 'guj-timepicker-style', 'https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css', [], time() );
			wp_enqueue_style( 'guj-dataTables-style', 'https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css', [], time() );
			wp_enqueue_style( 'guj-docs-style', GUJ_THEME_URL . '/assets/css/docs.css', [], time() );
			
			wp_enqueue_script( 'guj-jquery-ui-script', GUJ_THEME_URL . '/assets/js/jquery-ui.js', [ 'jquery' ], time() );		
			wp_enqueue_script( 'guj-jquery-validate', GUJ_THEME_URL . '/assets/js/jquery.validate.js', [ 'jquery' ], time() );
			wp_enqueue_script( 'bootstrap-min-js',GUJ_THEME_URL . '/assets/js/bootstrap.min.js', [ 'jquery' ], GUJ_THEME_VERSION );		
			wp_enqueue_script( 'seelct2-min-js',GUJ_THEME_URL . '/assets/js/select2.full.js', [ 'jquery' ], GUJ_THEME_VERSION );			
			wp_enqueue_script( 'guj-timepicker-script', 'https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js', [ 'jquery' ], time() );
			wp_enqueue_script( 'guj-datatables-script', 'https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js', [ 'jquery' ], time() );
			wp_register_script( 'guj-child-script', GUJ_THEME_URL . '/assets/js/child-script.js', [ 'jquery' ], time() );

			$ajax_params = [
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'gujNonce' => wp_create_nonce( 'gujNonce' ),
				'sitUrl'  => home_url(),
			];
			wp_localize_script( 'guj-child-script', 'ajaxPar', $ajax_params );
			wp_enqueue_script( 'guj-child-script' );

		}

		/**
		 * Enqueue Scripts and Styles to theme admin area
		 */
		public static function admin_enqueue_resources() {
			$ajax_params = [
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'ajaxNonce' => wp_create_nonce( 'gujAdminNonce' )
			];
			wp_register_script( 'child-admin-script', GUJ_THEME_URL . '/assets/js/admin-script.js', [ 'jquery' ], GUJ_THEME_VERSION );
			wp_localize_script( 'child-admin-script', 'ajaxPar', $ajax_params );
			wp_enqueue_script( 'child-admin-script' );

			wp_enqueue_style( 'child-admin-style', GUJ_THEME_URL . '/assets/css/admin-style.css', [], GUJ_THEME_VERSION );
			wp_enqueue_media();
		}
	}

	new Guj_General_Hooks();
}
