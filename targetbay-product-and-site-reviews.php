<?php
/**
 * Plugin Name: TargetBay Product and Site reviews
 * Plugin URI:  https://targetbay.com?utm_source=wordpress&utm_medium=plugin_link&utm_campaign=targetbay-product-and-site-reviews
 * Description: Fully Integrated Revenue Generation Products
 * Version:     1.3.4
 * Author:      TargetBay
 * Author URI:  https://targetbay.com/products/reviews-and-qa/woocommerce-reviews-extension-plugin?utm_source=wordpress&utm_medium=plugin_link&utm_campaign=targetbay-product-and-site-reviews
 * License:     GPLv2
 * Text Domain: targetbay-product-and-site-reviews
 * Domain Path: /languages
 *
 * Woo: 5347399:2e3fe9f9beb66fd042e7a55f53ba4bd8
 *
 * @link    https://targetbay.com
 *
 * @package Wc_Targetbay_Init
 * @version 1.3.4
 *
 * Built using generator-plugin-wp (https://github.com/WebDevStudios/generator-plugin-wp)
 */

/**
 * Copyright (c) 2017 TargetBay (email : palani.p@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


// Use composer autoload.
require 'vendor/autoload.php';

require plugin_dir_path( __FILE__ ) . 'includes/class-tbwc-targetbay-settings.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-tbwc-targetbay-tracking.php';
/**
 * Main initiation class.
 *
 * @since  0.1.0
 */
final class Wc_Targetbay_Init {

	/**
	 * Current version.
	 *
	 * @var    string
	 * @since  0.1.0
	 */
	const VERSION = '0.1.0';

	/**
	 * URL of plugin directory.
	 *
	 * @var    string
	 * @since  0.1.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @var    string
	 * @since  0.1.0
	 */
	protected $path = '';

	/**
	 * Plugin base_name.
	 *
	 * @var    string
	 * @since  0.1.0
	 */
	protected $base_name = 'TargetBay Product and Site reviews';

	/**
	 * Detailed activation error messages.
	 *
	 * @var    array
	 * @since  0.1.0
	 */
	protected $activation_errors = array();

	/**
	 * Singleton instance of plugin.
	 *
	 * @var    Wc_Targetbay_Init
	 * @since  0.1.0
	 */
	protected static $single_instance = null;

	/**
	 * Instance of TBWC_Targetbay_Settings
	 *
	 * @since0.1.0
	 * @var TBWC_Targetbay_Settings
	 */
	protected $wc_targetbay_settings;

	/**
	 * Instance of wc_targetbay_tracking
	 *
	 * @since0.1.0
	 * @var TW_Targetbay_tracking
	 */
	protected $wc_targetbay_tracking;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since   0.1.0
	 * @return  Wc_Targetbay_Init A single instance of this class.
	 */
	public static function wc_targetbay_get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin.
	 *
	 * @since  0.1.0
	 */
	protected function __construct() {
		$this->base_name = plugin_basename( __FILE__ );
		$this->url       = plugin_dir_url( __FILE__ );
		$this->path      = plugin_dir_path( __FILE__ );
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  0.1.0
	 */
	public function plugin_classes() {

		$this->wc_targetbay_settings = new TBWC_Targetbay_Settings( $this );
		$this->wc_targetbay_tracking = new TBWC_Targetbay_Tracking( $this );
	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Add hooks and filters.
	 * Priority needs to be
	 * < 10 for CPT_Core,
	 * < 5 for Taxonomy_Core,
	 * and 0 for Widgets because widgets_init runs at init priority 1.
	 *
	 * @since  0.1.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'wc_tb_init' ), 0 );
	}

	/**
	 * Activate the plugin.
	 *
	 * @since  0.1.0
	 */
	public function wc_targetbay_activate() {
		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
		if ( current_user_can( 'activate_plugins' ) ) {
			update_option( 'wc_targetbay_just_installed', true );
		}
	}

	/**
	 * Deactivate the plugin.
	 * Uninstall routines should be in uninstall.php.
	 *
	 * @since  0.1.0
	 */
	public function wc_targetbay_deactivate() {
		// Add deactivation cleanup functionality here.
	}

	/**
	 * Init hooks
	 *
	 * @since  0.1.0
	 */
	public function wc_tb_init() {

		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Load translated strings for plugin.
		load_plugin_textdomain( 'targetbay-product-and-site-reviews', false, dirname( $this->base_name ) . '/languages/' );

		// Initialize plugin classes.
		$this->plugin_classes();
		if ( get_option( 'wc_targetbay_just_installed', false ) ) {
			delete_option( 'wc_targetbay_just_installed' );
			exit( esc_url_raw( wp_safe_redirect( admin_url( 'admin.php?page=wc_targetbay_reviews_settings' ) ) ) );
		}

	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  0.1.0
	 *
	 * @return boolean True if requirements met, false if not.
	 */
	public function check_requirements() {

		// Bail early if plugin meets requirements.
		if ( $this->meets_requirements() ) {
			return true;
		}

		// Add a dashboard notice.
		add_action( 'all_admin_notices', array( $this, 'wc_targetbay_requirements_not_met_notice' ) );

		// Deactivate our plugin.
		add_action( 'admin_init', array( $this, 'wc_targetbay_deactivate_me' ) );

		// Didn't meet the requirements.
		return false;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  0.1.0
	 */
	public function wc_targetbay_deactivate_me() {

		// We do a check for deactivate_plugins before calling it, to protect
		// any developers from accidentally calling it too early and breaking things.
		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( $this->base_name );
		}
	}

	/**
	 * Check that all plugin requirements are met.
	 *
	 * @since  0.1.0
	 *
	 * @return boolean True if requirements are met.
	 */
	public function meets_requirements() {
		if ( class_exists( 'WooCommerce' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met.
	 *
	 * @since  0.1.0
	 */
	public function wc_targetbay_requirements_not_met_notice() {

		/* translators: Compile default message. */
		$default_message = sprintf( __( 'TargetBay Product and Site reviews Settings is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'targetbay-product-and-site-reviews' ), admin_url( 'plugins.php' ) );

		// Default details to null.
		$details = null;

		// Add details if any exist.
		if ( $this->activation_errors && is_array( $this->activation_errors ) ) {
			$details = '<small>' . implode( '</small><br /><small>', $this->activation_errors ) . '</small>';
		}

		// Output errors.
		?>
		<div id="message" class="error">
			<p><?php echo wp_kses_post( $default_message ); ?></p>
			<?php echo wp_kses_post( $details ); ?>
		</div>
		<?php
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  0.1.0
	 *
	 * @param  string $field Field to get.
	 * @throws Exception     Throws an exception if the field is invalid.
	 * @return mixed         Value of the field.
	 */
	public function wc_targetbay_get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'base_name':
			case 'url':
			case 'path':
			case 'wc_targetbay_settings':
			case 'wc_targetbay_tracking':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}
}

/**
 * Grab the Wc_Targetbay_Init object and return it.
 * Wrapper for Wc_Targetbay_Init::wc_targetbay_get_instance().
 *
 * @since  0.1.0
 * @return Wc_Targetbay_Init  Singleton instance of plugin class.
 */
function wc_targetbay() {
	return Wc_Targetbay_Init::wc_targetbay_get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( wc_targetbay(), 'hooks' ) );

// Activation and deactivation.
register_activation_hook( __FILE__, array( wc_targetbay(), 'wc_targetbay_activate' ) );
register_deactivation_hook( __FILE__, array( wc_targetbay(), 'wc_targetbay_deactivate' ) );
