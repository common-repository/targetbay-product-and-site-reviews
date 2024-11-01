<?php
/**
 * Unistall File.
 *
 * @file
 * Description of what this module (or file) is doing.
 * @package TargetBay_Product_and_Site_Reviews
 */

/**
 * Implements hook_help().
 */
if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}
delete_option( 'wc_targetbay_review_settings' );
