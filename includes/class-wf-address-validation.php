<?php
/**
 *
 * Easypost Address Validation API.
 *
 * @package Address Validation & Google Address Auto Complete Plugin for WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Class - For validation of address fields with Easypost/UPS/USPS/AddressFinder API.
if ( ! class_exists( 'Elex_Address_Validation' ) ) {

	/** Class - For validation of address fields */
	class Elex_Address_Validation {

		/**
		 * Array of settings stored in database.
		 *
		 * @var array $all_settings Array of settings stored in database.
		 */
		public $all_settings;

		public $enable_address_validation;

		/** Constructor */
		public function __construct() {
			$this->all_settings              = get_option( 'wf_address_autocomplete_validation_settings' );
			$this->enable_address_validation = isset( $this->all_settings['xa_address_validation'] ) ? $this->all_settings['xa_address_validation'] : 'none';
			add_action( 'wp_footer', array( $this, 'wf_address_validation_scripts' ) );
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'wf_order_note' ) );
			/**
			 * Exclude validation.
			 * 
			 * @since 1.0.0
			 */
			apply_filters( 'xa_exclude_validation', false );
		}

		/** Order Note.
		 *
		 * @param number $order_id Order ID.
		 */
		public function wf_order_note( $order_id ) {
			$validated = get_option( 'addr_val' );
			if ( $validated ) {
				$order = new WC_order( $order_id );
				$order->add_order_note( 'This order address is Validated' );
			}
			update_option( 'addr_val', false );
			delete_option( 'addr_val' );
		}

		/** Order note. */
		public function wf_order_note_update() {
			update_option( 'addr_val', true );
		}

		/** Function. */
		public function wf_address_validation() {
			// Create address.
			if ( ! ( isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ) ) ) ) { 
				return false;
			}
			$posted_address = isset( $_POST ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST ) ) : array( '' );
			$address_params = array(
				'verify'  => array( 'delivery' ),
				'street1' => $posted_address['street1_post'],
				'street2' => $posted_address['street2_post'],
				'city'    => $posted_address['city_post'],
				'state'   => $posted_address['state_post'],
				'zip'     => $posted_address['zip_post'],
				'country' => $posted_address['country_post'],
			);
			include_once 'api/easypost-api.php';
		}

		/** Scripts. */
		public function wf_address_validation_scripts() {
			if ( is_checkout() && ! is_order_received_page() ) {
				wp_enqueue_script( 'wf-address-validate-country-specific-script', plugins_url( '../assests/js/address_validate.js', __FILE__ ), array( 'jquery', 'wp-i18n' ), '1.4.6', true );
				wp_set_script_translations( 'wf-address-validate-country-specific-script', 'address-validation-address-auto-complete' );
				wp_enqueue_style( 'wf-modal-style-manadatory', plugins_url( '../assests/css/address-verification-popup-manadatory.css', __FILE__ ), false, true );
				wp_enqueue_style( 'wf-modal-style-manadatory1', plugins_url( '../assests/css/address-verification-popup-current.css', __FILE__ ), false, true );

				$italian_provinces_standard_to_wc_code = array(
					'AGRIGENTO' => 'AG',
					'ALESSANDRIA' => 'AL',
					'ANCONA' => 'AN',
					'AOSTA' => 'AO',
					'AREZZO' => 'AR',
					'ASCOLI PICENO' => 'AP',
					'ASTI' => 'AT',
					'AVELLINO' => 'AV',
					'BARI' => 'BA',
					'BARLETTA-ANDRIA-TRANI' => 'BT',
					'BELLUNO' => 'BL',
					'BENEVENTO' => 'BN',
					'BERGAMO' => 'BG',
					'BIELLA' => 'BI',
					'BOLOGNA' => 'BO',
					'BOLZANO' => 'BZ',
					'BRESCIA' => 'BS',
					'BRINDISI' => 'BR',
					'CAGLIARI' => 'CA',
					'CALTANISSETTA' => 'CL',
					'CAMPOBASSO' => 'CB',
					'CASERTA' => 'CE',
					'CATANIA' => 'CT',
					'CATANZARO' => 'CZ',
					'CHIETI' => 'CH',
					'COMO' => 'CO',
					'COSENZA' => 'CS',
					'CREMONA' => 'CR',
					'CROTONE' => 'KR',
					'CUNEO' => 'CN',
					'ENNA' => 'EN',
					'FERMO' => 'FM',
					'FERRARA' => 'FE',
					'FIRENZE' => 'FI',
					'FOGGIA' => 'FG',
					'FORLÌ-CESENA' => 'FC',
					'FROSINONE' => 'FR',
					'GENOVA' => 'GE',
					'GORIZIA' => 'GO',
					'GROSSETO' => 'GR',
					'IMPERIA' => 'IM',
					'ISERNIA' => 'IS',
					'LA SPEZIA' => 'SP',
					"L'AQUILA" => 'AQ',
					'LATINA' => 'LT',
					'LECCE' => 'LE',
					'LECCO' => 'LC',
					'LIVORNO' => 'LI',
					'LODI' => 'LO',
					'LUCCA' => 'LU',
					'MACERATA' => 'MC',
					'MANTOVA' => 'MN',
					'MASSA-CARRARA' => 'MS',
					'MATERA' => 'MT',
					'MESSINA' => 'ME',
					'MILAN' => 'MI',
					'MODENA' => 'MO',
					'MONZA E DELLA BRIANZA' => 'MB',
					'NAPOLI' => 'NA',
					'NOVARA' => 'NO',
					'NUORO' => 'NU',
					'ORISTANO' => 'OR',
					'PADOVA' => 'PD',
					'PALERMO' => 'PA',
					'PARMA' => 'PR',
					'PAVIA' => 'PV',
					'PERUGIA' => 'PG',
					'PESARO E URBINO' => 'PU',
					'PESCARA' => 'PE',
					'PIACENZA' => 'PC',
					'PISA' => 'PI',
					'PISTOIA' => 'PT',
					'PORDENONE' => 'PN',
					'POTENZA' => 'PZ',
					'PRATO' => 'PO',
					'RAGUSA' => 'RG',
					'RAVENNA' => 'RA',
					'REGGIO CALABRIA' => 'RC',
					'REGGIO EMILIA' => 'RE',
					'RIETI' => 'RI',
					'RIMINI' => 'RN',
					'ROMA' => 'RM',
					'ROVIGO' => 'RO',
					'SALERNO' => 'SA',
					'SASSARI' => 'SS',
					'SAVONA' => 'SV',
					'SIENA' => 'SI',
					'SIRACUSA' => 'SR',
					'SONDRIO' => 'SO',
					'SUD SARDEGNA' => 'SU',
					'TARANTO' => 'TA',
					'TERAMO' => 'TE',
					'TERNI' => 'TR',
					'TORINO' => 'TO',
					'TRAPANI' => 'TP',
					'TRENTO' => 'TN',
					'TREVISO' => 'TV',
					'TRIESTE' => 'TS',
					'UDINE' => 'UD',
					'VITERBO' => 'VT',
					'VARESE' => 'VA',
					'VENEZIA' => 'VE',
					'VERBANO-CUSIO-OSSOLA' => 'VB',
					'VERCELLI' => 'VC',
					'VERONA' => 'VR',
					'VIBO VALENTIA' => 'VV',
					'VICENZA' => 'VI',
				);
				wp_localize_script(
					'wf-address-validate-country-specific-script',
					'elex_setting_object',
					array(
						'italian_provience_standard' => $italian_provinces_standard_to_wc_code,
					) 
				);
				$html = '';
				if ( ! empty( $this->all_settings['wf_address_autocomplete_validation_enable_address_popup_css_edit'] ) ) {
					$popup_css = $this->all_settings['wf_address_autocomplete_validation_enable_address_popup_css_edit'];
					?>
					<style> <?php echo esc_html( $popup_css ); ?></style>
					<?php
				} else {
					wp_enqueue_style( 'wf-modal-style', plugins_url( '../assests/css/address-verification-popup-default.css', __FILE__ ), false, true );
				}

					echo "<div id='xa_addr_correction' class='' style='display: none;'>
							<div id='xa_orig_addr'>
								<div id='xa_addr_radio' class='xa-addr-radio'></div>    
								<div style='display: none;' id='xa_orig_placeholder'></div>                     
							</div>      
						</div>
					";
				
				echo '<div id="xa_error_placeholder"></div>';

				// add the ajax url var.
				echo '<script type="text/javascript">
					var ajaxurl = "' . esc_html( admin_url( 'admin-ajax.php' ) ) . '";
					</script>';


				// for address suggestion popup.
				$address_validation_popup = $this->all_settings['xa_address_validation_confirm'];
				$xa_address_popup         = array( // To avoid warning in console log.
					'confirm_validation'         => isset( $this->all_settings['wf_address_autocomplete_validation_confirm_validation'] ) ? $this->all_settings['wf_address_autocomplete_validation_confirm_validation'] : 'yes',
					'enforce_address_validation' => isset( $this->all_settings['wf_address_autocomplete_validation_validated_address_only'] ) ? $this->all_settings['wf_address_autocomplete_validation_validated_address_only'] : 'no',
				);
				if ( null !== $address_validation_popup && 'popup' === $address_validation_popup ) {   // ..checking for address validation in background or foreground.
					$xa_address_popup['enable'] = 'yes';
					wp_localize_script( 'wf-address-validate-country-specific-script', 'wf_address_autocomplete_validation_enable_address_popup_obj', $xa_address_popup );
					// Process Checkout on validated address details by giving option to customer to proceed with validated address or original address.
					include 'html-wf-device-address-validation.php';
				} else {         // ..Process Checkout on validated address details without asking customers, no popup.
						$xa_address_popup['enable'] = 'no';
					wp_localize_script( 'wf-address-validate-country-specific-script', 'wf_address_autocomplete_validation_enable_address_popup_obj', $xa_address_popup );
				}
			}
		}
	}
}

$obj = new Elex_Address_Validation();
add_action( 'wp_ajax_wf_address_validation', array( $obj, 'wf_address_validation' ) );
add_action( 'wp_ajax_nopriv_wf_address_validation', array( $obj, 'wf_address_validation' ) );
add_action( 'wp_ajax_wf_order_note', array( $obj, 'wf_order_note_update' ) );
add_action( 'wp_ajax_nopriv_wf_order_note', array( $obj, 'wf_order_note_update' ) );
