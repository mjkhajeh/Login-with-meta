<?php
namespace LWM\Includes\UserForms;

class WC_Register {
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
		add_action( 'woocommerce_register_form_start', array( $this, 'form' ) );
		add_action( 'woocommerce_register_post', array( $this, 'validate' ), 10, 3 );
		add_action( 'woocommerce_created_customer', array( $this, 'save' ) );
		
		if ( class_exists( 'WeDevs_Dokan' ) && get_option( 'lwm_login' ) == 'woocommerce_phone' ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		}
	}
	
	public function form() {
		$lwm_setting = array();
		
		$lwm_setting['login']						= get_option( 'lwm_login' );
		$lwm_setting['create_reg_field_flname_woo']	= get_option( 'lwm_create_reg_field_flname_woo' );
		$lwm_setting['create_reg_field_web_woo']	= get_option( 'lwm_create_reg_field_web_woo' );
		$lwm_setting['create_reg_field_custom_woo']	= get_option( 'lwm_create_reg_field_custom_woo' );
		
		if ( $lwm_setting['login'] == 'fullname' && $lwm_setting['create_reg_field_flname_woo'] ) {
		?>
			<p class="form-row form-group form-row-wide">
				<label for="first_name"><?php _e( 'First name', 'lwm' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" name="first_name" id="first_name" value="<?php esc_attr_e( $_POST['first_name'] ); ?>" />
			</p>
			
			<p class="form-row form-group form-row-wide">
				<label for="last_name"><?php _e( 'Last name', 'lwm' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" name="last_name" id="last_name" value="<?php esc_attr_e( $_POST['last_name'] ); ?>" />
			</p>
		<?php
		} elseif ( $lwm_setting['login'] == 'website' && $lwm_setting['create_reg_field_web_woo'] ) {
		?>
			<p class="form-row form-group form-row-wide">
				<label for="website"><?php _e( 'Website', 'lwm' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" name="website" id="website" value="<?php esc_attr_e( $_POST['website'] ); ?>" />
			</p>
		<?php
		} elseif ( $lwm_setting['login'] == 'woocommerce_phone' ) {
		?>
			<p class="form-row form-group form-row-wide">
				<label for="shop-phone"><?php _e( 'Phone Number', 'lwm' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php esc_attr_e( $_POST['billing_phone'] ); ?>" />
			</p>
		<?php
		} elseif ( $lwm_setting['login'] == 'custom' && $lwm_setting['create_reg_field_custom_woo'] ) {
			$lwm_setting['custom_reg_field_name'] = get_option( 'lwm_custom_reg_field_name' );
			$lwm_setting['reg_field_type'] = get_option( 'lwm_reg_field_type' );
			$lwm_setting['reg_field_label'] = get_option( 'lwm_reg_field_label' );
			
			$value = ( ! empty( $_POST[$lwm_setting['custom_reg_field_name']] ) ) ? sanitize_text_field( $_POST[$lwm_setting['custom_reg_field_name']] ) : '';
			?>
			<p class="form-row form-group form-row-wide">
				<label for="<?php echo $lwm_setting['custom_reg_field_name'] ?>" id="<?php echo $lwm_setting['custom_reg_field_name'] ?>"><?php echo $lwm_setting['reg_field_label']; ?> <span class="required">*</span></label>
				<input class="input-text" type="<?php echo $lwm_setting['reg_field_type']; ?>" name="<?php echo $lwm_setting['custom_reg_field_name'] ?>" id="<?php echo $lwm_setting['custom_reg_field_name'] ?>" value="<?php echo $value; ?>">
			</p>
			<?php
		}
	}
	
	public function validate( $username, $email, $validation_errors ) {
		$lwm_setting = array();
		
		$lwm_setting['login']						= get_option( 'lwm_login' );
		$lwm_setting['create_reg_field_flname_woo']	= get_option( 'lwm_create_reg_field_flname_woo' );
		$lwm_setting['create_reg_field_web_woo']	= get_option( 'lwm_create_reg_field_web_woo' );
		$lwm_setting['create_reg_field_wc_phone']	= get_option( 'lwm_create_reg_field_wc_phone' );
		$lwm_setting['create_reg_field_custom_woo']	= get_option( 'lwm_create_reg_field_custom_woo' );
		
		if ( $lwm_setting['login'] == 'fullname' && $lwm_setting['create_reg_field_flname_woo'] ) {
			// Check for fill input
			if ( empty( $_POST['first_name'] ) ) {
				$errors->add( 'lwm_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'lwm' ), __( 'Please enter your first name', 'lwm' ) ) );
			}
			
			// Check for fill input
			if ( empty( $_POST['last_name'] ) ) {
				$errors->add( 'lwm_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'lwm' ), __( 'Please enter your last name', 'lwm' ) ) );
			}
			
			// Check for not exist
			$users_id = get_users( array( 'fields' => array( 'id' ) ) );
			foreach ( $users_id as $user_id ) {
				$fname = get_userdata( $user_id->ID )->first_name;
				$lname = get_userdata( $user_id->ID )->last_name;
				$fullname = $fname . ' ' . $lname;
				if ( $fullname == $_POST['first_name'] . ' ' . $_POST['last_name'] ) {
					$errors->add( 'lwm_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'lwm' ), __( 'This name is exist', 'lwm' ) ) );
				}
			}
		} elseif ( $lwm_setting['login'] == 'website' && $lwm_setting['create_reg_field_web_woo'] ) {
			// Check for fill input
			if ( empty( $_POST['website'] ) ) {
				$errors->add( 'lwm_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'lwm' ), __( 'Please enter your website', 'lwm' ) ) );
			}
			
			// Check for not exist
			$users_id = get_users( array( 'fields' => array( 'id' ) ) );
			foreach ( $users_id as $user_id ) {
				$website = get_userdata( $user_id->ID )->user_url;
				if ( $_POST['website'] == $website ) {
					$errors->add( 'lwm_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'lwm' ), __( 'This website is exist', 'lwm' ) ) );
				}
			}
		} elseif ( $lwm_setting['login'] == 'woocommerce_phone' && $lwm_setting['create_reg_field_wc_phone'] ) {
			if ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] ) ) {
				$validation_errors->add( 'billing_phone_empty', __( 'Phone number is required!', 'lwm' ) );
			}
			if ( strlen( $_POST['billing_phone'] ) < 11 || ( !is_numeric( $_POST['billing_phone'] ) ) ) {
				$validation_errors->add( 'billing_phone_valid', __( 'Enter a valid phone number', 'lwm' ) );
			}
			$user = get_users( array( 'meta_key' => 'billing_phone', 'meta_value' => $_POST['billing_phone'] ) );
			if ( isset( $user[0] ) ) {
				$validation_errors->add( 'billing_phone_exist', __( 'This phone number is exist', 'lwm' ) );
			}
		} elseif ( $lwm_setting['login'] == "custom" && $lwm_setting['create_reg_field_custom_woo'] ) {
			$lwm_setting['custom_meta_key'] = get_option( 'lwm_custom_meta_key' );
			$lwm_setting['custom_reg_field_name'] = get_option( 'lwm_custom_reg_field_name' );
			$lwm_setting['reg_field_label'] = get_option( 'lwm_reg_field_label' );
			
			// Check for fill input
			if ( empty( $_POST[$lwm_setting['custom_reg_field_name']] ) ) {
				$Error = __( 'Please enter your ' . strtolower( $lwm_setting['reg_field_label'] ) );
				$errors->add( 'lwm_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'lwm' ), $Error ) );
			}
			$user = get_users( array( 'meta_key' => $lwm_setting['custom_meta_key'], 'meta_value' => $_POST[$lwm_setting['custom_reg_field_name']] ) );
			if ( isset( $user[0] ) ) {
				$errors->add( 'lwm_error', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'lwm' ), $Error ) );
			}
		}
		
		return $validation_errors;
	}
	
	public function save( $customer_id ) {
		$lwm_setting = array();
		
		$lwm_setting['login']						= get_option( 'lwm_login' );
		$lwm_setting['create_reg_field_flname_woo']	= get_option( 'lwm_create_reg_field_flname_woo' );
		$lwm_setting['create_reg_field_web_woo']	= get_option( 'lwm_create_reg_field_web_woo' );
		$lwm_setting['create_reg_field_custom_woo']	= get_option( 'lwm_create_reg_field_custom_woo' );
		
		if ( $lwm_setting['login'] == 'fullname' && $lwm_setting['create_reg_field_flname_woo'] ) {
			if ( isset( $_POST['first_name'] ) && isset( $_POST['last_name'] ) ) {
				update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['first_name'] ) );
				update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['last_name'] ) );
			}
		} elseif ( $lwm_setting['login'] == 'website' && $lwm_setting['create_reg_field_web_woo'] ) {
			if ( isset( $_POST['website'] ) ) {
				update_user_meta( $customer_id, 'user_url', $_POST['website'] );
			}
		} elseif ( get_option( 'lwm_login' ) == 'woocommerce_phone' ) {
			if ( isset( $_POST['billing_phone'] ) ) {
				update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
			}
		} elseif ( $lwm_setting['login'] == "custom" && $lwm_setting['create_reg_field_custom_woo'] ) {
			$lwm_setting['custom_reg_field_name'] = get_option( 'lwm_custom_reg_field_name' );
			update_user_meta( $user_id, $lwm_setting['custom_reg_field_name'], $_POST[$lwm_setting['custom_reg_field_name']] );
		}
	}
	
	// For Dokan
	public function enqueue() {
		wp_enqueue_script( 'lwm-woocommerce-reg-form', LWM_URI . 'assets/js/lwm_wc_register.js', array( 'jquery' ), null, true );
	}
}
if ( class_exists( 'woocommerce' ) ) {
	WC_Register::get_instance();
}