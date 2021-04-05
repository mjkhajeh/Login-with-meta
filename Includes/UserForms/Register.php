<?php
namespace LWM\Includes\UserForms;

class Register {
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
		// Add field in register form
		add_action( 'register_form', array( $this, 'field' ) );
		
		// Validate values
		add_filter( 'registration_errors', array( $this, 'registration_errors') , 10, 3 );
		
		//Save user meta
		add_action( 'user_register', array( $this, 'save_meta' ), 10, 1 );
	}
	
	public function field() {
		$lwm_setting = array();
		
		$lwm_setting['login']					= get_option( 'lwm_login' );
		$lwm_setting['create_reg_field_flname']	= get_option( 'lwm_create_reg_field_flname' );
		$lwm_setting['create_reg_field_web']	= get_option( 'lwm_create_reg_field_web' );
		$lwm_setting['create_reg_field_custom']	= get_option( 'lwm_create_reg_field_custom' );
		
		if ( $lwm_setting['login'] == 'fullname' && $lwm_setting['create_reg_field_flname'] ) {
			$fname = ( ! empty( $_POST['first_name'] ) ) ? sanitize_text_field( $_POST['first_name'] ) : '';
			$lname = ( ! empty( $_POST['last_name'] ) ) ? sanitize_text_field( $_POST['last_name'] ) : '';
			?>
			<p>
				<label for="first_name"><?php _e( 'First name', 'lwm' ); ?></label>
				<input size="25" class="input" type="text" name="first_name" id="first_name" value="<?php echo $fname; ?>">
			</p>
			<p>
				<label for="last_name"><?php _e( 'Last name', 'lwm' ); ?></label>
				<input size="25" class="input" type="text" name="last_name" id="last_name" value="<?php echo $lname; ?>">
			</p>
			<?php
		} elseif ( $lwm_setting['login'] == 'website' && $lwm_setting['create_reg_field_web'] ) {
			$website = ( ! empty( $_POST['website'] ) ) ? sanitize_text_field( $_POST['website'] ) : '';
			?>
			<p>
				<label for="website"><?php _e( 'Website', 'lwm' ); ?></label>
				<input size="25" class="input" type="url" name="website" id="website" value="<?php echo $website; ?>">
			</p>
			<?php
		} elseif ( $lwm_setting['login'] == 'custom' && $lwm_setting['create_reg_field_custom'] ) {
			$lwm_setting['custom_reg_field_name'] = get_option( 'lwm_custom_reg_field_name' );
			$lwm_setting['reg_field_type'] = get_option( 'lwm_reg_field_type' );
			$lwm_setting['reg_field_label'] = get_option( 'lwm_reg_field_label' );
			
			$value = ( ! empty( $_POST[$lwm_setting['custom_reg_field_name']] ) ) ? sanitize_text_field( $_POST[$lwm_setting['custom_reg_field_name']] ) : '';
			?>
			<p>
				<label for="<?php echo $lwm_setting['custom_reg_field_name'] ?>" id="<?php echo $lwm_setting['custom_reg_field_name'] ?>"><?php echo $lwm_setting['reg_field_label']; ?></label>
				<input size="25" class="input" type="<?php echo $lwm_setting['reg_field_type']; ?>" name="<?php echo $lwm_setting['custom_reg_field_name'] ?>" id="<?php echo $lwm_setting['custom_reg_field_name'] ?>" value="<?php echo $value; ?>">
			</p>
			<?php
		}
	}

	public function registration_errors( $errors, $sanitized_user_login, $user_email ) {
		$lwm_setting = array();
		
		$lwm_setting['login'] = get_option( 'lwm_login' );
		$lwm_setting['create_reg_field_flname'] = get_option( 'lwm_create_reg_field_flname' );
		$lwm_setting['create_reg_field_web'] = get_option( 'lwm_create_reg_field_web' );
		$lwm_setting['create_reg_field_custom'] = get_option( 'lwm_create_reg_field_custom' );
		if ( $lwm_setting['login'] == 'fullname' && $lwm_setting['create_reg_field_flname'] ) {
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
		} elseif ( $lwm_setting['login'] == 'website' && $lwm_setting['create_reg_field_web'] ) {
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
		} elseif ( $lwm_setting['login'] == "csutom" && $lwm_setting['create_reg_field_custom'] ) {
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
		
		return $errors;
	}
	
	public function save_meta( $user_id ) {
		$lwm_setting = array();
		
		$lwm_setting['login'] = get_option( 'lwm_login' );
		$lwm_setting['create_reg_field_flname'] = get_option( 'lwm_create_reg_field_flname' );
		$lwm_setting['create_reg_field_web'] = get_option( 'lwm_create_reg_field_web' );
		$lwm_setting['create_reg_field_custom'] = get_option( 'lwm_create_reg_field_custom' );
		
		if ( $lwm_setting['login'] == 'fullname' && $lwm_setting['create_reg_field_flname'] ) {
			update_user_meta( $user_id, 'first_name', $_POST['first_name'] );
			update_user_meta( $user_id, 'last_name', $_POST['last_name'] );
		} elseif ( $lwm_setting['login'] == 'website' && $lwm_setting['create_reg_field_web'] ) {
			update_user_meta( $user_id, 'user_url', $_POST['website'] );
		} elseif ( $lwm_setting['login'] == "csutom" && $lwm_setting['create_reg_field_custom'] ) {
			$lwm_setting['custom_reg_field_name'] = get_option( 'lwm_custom_reg_field_name' );
			update_user_meta( $user_id, $lwm_setting['custom_reg_field_name'], $_POST[$lwm_setting['custom_reg_field_name']] );
		}
	}
}

Register::get_instance();