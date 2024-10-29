<?php
/**
 *
 * Easypost Address Validation API.
 *
 * @package Address Validation & Google Address Auto Complete Plugin for WooCommerce
 */

try {
	$record                    = isset( $this->all_settings['wf_address_autocomplete_validation_record_log'] ) ? $this->all_settings['wf_address_autocomplete_validation_record_log'] : '';
	$validation_failed_message = 'Unable to verify address :';
	if ( 'yes' === $record ) {
		Elex_Address_Validation_Log::log_update( $address_params, 'EasyPost Request' );
	}
	$easypost_api_key = $this->all_settings['wf_address_autocomplete_validation_easypost_api_key'];
	// API Request.
	$params   = array(
		'method'      => 'POST',
		'timeout'     => 45,
		'httpversion' => '1.0',
		'blocking'    => true,
		'headers'     => array(
			'Content-Type'  => 'application/x-www-form-urlencoded',
			'Authorization' => 'Basic ' . base64_encode( "{$easypost_api_key}" ),
		),
	);
	$response = wp_remote_post( 'https://api.easypost.com/v2/addresses' . "?verify[]=delivery&address[street1]={$address_params['street1']}&address[street2]={$address_params['street2']}&address[city]={$address_params['city']}&address[state]={$address_params['state']}&address[zip]={$address_params['zip']}&address[country]={$address_params['country']}&address[company]=EasyPost&address[phone]=415-123-4567", $params );
	$address  = json_decode( $response['body'] );
	if ( isset( $address->verifications->delivery->success ) && true === $address->verifications->delivery->success ) { // Validation Successful.
		if ( 'yes' === $record ) {
			Elex_Address_Validation_Log::log_update( (array) $address, 'EasyPost Response' );
		}
		$vali_address_params = array(
			'status'  => 'success',
			'street1' => $address->street1,
			'street2' => $address->street2,
			'city'    => $address->city,
			'state'   => $address->state,
			'zip'     => $address->zip,
			'country' => $address->country,
			'rdi'     => $address->residential,
		);
		die( wp_json_encode( $vali_address_params ) );
	} else { // Validation Failed.
		$error_msg = array(
			'status'  => 'failure',
			'message' => 'Address Validation Failed',
		);
		if ( 'yes' === $record ) {
			Elex_Address_Validation_Log::log_update( $address, 'EasyPost Response' );
		}
		if ( 'yes' === $this->all_settings['wf_address_autocomplete_validation_validated_address_only'] ) {
			wc_add_notice( $validation_failed_message, 'error' );
			if ( 'E.ADDRESS.NOT_FOUND' === $address->verifications->delivery->errors[0]->code ) {
				wc_add_notice( __( '- Address not found.' ), 'error' );
			}
			if ( 'E.HOUSE_NUMBER.INVALID' === $address->verifications->delivery->errors[0]->code ) {
				wc_add_notice( __( '- House number is invalid.' ), 'error' );
			}
			if ( 'E.HOUSE_NUMBER.MISSING' === $address->verifications->delivery->errors[0]->code ) {
				wc_add_notice( __( '- House number is missing.' ), 'error' );
			}
			if ( 'E.STREET.INVALID' === $address->verifications->delivery->errors[0]->code ) {
				wc_add_notice( __( '- Street is invalid.' ), 'error' );
			}
			if ( 'E.STATE.INVALID' === $address->verifications->delivery->errors[0]->code ) {
				wc_add_notice( __( '- Invalid state.' ), 'error' );
			}
			if ( 'E.CITY_STATE.INVALID' === $address->verifications->delivery->errors[0]->code ) {
				wc_add_notice( __( '- Unverifiable city/state.' ), 'error' );
			}
			if ( 'E.ADDRESS.INSUFFICIENT' === $address->verifications->delivery->errors[0]->code ) {
				wc_add_notice( __( '- Insufficient/incorrect address data.' ), 'error' );
			}
			if ( 'E.ZIP.INVALID' === $address->verifications->delivery->errors[0]->code ) {
				wc_add_notice( __( '- Invalid zip.' ), 'error' );
			}
			if ( 'E.ADDRESS.INVALID' === $address->verifications->delivery->errors[0]->code ) {
				wc_add_notice( __( '- Invalid city/state/zip.' ), 'error' );
			}
			if ( 'E.STREET.MISSING' === $address->verifications->delivery->errors[0]->code ) {
				wc_add_notice( __( '- Street is missing.' ), 'error' );
			}
			if ( 'E.INPUT.INVALID' === $address->verifications->delivery->errors[0]->code ) {
				wc_add_notice( __( '- Street1 is required.' ), 'error' );
			}
		} else {
			die( wp_json_encode( $error_msg ) );
		}
	}
} catch ( Exception $e ) {  // Exception.
	if ( 'yes' === $this->all_settings['wf_address_autocomplete_validation_validated_address_only'] ) {
		wc_add_notice( $validation_failed_message, 'error' );
	}
	$error_msg = array(
		'status'  => 'failure',
		'message' => 'Exception thrown by API',
	);
	Elex_Address_Validation_Log::log_update( $error_msg, 'EasyPost Response' );
	die( wp_json_encode( $error_msg ) );
}
