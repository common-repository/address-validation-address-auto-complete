var suggestion = false;

const { __, _x, _n, sprintf } = wp.i18n;
const {
    elex_setting_object:elexAddressSettings,
}= window;
const{italian_provience_standard:italianProvienceStandard,}=elexAddressSettings;
function elexGetKeyByValue(object, value) {
    return Object.keys(object).find((key) => object[key] === value);
  }
jQuery(function () {
    jQuery("#xa_myModal").css("display", "none");

    var result = '';
    //for closing the suggestion popup
    jQuery("span.xa-closebtn").on("click", function () {
        jQuery('#xa_myModal').css("display", "none");
        jQuery("#place_order").removeProp("disabled");
    });
    //for the recheck address button
    jQuery("body").on("click", "button.recheck", function () {
        jQuery('#xa_myModal').css("display", "none");
        jQuery("#place_order").removeProp("disabled");
    });
    //for using the original address from popup
    jQuery("button.use_original").on("click", function () {
        jQuery('#xa_myModal').css("display", "none");
        jQuery("#place_order").removeProp("disabled");
        var form = jQuery("form.checkout, form#order_review, form#add_payment_method");
        form.submit();
    });
    //for using the validated address suggestion from popup
    jQuery("body").on("click", "button.use_validated", function () {
        jQuery('#xa_myModal').css("display", "none");
        jQuery("#place_order").removeProp("disabled");
        if (jQuery('#ship-to-different-address-checkbox').is(':checked')) {
            jQuery("#shipping_address_1")[0].value = (result.street1);
            jQuery("#shipping_city")[0].value = (result.city);
            jQuery("#shipping_postcode")[0].value = (result.zip);
            jQuery('#shipping_country')[0].value = (result.country);
			let selectedCountry = document.getElementById("shipping_country");
            // avoid read-only fields.
            if(selectedCountry && selectedCountry.readOnly !== true && selectedCountry.options !== undefined && selectedCountry.selectedIndex !== undefined){
                let countryText = selectedCountry.options[selectedCountry.selectedIndex].text; // will be undefined if country field is read only.
                if(countryText){ // check if countryText is defined.
                    document.querySelector("#select2-shipping_country-container").innerText = countryText;
                }
            }

            if('GB'!==result.country){
                jQuery('#shipping_state')[0].value = (result.state);
            }
            
			let selectedState = document.getElementById("shipping_state");
			let stateText = selectedState.options[selectedState.selectedIndex].text;
   
			document.querySelector("#select2-shipping_state-container").innerText = stateText;
        } else {
            jQuery("#billing_address_1")[0].value = (result.street1);
            jQuery("#billing_city")[0].value = (result.city);
            jQuery("#billing_postcode")[0].value = (result.zip);
            jQuery('#billing_country')[0].value = (result.country);
			let selectedCountry = document.getElementById("billing_country");
            // avoid read-only fields.
            if(selectedCountry && selectedCountry.readOnly !== true && selectedCountry.options !== undefined && selectedCountry.selectedIndex !== undefined){
                let countryText = selectedCountry.options[selectedCountry.selectedIndex].text; // will be undefined if country field is read only.
                if(countryText){ // check if countryText is defined.
                    document.querySelector("#select2-billing_country-container").innerText = countryText;
                }
            }
            if('GB'!==result.country){
                jQuery('#billing_state')[0].value = (result.state);
            }
			let selectedState = document.getElementById("billing_state");
            if(selectedState.options){
                let stateText = selectedState.options[selectedState.selectedIndex].text;
			document.querySelector("#select2-billing_state-container").innerText = stateText;
            }
			
        }
        xa_order_note();
        var form = jQuery("form.checkout, form#order_review, form#add_payment_method");
        form.submit();
    });
    //to hook the place order button from checkout page
    jQuery("body").on("click", "#place_order", function () {

        var street1, street2, city, state, zip, country = '';
        if (jQuery('#ship-to-different-address-checkbox').is(':checked'))
        {
            if (jQuery("#shipping_address_1").val() == '' ||
                    jQuery("#shipping_city").val() == '' ||
                    jQuery("#shipping_state").val() == '' ||
                    jQuery("#shipping_postcode").val() == '' ||
                    jQuery("#shipping_country").val() == '') {
                //return true;
            }
            street1 = jQuery("#shipping_address_1").val().trim();
            street2 = jQuery("#shipping_address_2").val().trim();
            city = jQuery("#shipping_city").val();
            state = jQuery("#shipping_state").val();
            zip = jQuery("#shipping_postcode").val();
            country = jQuery("#shipping_country").val();
        } else
        {
            if (jQuery("#billing_address_1").val() == '' ||
                    jQuery("#billing_city").val() == '' ||
                    jQuery("#billing_state").val() == '' ||
                    jQuery("#billing_postcode").val() == '' ||
                    jQuery("#billing_country").val() == '') {
                //return true;
            }
            street1 = jQuery("#billing_address_1").val().trim();
            street2 = jQuery("#billing_address_2").val().trim();
            city = jQuery("#billing_city").val();
            state = jQuery("#billing_state").val();
            zip = jQuery("#billing_postcode").val();
            country = jQuery("#billing_country").val();
        }
  		var nonce = jQuery('#woocommerce-process-checkout-nonce').val();
        //ajax call to send data to php
        if(country === 'IT' && state !== ''){
            state =elexGetKeyByValue(italianProvienceStandard,state);
        }
        jQuery.ajax({
                type: 'post',
                url: wc_checkout_params.ajax_url,
            
                data: {
                        action: 'wf_address_validation',
                        street1_post: street1,
                        street2_post: street2,
                        city_post: city,
                        state_post: state,
                        zip_post: zip,
                        country_post: country,
                        nonce : nonce
                },
                //get response back from php
                success: function (response) {

                    jQuery('#address_rdi').val('');
                    result = jQuery.parseJSON(response);
                    if(result.status ==='success'){
                        if (result.rdi === true ) {
                          jQuery("#address_rdi").val('Residential');
                       }else {
                          jQuery("#address_rdi").val('Commercial');
                       }
                      }
                      if(result.country === 'IT' && result.status ==='success'){
                        result.state=italianProvienceStandard[result.city];
                      }
					if( wf_address_autocomplete_validation_enable_address_popup_obj.enable != 'no' && wf_address_autocomplete_validation_enable_address_popup_obj.enforce_address_validation == 'no' && wf_address_autocomplete_validation_enable_address_popup_obj.confirm_validation == 'no' && (result.status == 'success' || result.status == 'array_success')){
						if( wf_address_autocomplete_validation_enable_address_popup_obj.confirm_validation == 'no' ) {
                            jQuery("#place_order").removeProp("disabled");
                            var form = jQuery("form.checkout, form#order_review, form#add_payment_method");
                            form.submit();
                            return true;
                        }
					}
                    if (result.status == 'failure' && wf_address_autocomplete_validation_enable_address_popup_obj.enforce_address_validation == 'no') {
                        // If Enforce address validation and suggestion disabled.(Result Failure case)
                        if( wf_address_autocomplete_validation_enable_address_popup_obj.confirm_validation == 'no' ) {
                            jQuery("#place_order").removeProp("disabled");
                            var form = jQuery("form.checkout, form#order_review, form#add_payment_method");
                            form.submit();
                            return true;
                        }
                        // If Popup Enabled.
                        if (wf_address_autocomplete_validation_enable_address_popup_obj.enable != 'no') {
                            jQuery('#original').empty();
                            jQuery('#validated').empty();
                            jQuery('#right_title').empty();
                            jQuery('#right_button').empty();

                            jQuery('#original').append(street1);

                            if( street2 !== null && street2 !== undefined && street2 !== "" ) {
                                jQuery('#original').append(street2);
                                jQuery('#original').append(', ');
                                jQuery('#original').append('<br>');            
                            } else {
                                jQuery('#original').append(',<br>');
                            }

                            jQuery('#original').append(city);
                            jQuery('#original').append(', ');
    

                            jQuery('#original').append(state);
                            jQuery('#original').append(', ');

                            jQuery('#original').append(country);
                            jQuery('#original').append(' - ');
                            jQuery('#original').append(zip);
                            jQuery('#right_title').append("<center><bold>"+__('Validation Successful','address-validation-address-auto-complete')+"</bold></center>");
                            jQuery('#right_button').append("<center><p style='color:gray'>"+__('Update or Proceed with your Original Address.','address-validation-address-auto-complete')+"<p></center>");
                            jQuery('#validated').append("<center><img src='images/Warning.jpg' width='30px'/></center>");
                            jQuery('#validated').append("<p>"+__('Address Validation Failed.','address-validation-address-auto-complete')+"</p>");
                            jQuery('#xa_myModal').css("display", "block");
                            return true;
                        }
                        // If popup disabled.
                        if (wf_address_autocomplete_validation_enable_address_popup_obj.enable == 'no')
                        {
                            jQuery('html, body').animate({scrollTop: 0}, 'fast');
                            jQuery('.validation_failed_msg_checkout_page').empty();       
                            jQuery('#customer_details').prepend('<div class="validation_failed_msg_checkout_page"><b>'+__('Address Validation Failed. Please update the address and try again or proceed to checkout by clicking here.','address-validation-address-auto-complete')+'<br><button type="submit" onclick="window.onbeforeunload = null;">'+'Place Order'+'</button></b><br><br></div>');
                            return true;
                        }
                    }
                    if( wf_address_autocomplete_validation_enable_address_popup_obj.enforce_address_validation == 'yes' && wf_address_autocomplete_validation_enable_address_popup_obj.confirm_validation == 'no' && (result.status == 'success' || result.status == 'array_success')){
                        if(result.status == 'array_success'){
                            var status = result['status']; 
                            result = result['0'];
                            if(result.country==='IT'){
                                result.state = italianProvienceStandard[result.city];
                            }
                        } else{
                            var status = result.status;
                        }
                        if (status == 'success' || status == 'array_success')
                        {
                            if (wf_address_autocomplete_validation_enable_address_popup_obj.confirm_validation == 'no') {
                                jQuery("#place_order").removeProp("disabled");
                                if (jQuery('#ship-to-different-address-checkbox').is(':checked')) {
                                    jQuery("#shipping_address_1").val(result.street1);
                                    jQuery("#shipping_city").val(result.city);
                                    jQuery("#shipping_postcode").val(result.zip);
                                    jQuery('#shipping_country').val(result.country).trigger("change");
                                    jQuery('#shipping_state').val(result.state).trigger("change");
                                } else {
                                    jQuery("#billing_address_1").val(result.street1);
                                    jQuery("#billing_city").val(result.city);
                                    jQuery("#billing_postcode").val(result.zip);
                                    jQuery('#billing_country').val(result.country).trigger("change");
                                    jQuery('#billing_state').val(result.state).trigger("change");
                                }
                                xa_order_note();
                                var form = jQuery("form.checkout, form#order_review, form#add_payment_method");
                                form.submit();
                                return true;
                            }
                        }
                    } 
                    var same_addr = true;
                    if (wf_address_autocomplete_validation_enable_address_popup_obj.enable != 'no' && wf_address_autocomplete_validation_enable_address_popup_obj.confirm_validation == 'yes' && (result.status == 'success' || result.status == 'array_success')){
                        if(result.status == 'array_success'){
                            var status = result['status']; 
                            result = result['0'];
                            if(result.country==='IT'){
                                result.state = italianProvienceStandard[result.state];
                            }
                        } else{
                            var status = result.status;
                        }

                        if(result == '0'){
                            jQuery("#place_order").removeProp("disabled");
                            var form = jQuery("form.checkout, form#order_review, form#add_payment_method");
                            form.submit();
                            return true;
                        }

                        jQuery('#original').empty();
                        jQuery('#validated').empty();
                        jQuery('#right_title').empty();
                        jQuery('#right_button').empty();
                        if ((result.street1) && result.street1.toLowerCase() != street1.toLowerCase()) { // check if result.street1 is null and then compare with street1.
                            //jQuery('#original').empty();
                            same_addr = false;
                            jQuery('#original').append('<span style="background-color:yellow !important;">' + street1 + '</span>');
                            jQuery('#original').append(', ');
                        } else {
                            //jQuery('#original').empty();
                            jQuery('#original').append(street1);
                            jQuery('#original').append(', ');
                        }
                        if( street2 !== null && street2 !== undefined && street2 !== "" ) {
                            jQuery('#original').append(street2);
                            jQuery('#original').append(', ');
                            jQuery('#original').append('<br>');            
                        } else {
                            jQuery('#original').append(',<br>');
                        }
                        if (result.city.toLowerCase() != city.toLowerCase()) {
                            same_addr = false;
                            jQuery('#original').append('<span style="background-color:yellow !important;">' + city + '</span>');
                        } else
                            jQuery('#original').append(city);
                        jQuery('#original').append(', ');

                        // null check, because in countries such as new zealand, state is optional
                        if(result.state != null && state != null) {
                            if (result.state.toLowerCase() != state.toLowerCase()) {
                                same_addr = false;
                                jQuery('#original').append('<span style="background-color:yellow !important;">' + state + '</span>');
                            } else
                                jQuery('#original').append(state);
                            jQuery('#original').append(', ');
                        }
                        if (result.country.toLowerCase() != country.toLowerCase()) {
                            same_addr = false;
                            jQuery('#original').append('<span style="background-color:yellow !important;">' + country + '</span>');
                        } else
                            jQuery('#original').append(country);
                        jQuery('#original').append(' - ');
                        if (result.zip != zip) {
                            same_addr = false;
                            jQuery('#original').append('<span style="background-color:yellow !important;">' + zip + '</span>');
                        } else
                            jQuery('#original').append(zip);

                        if (status == 'success' || status == 'array_success')
                        {
                            if (wf_address_autocomplete_validation_enable_address_popup_obj.confirm_validation == 'no') {
                                jQuery("#place_order").removeProp("disabled");
                                if (jQuery('#ship-to-different-address-checkbox').is(':checked')) {
                                    jQuery("#shipping_address_1").val(result.street1);
                                    jQuery("#shipping_city").val(result.city);
                                    jQuery("#shipping_postcode").val(result.zip);
                                    jQuery('#shipping_country').val(result.country).trigger("change");
                                    jQuery('#shipping_state').val(result.state).trigger("change");
                                } else {
                                    // jQuery("#billing_address_1").val(result.street1);
                                    // jQuery("#billing_city").val(result.city);
                                    // jQuery("#billing_postcode").val(result.zip);
                                    // jQuery('#billing_country').val(result.country).trigger("change");
                                    // jQuery('#billing_state').val(result.state).trigger("change");
                                }
                                xa_order_note();
                                var form = jQuery("form.checkout, form#order_review, form#add_payment_method");
                                form.submit();
                                return true;

                            }
                            jQuery('#right_title').append("<center><bold>"+ __('Validation Successful','address-validation-address-auto-complete') +"</bold></center>");
                            jQuery('#right_button').append("<center><button class='xa-btn xa-white xa-round-large xa-border use_validated'>"+__('Place Order with Suggested Address','address-validation-address-auto-complete')+"</button></center>");

                            jQuery('#validated').append(result.street1);
                            jQuery('#validated').append(',<br>');
                            jQuery('#validated').append(result.city);
                            jQuery('#validated').append(', ');
                            jQuery('#validated').append(result.state);
                            jQuery('#validated').append(', ');
                            jQuery('#validated').append(result.country);
                            jQuery('#validated').append(' - ');
                            jQuery('#validated').append(result.zip);
                            if (same_addr == false)
                                jQuery('#xa_myModal').css("display", "block");
                            else
                            {
                                xa_order_note();
                                jQuery('.checkout').submit();
                            }

                        }
                        //for invalid addresses
                        else
                        {
                            jQuery('#right_title').append("<center><bold>"+__('Address Validation Failed','address-validation-address-auto-complete')+"</bold></center>");
                            jQuery('#right_button').append("<center><button class='xa-btn xa-white xa-round-large xa-border recheck'>"+__('Recheck Address','address-validation-address-auto-complete')+"</button></center>");
                            jQuery('#xa_myModal').css("display", "block");
                        }
                    }

                    if (wf_address_autocomplete_validation_enable_address_popup_obj.enable == 'no' && wf_address_autocomplete_validation_enable_address_popup_obj.confirm_validation == 'yes' && (result.status == 'success' || result.status == 'array_success'))
                    {
                        //check if the entered address is same as address returned by api
                        var is_addr_same=false;
                        if(result.status == 'success'){
                            if(result.street1.toLowerCase() == street1.toLowerCase() && result.city.toLowerCase() == city.toLowerCase() && ( result.state != null && state != null && result.state.toLowerCase() == state.toLowerCase() ) && result.country.toLowerCase() == country.toLowerCase() && result.zip == zip) {
                                is_addr_same = true;
                            }
                        }
                        if(is_addr_same == true ){
                            jQuery('.checkout').submit();
                        }
                        if (suggestion) {
                            jQuery('.checkout').submit();
                        } else
                        {
                            suggestion = true;

                            // Paypal plugin compatibility
                                if(jQuery("#place_order.ppcp-hidden").length > 0) {
                                    jQuery("#place_order.ppcp-hidden").attr('style', 'display: block !important;');
                                    jQuery("#ppc-button-ppcp-gateway").hide();
                                }

                            jQuery('html, body').animate({scrollTop: 0}, 'fast');
                            jQuery('#xa_addr_radio').empty();

                            //lets populate the address info...
                            jQuery('#customer_details').prepend('<br>');
                            if (result.status == 'array_success')
                            {
                                for (var i = 0; i < result.array_length - 1; i = i + 1) {
                                    var k = result.array_length - i - 1;

                                    addr = ((result[i].street == "") ? "" : result[i].street + ", ");
                                    // addr += ((result[i].street2 =="") ? "" : result[i].street2 + ", ");   
                                    addr += ((result[i].city == "") ? "" : result[i].city + ", ");
                                    addr += ((result[i].state == "") ? "" : result[i].state + ", ");
                                    addr += ((result[i].zip == "") ? "" : result[i].zip);
                                    jQuery('.validation_failed_msg_checkout_page').empty(); 
                                    jQuery('#customer_details').prepend('<div class="xa-addr-radio">');
                                    jQuery('#customer_details').prepend('<input type="radio" name="xa_which_to_use" id="xa_radio_' + i + '" value="' + i + '"><b>'+ __('Suggestion: ','address-validation-address-auto-complete') + ' ' + k + ': </b>' + addr + '');
                                    jQuery('#customer_details').prepend('</div>');


                                    //The hidden fields that get posted back to our plugin
                                    jQuery('#xa_addr_radio').append("<div style='display: hidden;'>");
                                    jQuery('#xa_addr_radio').append("<input type='hidden' name='xa_addr_corrected_" + i + "_addr1' id='xa_addr_corrected_" + i + "_addr1' value='" + result[i].street + "'>");
                                    jQuery('#xa_addr_radio').append("<input type='hidden' name='xa_addr_corrected_" + i + "_city'' id='xa_addr_corrected_" + i + "_city' value='" + result[i].city + "'>");
                                    jQuery('#xa_addr_radio').append("<input type='hidden' name='xa_addr_corrected_" + i + "_state' id='xa_addr_corrected_" + i + "_state' value='" + result[i].state + "'>");
                                    jQuery('#xa_addr_radio').append("<input type='hidden' name='xa_addr_corrected_" + i + "_zip' id='xa_addr_corrected_" + i + "_zip' value='" + result[i].zip + "'>");
                                    jQuery('#xa_addr_radio').append("</div>");
                                }
                            }

                            if (result.status == 'success')
                            {
                                addr = ((result.street1 == "" || result.street1 == undefined) ? "" : result.street1 + ", ");
                                addr += ((result.street2 == "" || result.street2 == undefined) ? "" : result.street2 + ", ");
                                addr += ((result.city == "") ? "" : result.city + ", ");
                                addr += ((result.state == "") ? "" : result.state + ", ");
                                addr += ((result.zip == "") ? "" : result.zip);
                                jQuery('.validation_failed_msg_checkout_page').empty(); 
                                jQuery('#customer_details').prepend('<div class="xa-addr-radio">');
                                jQuery('#customer_details').prepend('<input type="radio" name="xa_which_to_use" id="xa_radio_obj" value="obj"><b> ' + __('Suggestion: ','address-validation-address-auto-complete') + ' </b>' + addr + '');
                                jQuery('#customer_details').prepend('</div>');
                                //The hidden fields that get posted back to our plugin
                                jQuery('#xa_addr_radio').append("<div style='display: hidden;'>");
                                jQuery('#xa_addr_radio').append("<input type='hidden' name='xa_addr_corrected_obj_addr1' id='xa_addr_corrected_obj_addr1' value='" + result.street1 + "'>");
                                jQuery('#xa_addr_radio').append("<input type='hidden' name='xa_addr_corrected_obj_city'' id='xa_addr_corrected_obj_city' value='" + result.city + "'>");
                                if('GB'!==result.country){
                                    jQuery('#xa_addr_radio').append("<input type='hidden' name='xa_addr_corrected_obj_state' id='xa_addr_corrected_obj_state' value='" + result.state + "'>");
                                }
                                
                                jQuery('#xa_addr_radio').append("<input type='hidden' name='xa_addr_corrected_obj_zip' id='xa_addr_corrected_obj_zip' value='" + result.zip + "'>");
                                jQuery('#xa_addr_radio').append("</div>");
                            }

                            addr = ((street1 == "" || street1 == undefined) ? "" : street1 + ", ");
                            addr += ((street2 == "" || street2 == undefined) ? "" : street2 + ", ");
                            addr += ((city == "") ? "" : city + ", ");
                            addr += ((state == "") ? "" : state + ", ");
                            addr += ((zip == "") ? "" : zip);

                            jQuery('#customer_details').prepend('<div class="xa-addr-radio">');
                            jQuery('#customer_details').prepend('<input type="radio" name="xa_which_to_use" id="xa_radio_orig" value="orig" checked><b> ' + __('Use Original Address: ','address-validation-address-auto-complete') + ' </b>' + addr + '');
                            jQuery('#customer_details').prepend('</div>');
                            jQuery('#customer_details').prepend('<b>'+__('There appears to be a problem with the address. Please correct or select one below.','address-validation-address-auto-complete')+'</b><br><br>');

                            //The hidden fields that get posted back to our plugin
                            jQuery('#xa_addr_radio').append("<div style='display: hidden;'>");
                            jQuery('#xa_addr_radio').append("<input type='hidden' name='xa_addr_orig_addr1' id='xa_addr_orig_addr1' value='" + street1 + "'>");
                            jQuery('#xa_addr_radio').append("<input type='hidden' name='xa_addr_orig_city'' id='xa_addr_orig_city' value='" + city + "'>");
                            //for new zealand
                            if(state != null){
                                jQuery('#xa_addr_radio').append("<input type='hidden' name='xa_addr_orig_state' id='xa_addr_orig_state' value='" + state + "'>");
                            }
                            jQuery('#xa_addr_radio').append("<input type='hidden' name='xa_addr_orig_zip' id='xa_addr_orig_zip' value='" + zip + "'>");
                            jQuery('#xa_addr_radio').append("</div>");

                            jQuery('#xa_addr_correction').show();
                            jQuery("#place_order").removeProp("disabled");

                        }
                        //capture radio button changes
                        jQuery('input[type=radio][name=xa_which_to_use]').change(function () {
                            xa_radio_changed(this);
                        });
                    } else {
                        // if validation fails give some option to customers
                        if (result.status == 'failure' || result == '0' || result.state == null || result.city == null || result.country == null || result.zip == null) {
                            jQuery("#place_order").removeProp("disabled");
                            var form = jQuery("form.checkout, form#order_review, form#add_payment_method");
                            form.submit();
                            return true;
                        }
                        //check if the owner has any map restrictions
                        if (result.map == 'undefined')
                        {
                            var form = jQuery("form.checkout, form#order_review, form#add_payment_method");
                            form.submit();
                            return true;
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                }
            });
        return false;
    });

    //Handle the radio button change
    function xa_radio_changed(item) {

         // Paypal plugin compatibility.
        if(jQuery("#place_order.ppcp-hidden").length === 0) {
            jQuery( 'body' ).trigger( 'update_checkout' );
         }

        //update checkout section when checkbox selected
        jQuery( 'body' ).trigger( 'update_checkout' );

        //lets copy the data into the appropriate fields
        if (item.value == 'orig') {
            //go with orig values
            addr1 = jQuery('#xa_addr_orig_addr1').val();
            addr2 = jQuery('#xa_addr_orig_addr2').val();
            city = jQuery('#xa_addr_orig_city').val();
            state = jQuery('#xa_addr_orig_state').val();
            zip = jQuery('#xa_addr_orig_zip').val();

        } else {
            //it is one of the corrected fields
            key = item.value;
            addr1 = jQuery('#xa_addr_corrected_' + key + '_addr1').val();
            city = jQuery('#xa_addr_corrected_' + key + '_city').val();
            state = jQuery('#xa_addr_corrected_' + key + '_state').val();
            zip = jQuery('#xa_addr_corrected_' + key + '_zip').val();
        }

        //OK are we shipping to different addr?
        if (jQuery('input[name=ship_to_different_address]').is(':checked')) {
            //shipping to different addr
            jQuery('#shipping_address_1').val(addr1);
            jQuery('#shipping_city').val(city);
            jQuery('#shipping_state').val(state);
            jQuery('#shipping_postcode').val(zip);
        } else {
            //shipping to billing
            jQuery('#billing_address_1').val(addr1);
            jQuery('#billing_city').val(city);
            jQuery('#billing_state').val(state);
            jQuery('#billing_postcode').val(zip);

            //always update the ship to in case they select it!
            jQuery('#shipping_address_1').val(addr1);
            jQuery('#shipping_city').val(city);
            jQuery('#shipping_state').val(state);
            jQuery('#shipping_postcode').val(zip);

        }


    }
    function xa_order_note() {
        jQuery.ajax({      
            url: wc_checkout_params.ajax_url,
             data: {
            action: 'wf_order_note',
        }
        });
    }

});