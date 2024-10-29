<?php
/**
 *
 * Main File for loading classes.
 *
 * @package Address Validation & Google Address Auto Complete Plugin for WooCommerce (Basic)
 */

/*
Plugin Name: ELEX Address Validation & Google Address Auto Complete Plugin for WooCommerce (Basic)
Plugin URI: https://elextensions.com/plugin/elex-address-validation-google-address-autocomplete-plugin-for-woocommerce-free-version/address-validation-address-auto-complete-plugin-for-woocommerce/
Description: Simple and easy to use address validation plugin that will help you to forget the pain of shipping to invalid addresses.
Version: 1.7.8
WC requires at least: 2.6.0
WC tested up to: 9.2
Author: ELEXtensions
Author URI: https://elextensions.com/
License: GPLv2
Text Domain: address-validation-address-auto-complete
*/
if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly.
}

if ( ! defined( 'ADDRESS_VALIDATION_MAIN_PATH' ) ) {
	define( 'ADDRESS_VALIDATION_MAIN_PATH', plugin_dir_url( __FILE__ ) );
}

if ( ! function_exists( 'Elex_woocommerce_version_check' ) ) {
	/** Woocommerce active check */
	function Elex_woocommerce_version_check() {
		if ( ! defined( 'WC_VERSION' ) && function_exists( 'WC' ) ) {
			define( 'WC_VERSION', WC()->version );
		}
	}
}
add_action( 'woocommerce_init', 'Elex_woocommerce_version_check' );

add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

add_action(
	'before_woocommerce_init',
	function() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, false );
		}
	} 
);

require_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( is_plugin_active( 'address-validation-and-auto-complete-plugin/class-wf-address-autocomplete-validation.php' ) ) {
	deactivate_plugins( basename( __FILE__ ) );
	wp_die( wp_kses_post( __( "You already have the Premium version installed in your website. For any issues, kindly raise a ticket via our <a target='_blank' href='https://elextensions.com/support/'>support page</a>.", 'wf-address-autocomplete-validation' ) ), '', array( 'back_link' => 1 ) );
} else {
	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		if ( ! class_exists( 'Elex_Address_Autocomplete_Validation' ) ) {
			/** Class - To setup the plugin */
			class Elex_Address_Autocomplete_Validation {
				/**
				 * Array of settings stored in database.
				 *
				 * @var array $all_settings Array of settings stored in database.
				 */
				protected $all_settings;

				public $settings;

				public $selected_api;

				public $enable_autocomplete_checkout;

				public $enable_address_validation;


				/** Constructor */
				public function __construct() {
					$this->all_settings = get_option( 'wf_address_autocomplete_validation_settings' );
					$this->wf_address_autocomplete_validation_init();
				
					add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'wf_address_autocomplete_validation_plugin_action_links' ) );
				}
				/** Function to check if woocommerce version>1.0. */
				public function wf_get_settings_url() {
					if ( defined( 'WC_VERSION' ) ) {
						return version_compare( WC_VERSION, '1.0', '>=' ) ? 'wc-settings' : 'woocommerce_settings';
					}
					// Fallback.
					return 'wc-settings';
				}
				/**
				 * To add settings url near plugin under installed plugin.
				 *
				 * @param array $links Array of Links.
				 */
				public function wf_address_autocomplete_validation_plugin_action_links( $links ) {

				
					$plugin_links = array(
						'<a href="' . admin_url( 'admin.php?page=' . $this->wf_get_settings_url() . '&tab=elex_address_autocomplete_validation' ) . '">' . __( 'Settings', 'wf-address-autocomplete-validation' ) . '</a>',
						'<a href="//elextensions.com/plugin/address-validation-google-address-auto-complete-plugin-for-woocommerce/" target="_blank">' . __( 'Premium Upgrade', 'wf_estimated_delivery' ) . '</a>',
						'<a href="https://elextensions.com/support/" target="_blank">' . __( 'Support', 'wf-address-autocomplete-validation' ) . '</a>',
					);
					return array_merge( $plugin_links, $links );
				}
				/** To include the necessary files for plugin */
				public function wf_address_autocomplete_validation_init() {
				
					include_once 'includes/class-wf-address-autocomplete-validation-settings.php';
				
					$this->settings = new Elex_Address_Autocomplete_Validation_Settings();
					$this->settings->Wf_Address_Autocomplete_Validation_Setup();
					$this->selected_api                 = isset( $this->all_settings['wf_address_autocomplete_restriction'] ) ? $this->all_settings['wf_address_autocomplete_restriction'] : 'none';
					$this->enable_autocomplete_checkout = isset( $this->all_settings['wf_aac_enable_google_autocomplete_checkout'] ) ? $this->all_settings['wf_aac_enable_google_autocomplete_checkout'] : 'no';
					$this->enable_address_validation    = isset( $this->all_settings['xa_address_validation'] ) ? $this->all_settings['xa_address_validation'] : 'none';
	
					if ( 'google' === $this->selected_api && 'yes' === $this->enable_autocomplete_checkout && $this->all_settings['wf_address_autocomplete_validation_google_api_key'] ) {
						include_once 'includes/class-wf-address-autocomplete.php';
						new Elex_Address_Autocomplete();
					}
	
					if ( 'easypost' === $this->enable_address_validation ) {
						include_once 'includes/class-wf-address-validation.php';
						new Elex_Address_Validation();
						include_once 'includes/class-address-validation-log.php';
					}
				
				}
			}
		}
	} else {
		?>
		<div id="message" class="error">
			<p>
			<?php	echo esc_html( __( 'WooCommerce plugin must be active for Address Validation & Google Address Auto Complete Plugin for WooCommerce(Basic) to work. ', 'wf-address-autocomplete-validation' ) ); ?>
			</p>
		</div>
		<?php
	return;
	}

	// Execute only on dashboard or on network dashboard.
	if ( is_admin() || is_network_admin() ) {
		new Elex_Address_Autocomplete_Validation();
	}

	if ( ! function_exists( 'elex_address_autocomplete_validation_setup_call' ) ) {
		/** To execute only on checkout page when visiting any woocommerce site. */
		function elex_address_autocomplete_validation_setup_call() {
			// the second(for elex amazon pay plugin) and third(for woocommerce amazon pay plugin) condition checks if amazon pay is used at checkout.
			// if amazon payment method is used, get request with parameters is made to checkout page, below we check if these parameters are set in checkout url.
			if ( is_checkout() && ! isset( $_GET['eh_amazon_payments'] ) && ! isset( $_GET['amazon_payments_advanced'] ) ) {
				new Elex_Address_Autocomplete_Validation();
			}
		}
	}
	add_action( 'woocommerce_checkout_init', 'elex_address_autocomplete_validation_setup_call' );

	add_filter( 'woocommerce_billing_fields', 'xa_wc_filter_state', 10, 1 );
	/** If Selects 'Sell to specific countries' in woocommerce settings.
	 *
	 * @param array $address_fields Array of address fields.
	 *
	 * @return $address_fields
	 */
	function xa_wc_filter_state( $address_fields ) {
		$address_fields['billing_state']['class'] = array( 'form-row-wide', 'address-field', 'update_totals_on_change' );
		return $address_fields;
	}

	add_filter( 'woocommerce_shipping_fields', 'xa_wc_filter_shipping_state', 10, 1 );
	/** Filters shipping state.
	 *
	 * @param array $address_fields Array of address fields.
	 *
	 * @return $address_fields
	 */
	function xa_wc_filter_shipping_state( $address_fields ) {
		$address_fields['shipping_state']['class'] = array( 'form-row-wide', 'address-field', 'update_totals_on_change' );
		return $address_fields;
	}

	//adding language
	function elex_addr_val_load_plugin_textdomain() {
		load_plugin_textdomain( 'address-validation-address-auto-complete', false, basename( dirname( __FILE__ ) ) . '/language/' );
	}
	add_action( 'plugins_loaded', 'elex_addr_val_load_plugin_textdomain' );

	// review component
	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once  ABSPATH . 'wp-admin/includes/plugin.php';
	}
	include_once __DIR__ . '/review_and_troubleshoot_notify/review-and-troubleshoot-notify-class.php';
	$data                      = get_plugin_data( __FILE__ );
	$data['name']              = $data['Name'];
	$data['basename']          = plugin_basename( __FILE__ );
	$data['rating_url']        = 'https://elextensions.com/plugin/elex-address-validation-google-address-autocomplete-plugin-for-woocommerce-free-version/#reviews';
	$data['documentation_url'] = 'https://elextensions.com/knowledge-base/set-up-elex-address-validation-google-address-autocomplete-plugin-for-woocommerce/';
	$data['support_url']       = 'https://wordpress.org/support/plugin/address-validation-address-auto-complete/';

	add_filter(
		'elex_address_autocomplete_street_address', 
		function() {
		return 'default';
		}, 
		10
	);

	new \Elex_Review_Components( $data );
}

