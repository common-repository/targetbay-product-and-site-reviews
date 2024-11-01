<?php
/**
 * TargetBay Product and Site reviews Settings.
 *
 * @since 0.1.0
 * @package TargetBay_Product_and_Site_Reviews
 */

use GuzzleHttp\Client;

/**
 * TargetBay Product and Site reviews Settings class.
 *
 * @since 0.1.0
 */
class TBWC_Targetbay_Settings {
	public const VERSION = 1;

	/**
	 * Parent plugin class.
	 *
	 * @var TargetBay_Product_Review_and_Site_Reviews
	 *
	 * @since  0.1.0
	 */
	protected $plugin;

	/**
	 * Option key, and option page slug.
	 *
	 * @var string
	 *
	 * @since  0.1.0
	 */
	protected $key = 'wc_targetbay_reviews_settings';

	/**
	 * Options page metabox ID.
	 *
	 * @var string
	 *
	 * @since  0.1.0
	 */
	protected $metabox_id = 'wc_targetbay_reviews_settings_metabox';

	/**
	 * Options Menu title.
	 *
	 * @var string
	 *
	 * @since  0.1.0
	 */
	protected $menu_title = '';

	/**
	 * Options Page title.
	 *
	 * @var string
	 *
	 * @since  0.1.0
	 */
	protected $title = '';

	/**
	 * Options Page hook.
	 *
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Options Url.
	 *
	 * @var string
	 */
	protected $target_bay = 'https://app.targetbay.com';

	/**
	 * Constructor.
	 *
	 * @since  0.1.0
	 *
	 * @param TargetBay_Product_Review_and_Site_Reviews $plugin main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->wc_targetbay_hooks();

		// Set our title.
		$this->menu_title = esc_attr__( 'TargetBay', 'TargetBay Product and Site Reviews' );
		$this->title      = esc_attr__( 'TargetBay - Settings', 'TargetBay Product and Site Reviews' );
	}

	/**
	 * Initiate our wc_targetbay_hooks.
	 *
	 * @since  0.1.0
	 */
	public function wc_targetbay_hooks() {
		add_action( 'admin_init', array( $this, 'wc_targetbay_admin_init' ) );
		add_action( 'admin_menu', array( $this, 'wc_targetbay_add_options_page' ) );
	}

	/**
	 * Register our setting to WP.
	 *
	 * @since  0.1.0
	 */
	public function wc_targetbay_admin_init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page.
	 *
	 * @since  0.1.0
	 */
	public function wc_targetbay_add_options_page() {
		$this->options_page = add_menu_page(
			$this->title,
			$this->menu_title,
			'manage_options',
			$this->key,
			array( $this, 'wc_targetbay_admin_page_display' )
		);

		add_action( "admin_print_styles-{$this->options_page}", array( $this, 'wc_targetbay_admin_styles' ) );
	}

	/**
	 * Admin page settings.
	 *
	 * @since  0.1.0
	 */
	public function wc_targetbay_admin_page_display() {
		try {
			if ( isset( $_POST['wc_targetbay_settings_form'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wc_targetbay_settings_form'] ) ), 'setting_form_action' ) ) {
				$this->wc_proccess_targetbay_settings();
				$this->wc_display_targetbay_settings();
			} else {
				$tb_settings            = get_option( 'wc_targetbay_review_settings', $this->wc_targetbay_get_default_settings() );
				$check_account_key_exit = ( $tb_settings['wc_targetbay_api_secret'] ) ? $tb_settings['wc_targetbay_api_secret'] : '';
				if ( '' === $check_account_key_exit ) {
					$statuscheck = $this->wc_targetbay_account_signup();
				}
				$this->wc_display_targetbay_settings();
			}
		} catch ( Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
			$this->wc_display_targetbay_settings();
		}
	}

	/**
	 * Account creation under TargetBay.
	 */
	public function wc_targetbay_account_signup() {
		try {
			$base_url     = $this->target_bay . '/on-boarding/woocommerce-sign-up';
			$current_user = wp_get_current_user();
			$data['name'] = get_bloginfo();
			$display_name = sanitize_user( get_bloginfo(), true );
			if ( '' === trim( $data['name'] ) ) {
				$data['name'] = $display_name;
			}
			$data['team']  = $display_name;
			$data['slug']  = $display_name;
			$data['email'] = sanitize_email( get_bloginfo( 'admin_email' ) );
			if ( '' !== $data['email'] && null !== $data['email'] ) {
				$data['account'] = $display_name;
				$check_user_id   = get_user_by( 'email', $data['email'] );
				$user_id_check   = $current_user->ID;
				if ( isset( $check_user_id->ID ) ) {
					$user_id_check = $check_user_id->ID;
				}
				$data['phone_no'] = get_user_meta( $user_id_check, 'billing_phone', true );
				$data['city']     = get_user_meta( $user_id_check, 'billing_city', true );
				$data['country']  = get_user_meta( $user_id_check, 'billing_country', true );
				$data['web_url']  = site_url();
				$client           = new Client(
					array(
						'base_uri' => $base_url,
						'debug'    => false,
					)
				);
				$response         = $client->post(
					$base_url,
					array(
						'json'  => $data,
						'debug' => false,
					)
				);
				$data_list        = json_decode( $response->getBody()->getContents(), true );

				if ( isset( $data_list['token'] ) ) {
					$new_settings = array(
						'wc_targetbay_server'        => 'live',
						'wc_targetbay_api_secret'    => $data_list['token'],
						'wc_targetbay_rich_snippets' => 'manual',
						'wc_targetbay_pro_review'    => 'enable',
						'wc_targetbay_bulk_reviews'  => 'enable',
						'wc_targetbay_script_load'   => 'footer',
						'wc_targetbay_disable_review_system' => true,
					);
					update_option( 'wc_targetbay_review_settings', $new_settings );

					exit( esc_url_raw( wp_safe_redirect( $this->wc_targetbay_build_url( $data_list, 'authorize' ) ) ) );
				}
			}
		} catch ( Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * Admin style css.
	 *
	 * @param string $hook url for css file.
	 */
	public function wc_targetbay_admin_styles( $hook ) {
		wp_enqueue_style( 'tbSettingsStylesheet', plugins_url( '../assets/css/targetbay.css', __FILE__ ), array(), '1.0' );
	}

	/**
	 * Targetbay Settings.
	 *
	 * @param bool $success_type success status.
	 */
	public function wc_display_targetbay_settings( $success_type = false ) {
		$tb_settings             = get_option( 'wc_targetbay_review_settings', $this->wc_targetbay_get_default_settings() );
		$wc_targetbay_api_secret = $tb_settings['wc_targetbay_api_secret'];
		if ( empty( $tb_settings['wc_targetbay_api_secret'] ) ) {
			$this->wc_targetbay_display_message( 'Set your API secret in order the TargetBay plugin to work correctly' );
		}
		$link_html_url                    = esc_html( 'https://app.targetbay.com/login' );
		$wc_targetbay_tracking_params     = $link_html_url . '?utm_source=targetbay_woocommerce&utm_medium=header_link&utm_campaign=woocommerce_customize_link';
		$dashboard_link                   = "<a href='{$wc_targetbay_tracking_params}' target='_blank'>TargetBay.</a>";
		$read_only                        = isset( $_POST['log_in_button'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['log_in_button'] ) ), 'wc_targetbay_settings_form' ) || 'b2c' === $success_type ? '' : 'readonly';
		$credentials_location_explanation = isset( $_POST['log_in_button'] ) ? "<tr valign='top'>     
                                                                                <th>
                                                                                <p class='description'>To get your api key and secret token <a href='" . $link_html_url . "' target='_blank'>log in here</a> and go to your account settings.</p>
                                                                                </th>
                                                                               </tr>" : '';
		ob_start(); ?>
		<div class='wrap tb-wrap'>
			<h2>TargetBay Settings</h2>
			<h4>To customize the look and feel of the widget, and to edit your Mail After Purchase settings, just head to the " . <?php echo esc_url_raw( $dashboard_link ); ?> . "</h4>
			<form method='post' id='wc_targetbay_settings_form'>
				<?php wp_nonce_field( 'setting_form_action', 'wc_targetbay_settings_form' ); ?>
				<table class='form-table'>
					<tr valign='top'>
						<th>TargetBay API Server</th>
						<td>
							<select id='wc_targetbay_server' name='wc_targetbay_server' class='tb-server'>
								<option value=<?php echo 'dev' . selected( 'dev', esc_attr( $tb_settings['wc_targetbay_server'] ), false ); ?>>Test</option>
								<option value=<?php echo 'live' . selected( 'live', esc_attr( $tb_settings['wc_targetbay_server'] ), false ); ?>>Live</option>
							</select>
						</td>
					</tr>
					<tr valign='top' class='targetbay-widget-tab-name'>
						<th>TargetBay API Secret:</th>
						<td>
							<input type='text' id='wc_targetbay_api_secret' name='wc_targetbay_api_secret' value=<?php echo esc_attr( $wc_targetbay_api_secret ); ?>>
						</td>
					</tr>
					<tr valign='top' class='targetbay-widget-tab-name'>
						<th>TargetBay Script Load:</th>
						<td>
							<select id='wc_targetbay_script_load' name='wc_targetbay_script_load' class='tb-server'>
								<option value=<?php echo 'footer' . selected( 'footer', esc_attr( $tb_settings['wc_targetbay_script_load'] ), false ); ?>>Footer</option>
								<option value=<?php echo 'head' . selected( 'head', esc_attr( $tb_settings['wc_targetbay_script_load'] ), false ); ?>>Head</option>
							</select>
						</td>
					</tr>
					<tr valign='top'>
						<th>Rich Snippets</th>
						<td>
							<select id='wc_targetbay_rich_snippets' name='wc_targetbay_rich_snippets' class='tb-tracking'>
								<option value=<?php echo 'automatic' . selected( 'automatic', esc_attr( $tb_settings['wc_targetbay_rich_snippets'] ), false ); ?>>Automatic</option>
								<option value=<?php echo 'manual' . selected( 'manual', esc_attr( $tb_settings['wc_targetbay_rich_snippets'] ), false ); ?>>Manual</option>
							</select>
						</td>
					</tr>
					<tr valign='top'>
						<th>Product Review</th>
						<td>
							<select id='wc_targetbay_pro_review' name='wc_targetbay_pro_review' class='tb-tracking'>
								<option value=<?php echo 'enable' . selected( 'enable', esc_attr( $tb_settings['wc_targetbay_pro_review'] ), false ); ?>>Enable</option>
								<option value=<?php echo 'disable' . selected( 'disable', esc_attr( $tb_settings['wc_targetbay_pro_review'] ), false ); ?>>Disable</option>
							</select>
						</td>
					</tr>
					<tr valign='top'>
						<th>Bulk Review</th>
						<td>
							<select id='wc_targetbay_bulk_reviews' name='wc_targetbay_bulk_reviews' class='tb-tracking'>
								<option value=<?php echo 'enable' . selected( 'enable', esc_attr( $tb_settings['wc_targetbay_bulk_reviews'] ), false ); ?>>Enable</option>
								<option value=<?php echo 'disable' . selected( 'disable', esc_attr( $tb_settings['wc_targetbay_bulk_reviews'] ), false ); ?>>Disable</option>
							</select>
						</td>
					</tr>
					<tr valign='top'>
						<th>Disable native reviews system:</th>
						<td>
							<input type='checkbox' name='wc_targetbay_disable_review_system' value=<?php echo '1' . checked( 1, esc_attr( $tb_settings['wc_targetbay_disable_review_system'] ), false ); ?>>
						</td>
					</tr>
				</table><br>
				<div class='buttons-container'>
					<input type='submit' id='targetbay_settings' name='targetbay_settings' value='Update' class='button-primary' id='save_targetbay_settings'>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Save options.
	 */
	public function wc_proccess_targetbay_settings() {
		$current_settings = get_option( 'wc_targetbay_review_settings', $this->wc_targetbay_get_default_settings() );
		$new_settings     = array(
			'wc_targetbay_server'                => isset( $_POST['wc_targetbay_server'] ) ? sanitize_text_field( wp_unslash( $_POST['wc_targetbay_server'] ) ) : '',
			'wc_targetbay_api_secret'            => isset( $_POST['wc_targetbay_api_secret'] ) && isset( $_POST['wc_targetbay_settings_form'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wc_targetbay_settings_form'] ) ), 'setting_form_action' ) ? sanitize_text_field( wp_unslash( $_POST['wc_targetbay_api_secret'] ) ) : '',
			'wc_targetbay_rich_snippets'         => isset( $_POST['wc_targetbay_rich_snippets'] ) ? sanitize_text_field( wp_unslash( $_POST['wc_targetbay_rich_snippets'] ) ) : '',
			'wc_targetbay_pro_review'            => isset( $_POST['wc_targetbay_pro_review'] ) ? sanitize_text_field( wp_unslash( $_POST['wc_targetbay_pro_review'] ) ) : '',
			'wc_targetbay_bulk_reviews'          => isset( $_POST['wc_targetbay_bulk_reviews'] ) ? sanitize_text_field( wp_unslash( $_POST['wc_targetbay_bulk_reviews'] ) ) : '',
			'wc_targetbay_script_load'           => isset( $_POST['wc_targetbay_script_load'] ) ? sanitize_text_field( wp_unslash( $_POST['wc_targetbay_script_load'] ) ) : '',
			'wc_targetbay_disable_review_system' => isset( $_POST['wc_targetbay_disable_review_system'] ) ? true : false,
		);
		update_option( 'wc_targetbay_review_settings', $new_settings );
	}

	/**
	 * Display message.
	 *
	 * @param array $messages text to be displayed.
	 * @param bool  $is_error error status variable.
	 */
	public function wc_targetbay_display_message( $messages, $is_error = false ) {
		$class = $is_error ? 'error' : 'updated fade';
		if ( is_array( $messages ) ) {
			foreach ( $messages as $message ) {
				echo esc_html(
					"<div id='message' class='{$class}'>
                        <p><strong>{$message}</strong></p>
                      </div>"
				);
			}
		} elseif ( is_string( $messages ) ) {
			echo esc_html(
				"<div id='message' class='{$class}'>
                    <p><strong>{$messages}</strong></p>
                  </div>"
			);
		}
	}

	/**
	 * Default settings.
	 *
	 * @return array
	 */
	public function wc_targetbay_get_default_settings() {
		return array(
			'wc_targetbay_server'                => 'live',
			'wc_targetbay_api_secret'            => '',
			'wc_targetbay_rich_snippets'         => 'manual',
			'wc_targetbay_pro_review'            => 'enable',
			'wc_targetbay_bulk_reviews'          => 'enable',
			'wc_targetbay_disable_review_system' => true,
			'wc_targetbay_star_ratings_enabled'  => 'no',
			'wc_targetbay_api_secret'            => '',
			'wc_targetbay_script_load'           => 'footer',
		);
	}

	/**
	 * Rest api url.
	 *
	 * @param array  $data     user data.
	 * @param string $endpoint endpoint url.
	 */
	protected function wc_targetbay_build_url( $data, $endpoint ) {
		$url = wc_get_endpoint_url( 'wc-auth/v' . self::VERSION, $endpoint, home_url( '/' ) );

		return add_query_arg(
			array(
				'app_name'     => wc_clean( 'TargetBay Product and Site Reviews' ),
				'user_id'      => $data['team_id'],
				'return_url'   => rawurlencode( $this->target_bay . '/on-boarding/woocommerce-rest-api-return-url?_key=' . $data['hash_key'] . '&_pass_key=' . $data['hash_pass'] ),
				'callback_url' => rawurlencode( $this->target_bay . '/api/v1/woo/on-boarding/woocommerce-rest-api-callback-url?_t=' . $data['token'] ),
				'scope'        => wc_clean( 'read_write' ),
			),
			$url
		);
	}
}
