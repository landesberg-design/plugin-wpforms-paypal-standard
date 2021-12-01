<?php
/**
 * Plugin Name:       WPForms PayPal Standard
 * Plugin URI:        https://wpforms.com
 * Description:       PayPal Standard integration with WPForms.
 * Requires at least: 4.9
 * Requires PHP:      5.5
 * Author:            WPForms
 * Author URI:        https://wpforms.com
 * Version:           1.4.0
 * Text Domain:       wpforms-paypal-standard
 * Domain Path:       languages
 *
 * WPForms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WPForms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WPForms. If not, see <https://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin version.
define( 'WPFORMS_PAYPAL_STANDARD_VERSION', '1.4.0' );
define( 'WPFORMS_PAYPAL_STANDARD_FILE', __FILE__ );
define( 'WPFORMS_PAYPAL_STANDARD_PATH', plugin_dir_path( WPFORMS_PAYPAL_STANDARD_FILE ) );
define( 'WPFORMS_PAYPAL_STANDARD_URL', plugin_dir_url( WPFORMS_PAYPAL_STANDARD_FILE ) );

/**
 * Load the provider class.
 *
 * @since 1.4.0
 */
function wpforms_paypal_standard_load() {

	// Load translated strings.
	load_plugin_textdomain( 'wpforms-paypal-standard', false, dirname( plugin_basename( WPFORMS_PAYPAL_STANDARD_FILE ) ) . '/languages/' );

	// Check requirements.
	if ( ! wpforms_paypal_standard_required() ) {
		return;
	}

	// Load the plugin.
	wpforms_paypal_standard();
}

add_action( 'plugins_loaded', 'wpforms_paypal_standard_load' );

/**
 * Check addon requirements.
 *
 * @since 1.4.0
 */
function wpforms_paypal_standard_required() {

	if ( version_compare( PHP_VERSION, '5.5', '<' ) ) {
		add_action( 'admin_init', 'wpforms_paypal_standard_deactivate' );
		add_action( 'admin_notices', 'wpforms_paypal_standard_fail_php_version' );

		return false;
	}

	if ( ! function_exists( 'wpforms' ) || ! wpforms()->pro ) {
		return false;
	}

	if ( version_compare( wpforms()->version, '1.6.6', '<' ) ) {
		add_action( 'admin_init', 'wpforms_paypal_standard_deactivate' );
		add_action( 'admin_notices', 'wpforms_paypal_standard_fail_wpforms_version' );

		return false;
	}

	if ( ! function_exists( 'wpforms_get_license_type' ) || ! in_array( wpforms_get_license_type(), [ 'pro', 'elite', 'agency', 'ultimate' ], true ) ) {
		return false;
	}

	return true;
}

/**
 * Deactivate the plugin.
 *
 * @since 1.4.0
 */
function wpforms_paypal_standard_deactivate() {

	deactivate_plugins( plugin_basename( WPFORMS_PAYPAL_STANDARD_FILE ) );
}

/**
 * Admin notice for minimum PHP version.
 *
 * @since 1.4.0
 */
function wpforms_paypal_standard_fail_php_version() {

	echo '<div class="notice notice-error"><p>';
	printf(
		wp_kses( /* translators: %s - WPForms.com documentation page URL. */
			__( 'The WPForms PayPal Standard plugin is not accepting payments anymore because your site is running an outdated version of PHP that is no longer supported and is not compatible with the plugin. <a href="%s" target="_blank" rel="noopener noreferrer">Read more</a> for additional information.', 'wpforms-paypal-standard' ),
			[
				'a' => [
					'href'   => [],
					'rel'    => [],
					'target' => [],
				],
			]
		),
		'https://wpforms.com/docs/supported-php-version/'
	);

	echo '</p></div>';

	if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		unset( $_GET['activate'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
}

/**
 * Admin notice for minimum WPForms version.
 *
 * @since 1.4.0
 */
function wpforms_paypal_standard_fail_wpforms_version() {

	echo '<div class="notice notice-error"><p>';
	esc_html_e( 'The WPForms PayPal Standard plugin has been deactivated, because it requires WPForms v1.6.6 or later to work.', 'wpforms-paypal-standard' );
	echo '</p></div>';

	if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		unset( $_GET['activate'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

}

/**
 * Load the payment class.
 *
 * @since 1.0.0
 */
function wpforms_paypal_standard() {

	require_once WPFORMS_PAYPAL_STANDARD_PATH . 'class-paypal-standard.php';
}

/**
 * Load the plugin updater.
 *
 * @since 1.0.0
 *
 * @param string $key
 */
function wpforms_paypal_standard_updater( $key ) {

	new WPForms_Updater(
		[
			'plugin_name' => 'WPForms PayPal Standard',
			'plugin_slug' => 'wpforms-paypal-standard',
			'plugin_path' => plugin_basename( WPFORMS_PAYPAL_STANDARD_FILE ),
			'plugin_url'  => trailingslashit( WPFORMS_PAYPAL_STANDARD_URL ),
			'remote_url'  => WPFORMS_UPDATER_API,
			'version'     => WPFORMS_PAYPAL_STANDARD_VERSION,
			'key'         => $key,
		]
	);
}

add_action( 'wpforms_updater', 'wpforms_paypal_standard_updater' );
