<?php
/*
Plugin Name: Login with meta
Plugin URI: https://wordpress.org/plugins/login-with-meta/
Description: With this plugin your users can login with any user meta you selected. like: Full name, Website address and etc.
Version: 1.0.0.0
Author: Mohammad Jafar Khajeh
Author URI: http://mjkhajeh.com
Text Domain: lwm
Domain Path: /languages
*/
namespace LWM;

if (!defined('ABSPATH')) exit;

class Init {
	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return	A single instance of this class.
	 */
	public static function get_instance() {
		static $instance = null;
		if( $instance === null ) {
			$instance = new self;
		}
		return $instance;
	}
	
	private function __construct() {
		// Bootstrap APIs
		$this->i18n();
		$this->constants();
		$this->includes();
	}

	public function constants() {
		if( ! defined( 'LWM_DIR' ) )
			define( 'LWM_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

		if( ! defined( 'LWM_URI' ) )
			define( 'LWM_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
	}

	public function includes() {
		include_once( LWM_DIR . 'Backend/Settings.php' );
		include_once( LWM_DIR . 'Backend/UserProfile.php' );
		
		include_once( LWM_DIR . 'Includes/UserForms/Login.php' );
		include_once( LWM_DIR . 'Includes/UserForms/Register.php' );
		
		if ( class_exists( 'woocommerce' ) ) {
			include_once( LWM_DIR . 'Includes/UserForms/WC_Register.php' );
		}
	}

	public function i18n() {
		load_plugin_textdomain( 'lwm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
}

Init::get_instance();