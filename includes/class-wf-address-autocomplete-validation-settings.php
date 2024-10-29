<?php
/**
 *
 * Settings File.
 *
 * @package Address Validation & Google Address Auto Complete Plugin for WooCommerce (Basic)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

require_once WP_PLUGIN_DIR . '/woocommerce/includes/admin/settings/class-wc-settings-page.php';

/** Class - For settings */
class Elex_Address_Autocomplete_Validation_Settings extends WC_Settings_Page {
	/**
	 * Array of settings stored in database.
	 *
	 * @var array $all_settings Array of settings stored in database.
	 */
	protected $all_settings;

	public $id;

	/** Constructor. */
	public function __construct() {
		$this->all_settings = get_option( 'wf_address_autocomplete_validation_settings' );
		$this->id           = 'elex_address_autocomplete_validation';
	}

	/**
	 * Get an option set in our settings tab.
	 *
	 * @param string $key Key.
	 */
	public function wf_address_autocomplete_validation_get_option( $key ) {
		$fields = $this->wf_address_autocomplete_validation_get_fields();
		/**
		 * Get option value.
		 * 
		 * @since 1.0.0
		 */
		return apply_filters( 'wc_option_' . $key, wf_address_autocomplete_validation_get_option( 'wc_settings_wf_address_autocomplete_validation_' . $key, ( ( isset( $fields[ $key ] ) && isset( $fields[ $key ]['default'] ) ) ? $fields[ $key ]['default'] : '' ) ) );
	}

	/** Setup the WooCommerce settings */
	public function wf_address_autocomplete_validation_setup() {
		// Filters for adding tabs and sections.
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'wf_address_autocomplete_validation_add_settings_tab' ), 70 );
		add_filter( 'woocommerce_sections_elex_address_autocomplete_validation', array( $this, 'output_sections' ) );

		add_filter( 'woocommerce_settings_elex_address_autocomplete_validation', array( $this, 'elex_address_validation_output_section' ) );
		add_action( 'woocommerce_update_options_elex_address_autocomplete_validation', array( $this, 'elex_address_validation_update_section' ) );
		add_action( 'woocommerce_settings_tabs_elex_address_autocomplete_validation', array( $this, 'wf_address_autocomplete_validation_tab_content' ) );
		if ( empty( $this->all_settings['wf_address_autocomplete_validation_enable_address_popup_css_edit'] ) ) {
		$this->all_settings['wf_address_autocomplete_validation_enable_address_popup_css_edit'] = ".xa-container{padding:0.01em 16px}
		.xa-center{text-align:center!important}
		.xa-btn:disabled *{pointer-events:none}
		.xa-btn:disabled:hover{box-shadow:none}
		.xa-container:after{content:'';display:table;clear:both}
		.xa-modal-content{margin:auto;background-color:#fff;position:relative;padding:0;outline:0;width:600px}
		.xa-white{color:#000!important;background-color:#fff!important}
		.xa-red{color:#fff!important;background-color:#f44336!important}
		.xa-btn{border:none;display:inline-block;outline:0;padding:8px 16px;vertical-align:middle;overflow:hidden;text-decoration:none!important;color:white!important;background-color:blue !important;text-align:center;cursor:pointer;white-space:nowrap}
		.xa-btn:hover{box-shadow:0 8px 16px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19)}
		.xa-btn:disabled{cursor:not-allowed;opacity:0.3}
		.xa-btn{box-shadow:none!important;text-shadow:none!important;background-color:inherit;color:inherit;float:left;width:100%}
		.xa-btn{-webkit-transition:background-color .3s,color .15s,box-shadow .3s,opacity 0.3s,filter 0.3s;transition:background-color .3s,color .15s,box-shadow .3s,opacity 0.3s,filter 0.3s}
		.xa-round-large{border-radius:8px!important}.xa-round-xlarge{border-radius:16px!important}
		.xa-border{border:1px solid #ccc!important}
		@media (max-width:600px){.xa-modal-content{margin:0 10px;width:auto!important}.xa-modal{padding-top:30px}}
		@media (max-width:768px){.xa-modal-content{width:500px}.xa-modal{padding-top:50px}}
		@media (min-width:993px){.xa-modal-content{width:900px}}
		.xa-closebtn{-webkit-transition:background-color .3s,color .15s,box-shadow .3s,opacity 0.3s,filter 0.3s;transition:background-color .3s,color .15s,box-shadow .3s,opacity 0.3s,filter 0.3s}
		.xa-closebtn{text-decoration:none;float:right;font-size:24px;font-weight:bold;color:inherit}
		.xa-closebtn:hover,.xa-closebtn:focus{color:#000;text-decoration:none;cursor:pointer}
		";
		}
		update_option( 'wf_address_autocomplete_validation_settings', $this->all_settings );
	}

	/** Add Address Validation settings tab to the settings page.
	 *
	 * @param array $settings_tabs Settings Tabs.
	 */
	public function wf_address_autocomplete_validation_add_settings_tab( $settings_tabs ) {
		$settings_tabs['elex_address_autocomplete_validation'] = __( 'Address Validation & Autocomplete', 'address-validation-address-auto-complete' );
		return $settings_tabs;
	}

	/** To add sections to a tab. */
	public function get_sections() {
		$sections = array(
			''                        => __( 'Address Autocomplete', 'address-validation-address-auto-complete' ),
			'elex-address-validation' => __( 'Address Validation', 'address-validation-address-auto-complete' ),
			'elex-api-credentials'    => __( 'API Credentials', 'address-validation-address-auto-complete' ),
			'elex-customise-labels'   => __( 'Customise', 'address-validation-address-auto-complete' ),
			'elex-go-premium'         => __( 'Go Premium!', 'address-validation-address-auto-complete' ),
		);

		/**
		 * Get sections to be added to settings tab.
		 * 
		 * @since 1.0.0
		 */
		return apply_filters( 'woocommerce_get_sections_elex_address_autocomplete_validation', $sections );
	}


	/** Function to change section when clicked on them. */
	public function elex_address_validation_output_section() {
		global $current_section;
		if ( '' === $current_section ) {
			$settings = $this->elex_google_autocomplete_settings( $current_section );
			WC_Admin_Settings::output_fields( $settings );
		} elseif ( 'elex-address-validation' === $current_section ) {
			$settings = $this->elex_address_validation_settings( $current_section );
			WC_Admin_Settings::output_fields( $settings );
		} elseif ( 'elex-api-credentials' === $current_section ) {
			$settings = $this->elex_api_credentials_settings( $current_section );
			WC_Admin_Settings::output_fields( $settings );
		} elseif ( 'elex-customise-labels' === $current_section ) {
			$settings = $this->elex_customise_labels_settings( $current_section );
			WC_Admin_Settings::output_fields( $settings );
		} elseif ( 'elex-go-premium' === $current_section ) {
			wp_enqueue_style( 'bootstrap', plugins_url( '../assests/css/bootstrap.css', __FILE__ ), false, true );
			include_once 'market.php';
		}
	}

	/** Function to update settings.
	 *
	 * @param string $current_section Current Section.
	 */
	public function elex_address_validation_update_section( $current_section ) {
		global $current_section;
		if ( '' === $current_section ) {
			$options = $this->elex_google_autocomplete_settings( $current_section );
			woocommerce_update_options( $options );
		}
		if ( 'elex-address-validation' === $current_section ) {
			$options = $this->elex_address_validation_settings( $current_section );
			woocommerce_update_options( $options );
		}
		if ( 'elex-api-credentials' === $current_section ) {
			$options = $this->elex_api_credentials_settings( $current_section );
			woocommerce_update_options( $options );
		}
		if ( 'elex-customise-labels' === $current_section ) {
			$options = $this->elex_customise_labels_settings( $current_section );
			woocommerce_update_options( $options );
		}
	}
	/** Autocomplete tab settings. */
	public function elex_google_autocomplete_settings() {
		global $woocommerce;
		$settings = array(
			'section_title'                      => array(
				'name' => '',
				'type' => 'title',
				'desc' => __( '<br>Enable the required fields to activate Address Autocomplete and Validation. Respective API keys are MANDATORY requirements.', 'wf-address-autocomplete-validation' ),
			),

			'address_autocomplete'               => array(
				'title'    => __( 'Address Autocomplete', 'address-validation-address-auto-complete' ),
				'type'     => 'select',
				'css'      => 'padding: 0px;',
				'default'  => '',
				'desc'     => __( 'Choose the service for Address Autocomplete', 'address-validation-address-auto-complete' ),
				'desc_tip' => true,
				'class'    => 'wf_address_autocomplete_restriction',
				'id'       => 'wf_address_autocomplete_validation_settings[wf_address_autocomplete_restriction]',
				'options'  => array(
					''              => __( 'None', 'address-validation-address-auto-complete' ),
					'google'        => __( 'Google', 'address-validation-address-auto-complete' ),
					'addressfinder' => __( 'AddressFinder (Australia) [Premium]', 'address-validation-address-auto-complete' ),
				),
			),


			'set_address_autocomplete_field'     => array(
				'title'    => __( 'Autocomplete Field', 'address-validation-address-auto-complete' ),
				'type'     => 'select',
				'css'      => 'padding: 0px;',
				'default'  => 'default',
				'desc'     => __( 'Select an option to choose where to display the autocomplete field. The default option will be a dedicated autocomplete field for both billing and shipping address sections. By selecting the option Street 1, the Street Address field 1 will be enabled with the autocomplete feature.', 'address-validation-address-auto-complete' ),
				'desc_tip' => true,
				'class'    => 'wf_address_autocomplete_validation_set_address_autocomplete_field_class',
				'options'  => array(
					'default' => __( 'Dedicated Field', 'address-validation-address-auto-complete' ),
					'street1' => __( 'Street 1 [Premium]', 'address-validation-address-auto-complete' ),
				),
			),

			'enable_autocomplete_checkout'       => array(
				'title'   => __( 'Address Autocomplete (Checkout Page)', 'address-validation-address-auto-complete' ),
				'type'    => 'checkbox',
				'default' => 'no',
				'desc'    => __( 'Enable<br><span style="font-style: italic;font-size:13px;">Activates Google Address Autocomplete on the checkout page.</span>', 'address-validation-address-auto-complete' ),
				'id'      => 'wf_address_autocomplete_validation_settings[wf_aac_enable_google_autocomplete_checkout]',
				'class'   => 'wf_aac_enable_google_autocomplete_checkout_class',
			),

			'enable_autocomplete_backend'        => array(
				'title'             => __( 'Address Autocomplete (Backend Orders)', 'address-validation-address-auto-complete' ),
				'type'              => 'checkbox',
				'default'           => 'no',
				'desc'              => __( 'Enable <span style="color:green;">[Premium]</span><br><span style="font-style: italic;font-size:13px;">Activates Google Address Autocomplete on the backend order page.</span>', 'wf-address-autocomplete-validation' ),
				'class'             => 'wf_aac_enable_google_autocomplete_backend_class',
				'custom_attributes' => array( 'disabled' => 'disabled' ),
			),
			'enable_restrict_street_checkout'        => array(
				'title'   => __( 'Restrict Address Line 2', 'wf-address-autocomplete-validation' ),
				'type'    => 'checkbox',
				'default' => 'no',
				'desc'    => __( 'Enable<br><span style="font-style: italic;font-size:13px;">If enabled, the Address Autocomplete feature will not be available for Address Line 2. </br>However, all other fields will be automatically populated based on the address fetched by the API.</span>', 'wf-address-autocomplete-validation' ),
				'id'      => 'wf_address_autocomplete_validation_settings[wf_aac_enable_restrict_street_checkout]',
				'class'   => 'wf_aac_enable_restrict_street_checkout_class',
			),

			'enable_country_availability'        => array(
				'title'    => __( 'Address Autocomplete Available to', 'address-validation-address-auto-complete' ),
				'type'     => 'select',
				'css'      => 'padding: 0px;',
				'default'  => 'all',
				'desc'     => __( 'Select the countries', 'address-validation-address-auto-complete' ),
				'desc_tip' => true,
				'class'    => 'availabilityy wf_address_autocomplete_validation_availability ',
				'id'       => 'wf_address_autocomplete_validation_settings[wf_address_autocomplete_validation_availability]',
				'options'  => array(
					'all'      => __( 'All Countries', 'address-validation-address-auto-complete' ),
					'specific' => __( 'Specific Countries', 'address-validation-address-auto-complete' ),
				),
			),

			'countries_availabilty'              => array(
				'title'    => __( 'Specific Countries', 'address-validation-address-auto-complete' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select wf_address_autocomplete_validation_restrict_countries',
				'desc_tip' => true,
				'desc'     => __( 'Select the countries to show the suggestion (Maximum five countries)', 'address-validation-address-auto-complete' ),
				'id'       => 'wf_address_autocomplete_validation_settings[wf_address_autocomplete_validation_countries_availabilty]',
				'css'      => 'width: 300px;',
				'default'  => '',
				'options'  => $woocommerce->countries->get_allowed_countries(),
			),

			'google_api_key'                     => array(
				'title'             => __( 'Google API Key', 'address-validation-address-auto-complete' ),
				'type'              => 'password',
				'desc'              => __( "Enter the <a href='https://developers.google.com/places/web-service/autocomplete'>Google API</a> Key.<br />By default, google address autocomplete api usage is limited.<br />To increase the usage limit, check this <a href='https://developers.google.com/places/web-service/usage' target='_blank'>article from google.</a>", 'address-validation-address-auto-complete' ),
				'custom_attributes' => array(
					'autocomplete' => 'off',
				),
				'id'                => 'wf_address_autocomplete_validation_settings[wf_address_autocomplete_validation_google_api_key]',
				'class'             => 'wf_address_autocomplete_validation_google_api_key_class',
			),

			'Enable_disable_autocomplete_fields' => array(
				'title'   => __( 'Editable Address Fields', 'address-validation-address-auto-complete' ),
				'type'    => 'checkbox',
				'default' => 'yes',
				'desc'    => __( 'Enable<br><span style="font-style: italic;font-size:13px;">To make the checkout address fields editable when Address Autocomplete is already enabled.</span>', 'address-validation-address-auto-complete' ),
				'id'      => 'wf_address_autocomplete_validation_settings[wf_address_autocomplete_validation_enable_disable_autocomplete_fields]',
				'class'   => 'wf_address_autocomplete_validation_enable_disable_autocomplete_fields_class',
			),

			'section_end'                        => array(
				'type' => 'sectionend',
			),
		);

		/**
		 * Add settings fields.
		 * 
		 * @since 1.0.0
		 */
		return apply_filters( 'elex_address_autocomplete_validation_autocomplete_settings', $settings );
	}

	/** Address validation settings. */
	public function elex_address_validation_settings() {
		global $woocommerce;

		$settings = array(
			'section_title'                 => array(
				'name' => '',
				'type' => 'title',
			),

			'address_validation'            => array(
				'title'    => __( 'Address Validation', 'address-validation-address-auto-complete' ),
				'type'     => 'select',
				'css'      => 'padding: 0px;',
				'default'  => 'none',
				'desc'     => __( 'Choose the service for Address Validation', 'address-validation-address-auto-complete' ),
				'desc_tip' => true,
				'class'    => 'wf_address_validation_restriction',
				'id'       => 'wf_address_autocomplete_validation_settings[xa_address_validation]',
				'options'  => array(
					'none'            => __( 'None', 'address-validation-address-auto-complete' ),
					'easypost'        => __( 'EasyPost', 'address-validation-address-auto-complete' ),
					'ups'             => __( 'UPS [Premium]', 'address-validation-address-auto-complete' ),
					'usps'            => __( 'USPS [Premium]', 'address-validation-address-auto-complete' ),
					'addressfinder'   => __( 'AddressFinder API [Premium]', 'address-validation-address-auto-complete' ),
					'countryspecific' => __( 'Country Specific [Premium]', 'address-validation-address-auto-complete' ),
				),
			),

			'validated_address_only'        => array(
				'title'   => __( 'Enforce Address Validation', 'address-validation-address-auto-complete' ),
				'type'    => 'checkbox',
				'default' => 'no',
				'id'      => 'wf_address_autocomplete_validation_settings[wf_address_autocomplete_validation_validated_address_only]',
				'desc'    => __( 'Enable<br><span style="font-style: italic;font-size:13px;">By enabling this, the user will not be able to proceed with checkout if address validation fails.<br />This option will be ignored if EasyPost/UPS/USPS/AddressFinder API server is down.</span>', 'address-validation-address-auto-complete' ),
				'class'   => 'wf_address_autocomplete_validation_validated_address_only',
			),

			'record_log'                    => array(
				'title'             => __( 'Debug Log', 'address-validation-address-auto-complete' ),
				'type'              => 'checkbox',
				'default'           => 'no',
				'desc'              => __( 'Enable<br><span style="font-style: italic;font-size:13px;">Find request and response logs here (wp-content\uploads\wc-logs)</span>', 'address-validation-address-auto-complete' ),
				'custom_attributes' => array(
					'autocomplete' => 'off',
				),
				'id'                => 'wf_address_autocomplete_validation_settings[wf_address_autocomplete_validation_record_log]',
				'class'             => 'wf_address_autocomplete_validation_record_log_class',
			),

			'confirm_validation'            => array(
				'title'   => __( 'Confirm Before Validation', 'address-validation-address-auto-complete' ),
				'type'    => 'checkbox',
				'default' => 'yes',
				'desc'    => __( 'Enable<br><span style="font-style: italic;font-size:13px;"> Enable it if you want customers to confirm any address before validation.</span>', 'address-validation-address-auto-complete' ),
				'id'      => 'wf_address_autocomplete_validation_settings[wf_address_autocomplete_validation_confirm_validation]',
				'class'   => 'wf_address_autocomplete_validation_confirm_validation_class',
			),

			'address_confirm'               => array(
				'title'    => __( 'Confirm Using ', 'address-validation-address-auto-complete' ),
				'type'     => 'select',
				'css'      => 'padding: 0px;',
				'default'  => 'msg',
				'desc'     => __( 'Choose either of the options to get a confirmation from customer, if they want the Entered Address or the Suggested Address on the checkout page.', 'address-validation-address-auto-complete' ),
				'desc_tip' => true,
				'class'    => 'wf_address_autocomplete_validation_settings_popup_msg',
				'id'       => 'wf_address_autocomplete_validation_settings[xa_address_validation_confirm]',
				'options'  => array(
					'popup' => __( 'A Popup Window', 'address-validation-address-auto-complete' ),
					'msg'   => __( 'Checkout Page Message', 'address-validation-address-auto-complete' ),
				),
			),

			'enable_address_popup_css_edit' => array(
				'title'    => __( 'Confirmation Window CSS', 'address-validation-address-auto-complete' ),
				'type'     => 'textarea',
				'css'      => 'width:700px; height:400px',
				'id'       => 'wf_address_autocomplete_validation_settings[wf_address_autocomplete_validation_enable_address_popup_css_edit]',
				'class'    => 'wf_address_autocomplete_validation_enable_address_popup_css_edit_class',
				'desc'     => __( 'Modify the CSS to change the design of the address confirmation window. Leave it without modification to keep the default design. Clear the field to restore the default.', 'address-validation-address-auto-complete' ),
				'desc_tip' => true,
			),

			'section_end'                   => array(
				'type' => 'sectionend',
			),
		);

		/**
		 * Add settings fields.
		 * 
		 * @since 1.0.0
		 */
		return apply_filters( 'elex_address_autocomplete_validation_validation_settings', $settings );
	}
	/** API Credential settings. */
	public function elex_api_credentials_settings() {
		$settings = array(
			'easypost_header'           => array(
				'title' => __( 'EasyPost', 'address-validation-address-auto-complete' ),
				'type'  => 'title',
				'desc'  => __( 'This address validation method is recommended for most countries. Check the <a href="https://www.easypost.com/docs/address-verification-by-country" target="_blank">list of countries supported</a>.', 'address-validation-address-auto-complete' ),
				'id'    => 'easypost_header',
			),
			'easypost_api_key'          => array(
				'title'             => __( 'EasyPost API Key', 'address-validation-address-auto-complete' ),
				'type'              => 'password',
				'desc'              => __( "Enter the <a href='https://www.easypost.com/pricing.html'>EasyPost API</a> Key.", 'address-validation-address-auto-complete' ),
				'custom_attributes' => array(
					'autocomplete' => 'off',
				),
				'id'                => 'wf_address_autocomplete_validation_settings[wf_address_autocomplete_validation_easypost_api_key]',
				'class'             => 'wf_address_autocomplete_validation_easypost_api_key_class',
			),

			'easypost_section_end'      => array(
				'type' => 'sectionend',
				'id'   => 'easypost_header',
			),

			'ups_header'                => array(
				'title' => '', // the title is given in the disc
				'type'  => 'title',
				'desc'  => __(
					'<h2>UPS<span style="color: green"><sup>[Premium]</sup></span></h2>
								This address validation method is recommended for United States and Puerto Rico.', 
					'address-validation-address-auto-complete'
				),
				'id'    => 'style_cs',

			),

			'ups_username'              => array(
				'title'             => __( 'UPS Username', 'address-validation-address-auto-complete' ),
				'type'              => 'text',
				'desc'              => __( 'Obtained from UPS after getting an account', 'address-validation-address-auto-complete' ),
				'desc_tip'          => true,
				'custom_attributes' => array( 'disabled' => 'disabled' ),
			),

			'ups_password'              => array(
				'title'             => __( 'UPS Password', 'address-validation-address-auto-complete' ),
				'type'              => 'password',
				'desc'              => __( 'Obtained from UPS after getting an account', 'address-validation-address-auto-complete' ),
				'desc_tip'          => true,
				'custom_attributes' => array( 'disabled' => 'disabled' ),
			),

			'ups_license_key'           => array(
				'title'             => __( 'UPS License Key', 'address-validation-address-auto-complete' ),
				'type'              => 'password',
				'desc'              => __( 'Obtained from UPS after getting an account', 'address-validation-address-auto-complete' ),
				'desc_tip'          => true,
				'custom_attributes' => array( 'disabled' => 'disabled' ),
			),

			'ups_section_end'           => array(
				'type' => 'sectionend',
				'id'   => 'ups_header',
			),

			'usps_header'               => array(
				'title' => '', // the title is given in the disc
				'type'  => 'title',
				'desc'  => __(
					'<h2>USPS<span style="color: green"><sup>[Premium]</sup></span></h2>
							This address validation method is recommended for United States.', 
					'address-validation-address-auto-complete'
				),
				'id'    => 'style_cs',
			),

			'usps_userid'               => array(
				'title'             => __( 'USPS User Id', 'address-validation-address-auto-complete' ),
				'type'              => 'text',
				'desc'              => __( 'Obtained from USPS after getting an account', 'address-validation-address-auto-complete' ),
				'desc_tip'          => true,
				'custom_attributes' => array( 'disabled' => 'disabled' ),
			),

			'usps_section_end'          => array(
				'type' => 'sectionend',
				'id'   => 'usps_header',
			),

			'addressfinder_header'      => array(
				'title' => '', // the title is given in the disc
				'type'  => 'title',
				'desc'  => __(
					'<h2>AddressFinder API<span style="color: green"><sup>[Premium]</sup></span></h2>
							This address validation method is recommended for Australia and New Zealand.', 
					'address-validation-address-auto-complete'
				),
				'id'    => 'style_cs',
				'class' => 'text-success',

			),
			'addressfinder_license_key' => array(
				'title'             => __( 'AddressFinder License Key', 'address-validation-address-auto-complete' ),
				'type'              => 'password',
				'desc'              => __( "Enter the <a href='https://portal.addressfinder.net/signup/au/au_free5'>AddressFinder License</a> Key.", 'address-validation-address-auto-complete' ),
				'custom_attributes' => array( 'disabled' => 'disabled' ),
			),
			'addressfinder_business_account'                 => array(
				'title'   => __( 'AddressFinder Business Account', 'address-validation-address-auto-complete' ),
				'type'    => 'checkbox',
				'custom_attributes' => array( 'disabled' => 'disabled' ),
				'desc'    => __( 'To enable the business account, the user needs premium version of address validation', 'address-validation-address-auto-complete' ),
				'desc_tip'          => true,
				'id'      => 'addressfinder_business_account',
			),
			'addressfinder_section_end' => array(
				'type' => 'sectionend',
				'id'   => 'addressfinder_header',
			),
		);

		/**
		 * Add settings fields.
		 * 
		 * @since 1.0.0
		 */
		return apply_filters( 'elex_address_autocomplete_validation_api_credentials_settings', $settings );
	}

	/** Customise labels settings. */
	public function elex_customise_labels_settings() {
		$settings = array(
			'customise_address_validation_header'       => array(
				'title' => '',
				'type'  => 'title',
				'desc'  => __(
					'<h2>Address Validation <span style="color: green"><sup>[Premium]</sup></span></h2>
					', 
					'address-validation-address-auto-complete'
				),
				'id'    => 'customise_address_validation_header',
			),

			'original_address_label'                    => array(
				'title'             => __( 'Original Address Label', 'address-validation-address-auto-complete' ),
				'type'              => 'text',
				'desc'              => __( 'Sets the label for the original address in the popup window/checkout page message. Leave it empty if you want to display the default label.', 'address-validation-address-auto-complete' ),
				'desc_tip'          => true,
				'placeholder'       => __( 'Use Original Address', 'address-validation-address-auto-complete' ),
				'custom_attributes' => array( 'disabled' => 'disabled' ),
			),

			'validated_address_label'                   => array(
				'title'             => __( 'Validated Address Label', 'address-validation-address-auto-complete' ),
				'type'              => 'text',
				'desc'              => __( 'Sets the label for the validated address in the popup window. Leave it empty if you want to display the default label.', 'address-validation-address-auto-complete' ),
				'desc_tip'          => true,
				'placeholder'       => __( 'Validation Successful', 'address-validation-address-auto-complete' ),
				'custom_attributes' => array( 'disabled' => 'disabled' ),
			),

			'address_suggestion_label'                  => array(
				'title'             => __( 'Address Suggestion Label', 'address-validation-address-auto-complete' ),
				'type'              => 'text',
				'desc'              => __( 'Sets the label for the suggested address in the checkout page message. Leave it empty if you want to display the default label.', 'address-validation-address-auto-complete' ),
				'desc_tip'          => true,
				'placeholder'       => __( 'Suggestion:', 'address-validation-address-auto-complete' ),
				'custom_attributes' => array( 'disabled' => 'disabled' ),
			),

			'place_order_original_button_label'         => array(
				'title'             => __( 'Place Order With Original Address Button Label', 'address-validation-address-auto-complete' ),
				'type'              => 'text',
				'desc'              => __( 'Sets the label for Place Order with Original Address button in the popup window. Leave it empty if you want to display the default label.', 'address-validation-address-auto-complete' ),
				'desc_tip'          => true,
				'placeholder'       => __( 'Place Order With Original Address', 'address-validation-address-auto-complete' ),
				'custom_attributes' => array( 'disabled' => 'disabled' ),
			),

			'place_order_suggested_button_label'        => array(
				'title'             => __( 'Place Order With Suggested Address Button Label', 'address-validation-address-auto-complete' ),
				'type'              => 'text',
				'desc'              => __( 'Sets the label for Place Order with Suggested Address button in the popup window. Leave it empty if you want to display the default label.', 'address-validation-address-auto-complete' ),
				'desc_tip'          => true,
				'placeholder'       => __( 'Place Order With Suggested Address', 'address-validation-address-auto-complete' ),
				'custom_attributes' => array( 'disabled' => 'disabled' ),
			),

			'update_proceed_address_text_label'         => array(
				'title'             => __( 'Update or Proceed With Your Original Address Text Label', 'address-validation-address-auto-complete' ),
				'type'              => 'text',
				'desc'              => __( 'Sets the label for Update or Proceed With Your Original Address Text in the popup window when validation fails. Leave it empty if you want to display the default label.', 'address-validation-address-auto-complete' ),
				'desc_tip'          => true,
				'placeholder'       => __( 'Update or Proceed with your Original Address', 'address-validation-address-auto-complete' ),
				'custom_attributes' => array( 'disabled' => 'disabled' ),
			),

			'custom_checkout_button_label'              => array(
				'title'             => __( 'Customize Button Text In The Checkout Page Message', 'address-validation-address-auto-complete' ),
				'type'              => 'text',
				'desc'              => __( 'Sets the label for the Place Order button in the checkout page message. Leave it empty if you want to display the default label.', 'address-validation-address-auto-complete' ),
				'desc_tip'          => true,
				'placeholder'       => __( 'Place Order', 'address-validation-address-auto-complete' ),
				'custom_attributes' => array( 'disabled' => 'disabled' ),
			),

			'custom_validaton_failed_message'           => array(
				'title'             => __( 'Address Validation Failed Custom Message', 'address-validation-address-auto-complete' ),
				'type'              => 'text',
				'desc'              => __( 'The error message will be displayed when validation fails at the checkout page. Leave it empty if you want to display the default validation failure message.', 'address-validation-address-auto-complete' ),
				'desc_tip'          => true,
				'placeholder'       => __( 'Address Validation Failed', 'address-validation-address-auto-complete' ),
				'custom_attributes' => array( 'disabled' => 'disabled' ),
			),

			'customise_address_validation_section_end'  => array(
				'type' => 'sectionend',
				'id'   => 'customise_address_validation_header',
			),

			'customise_google_autocomplete_header'      => array(
				'title' => '',
				'type'  => 'title',
				'desc'  => __(
					'<h2>Google Autocomplete <span style="color: green"><sup>[Premium]</sup></span></h2>
					', 
					'address-validation-address-auto-complete'
				),
				'id'    => 'customise_google_autocomplete_header',
			),

			'label_name'                                => array(
				'title'             => __( 'Label Name', 'address-validation-address-auto-complete' ),
				'type'              => 'text',
				'placeholder'       => __( 'Address Autocomplete', 'address-validation-address-auto-complete' ),
				'desc'              => __( 'This controls the Label text in the checkout page', 'address-validation-address-auto-complete' ),
				'desc_tip'          => true,
				'placeholder'       => __( 'Address Autocomplete', 'address-validation-address-auto-complete' ),
				'custom_attributes' => array( 'disabled' => 'disabled' ),
			),

			'customise_google_autocomplete_section_end' => array(
				'type' => 'sectionend',
				'id'   => 'customise_google_autocomplete_header',
			),
		);

		/**
		 * Add settings fields.
		 * 
		 * @since 1.0.0
		 */
		return apply_filters( 'elex_address_autocomplete_validation_customise_labels_settings', $settings );
	}

	/** Output the tab content. */
	public function wf_address_autocomplete_validation_tab_content() {
		wc_enqueue_js(
			"
			jQuery(document).ready(function() {
				jQuery('option[value=addressfinder]').attr('disabled','disabled');
				jQuery('option[value=street1]').attr('disabled','disabled');
			});

			// Styles to be applied for Related products link in settings page.
			jQuery('ul.subsubsub li a:contains(Go Premium!)').css({'color':'red', 'fontWeight':'bold'});

            jQuery('.wf_address_autocomplete_restriction').on('change',function() {
				if(jQuery('.wf_address_autocomplete_restriction').val() == 'google'){
					// Show Google fields.
                    jQuery('.wf_address_autocomplete_validation_availability').on('change',function(){
                        var value= jQuery('.wf_address_autocomplete_validation_availability').val();
                        if(value=='specific')
                        {
                            jQuery('.wf_address_autocomplete_validation_restrict_countries').closest('tr').show();
                        }   
                        else
                        {
                            jQuery('.wf_address_autocomplete_validation_restrict_countries').closest('tr').hide();
                        }
					});
					jQuery('.wf_aac_enable_google_autocomplete_checkout_class').closest('tr').show();
					jQuery('.wf_aac_enable_google_autocomplete_backend_class').closest('tr').show();
                    jQuery('.wf_address_autocomplete_validation_availability').closest('tr').show();
                    jQuery('.wf_address_autocomplete_validation_availability').trigger('change'); 
                    jQuery('.wf_address_autocomplete_validation_google_api_key_class').closest('tr').show();
					jQuery('.wf_aac_enable_restrict_street_checkout_class').closest('tr').show();


					// Show general fields.
					jQuery('.wf_address_autocomplete_validation_enable_disable_autocomplete_fields_class').closest('tr').show();
					jQuery('.wf_address_autocomplete_validation_set_address_autocomplete_field_class').closest('tr').show();
					
				} else if(jQuery('.wf_address_autocomplete_restriction').val() == 'addressfinder'){
					// Hide Google fields.
					jQuery('.wf_aac_enable_google_autocomplete_checkout_class').closest('tr').hide();
					jQuery('.wf_aac_enable_google_autocomplete_backend_class').closest('tr').hide();
                    jQuery('.wf_address_autocomplete_validation_google_api_key_class').closest('tr').hide();
                    jQuery('.wf_address_autocomplete_validation_restrict_countries').closest('tr').hide();
                    jQuery('.wf_address_autocomplete_validation_availability').closest('tr').hide();
					jQuery('.wf_aac_enable_restrict_street_checkout_class').closest('tr').hide();


					// Show General fields.
					jQuery('.wf_address_autocomplete_validation_enable_disable_autocomplete_fields_class').closest('tr').show();
					jQuery('.wf_address_autocomplete_validation_set_address_autocomplete_field_class').closest('tr').show();
				} else{
					// Google fields.
					jQuery('.wf_aac_enable_google_autocomplete_checkout_class').closest('tr').hide();
					jQuery('.wf_aac_enable_google_autocomplete_backend_class').closest('tr').hide();
                    jQuery('.wf_address_autocomplete_validation_google_api_key_class').closest('tr').hide();
                    jQuery('.wf_address_autocomplete_validation_restrict_countries').closest('tr').hide();
                    jQuery('.wf_address_autocomplete_validation_availability').closest('tr').hide();
					jQuery('.wf_aac_enable_restrict_street_checkout_class').closest('tr').hide();


					// General fields.
					jQuery('.wf_address_autocomplete_validation_enable_disable_autocomplete_fields_class').closest('tr').hide();
					jQuery('.wf_address_autocomplete_validation_set_address_autocomplete_field_class').closest('tr').hide();
				}
            }).change();  

                    
            jQuery('.wf_address_autocomplete_validation_confirm_validation_class').on('change',function() {
            if(jQuery('.wf_address_autocomplete_validation_confirm_validation_class').is(':checked')) { 
                jQuery('.wf_address_autocomplete_validation_settings_popup_msg').closest('tr').show();
                if(jQuery('.wf_address_autocomplete_validation_settings_popup_msg').val() == 'popup') {
                    jQuery('.wf_address_autocomplete_validation_enable_address_popup_css_edit_class').closest('tr').show();
                }
                }else {
                    jQuery('.wf_address_autocomplete_validation_settings_popup_msg').closest('tr').hide();
                    jQuery('.wf_address_autocomplete_validation_enable_address_popup_css_edit_class').closest('tr').hide();
                }
            }).change();
            

            jQuery(document).ready(function() {
				jQuery('.wf_address_autocomplete_validation_settings_popup_msg').change(function(){
					if(jQuery('.wf_address_autocomplete_validation_settings_popup_msg').val() == 'popup') {
						jQuery('.wf_address_autocomplete_validation_enable_address_popup_css_edit_class').closest('tr').show();
					}else{
						jQuery('.wf_address_autocomplete_validation_enable_address_popup_css_edit_class').closest('tr').hide();
					}
				}).change();     
			});

            jQuery(document).ready(function() {
				jQuery('option[value=ups]').attr('disabled','disabled');
				jQuery('option[value=usps]').attr('disabled','disabled');
				jQuery('option[value=addressfinder]').attr('disabled','disabled');
				jQuery('option[value=countryspecific]').attr('disabled','disabled');
                jQuery('.wf_address_validation_restriction').change(function(){
                    if(jQuery('.wf_address_validation_restriction').val() == 'none'){
                        jQuery('.wf_address_autocomplete_validation_validated_address_only').closest('tr').hide();
                        jQuery('.wf_address_autocomplete_validation_record_log_class').closest('tr').hide();
                        jQuery('.wf_address_autocomplete_validation_confirm_validation_class').closest('tr').hide();
                        jQuery('.wf_address_autocomplete_validation_enable_address_popup_css_edit_class').closest('tr').hide();
                        jQuery('.wf_address_autocomplete_validation_settings_popup_msg').closest('tr').hide();
                    }

                    if(jQuery('.wf_address_validation_restriction').val() == 'easypost'){
                        jQuery('.wf_address_autocomplete_validation_validated_address_only').closest('tr').show();
                        jQuery('.wf_address_autocomplete_validation_record_log_class').closest('tr').show();
                        jQuery('.wf_address_autocomplete_validation_confirm_validation_class').closest('tr').show();
                        if(jQuery('.wf_address_autocomplete_validation_confirm_validation_class').is(':checked')) {
                            jQuery('.wf_address_autocomplete_validation_settings_popup_msg').closest('tr').show();
							if(jQuery('.wf_address_autocomplete_validation_settings_popup_msg').val() == 'popup') {
								jQuery('.wf_address_autocomplete_validation_enable_address_popup_css_edit_class').closest('tr').show();
							}else{
								jQuery('.wf_address_autocomplete_validation_enable_address_popup_css_edit_class').closest('tr').hide();
							}
                        }
                        else{
                            jQuery('.wf_address_autocomplete_validation_settings_popup_msg').closest('tr').hide();
                            jQuery('.wf_address_autocomplete_validation_enable_address_popup_css_edit_class').closest('tr').hide();
                        }
                    }
                }).change();
            });
		"
		);
	}

	/** License activation settings */
	public function generate_activate_box_html() {
		$plugin_name = 'addrvalid';
		include 'wf_api_manager/html/html-wf-activation-window.php';
	}

}
