<?php
/**
 *
 * File for creating logs.
 *
 * @package Address Validation & Google Address Auto Complete Plugin for WooCommerce (Basic)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Address Validation Log */
class Elex_Address_Validation_Log {
	/** Log Heading  */
	public static function init_log() {
		$content = "<------------------- Address Validation Log File  ------------------->\n";
		return $content;
	}

	/**
	 * Function to write EasyPost and UPS response and request header for address validation in /wp-content/uploads/wc-logs/address_validation_log-****.php.
	 *
	 * @param array  $msg Log message.
	 *
	 * @param string $title Log title.
	 */
	public static function log_update( $msg, $title ) {
		$validation_settings = get_option( 'wf_address_autocomplete_validation_settings' );
		$check               = $validation_settings['wf_address_autocomplete_validation_record_log'];
		if ( 'yes' === $check ) {
			$log      = new WC_Logger();
			$head     = '<------------------- ( ' . $title . " ) ------------------->\n";
			$log_text = $head . print_r( (object) $msg, true );
			$log->add( 'address_validation_log', $log_text );
		}
	}
}
