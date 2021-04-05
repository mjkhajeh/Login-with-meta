<?php
namespace LWM\Backend;

class UserProfile {
	public static function get_instance() {
		static $instance = null;
		if( $instance === null ) {
			$instance = new self;
		}
		return $instance;
	}

	private function __construct() {
		add_action( 'show_user_profile', array( $this, "fields" ) );
		add_action( 'edit_user_profile', array( $this, "fields" ) );
		add_action( 'personal_options_update', array( $this, "save" ) );
		add_action( 'edit_user_profile_update', array( $this, "save" ) );
	}
	
	function fields( $user ) {
		$lwm_settings = array();
		
		$lwm_settings['login'] = get_option( 'lwm_login' );
		$lwm_settings['create_reg_field_custom'] = get_option( 'lwm_create_reg_field_custom' );
		if ( $lwm_settings['login'] == 'custom' && $lwm_settings['create_reg_field_custom'] ) {
			$lwm_settings['custom_reg_field_name'] = get_option( 'lwm_lwm_custom_reg_field_name' );
			$lwm_settings['reg_field_type'] = get_option( 'lwm_reg_field_type' );
			$lwm_settings['reg_field_label'] = get_option( 'lwm_reg_field_label' );
			?>
			<h2><?php _e( 'LWM custom field', 'lwm' ); ?></h2>
			<table class="form-table">
				<tr>
					<th>
						<label for="<?php echo $lwm_settings['custom_reg_field_name']; ?>"><?php echo $lwm_settings['reg_field_label']; ?></label>
					</th>
					<td>
						<input type="<?php echo $lwm_settings['reg_field_type']; ?>" name="<?php echo $lwm_settings['custom_reg_field_name']; ?>" id="<?php echo $lwm_settings['custom_reg_field_name']; ?>" value="<?php echo esc_attr( get_the_author_meta( $lwm_settings['custom_reg_field_name'], $user->ID ) ); ?>" class="regular-text" />
					</td>
				</tr>
			</table>
			<?php
		}
	}
	
	public function save( $user_id ) {
		$lwm_settings = array();
		
		$lwm_settings['login'] = get_option( 'lwm_login' );
		$lwm_settings['create_reg_field_custom'] = get_option( 'lwm_create_reg_field_custom' );
		if ( $lwm_settings['login'] == 'custom' && $lwm_settings['create_reg_field_custom'] ) {
			if ( current_user_can( 'edit_user', $user_id ) ) {
				update_user_meta( $user_id, $lwm_settings['custom_reg_field_name'], sanitize_text_field( $_POST[$lwm_settings['custom_reg_field_name']] ) );
			}
		}
	}
}

UserProfile::get_instance();