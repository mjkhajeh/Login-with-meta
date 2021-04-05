<?php
namespace LWM\Includes\UserForms;

class Login {
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
		add_filter( 'gettext', array( $this, 'lwm_username_label' ), 20, 3 );
		remove_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
		add_filter( 'authenticate', array( $this, 'login' ), 20, 3 );
	}

	public function login( $user, $username, $password ) {
		$verify = false;
		if ( is_a( $user, 'WP_User' ) )
			return $user;

		if ( !empty( $username ) ){
			$users = get_users();

			// Login with fullname
			if ( get_option( 'lwm_login' ) == 'fullname' ) {
				foreach( $users as $user ) {
					$first_name	= get_user_meta( $user->ID, 'first_name', true );
					$last_name	= get_user_meta( $user->ID, 'last_name', true );
					$userdata = get_userdata( $user->ID );
					if( $username == $first_name . " " . $last_name && wp_check_password( $password, $userdata->user_pass, $userdata->ID ) ) {
						$username = $userdata->user_login;
						$verify = true;
					}
				}
			} elseif ( get_option( 'lwm_login' ) == 'website' ) {
				// Login with website
				foreach( $users as $user ) {
					$userdata = get_userdata( $user->ID );
					if ( $username == $userdata->user_url && wp_check_password( $password, $userdata->user_pass, $userdata->ID ) ) {
						$username = $userdata->user_login;
						$verify = true;
					}
				}
			} elseif ( get_option( 'lwm_login' ) == 'woocommerce_phone' && class_exists( 'woocommerce' ) ) {
				// Login with Woocommerce: billing phone
				$get_user = get_users( array(
					'meta_key' => 'billing_phone',
					'meta_value' => $username
				) );

				if( !empty( $get_user ) && isset( $get_user[0] ) && $get_user[0] ) {
					$get_user = $get_user[0];
					if( wp_check_password( $password, $get_user->user_pass, $get_user->ID ) ) {
						$username = $get_user->user_login;
						$verify = true;
					}
				}
			} elseif ( get_option( 'lwm_login' ) == 'custom' ) {
				// Login with custom meta
				$get_user = get_users( array(
					'meta_key' => get_option( 'lwm_custom_meta_key', '' ),
					'meta_value' => $username
				) );
				if( !empty( $get_user ) && isset( $get_user[0] ) && $get_user[0] ) {
					$get_user = $user[0];
					if( wp_check_password( $password, $get_user->user_pass, $get_user->ID ) ) {
						$username = $get_user->user_login;
						$verify = true;
					}
				}
			}
		}

		if( $verify === true ) {
			return wp_authenticate_username_password( $user, $username, $password );
		}
		return;
	}
		
	function lwm_username_label( $translated_text, $text, $domain ) {
		if ( 'Username or Email Address' === $text || 'Username' === $text || 'Username or email address' === $text ) {
			$translated_text .= ' ';
			if ( get_option( 'lwm_login' ) == 'fullname' ) {
				$translated_text .= __( 'or Fullname', 'lwm' );
			} elseif ( get_option( 'lwm_login' ) == 'website' ) {
				$translated_text .= __( 'or Website', 'lwm' );
			} elseif ( get_option( 'lwm_login' ) == 'woocommerce_phone' && class_exists( 'woocommerce' ) ) {
				$translated_text .= __( 'or Phone', 'lwm' );
			} elseif ( get_option( 'lwm_login' ) == 'custom' ) {
				$translated_text .= __( 'or', 'lwm' );
				$translated_text .= ' ';
				$translated_text .= get_option( 'lwm_reg_field_label' );
			}
		}
		
		return $translated_text;
	}
}
Login::get_instance();