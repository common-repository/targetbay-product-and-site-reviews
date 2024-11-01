<?php
/**
 * TargetBay Product and Site Reviews Targetbay_tracking.
 *
 * @since   0.1.0
 * @package TargetBay_Product_and_Site_Reviews
 */

use GuzzleHttp\Client;

/**
 * TargetBay Product and Site Reviews Targetbay_tracking.
 *
 * @since 0.1.0
 */
class TBWC_Targetbay_Tracking {

	/**
	 * Parent plugin class.
	 *
	 * @since 0.1.0
	 *
	 * @var TargetBay_Product_and_Site_Reviews
	 */
	protected $wc_targetbay_api_key;

	/**
	 * Woocommerce Index property.
	 *
	 * @var $wc_targetbay_index_name.
	 */

	protected $wc_targetbay_index_name;

	/**
	 * TargetBay.
	 *
	 * @var $targetBay.
	 */

	protected $target_bay;

	/**
	 * Targetbay token.
	 *
	 * @var $token_tb.
	 */

	protected $token_tb;

	/**
	 * Woocommerce User name.
	 *
	 * @var $wc_targetbay_user_name.
	 */

	protected $wc_targetbay_user_name;

	/**
	 * Woocommerce User Email.
	 *
	 * @var $wc_targetbay_user_mail.
	 */

	protected $wc_targetbay_user_mail;

	/**
	 * Woocommerce User id.
	 *
	 * @var $wc_targetbay_user_id.
	 */

	protected $wc_targetbay_user_id;

	/**
	 * Tbay Session Id.
	 *
	 * @var $wc_targetbay_session_id.
	 */

	protected $wc_targetbay_session_id;

	/**
	 * Targetbay Path.
	 *
	 * @var $wc_targetbay_path.
	 */

	protected $wc_targetbay_path;

	/**
	 * Woocommerce Utm token.
	 *
	 * @var $wc_targetbay_utm_token.
	 */

	protected $wc_targetbay_utm_token;

	/**
	 * Woocommerce Utm Source.
	 *
	 * @var $wc_targetbay_utm_source.
	 */

	protected $wc_targetbay_utm_source;

	/**
	 * Woocommerce Utm Medium.
	 *
	 * @var $wc_targetbay_utm_medium.
	 */

	protected $wc_targetbay_utm_medium;

	/**
	 * Woocommerce Pro review.
	 *
	 * @var $wc_targetbay_pro_review.
	 */

	protected $wc_targetbay_pro_review;

	/**
	 * Woocommerce Bulk review.
	 *
	 * @var $wc_targetbay_bulk_reviews.
	 */

	protected $wc_targetbay_bulk_reviews;

	/**
	 * Woocommerce Order Id.
	 *
	 * @var $wc_targetbay_order_id.
	 */

	protected $wc_targetbay_order_id;

	/**
	 * Woocommerce Auth token.
	 *
	 * @var $wc_targetbay_auth_token.
	 */

	protected $wc_targetbay_auth_token;

	/**
	 * Woocommerce Loaded script.
	 *
	 * @var $wc_targetbay_script_load.
	 */

	protected $wc_targetbay_script_load;

	/**
	 * Woocommerce Send Data.
	 *
	 * @var $wc_targetbay_send_data.
	 */

	protected $wc_targetbay_send_data;

	/**
	 * Woocommerce add to track.
	 *
	 * @var $wc_targetbay_cart_tracking.
	 */

	protected $wc_targetbay_cart_tracking;
	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param TargetBay_Product_and_Site_Reviews $wc_targetbay_plugin Main plugin object.
	 */
	public function __construct( $wc_targetbay_plugin ) {
		try {
			$settings_details = get_option( 'wc_targetbay_review_settings', $this->wc_targetbay_get_default_settings() );
			if ( isset( $settings_details ) && count( $settings_details ) > 0 && ! is_admin() && class_exists( 'WooCommerce' ) ) {
				if ( isset( $settings_details['wc_targetbay_api_secret'] ) && '' !== $settings_details['wc_targetbay_api_secret'] ) {
					$wc_session_new = new WC_Session_Handler();
					$this->wc_targetbay_utm_tracking_handle();
					$params_array                     = explode( '&', base64_decode( $settings_details['wc_targetbay_api_secret'] ) );
					$api_token                        = explode( '=', $params_array[0] );
					$index_name                       = explode( '=', $params_array[1] );
					$this->wc_targetbay_auth_token    = $settings_details['wc_targetbay_api_secret'];
					$this->wc_targetbay_api_key       = $index_name[1];
					$this->wc_targetbay_index_name    = $api_token[1];
					$this->wc_targetbay_pro_review    = isset( $settings_details['wc_targetbay_pro_review'] ) ? $settings_details['wc_targetbay_pro_review'] : '';
					$this->wc_targetbay_bulk_reviews  = isset( $settings_details['wc_targetbay_bulk_reviews'] ) ? $settings_details['wc_targetbay_bulk_reviews'] : '';
					$this->wc_targetbay_script_load   = isset( $settings_details['wc_targetbay_script_load'] ) ? $settings_details['wc_targetbay_script_load'] : '';
					$this->wc_targetbay_cart_tracking = 'enable';
					$path_new                         = 'app';
					if ( 'dev' === $settings_details['wc_targetbay_server'] ) {
						$path_new = 'dev';
					} elseif ( 'stage' === $settings_details['wc_targetbay_server'] ) {
						$path_new = 'stage';
					} elseif ( 'trail' === $settings_details['wc_targetbay_server'] ) {
						$path_new = 'trail';
					}
					$this->wc_targetbay_path   = $path_new;
					$this->wc_targetbay_plugin = $wc_targetbay_plugin;
					$this->wc_targetbay_hooks();

					if ( is_user_logged_in() ) {
						$current_user      = wp_get_current_user();
						$user_session      = isset( $_COOKIE['tb_user_session'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['tb_user_session'] ) ) : '';
						$tb_session_id     = isset( $_COOKIE['targetbay_session_id'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['targetbay_session_id'] ) ) : '';
						$user_data_created = 1;
						$input_src         = '_un=' . $current_user->display_name;
						$input_src        .= '&_uid=' . $current_user->ID;
						$input_src        .= '&_uem=' . $current_user->user_email;
						$input_src        .= '&_utid=' . $tb_session_id;
						$input_src        .= '&_usid=' . $current_user->ID;
						$input_src        .= '&_uc=1&_ulogin=' . $user_data_created;
						$input_src        .= '&_uasid=' . $user_session;
						$this->tb_set_cookie( $input_src );
					}
				}
			}
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * User Data Handle.
	 *
	 * @return array.
	 */
	public function wc_targetbay_user_data() {

		$tb_session_id = '';
		if ( isset( $_COOKIE['targetbay_session_id'] ) ) {
			$tb_session_id = sanitize_text_field( wp_unslash( $_COOKIE['targetbay_session_id'] ) );
		}
		$arr['user_mail']  = '';
		$arr['user_id']    = $tb_session_id;
		$arr['session_id'] = $tb_session_id;
		$arr['user_name']  = 'anonymous';

		if ( is_user_logged_in() ) {
			$current_user                  = wp_get_current_user();
			$this->wc_targetbay_user_name  = sanitize_user( $current_user->user_login, true );
			$this->wc_targetbay_user_mail  = sanitize_email( $current_user->user_email );
			$this->wc_targetbay_user_id    = $current_user->ID;
			$this->wc_targetbay_session_id = $current_user->ID;
			$arr['user_mail']              = sanitize_email( $current_user->user_email );
			$arr['user_id']                = $current_user->ID;
			$arr['session_id']             = $current_user->ID;
			$arr['user_name']              = sanitize_user( $current_user->user_login, true );
		}

		return $arr;
	}

	/**
	 * Utm Tracking Handle.
	 *
	 * @return array.
	 */
	public function wc_targetbay_utm_tracking_handle() {
		$utm_check                      = isset( $_GET['utm_source'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['utm_source'] ) ) ) ? sanitize_text_field( wp_unslash( $_GET['utm_source'] ) ) : '';
		$utm_token_check                = isset( $_GET['utm_token'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['utm_token'] ) ) ) ? sanitize_text_field( wp_unslash( $_GET['utm_token'] ) ) : '';
		$utm_medium_check               = isset( $_GET['utm_medium'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['utm_medium'] ) ) ) ? sanitize_text_field( wp_unslash( $_GET['utm_medium'] ) ) : '';
		$arr['wc_targetbay_utm_token']  = isset( $_COOKIE['wc_targetbay_utm_token'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['wc_targetbay_utm_token'] ) ) : '';
		$arr['wc_targetbay_utm_token']  = isset( $_COOKIE['utm_token'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['utm_token'] ) ) : '';
		$arr['wc_targetbay_utm_source'] = isset( $_COOKIE['wc_targetbay_utm_source'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['wc_targetbay_utm_source'] ) ) : '';
		$arr['wc_targetbay_utm_medium'] = isset( $_COOKIE['wc_targetbay_utm_medium'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['wc_targetbay_utm_medium'] ) ) : '';
		if ( trim( $utm_check ) !== '' ) {
			if ( isset( $_COOKIE['wc_targetbay_utm_source'] ) ) {
				unset( $_COOKIE['wc_targetbay_utm_source'] );
			}
			if ( '' !== $utm_check && 'undefined' !== $utm_check && 'null' !== $utm_check ) {
				if ( isset( $_SERVER['HTTP_HOST'] ) ) {
					$server_host = esc_url_raw( wp_unslash( $_SERVER['HTTP_HOST'] ) );
				} else {
					$server_host = '';
				}
				setcookie( 'wc_targetbay_utm_source', $utm_check, time() + ( 86400 * 30 ), '/' );
				$arr['wc_targetbay_utm_source'] = $utm_check;
			}
		}
		if ( trim( $utm_token_check ) !== '' ) {
			if ( isset( $_COOKIE['wc_targetbay_utm_token'] ) ) {
				unset( $_COOKIE['wc_targetbay_utm_token'] );
			}
			if ( '' !== $utm_token_check && 'undefined' !== $utm_token_check && 'null' !== $utm_token_check ) {
				setcookie( 'wc_targetbay_utm_token', $utm_token_check, time() + ( 86400 * 30 ), '/' );
				$arr['wc_targetbay_utm_token'] = $utm_token_check;
			}
		}
		if ( trim( $utm_medium_check ) !== '' ) {
			if ( isset( $_COOKIE['wc_targetbay_utm_medium'] ) ) {
				unset( $_COOKIE['wc_targetbay_utm_medium'] );
			}
			if ( '' !== $utm_medium_check && 'undefined' !== $utm_medium_check && 'null' !== $utm_medium_check ) {
				setcookie( 'wc_targetbay_utm_medium', $utm_medium_check, time() + ( 86400 * 30 ), '/' );
				$arr['wc_targetbay_utm_medium'] = $utm_medium_check;
			}
		}
		$cart_url = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

		if ( strpos( $cart_url, 'add-to-cart' ) === true ) {
			add_action( 'wp_loaded', array( $this, 'woocommerce_add_multiple_products_to_cart' ) );
		}

		return $arr;
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 0.1.0
	 */
	public function wc_targetbay_hooks() {
		try {
			add_action( 'wp_logout', array( $this, 'wc_targetbay_logout' ) );
			add_action( 'wp_login', array( $this, 'wc_targetbay_login' ), 10, 2 );
			if ( ! is_admin() ) {

				if ( 'enable' === $this->wc_targetbay_cart_tracking ) {
					add_action( 'woocommerce_add_to_cart', array( $this, 'wc_targetbay_action_woocommerce_add_to_cart' ), 10, 4 );
					add_action( 'woocommerce_ajax_added_to_cart', array( $this, 'wc_targetbay_ajax_action_woocommerce_add_to_cart' ), 10, 1 );
					add_action( 'woocommerce_update_cart_action_cart_updated', array( $this, 'wc_targetbay_action_woocommerce_update_to_cart' ), 10, 2 );
					add_action( 'woocommerce_cart_item_removed', array( $this, 'wc_targetbay_action_woocommerce_cart_item_removed' ), 10, 2 );
				}

				// Product reviews start.
				if ( 'enable' === $this->wc_targetbay_pro_review ) {
					add_action( 'woocommerce_after_single_product_summary', array( $this, 'wc_targetbay_single_product_closing_div' ), 10, 2 );
					add_action( 'woocommerce_single_product_summary', array( $this, 'wc_targetbay_action_after_single_product_title' ), 10, 2 );
				}
				// Bulk reviews start.
				if ( 'enable' === $this->wc_targetbay_bulk_reviews ) {
					add_action( 'woocommerce_shop_loop_item_title', array( $this, 'wc_targetbay_action_loop_product' ), 10, 2 );
				}
				// Bulk reviews end.
				add_action( 'woocommerce_thankyou', array( $this, 'wc_targetbay_action_woocommerce_thankyou' ), 10, 2 );
			}
			$place_script = 'wp_footer';
			if ( 'head' === $this->wc_targetbay_script_load ) {
				$place_script = 'wp_head';
			}

			add_action( 'user_register', array( $this, 'wc_targetbay_user_register' ), 10, 2 );
			add_action( 'woocommerce_order_status_completed', array( $this, 'wc_targetbay_order_completed' ), 10, 2 );
			add_action( 'woocommerce_order_status_cancelled', array( $this, 'wc_targetbay_order_cancelled' ), 10, 2 );
			add_action( 'woocommerce_order_status_refunded', array( $this, 'wc_targetbay_order_refunded' ), 10, 2 );
			add_action( 'woocommerce_order_status_processing', array( $this, 'wc_targetbay_order_processing' ), 10, 2 );
			add_action( 'woocommerce_order_status_on-hold', array( $this, 'wc_targetbay_order_on_hold' ), 10, 2 );
			add_filter( $place_script, array( $this, 'wc_targetbay_add_script' ), 10, 2 );
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * User register event.
	 *
	 * @param string $wc_targetbay_user_id woocommerce users id.
	 */
	public function wc_targetbay_user_register( $wc_targetbay_user_id ) {
		try {
			$tb_session_id       = '';
			$current_workflow_id = '';
			if ( isset( $_COOKIE['targetbay_session_id'] ) ) {
				$tb_session_id = sanitize_text_field( wp_unslash( $_COOKIE['targetbay_session_id'] ) );
			}
			if ( isset( $_COOKIE['current_workflow_id'] ) ) {
				$current_workflow_id = sanitize_text_field( wp_unslash( $_COOKIE['current_workflow_id'] ) );
			}

			$user_info              = get_userdata( $wc_targetbay_user_id );
			$data_list['user_name'] = isset( $user_info->user_nicename ) ? $user_info->user_nicename : '';
			if ( '' === $data_list['user_name'] ) {
				$data_list['user_name'] = isset( $user_info->first_name ) ? $user_info->first_name : '';
			}
			if ( '' === $data_list['user_name'] ) {
				$data_list['user_name'] = isset( $user_info->user_email ) ? $user_info->user_email : '';
			}

			$data_list['user_mail']           = $user_info->user_email;
			$data_list['firstname']           = isset( $user_info->first_name ) ? $user_info->first_name : $data_list['user_name'];
			$data_list['lastname']            = isset( $user_info->last_name ) ? $user_info->last_name : $data_list['user_name'];
			$data_list['session_id']          = $wc_targetbay_user_id;
			$data_list['user_id']             = $wc_targetbay_user_id;
			$data_list['account_created']     = gmdate( 'Y-m-d' );
			$data_list['timestamp']           = strtotime( gmdate( 'Y-m-d' ) );
			$data_list['previous_session_id'] = $tb_session_id;
			$data_list['current_workflow_id'] = $current_workflow_id;
			$data_list['ip_address']          = $this->get_user_ip();
			$data_list['user_agent']          = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
			if ( '' !== $data_list['user_mail'] && 'undefined' !== $data_list['user_mail'] && 'null' !== $data_list['user_mail'] ) {
				if ( isset( $_SERVER['HTTP_HOST'] ) ) {
					$server_host = wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) );
				} else {
					$server_host = '';
				}
				setcookie( 'targetbay_session_id', $wc_targetbay_user_id, time() + ( 86400 * 30 ), '/', '.' . $server_host['path'] );
				$this->wc_targetbay_send_data( $data_list, 'customer-created' );
				$data_list_login['user_name']           = isset( $data_list['user_name'] ) ? $data_list['user_name'] : $data_list['user_mail'];
				$data_list_login['user_mail']           = $data_list['user_mail'];
				$data_list_login['session_id']          = $wc_targetbay_user_id;
				$data_list_login['user_id']             = $wc_targetbay_user_id;
				$data_list_login['login_date']          = gmdate( 'Y-m-d' );
				$data_list_login['timestamp']           = strtotime( gmdate( 'Y-m-d' ) );
				$data_list_login['previous_session_id'] = $tb_session_id;
				$data_list_login['current_workflow_id'] = $current_workflow_id;
				$data_list_login['ip_address']          = $this->get_user_ip();
				$data_list_login['user_agent']          = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
				$this->wc_targetbay_send_data( $data_list_login, 'login' );
			}
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * TargetBay bulk reviews placeholder.
	 */
	public function wc_targetbay_action_loop_product() {
		global $product;
		echo '<div class="targetbay_star_container" id="' . esc_attr( $product->get_id() ) . '"></div>';
	}

	/**
	 * TargetBay placeholder for single product.
	 */
	public function wc_targetbay_action_after_single_product_title() {
		echo '<div class="product-name"></div>';
	}

	/**
	 * TargetBay reviews placeholder for single product.
	 */
	public function wc_targetbay_single_product_closing_div() {
		echo '<div id="targetbay_reviews"></div>';
	}

	/**
	 * TargetBay page tracking.
	 */
	public function tb_track_views() {
		try {
			if ( is_home() || is_archive() || is_category() || is_single() || is_page() || is_search() ) {
				$data_list['page_type'] = 'pages';
				$this->wc_targetbay_insert_tracking( $data_list );
			}

			if ( is_product_category() ) {
				$data_list['page_type'] = 'product-category';
				$this->wc_targetbay_insert_tracking( $data_list );
			}

			if ( is_product() ) {
				$get_tb_user_details       = $this->wc_targetbay_user_data();
				$get_tb_utm_data           = $this->wc_targetbay_utm_tracking_handle();
				$data_list['user_name']    = $get_tb_user_details['user_name'];
				$data_list['user_mail']    = $get_tb_user_details['user_mail'];
				$data_list['session_id']   = $get_tb_user_details['session_id'];
				$data_list['user_id']      = $get_tb_user_details['user_id'];
				$product                   = wc_get_product();
				$data_list['product_id']   = $product->get_id();
				$data_list['product_name'] = $product->get_title();
				$data_list['page_url']     = $product->get_permalink();
				$data_list['ip_address']   = $this->get_user_ip();
				$data_list['user_agent']   = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
				$data_list['referrer']     = isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
				$data_list['utm_sources']  = $get_tb_utm_data['wc_targetbay_utm_source'];
				$data_list['utm_token']    = $get_tb_utm_data['wc_targetbay_utm_token'];
				$data_list['utm_medium']   = $get_tb_utm_data['wc_targetbay_utm_medium'];
				$img_details               = get_the_post_thumbnail_url( $product->get_id() );
				if ( '' === $img_details ) {
					$img_details = 'https://' . $this->wc_targetbay_path . '.targetbay.com/images/no-image.jpg';
				}
				$data_list['product_img'] = $img_details;
				$this->wc_targetbay_send_data( $data_list, 'product-view' );
			}
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * TargetBay insert tracking.
	 *
	 * @param array $data_list Data list.
	 */
	public function wc_targetbay_insert_tracking( $data_list ) {
		try {
			$get_tb_user_details      = $this->wc_targetbay_user_data();
			$get_tb_utm_data          = $this->wc_targetbay_utm_tracking_handle();
			$data['tbcustomer_name']  = $get_tb_user_details['user_name'];
			$data['tbcustomer_email'] = $get_tb_user_details['user_mail'];
			$data['session_id']       = $get_tb_user_details['session_id'];
			$data['tbcustomer_id']    = $get_tb_user_details['user_id'];
			$data['page_type']        = $data_list['page_type'];
			$data['tb_pageurl']       = get_page_link();
			$data['tbpage_title']     = get_the_title();
			$data['tb_user_agent']    = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
			$data['referrer']         = isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
			$data['utm_sources']      = $get_tb_utm_data['wc_targetbay_utm_source'];
			$data['utm_token']        = $get_tb_utm_data['wc_targetbay_utm_token'];
			$data['utm_medium']       = $get_tb_utm_data['wc_targetbay_utm_medium'];
			$this->wc_targetbay_send_data( $data, 'page-visit' );
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * TargetBay Thank you action.
	 *
	 * @param string $order_id Order id.
	 */
	public function wc_targetbay_action_woocommerce_thankyou( $order_id ) {
		try {
			$this->wc_targetbay_order_id = $order_id;
			$get_tb_user_details         = $this->wc_targetbay_user_data();
			$get_tb_utm_data             = $this->wc_targetbay_utm_tracking_handle();
			$data_list['session_id']     = $get_tb_user_details['session_id'];
			$data_list['user_id']        = $get_tb_user_details['user_id'];
			$data_list['order_id']       = $order_id;
			$data_list['utm_sources']    = $get_tb_utm_data['wc_targetbay_utm_source'];
			$data_list['utm_token']      = $get_tb_utm_data['wc_targetbay_utm_token'];
			$data_list['utm_medium']     = $get_tb_utm_data['wc_targetbay_utm_medium'];

			$this->wc_targetbay_send_data( $data_list, 'order-created' );
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * TargetBay Order complete check.
	 *
	 * @param string $order_id Order id.
	 */
	public function wc_targetbay_order_completed( $order_id ) {
		try {
			$data_list['order_id']     = $order_id;
			$data_list['order_status'] = 'completed';
			$this->wc_targetbay_send_data( $data_list, 'order-updated' );
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * TargetBay order cancel track.
	 *
	 * @param string $order_id Order Id.
	 *
	 * @return void
	 */
	public function wc_targetbay_order_cancelled( $order_id ) {
		try {
			$data_list['order_id']     = $order_id;
			$data_list['order_status'] = 'cancelled';
			$this->wc_targetbay_send_data( $data_list, 'order-updated' );
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * TargetBay order refund track.
	 *
	 * @param string $order_id Order Id.
	 */
	public function wc_targetbay_order_refunded( $order_id ) {
		try {
			$data_list['order_id']     = $order_id;
			$data_list['order_status'] = 'refunded';
			$this->wc_targetbay_send_data( $data_list, 'order-updated' );
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * Target bay order processing.
	 *
	 * @param string $order_id Order id.
	 */
	public function wc_targetbay_order_processing( $order_id ) {
		try {
			$data_list['order_id']     = $order_id;
			$data_list['order_status'] = 'processing';
			$this->wc_targetbay_send_data( $data_list, 'order-updated' );
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * Target bay order on hold.
	 *
	 * @param string $order_id Order Id.
	 */
	public function wc_targetbay_order_on_hold( $order_id ) {
		try {
			$data_list['order_id']     = $order_id;
			$data_list['order_status'] = 'hold';
			$this->wc_targetbay_send_data( $data_list, 'order-updated' );
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * Send Woocommerce Data.
	 *
	 * @param array  $data_list Data List.
	 * @param string $end_point End point.
	 */
	public function wc_targetbay_send_data( $data_list, $end_point ) {

		try {
			$tb_api   = $end_point . '?_t=' . $this->wc_targetbay_auth_token;
			$base_url = 'https://' . $this->wc_targetbay_path . '.targetbay.com/api/v1/woo/';
			if ( in_array( 'curl', get_loaded_extensions(), true ) ) {
				$input['method']      = 'POST';
				$input['httpversion'] = '1.0';
				$input['headers']     = array( 'Content-Type: application/json' );
				$input['body']        = wp_json_encode( $data_list );
				$response             = wp_remote_post( $base_url . $tb_api, $input );

				if ( is_wp_error( $response ) ) {
					$error_msg = $response->get_error_message();
				}
			} else {
				$client = new Client(
					array(
						'base_uri' => $base_url,
						'debug'    => false,
					)
				);
				$client->post(
					$base_url . $tb_api,
					array(
						'json'  => $data_list,
						'debug' => false,
					)
				);
			}
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * Script
	 */
	public function wc_targetbay_add_script() {

		try {
			$order_id = '';
			if ( isset( $_GET['key'] ) && sanitize_text_field( wp_unslash( $_GET['key'] ) ) ) {
				$order_id = wc_get_order_id_by_order_key( sanitize_text_field( wp_unslash( $_GET['key'] ) ) );
			}
			$product_name = '';
			$product_id   = '';
			$product_url  = '';
			$product_img  = '';
			$page_url     = '';
			$pro_cat      = '';
			if ( is_product_category() ) {
				$page_url      = 'category-view';
				$category      = get_queried_object();
				$category_id   = $category->term_id;
				$category_url  = get_category_link( $category->term_id );
				$category_name = $category->name;
				$pro_cat       = 'category: {id: ' . $category_id . ', link: \'' . $category_url . '\', name: \'' . $category_name . '\'},';
			}

			if ( is_home() || is_archive() || is_single() || is_page() ) {
				$page_url = 'page-visit';
			}
			if ( is_category() ) {
				$page_url = 'category-view';
			}

			if ( is_search() ) {
				$page_url = 'searched';
			}
			$status = 0;
			if ( is_product() ) {
				$page_url     = 'product-view';
				$product      = wc_get_product();
				$product_id   = $product->get_id();
				$product_name = $product->get_title();
				$product_url  = $product->get_permalink();
				$product_img  = get_the_post_thumbnail_url( $product->get_id() );
				$pr_status    = $product->get_stock_status();
				if ( 'outofstock' === $pr_status ) {
					$status = 1;
				}
			}
			$get_tb_user_details = $this->wc_targetbay_user_data();
			$get_tb_utm_data     = $this->wc_targetbay_utm_tracking_handle();
			$wc_tb_user_id       = $get_tb_user_details['user_id'];
			$wc_tb_user_name     = $get_tb_user_details['user_name'];
			$wc_tb_user_mail     = $get_tb_user_details['user_mail'];
			$wc_tb_utm_source    = $get_tb_utm_data['wc_targetbay_utm_source'];
			$wc_tb_utm_token     = $get_tb_utm_data['wc_targetbay_utm_token'];
			$settings_details    = get_option( 'wc_targetbay_review_settings', $this->wc_targetbay_get_default_settings() );
			if ( isset( $settings_details ) && count( $settings_details ) > 0 ) {
				$script_data = "<script>
				window.tbConfig = {
					platform: 'wc',
					apiStatus: '$this->wc_targetbay_path',
					publicKey: '$this->wc_targetbay_auth_token',
					apiKey: '$this->wc_targetbay_api_key',
					apiToken: '$this->wc_targetbay_index_name',
					apiVersion: 'v1',
					trackingType: '1',
					productName: '$product_name',
					productId: '$product_id',
					productImageUrl: '$product_img',
					productUrl: '$product_url',
					productStockStatus: '$status',
					userId: '$wc_tb_user_id',
					userMail: '$wc_tb_user_mail',
					userName: '$wc_tb_user_name',
					userAvatar: '',
					pageUrl: '$page_url',
					utmSources: '$wc_tb_utm_source',
					utmToken: '$wc_tb_utm_token',
					pageData: '',
					orderId: '$order_id',
					tbWooBulkReview : true,
					$pro_cat
					tbTrack: true,
					tbMessage: true,
					tbRecommendations: true,
					tbReview: {
					tbSiteReview: true,
					tbProductReview: true,
					tbBulkReview: true,
					tbQa: true,
					tbReviewBadge: true
					}
				};";
				if ( $settings_details['wc_targetbay_disable_review_system'] ) {
					// For site review creating div.
					$script_data .= "var iDiv = document.createElement( 'div' );
						iDiv.id = 'targetbay_site_reviews';
						var innerDiv = document.createElement( 'div' );
						innerDiv.className = 'block-2';
						iDiv.appendChild( innerDiv );
						document.getElementsByTagName( 'body' )[0].appendChild( iDiv );";
				}

				// For order reviews.
				$script_data .= "var iDivP = document.createElement( 'div' );
			iDivP.id = 'targetbay_message';
			var innerDivP = document.createElement( 'div' );
			innerDivP.className = 'block-2';
			iDivP.appendChild( innerDivP );
			document.getElementsByTagName( 'body' )[0].appendChild( iDivP );

			var iDivNew = document.createElement( 'div' );
				iDivNew.id = 'targetbay_order_reviews';
				iDivNew.className = 'targetbay_order_reviews';
				var innerDivNew = document.createElement( 'div' );
				innerDivNew.className = 'block-2';
				iDivNew.appendChild( innerDivNew );
				document.getElementsByTagName( 'body' )[0].appendChild( iDivNew );

				var sNew = document.scripts[0], gNew;
				gNew = document.createElement( 'script' );
				gNew.src = '" . 'https://' . $this->wc_targetbay_path . '.targetbay.com' . "/js/wc-events.js';
				gNew.type = 'text/javascript';
				gNew.async = true;
				sNew.parentNode.insertBefore( gNew, sNew );
				</script>";
			echo $script_data;
			}
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * Targetbay Login.
	 *
	 * @param string $redirect Redirect Url.
	 * @param string $user User Id.
	 */
	public function wc_targetbay_login( $redirect, $user ) {
		try {
			if ( isset( $user->ID ) ) {
				$tb_session_id = '';
				if ( isset( $_COOKIE['targetbay_session_id'] ) ) {
					$tb_session_id = sanitize_text_field( wp_unslash( $_COOKIE['targetbay_session_id'] ) );
				}
				$data_list['user_name']           = isset( $user->display_name ) ? sanitize_user( $user->display_name, true ) : sanitize_email( $user->user_email );
				$data_list['user_mail']           = sanitize_email( $user->user_email );
				$data_list['session_id']          = $user->ID;
				$data_list['user_id']             = $user->ID;
				$data_list['login_date']          = gmdate( 'Y-m-d' );
				$data_list['timestamp']           = strtotime( gmdate( 'Y-m-d' ) );
				$data_list['previous_session_id'] = $tb_session_id;
				$data_list['ip_address']          = $this->get_user_ip();
				$data_list['user_agent']          = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
				if ( '' !== $data_list['user_mail'] && 'undefined' !== $data_list['user_mail'] && 'null' !== $data_list['user_mail'] ) {
					$this->wc_targetbay_send_data( $data_list, 'login' );
				}
				if ( isset( $_SERVER['HTTP_HOST'] ) ) {
					$server_host = wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) );
				} else {
					$server_host = '';
				}
				setcookie( 'targetbay_session_id', $user->ID, time() + ( 86400 * 30 ), '/', '.' . $server_host['path'] );
			}
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * Logout event.
	 */
	public function wc_targetbay_logout() {
		try {
			$get_tb_user_details      = $this->wc_targetbay_user_data();
			$data_list['user_id']     = $get_tb_user_details['user_id'];
			$data_list['session_id']  = $get_tb_user_details['session_id'];
			$data_list['user_name']   = $get_tb_user_details['user_name'];
			$data_list['user_mail']   = $get_tb_user_details['user_mail'];
			$data_list['logout_date'] = gmdate( 'Y-m-d' );
			$data_list['timestamp']   = strtotime( gmdate( 'Y-m-d' ) );
			$data_list['ip_address']  = $this->get_user_ip();
			$this->wc_targetbay_send_data( $data_list, 'logout' );

			if ( isset( $_SERVER['HTTP_HOST'] ) ) {
				$server_host = wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) );
			} else {
				$server_host = '';
			}
			$session_id_tb = (string) wp_rand( 1000000000, 9999999999 );
			$user_session  = isset( $_COOKIE['tb_user_session'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['tb_user_session'] ) ) : '';
			$tb_session_id = isset( $_COOKIE['targetbay_session_id'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['targetbay_session_id'] ) ) : '';
			setcookie( 'targetbay_session_id', $session_id_tb, time() + ( 86400 * 30 ), '/', '.' . $server_host['path'] );
			setcookie( 'utm_token', '', time() + ( 86400 * 30 ), '/', '.' . $server_host['path'] );
			$user_data_created = '';
			$input_src         = '_un=';
			$input_src        .= '&_uid=' . $session_id_tb;
			$input_src        .= '&_uem=anonymous';
			$input_src        .= '&_utid=' . $session_id_tb;
			$input_src        .= '&_usid=' . $session_id_tb;
			$input_src        .= '&_uc=1&_ulogin=' . $user_data_created;
			$input_src        .= '&_uasid=' . $user_session;
			$this->tb_set_cookie( $input_src );
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * Get visitor IP address.
	 *
	 * @return mixed
	 */
	private function get_user_ip() {
		try {
			if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
				return sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
			} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
				return sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
			} elseif ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
				return sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED'] ) );
			} elseif ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
				return sanitize_text_field( wp_unslash( $_SERVER['HTTP_FORWARDED_FOR'] ) );
			} elseif ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
				return sanitize_text_field( wp_unslash( $_SERVER['HTTP_FORWARDED'] ) );
			} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
				return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
			}
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
			return '';
		}
	}

	/**
	 * Get default setting.
	 *
	 * @return array
	 */
	public function wc_targetbay_get_default_settings() {
		try {
			return array(
				'wc_targetbay_server'                => 'live',
				'wc_targetbay_api_secret'            => '',
				'wc_targetbay_rich_snippets'         => 'manual',
				'wc_targetbay_pro_review'            => 'enable',
				'wc_targetbay_bulk_review'           => 'enable',
				'wc_targetbay_disable_review_system' => true,
				'wc_targetbay_star_ratings_enabled'  => 'no',
				'wc_targetbay_api_secret'            => '',
				'wc_targetbay_script_load'           => 'footer',
				'wc_targetbay_cart_tracking'         => 'enable',
			);
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
			$data_list  = array();
			return $data_list;
		}
	}
	/**
	 * Get parent grouped id
	 *
	 * @param integer $children_id child id.
	 */
	public function get_parent_grouped_id( $children_id ) {
		global $wpdb;

		$results = $wpdb->prepare(
			"SELECT
				post_id
			FROM
				{ $wpdb->prefix }postmeta
			WHERE
				meta_key = `_children`
				AND
				meta_value LIKE %s;",
			'%' . $wpdb->esc_like( $children_id ) . '%'
		);
		// Will only return one product Id or false if there is zero or many.
		return is_array( $results ) ? ( count( $results ) === 1 ? reset( $results ) : '' ) : '';
	}

	/**
	 * Woocommerce add to cart.
	 *
	 * @param array  $cart_item cart item.
	 * @param string $cart_item_key cart item key.
	 * @param string $qty cart qty.
	 * @param string $variation_id cart variationid.
	 */
	public function wc_targetbay_action_woocommerce_add_to_cart( $cart_item, $cart_item_key, $qty, $variation_id ) {
		try {
			$data_list   = array();
			$arr         = array();
			$update_data = false;
			if ( count( wC()->cart->get_cart() ) > 0 ) {
				foreach ( wC()->cart->get_cart() as $key => $cart_item ) {
					if ( $cart_item['product_id'] === $cart_item_key && 0 === $variation_id ) {
						$product_details = wc_get_product( $cart_item['data']->get_id() );
						$product_cats    = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'names' ) );
						$price_details   = null !== get_post_meta( $cart_item['product_id'], '_price', true ) && '' !== get_post_meta( $cart_item['product_id'], '_price', true ) ? get_post_meta( $cart_item['product_id'], '_price', true ) : get_post_meta( $cart_item['product_id'], 'price', true );
					}
					if ( $cart_item['variation_id'] === $variation_id && 0 !== $variation_id ) {
						$product_details = wc_get_product( $cart_item['data']->get_id() );
						$product_cats    = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'names' ) );
						$price_details   = null !== get_post_meta( $cart_item['variation_id'], '_price', true ) && '' !== get_post_meta( $cart_item['variation_id'], '_price', true ) ? get_post_meta( $cart_item['variation_id'], '_price', true ) : '';
					}
					if ( isset( $product_details ) ) {
						$img_details = get_the_post_thumbnail_url( $cart_item['product_id'] );
						if ( $cart_item['variation_id'] === $variation_id ) {
							if ( isset( $_COOKIE[ 'tb_old_qty_' . $cart_item['product_id'] . '_' . $cart_item['variation_id'] ] ) ) {
								$update_data                         = true;
								$data_list_cart['order_id']          = $cart_item['key'];
								$data_list_cart['product_id']        = $cart_item['product_id'];
								$data_list_cart['variant_id']        = $cart_item['variation_id'];
								$data_list_cart['product_sku']       = $product_details->get_sku();
								$data_list_cart['product_name']      = $product_details->get_title();
								$data_list_cart['price']             = $price_details;
								$data_list_cart['special_price']     = $product_details->get_sale_price();
								$data_list_cart['productimg']        = $img_details;
								$data_list_cart['category_name']     = implode( ',', $product_cats );
								$data_list_cart['category']          = '';
								$data_list_cart['product_parent_id'] = $this->get_parent_grouped_id( $cart_item['product_id'] );
								$old_qty                             = isset( $_COOKIE[ 'tb_old_qty_' . $cart_item['product_id'] . '_' . $cart_item['variation_id'] ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ 'tb_old_qty_' . $cart_item['product_id'] . '_' . $cart_item['variation_id'] ] ) ) : $cart_item['quantity'];
								$data_list_cart['old_quantity']      = isset( $old_qty ) ? $old_qty : $cart_item['quantity'];
								if ( $data_list_cart['old_quantity'] !== $cart_item['quantity'] ) {
									$data_list_cart['quantity']     = $cart_item['quantity'];
									$data_list_cart['new_quantity'] = $cart_item['quantity'];
									$data_list_cart['page_url']     = $product_details->get_permalink();
									$data_list_cart['product_type'] = $product_details->get_type();
									$arr[]                          = $data_list_cart;
								}
							} else {
								$update_data = false;
							}
						}
						if ( ! $update_data ) {
							$data_list['product_id']        = $cart_item['product_id'];
							$data_list['variant_id']        = $cart_item['variation_id'];
							$data_list['product_sku']       = $product_details->get_sku();
							$data_list['product_name']      = $product_details->get_title();
							$data_list['price']             = $price_details;
							$data_list['special_price']     = $product_details->get_sale_price();
							$data_list['productimg']        = $img_details;
							$data_list['category_name']     = implode( ',', $product_cats );
							$data_list['category']          = '';
							$data_list['quantity']          = $cart_item['quantity'];
							$data_list['page_url']          = $product_details->get_permalink();
							$data_list['product_type']      = $product_details->get_type();
							$data_list['product_parent_id'] = $this->get_parent_grouped_id( $cart_item['product_id'] );
						}
						setcookie( 'tb_old_qty_' . $cart_item['product_id'] . '_' . ( isset( $cart_item['variation_id'] ) ? isset( $cart_item['variation_id'] ) : 0 ), $cart_item['quantity'], time() + ( 86400 * 30 ), '/' );
					}
				}
			}
			$url                      = 'add-to-cart';
			$get_tb_user_details      = $this->wc_targetbay_user_data();
			$data_list['user_id']     = $get_tb_user_details['user_id'];
			$data_list['session_id']  = $get_tb_user_details['session_id'];
			$data_list['user_name']   = $get_tb_user_details['user_name'];
			$data_list['user_mail']   = $get_tb_user_details['user_mail'];
			$get_tb_utm_data          = $this->wc_targetbay_utm_tracking_handle();
			$data_list['utm_sources'] = $get_tb_utm_data['wc_targetbay_utm_source'];
			$data_list['utm_token']   = $get_tb_utm_data['wc_targetbay_utm_token'];
			$data_list['utm_medium']  = $get_tb_utm_data['wc_targetbay_utm_medium'];
			if ( $update_data && count( $arr ) > 0 ) {
				$data_list['cart_items'] = $arr;
				$url                     = 'update-cart';
			}

			$this->wc_targetbay_send_data( $data_list, $url );
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * Woocommerce add to cart
	 *
	 * @param string $product_id product id.
	 */
	public function wc_targetbay_ajax_action_woocommerce_add_to_cart( $product_id ) {
		try {
			$data_list               = array();
			$arr                     = array();
			$update_data             = false;
			$cart_item['product_id'] = $product_id;
			$cart_item['quantity']   = 1;
			$product_details         = wc_get_product( $product_id );
			$product_cats            = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'names' ) );
			$img_details             = get_the_post_thumbnail_url( $cart_item['product_id'] );
			if ( $cart_item['product_id'] === $cart_item_key && 0 === $variation_id ) {
				$price_details = null !== get_post_meta( $cart_item['product_id'], '_price', true ) && '' !== get_post_meta( $cart_item['product_id'], '_price', true ) ? get_post_meta( $cart_item['product_id'], '_price', true ) : get_post_meta( $cart_item['product_id'], 'price', true );
			}
			if ( $cart_item['variation_id'] === $variation_id && 0 !== $variation_id ) {
				$price_details = null !== get_post_meta( $cart_item['variation_id'], '_price', true ) && '' !== get_post_meta( $cart_item['variation_id'], '_price', true ) ? get_post_meta( $cart_item['variation_id'], '_price', true ) : '';
			}
			if ( isset( $_COOKIE[ 'tb_old_qty_' . $cart_item['product_id'] . '_' . $cart_item['variation_id'] ] ) ) {
				$update_data                         = true;
				$data_list_cart['product_id']        = $cart_item['product_id'];
				$data_list_cart['variant_id']        = $cart_item['variation_id'];
				$data_list_cart['product_sku']       = $product_details->get_sku();
				$data_list_cart['product_name']      = $product_details->get_title();
				$data_list_cart['price']             = $price_details;
				$data_list_cart['special_price']     = $product_details->get_sale_price();
				$data_list_cart['productimg']        = $img_details;
				$data_list_cart['category_name']     = implode( ',', $product_cats );
				$data_list_cart['category']          = '';
				$old_qty                             = isset( $_COOKIE[ 'tb_old_qty_' . $cart_item['product_id'] . '_' . $cart_item['variation_id'] ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ 'tb_old_qty_' . $cart_item['product_id'] . '_' . $cart_item['variation_id'] ] ) ) : $cart_item['quantity'];
				$data_list_cart['product_parent_id'] = $this->get_parent_grouped_id( $cart_item['product_id'] );
				$data_list_cart['old_quantity']      = isset( $old_qty ) ? $old_qty : $cart_item['quantity'];
				if ( $data_list_cart['old_quantity'] !== $cart_item['quantity'] ) {
					$data_list_cart['quantity']     = $cart_item['quantity'];
					$data_list_cart['new_quantity'] = $cart_item['quantity'];
					$data_list_cart['page_url']     = $product_details->get_permalink();
					$data_list_cart['product_type'] = $product_details->get_type();
					$arr[]                          = $data_list_cart;
				}
			} else {
				$update_data = false;
			}
			if ( ! $update_data ) {
				$data_list['product_id']        = $cart_item['product_id'];
				$data_list['variant_id']        = $cart_item['variation_id'];
				$data_list['product_sku']       = $product_details->get_sku();
				$data_list['product_name']      = $product_details->get_title();
				$data_list['price']             = $price_details;
				$data_list['special_price']     = $product_details->get_sale_price();
				$data_list['productimg']        = $img_details;
				$data_list['category_name']     = implode( ',', $product_cats );
				$data_list['category']          = '';
				$data_list['quantity']          = $cart_item['quantity'];
				$data_list['page_url']          = $product_details->get_permalink();
				$data_list['product_type']      = $product_details->get_type();
				$data_list['product_parent_id'] = $this->get_parent_grouped_id( $cart_item['product_id'] );
			}
			setcookie( 'tb_old_qty_' . $cart_item['product_id'] . '_' . ( isset( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : 0 ), $cart_item['quantity'], time() + ( 86400 * 30 ), '/' );
			$url                      = 'add-to-cart';
			$get_tb_user_details      = $this->wc_targetbay_user_data();
			$data_list['user_id']     = $get_tb_user_details['user_id'];
			$data_list['session_id']  = $get_tb_user_details['session_id'];
			$data_list['user_name']   = $get_tb_user_details['user_name'];
			$data_list['user_mail']   = $get_tb_user_details['user_mail'];
			$get_tb_utm_data          = $this->wc_targetbay_utm_tracking_handle();
			$data_list['utm_sources'] = $get_tb_utm_data['wc_targetbay_utm_source'];
			$data_list['utm_token']   = $get_tb_utm_data['wc_targetbay_utm_token'];
			$data_list['utm_medium']  = $get_tb_utm_data['wc_targetbay_utm_medium'];
			if ( $update_data && count( $arr ) > 0 ) {
				$data_list['cart_items'] = $arr;
				$url                     = 'update-cart';
			}
			$this->wc_targetbay_send_data( $data_list, $url );
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * Update cart event tracking.
	 */
	public function wc_targetbay_action_woocommerce_update_to_cart() {
		$arr = array();
		try {
			if ( isset( $_REQUEST['cart'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( count( $_REQUEST['cart'] ) ) ) ) > 0 ) {
				$array = array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['cart'] ) );
				foreach ( $array as $key => $value ) {
					if ( count( wC()->cart->get_cart() ) > 0 ) {
						foreach ( wC()->cart->get_cart() as $cart_item ) {
							if ( $cart_item['key'] === $key ) {
								$product_details                 = wc_get_product( $cart_item['data']->get_id() );
								$product_cats                    = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'names' ) );
								$img_details                     = get_the_post_thumbnail_url( $cart_item['product_id'] );
								$data_list_cart['order_id']      = $cart_item['key'];
								$data_list_cart['product_id']    = $cart_item['product_id'];
								$data_list_cart['variant_id']    = $cart_item['variation_id'];
								$data_list_cart['product_sku']   = $product_details->get_sku();
								$data_list_cart['product_name']  = $product_details->get_title();
								$data_list_cart['price']         = $product_details->get_price();
								$data_list_cart['special_price'] = $product_details->get_sale_price();
								$data_list_cart['productimg']    = $img_details;
								$data_list_cart['category_name'] = implode( ',', $product_cats );
								$data_list_cart['category']      = '';
								$data_list_cart['old_quantity']  = isset( $_COOKIE[ 'tb_old_qty_' . $cart_item['product_id'] . '_' . $cart_item['variation_id'] ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ 'tb_old_qty_' . $cart_item['product_id'] . '_' . $cart_item['variation_id'] ] ) ) : $cart_item['quantity'];
								if ( $data_list_cart['old_quantity'] !== $cart_item['quantity'] ) {
									$data_list_cart['new_quantity'] = $cart_item['quantity'];
									$data_list_cart['page_url']     = $product_details->get_permalink();
									$data_list_cart['product_type'] = $product_details->get_type();
									setcookie( 'tb_old_qty_' . $cart_item['product_id'] . '_' . ( isset( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : 0 ), $cart_item['quantity'], time() + ( 86400 * 30 ), '/' );
									$arr[] = $data_list_cart;
								}
							}
						}
					}
				}

				$get_tb_user_details      = $this->wc_targetbay_user_data();
				$data_list['user_id']     = $get_tb_user_details['user_id'];
				$data_list['session_id']  = $get_tb_user_details['session_id'];
				$data_list['user_name']   = $get_tb_user_details['user_name'];
				$data_list['user_mail']   = $get_tb_user_details['user_mail'];
				$get_tb_utm_data          = $this->wc_targetbay_utm_tracking_handle();
				$data_list['utm_sources'] = $get_tb_utm_data['wc_targetbay_utm_source'];
				$data_list['utm_token']   = $get_tb_utm_data['wc_targetbay_utm_token'];
				$data_list['utm_medium']  = $get_tb_utm_data['wc_targetbay_utm_medium'];
				$data_list['cart_items']  = $arr;
				if ( count( $arr ) > 0 ) {
					$this->wc_targetbay_send_data( $data_list, 'update-cart' );
				}
			}
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}

	/**
	 * Remove cart item event.
	 *
	 * @param string $cart_item_key cart item key.
	 */
	public function wc_targetbay_action_woocommerce_cart_item_removed( $cart_item_key ) {
		try {
			$data_check_list = ( null !== WC()->cart->get_removed_cart_contents() && '' !== WC()->cart->get_removed_cart_contents() ) ? WC()->cart->get_removed_cart_contents() : false;
			if ( $data_check_list ) {
				$data_insert = WC()->cart->get_removed_cart_contents();
				foreach ( $data_insert as $key => $cart_item ) {
					$product_details            = wc_get_product( $cart_item['product_id'] );
					$product_cats               = wp_get_post_terms( $cart_item['product_id'], 'product_cat', array( 'fields' => 'names' ) );
					$img_details                = get_the_post_thumbnail_url( $cart_item['product_id'] );
					$price_details              = ( null !== get_post_meta( $cart_item['product_id'], '_price', true ) && '' !== get_post_meta( $cart_item['product_id'], '_price', true ) ) ? get_post_meta( $cart_item['product_id'], '_price', true ) : '';
					$get_tb_user_details        = $this->wc_targetbay_user_data();
					$data_list['user_id']       = $get_tb_user_details['user_id'];
					$data_list['session_id']    = $get_tb_user_details['session_id'];
					$data_list['user_name']     = $get_tb_user_details['user_name'];
					$data_list['user_mail']     = $get_tb_user_details['user_mail'];
					$get_tb_utm_data            = $this->wc_targetbay_utm_tracking_handle();
					$data_list['utm_sources']   = $get_tb_utm_data['wc_targetbay_utm_source'];
					$data_list['utm_token']     = $get_tb_utm_data['wc_targetbay_utm_token'];
					$data_list['utm_medium']    = $get_tb_utm_data['wc_targetbay_utm_medium'];
					$data_list['order_id']      = '';
					$data_list['product_id']    = $cart_item['product_id'];
					$data_list['variant_id']    = $cart_item['variation_id'];
					$data_list['product_sku']   = $product_details->get_sku();
					$data_list['product_name']  = $product_details->get_title();
					$data_list['price']         = $product_details->get_price();
					$data_list['special_price'] = $product_details->get_sale_price();
					$data_list['productimg']    = $img_details;
					$data_list['category_name'] = implode( ',', $product_cats );
					$data_list['category']      = '';
					$data_list['quantity']      = $cart_item['quantity'];
					$data_list['page_url']      = $product_details->get_permalink();
					$data_list['product_type']  = $product_details->get_type();
					setcookie( 'tb_old_qty_' . $cart_item['product_id'] . '_' . ( isset( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : 0 ), 0, time() + ( 86400 * 30 ), '/' );
					$this->wc_targetbay_send_data( $data_list, 'remove-to-cart' );
				}
			}
		} catch ( \Exception $e ) {
			$error_msg  = ', Message: ' . $e->getMessage();
			$error_msg .= ', Line: ' . $e->getLine();
		}
	}
	/**
	 * Encode public data.
	 *
	 * @param string $plaintext plain text.
	 */
	public function tb_set_cookie( $plaintext ) {
		setcookie( 'tb_fetch_points', base64_encode( $plaintext ), time() + ( 86400 * 30 ), '/', '.' . isset( $_SERVER['HTTP_HOST'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '' );
	}
	/**
	 * Add to cart multiple product.
	 */
	public function woocommerce_add_multiple_products_to_cart() {
		$cart_url = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

		if ( ! class_exists( 'WC_Form_Handler' ) || empty( $cart_url ) || strpos( $cart_url, 'add-to-cart' ) === false ) {
			return;
		}
		$cart_split_step_one = explode( '?', $cart_url )[1];
		$add_to_cart         = ( explode( '=', explode( '&', $cart_split_step_one )[0] )[1] );
		$quantity            = ( explode( '=', explode( '&', $cart_split_step_one )[1] )[1] );
		remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ), 20 );
		$product_ids = explode( ',', $add_to_cart );
		$quantity    = empty( $_REQUEST['quantity'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['quantity'] ) ) ) ? 1 : explode( ',', $quantity );
		foreach ( $product_ids as $key => $product_id ) {
			$quantity_value    = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $quantity[ $key ] );
			$adding_to_cart    = wc_get_product( $product_id );
			$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity_value );
			if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity_value ) ) {
				wc_add_to_cart_message( array( $product_id => $quantity_value ), true );
			}
			if ( ! $adding_to_cart ) {
				continue;
			}
		}
	}
}
