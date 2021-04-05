<?php
namespace LWM\Backend;

class Settings {
	public static function get_instance() {
		static $instance = null;
		if( $instance === null ) {
			$instance = new self;
		}
		return $instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
	}

	public function menu() {
		add_options_page(
			__( 'LWM Settings', 'lwm' ),	// Page title,
			__( 'LWM Settings', 'lwm' ),	// Menu title,
			'manage_options',				// Capability
			'lwm',							// Menu slug
			array( $this, 'view' )			// Function
		);
	}

	public function view() {
		if( !empty( $_POST ) ) {
			if( $_POST['page_options'] ) {
				$fields = sanitize_text_field( $_POST['page_options'] );
				$fields = explode( ",", $fields );
				foreach( $fields as $field ) {
					if( isset( $_POST[$field] ) && $_POST[$field] ) {
						$value = sanitize_text_field( $_POST[$field] );
						update_option( $field, $value );
					}
				}
			}
		}
		wp_enqueue_script( 'lwm-setting-script', LWM_URI . 'assets/js/lwm_backend.js', array( 'jquery' ), '', true );
		
		$lwm_setting = array();

		$lwm_setting['login']							= get_option( 'lwm_login', 'fullname' );
		$lwm_setting['create_reg_field_flname']			= get_option( 'lwm_create_reg_field_flname', '' );
		$lwm_setting['create_reg_field_flname_woo']		= get_option( 'lwm_create_reg_field_flname_woo', '' );
		$lwm_setting['create_reg_field_web']			= get_option( 'lwm_create_reg_field_web', '' );
		$lwm_setting['create_reg_field_web_woo']		= get_option( 'lwm_create_reg_field_web_woo', '' );
		$lwm_setting['create_reg_field_wc_phone']		= get_option( 'lwm_create_reg_field_wc_phone', '' );
		$lwm_setting['create_reg_field_wc_phone_dokan']	= get_option( 'lwm_create_reg_field_wc_phone_dokan', '' );
		$lwm_setting['custom_meta_key']					= get_option( 'lwm_custom_meta_key', '' );
		$lwm_setting['custom_reg_field_name']			= get_option( 'lwm_custom_reg_field_name', '' );
		$lwm_setting['custom_reg_field_error']			= get_option( 'lwm_custom_reg_field_error', '' );
		$lwm_setting['create_reg_field_custom']			= get_option( 'lwm_create_reg_field_custom', '' );
		$lwm_setting['reg_field_label']					= get_option( 'lwm_reg_field_label', '' );
		$lwm_setting['reg_field_type']					= get_option( 'lwm_reg_field_type', '' );
		$lwm_setting['create_reg_field_custom_woo']		= get_option( 'lwm_create_reg_field_custom_woo', '' );

		?>
		<div class="wrap">
			<h1><?php _e( 'Login with meta settings', 'lwm' ) ?></h1>
			<form action="" method="post">
				<table class="form-table">
					<tbody>
					<tr>
						<th scope="row"><?php _e( 'Allow users login with', 'lwm' ) ?>: </th>
						<td>
							<label>
								<input class="lwm_login" type="radio" name="lwm_login" value="fullname" <?php echo $lwm_setting['login'] == 'fullname' ? 'checked' : '' ?>>
								<?php _e( 'Fullname', 'lwm' ) ?>
							</label>
							<div id="lwm_fullname">
								<label>
									<input id="lwm_create_reg_field_flname" name="lwm_create_reg_field_flname" type="checkbox" <?php echo $lwm_setting['create_reg_field_flname'] ? 'checked' : '' ?>>
									<?php _e( 'Create first name and last name fields in registration form', 'lwm' ) ?>
								</label>
								<?php if ( class_exists( 'woocommerce' ) ) { ?>
									<br>
									<label>
										<input id="lwm_create_reg_field_flname_woo" name="lwm_create_reg_field_flname_woo" type="checkbox" <?php echo $lwm_setting['create_reg_field_flname_woo'] ? 'checked' : '' ?>>
										<?php _e( 'Create first name and last name fields in woocommerce registration form', 'lwm' ) ?>
									</label>
								<?php } ?>
							</div>
							<br>
							<label>
								<input class="lwm_login" type="radio" name="lwm_login" value="website" <?php echo $lwm_setting['login'] == 'website' ? 'checked' : '' ?>>
								<?php _e( 'Website Address', 'lwm' ) ?>
							</label>
							<div id="lwm_website">
								<label>
									<input id="lwm_create_reg_field_web" name="lwm_create_reg_field_web" type="checkbox" <?php echo $lwm_setting['create_reg_field_web'] ? 'checked' : '' ?>>
									<?php _e( 'Create website filed in registration form', 'lwm' ) ?>
								</label>
								<?php if ( class_exists( 'woocommerce' ) ) { ?>
									<br>
									<label>
										<input id="lwm_create_reg_field_web_woo" name="lwm_create_reg_field_web_woo" type="checkbox" <?php echo $lwm_setting['create_reg_field_web_woo'] ? 'checked' : '' ?>>
										<?php _e( 'Create website filed in woocommerce registration form', 'lwm' ) ?>
									</label>
								<?php } ?>
							</div>
							<br>
							<?php if ( class_exists( 'woocommerce' ) ) { ?>
								<label>
									<input class="lwm_login" type="radio" name="lwm_login" value="woocommerce_phone" <?php echo $lwm_setting['login'] == 'woocommerce_phone' ? 'checked' : '' ?>>
									<?php _e( 'Woocommerce phone', 'lwm' ) ?>
								</label>
								<div id="lwm_wc_phone">
									<?php if ( !class_exists( 'WeDevs_Dokan' ) ) { ?>
										<label>
											<input id="lwm_create_reg_field_wc_phone" name="lwm_create_reg_field_wc_phone" type="checkbox" <?php echo $lwm_setting['create_reg_field_wc_phone'] ? 'checked' : '' ?>>
											<?php _e( 'Create phone number field in woocommerce register form', 'lwm' ) ?>
										</label>
									<?php } else { ?>
										<label>
											<input id="lwm_create_reg_field_wc_phone" name="lwm_create_reg_field_wc_phone" type="checkbox" <?php echo $lwm_setting['create_reg_field_wc_phone_dokan'] ? 'checked' : '' ?>>
											<?php _e( "Move 'Phone Number' field for customers register form", 'lwm' ) ?>
										</label>
									<?php } ?>
								</div>
								<br>
							<?php } ?>
							<label>
								<input class="lwm_login" type="radio" name="lwm_login" value="custom" <?php echo $lwm_setting['login'] == 'custom' ? 'checked' : '' ?>>
								<?php _e( 'Custom Meta', 'lwm' ) ?>
							</label>
							<div id="lwm_custom" style="display:none">
								<label for="lwm_custom_meta_key" style="min-width: 11em;"><?php _e( 'Meta key', 'lwm' ) ?></label>
								<input type="text" name="lwm_custom_meta_key" id="lwm_custom_meta_key" value="<?php echo $lwm_setting['custom_meta_key'] ?>" >
								
								<br>
								
								<label for="lwm_custom_reg_field_name" style="min-width: 11em;"><?php _e( 'Register filed name', 'lwm' ) ?></label>
								<input type="text" name="lwm_custom_reg_field_name" id="lwm_custom_reg_field_name" value="<?php echo $lwm_setting['custom_reg_field_name'] ?>" >
								<p class="description"><?php _e( 'Name of field of your custom meta in registration form', 'lwm' ) ?></p>
								
								<br>

								<label for="lwm_custom_reg_field_error" style="min-width: 11em;"><?php _e( 'Register filed error', 'lwm' ) ?></label>
								<input class="regular-text" type="text" name="lwm_custom_reg_field_error" id="lwm_custom_reg_field_error" value="<?php echo $lwm_setting['custom_reg_field_error'] ?>" >
								<p class="description"><?php _e( 'Error for when value of this custom meta is exist for other user', 'lwm' ) ?></p>
								
								<br>

								<label for="lwm_reg_field_label" style="min-width: 11em;"><?php _e( 'Label', 'lwm' ) ?></label>
								<input class="regular-text" type="text" name="lwm_reg_field_label" id="lwm_reg_field_label" value="<?php echo $lwm_setting['reg_field_label'] ?>" >
								<br>
								<label>
									<input id="lwm_create_reg_field_custom" name="lwm_create_reg_field_custom" type="checkbox" <?php echo $lwm_setting['create_reg_field_custom'] ? 'checked' : '' ?>>
									<?php _e( 'Create this field in registration form', 'lwm' ) ?>
								</label>
								<div id="lwm_reg_field">
									<label for="lwm_reg_field_type" style="min-width: 11em;"><?php _e( 'Field type', 'lwm' ) ?></label>
									<select id="lwm_reg_field_type" name="lwm_reg_field_type">
										<option value="" selected="selected" disabled="disabled"> <?php _e( 'Choose', 'lwm' ) ?> </option>
										<option value="text" <?php echo $lwm_setting['reg_field_type'] == 'text' ? 'selected' : '' ?>> <?php _e( 'Input(Text)', 'lwm' ) ?> </option>
										<option value="email" <?php echo $lwm_setting['reg_field_type'] == 'email' ? 'selected' : '' ?>> <?php _e( 'Email', 'lwm' ) ?> </option>
										<option value="url" <?php echo $lwm_setting['reg_field_type'] == 'url' ? 'selected' : '' ?>> <?php _e( 'URL', 'lwm' ) ?> </option>
										<option value="number" <?php echo $lwm_setting['reg_field_type'] == 'number' ? 'selected' : '' ?>> <?php _e( 'Number', 'lwm' ) ?> </option>
										<option value="date" <?php echo $lwm_setting['reg_field_type'] == 'date' ? 'selected' : '' ?>> <?php _e( 'Date', 'lwm' ) ?> </option>
									</select>
									<?php if ( class_exists( 'woocommerce' ) ) { ?>
										<br>
										<label>
											<input id="lwm_create_reg_field_custom_woo" name="lwm_create_reg_field_custom_woo" type="checkbox" <?php echo $lwm_setting['create_reg_field_custom_woo'] ? 'checked' : '' ?>>
											<?php _e( 'Create this filed in woocommerce registration form', 'lwm' ) ?>
										</label>
									<?php } ?>
								</div>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
				<input type="hidden" name="page_options" value="lwm_login,lwm_create_reg_field_flname,lwm_create_reg_field_flname_woo,lwm_create_reg_field_web,lwm_create_reg_field_web_woo,lwm_create_reg_field_wc_phone,lwm_create_reg_field_wc_phone_dokan,lwm_custom_meta_key,lwm_custom_reg_field_name,lwm_custom_reg_field_error,lwm_create_reg_field_custom,lwm_create_reg_field_custom_woo,lwm_reg_field_type,lwm_reg_field_label">
				<?php submit_button() ?>
			</form>
		</div>
		<?php
	}
}
Settings::get_instance();