var placeSearch, billing_autocomplete,shipping_autocomplete;
//billing_autocomplete - search bar for billing address
//shipping_autocomplete - search bar for shipping address
//componentForm - to select the preferred result format
var componentForm = {
  administrative_area_level_1 : 'short_name',   
  administrative_area_level_2 : 'short_name',
  country                     : 'short_name',
  locality                    : 'long_name',
  postal_code                 : 'long_name',
  subpremise                  : 'long_name',
  premise                     : 'long_name',
  route                       : 'long_name',
  street_address              : 'long_name',
  street_number               : 'long_name',
  sublocality                 : 'long_name',
  sublocality_level_1         : 'long_name',
  sublocality_level_2         : 'long_name',
  neighborhood                : 'long_name',
  postal_town                 : 'long_name',
};
//billing_mappingForm - local billing variables with google variables  
var billing_mappingForm = {
  administrative_area_level_1 : 'billing_state',   
  country                     : 'billing_country',
  locality                    : 'billing_city',
  administrative_area_level_2 : 'billing_state',
  postal_code                 : 'billing_postcode',
  premise                     : 'billing_address_1',
  subpremise                  : 'billing_address_1',
  route                       : 'billing_address_1',
  street_address              : 'billing_address_1',
  street_number               : 'billing_address_1',
  sublocality                 : 'billing_address_2',
  sublocality_level_1         : 'billing_address_2',
  sublocality_level_2         : 'billing_address_2',
  neighborhood                : 'billing_address_2',
  postal_town                 : 'billing_city',
};
//shipping_mappingForm - local shipping variables with google variables
var shipping_mappingForm = {
  administrative_area_level_1 : 'shipping_state',   
  administrative_area_level_2 : 'shipping_state',
  country                     : 'shipping_country',
  locality                    : 'shipping_city',
  postal_code                 : 'shipping_postcode',
  subpremise                  : 'shipping_address_1',
  premise                     : 'shipping_address_1',
  route                       : 'shipping_address_1',
  street_address              : 'shipping_address_1',
  street_number               : 'shipping_address_1',
  sublocality                 : 'shipping_address_2',
  sublocality_level_1         : 'shipping_address_2',
  sublocality_level_2         : 'shipping_address_2',
  neighborhood                : 'shipping_address_2',
  postal_town                 : 'shipping_city',
};

var isNZAddress = false;
var isVNAddress = false;
var isUKAddress = false;
// onload function to clear the checkout fields
document.addEventListener("DOMContentLoaded", function(){
  if (jQuery("#ship-to-different-address-checkbox").is(":checked")) {
    if(jQuery('#shipping_country').val() === "NZ"){
      isNZAddress = true;
      isVNAddress = false;
      isUKAddress = false;
    } 
    else if(jQuery('#shipping_country').val() === "VN"){
      isVNAddress = true;
      isNZAddress = false;
      isUKAddress =false;
    }
    else if(jQuery('#shipping_country').val() === "GB"){
      isVNAddress = false;
      isNZAddress = false;
      isUKAddress = true;
    }

  } else {
    if(jQuery('#billing_country').val() === "NZ"){
      isNZAddress = true;
      isVNAddress = false;
      isUKAddress = false;
    } 
    else if(jQuery('#billing_country').val() === "VN"){
      isVNAddress = true;
      isNZAddress = false;
      isUKAddress = false;
    }
    else if(jQuery('#billing_country').val() === "GB"){
      isVNAddress = false;
      isNZAddress = false;
      isUKAddress = true;
    }
  }
	
  //jQuery('#address_rdi').prop("hidden","hidden");
  var address_rdi_value=document.getElementById('address_rdi');

  if(address_rdi_value !== null){
    document.getElementById('address_rdi').value = '';
  }
  jQuery("#address_rdi").hide();
  if(result.address_autocomplete_field === "default"){
    if(document.getElementById('billing_autocomplete')!==null){
      document.getElementById('billing_autocomplete').value = '';
    }
    if(jQuery('#ship-to-different-address-checkbox').is(':checked')){
      document.getElementById('shipping_autocomplete').value = '';
    }
  }
  if(result.auto_complete_restrict_status != 'all'){
    billing_autocomplete.setComponentRestrictions(
      {'country': result.auto_complete_country_allowed});    
    shipping_autocomplete.setComponentRestrictions(
      {'country': result.auto_complete_country_allowed});    
  }
  if(result.status_enable_disable !== 'yes'){
    // Code to reset and disable billing fields, when editable address fields is disabled.
   if(jQuery("#billing_country").attr('readonly') !== 'readonly' ){
	  jQuery("#billing_country").val("").trigger("change").attr("disabled", true); 
   }
	  
    jQuery("#billing_state").val("").trigger("change").attr("disabled", true);
    if(jQuery("#billing_autocomplete").length != 0){ // check if element exists.
      jQuery("#billing_autocomplete").val("");
    }
	  
    if ( jQuery( "#billing_address_1" ).length != 0 && result.address_autocomplete_field === "default" ){
      jQuery("#billing_address_1").val("").attr("disabled", true);
    }
    if ( jQuery("#billing_address_2").length != 0) {
      jQuery("#billing_address_2").val("").attr("disabled", true);
    }
    jQuery("#billing_city").val("").attr("disabled", true);
    jQuery("#billing_postcode").val("").attr("disabled", true);
  }
});

// Code to reset and disable shipping fields, when editable address fields is disabled.
jQuery("#ship-to-different-address-checkbox").on('change', function(){
  if( jQuery(this).is(':checked') && result.status_enable_disable !== "yes" ){
    if (jQuery("#ship-to-different-address-checkbox").is(":checked")) {
      if(jQuery("#shipping_country").attr('readonly') !== 'readonly'){
		 jQuery("#shipping_country").val("").trigger("change").attr("disabled", true);
	  }
		
      jQuery("#shipping_state").val("").trigger("change").attr("disabled", true);
      if(jQuery("#shipping_autocomplete").length){ // check if element exists.
        jQuery("#shipping_autocomplete").val("");
      }
      if ( jQuery( "#shipping_address_1" ).length != 0 && result.address_autocomplete_field === "default" ){
        jQuery("#shipping_address_1").val("").attr("disabled", true);
      }
      if ( jQuery("#shipping_address_2").length != 0) {
        jQuery("#shipping_address_2").val("").attr("disabled", true);
      }
      jQuery("#shipping_city").val("").attr("disabled", true);
      jQuery("#shipping_postcode").val("").attr("disabled", true);
    }
  }
});

//For Billing Address
function initAutocomplete() {
  // Create the autocomplete object, restricting the search to geographical
  // location types.
  
  var billing_autocomplete_field = result.address_autocomplete_field === "default" ? 'billing_autocomplete' : 'billing_address_1';
  var shipping_autocomplete_field = result.address_autocomplete_field === "default" ? 'shipping_autocomplete' : 'shipping_address_1';
  billing_autocomplete = new google.maps.places.Autocomplete(
    /** @type {!HTMLInputElement} */(document.getElementById(billing_autocomplete_field)),
    {types: ['geocode']});
  shipping_autocomplete = new google.maps.places.Autocomplete(
    /** @type {!HTMLInputElement} */(document.getElementById(shipping_autocomplete_field)),
    {types: ['geocode']});
  // When the user selects an address from the dropdown, populate the address
  // fields in the form.
  billing_autocomplete.addListener('place_changed', billing_fillInAddress);
  shipping_autocomplete.addListener('place_changed', shipping_fillInAddress);
}
function billing_fillInAddress() {

  const NZStatesMapping ={
    "Northland": "NTL",
    "Auckland": "AUK",
    "Waikato": "WKO",
    "Bay of Plenty": "BOP",
    "Taranaki": 'TKI',
    "Gisborne": "GIS",
    "Hawke’s Bay":"HKB",
    "Manawatu-Wanganui": "MWT",
    "Wellington": "WGN",
    "Nelson": "NSN",
    "Marlborough": "MBH",
    "Tasman": "TAS",
    "West Coast": "WTC",
    "Canterbury": "CAN",
    "Otago": "OTA",
    "Southland": "STL",
  }

  // Get the place details from the autocomplete object.
  var place = billing_autocomplete.getPlace();
	for (var component in componentForm) {
   if(document.getElementById(billing_mappingForm[component]) && document.getElementById(billing_mappingForm[component]).value){
	   //to autoclear the fields after every search
	   document.getElementById(billing_mappingForm[component]).value = '';
	   //to enable the input fields after search
		document.getElementById(billing_mappingForm[component]).disabled = false;
   }
  }
  if(place.address_components != undefined && place.address_components != null)
  {
  // Get each component of the address from the place details
  // and fill the corresponding field on the form.
  //to update the state select field after updating country select fields
  
  for (var i = 0; i < place.address_components.length; i++) {
  var addressType = place.address_components[i].types[0];
    if (componentForm[addressType]) {
      var val = place.address_components[i][componentForm[addressType]];
      if(billing_mappingForm[addressType] === 'billing_country' && jQuery("#billing_country").attr('readonly') !== 'readonly' )
      {  
          if(val === "NZ") {
            isNZAddress = true;
            isVNAddress = false;
            isUKAddress =false;
          } else if(val === "VN") {
            isVNAddress = true;
            isNZAddress = false;
            isUKAddress =false;
          } else if(val === 'GB') {
            isUKAddress =true;
            isNZAddress =false;
            isVNAddress =false;
          } else {
            isVNAddress = false;
            isNZAddress = false;
            isUKAddress =false;
          }
          jQuery('#billing_country').val(val).trigger("change");
      }
    }
  }

    //to update fields with appropiate values recieved from google api
	let is_sub_premise = false;
  let sub_premise = '';
  var street_number='';
  var street_address_1='';
  var route='';
  var premise = '';
	for (var i = 0; i < place.address_components.length; i++) {
      var addressType = place.address_components[i].types[0];
      if (componentForm[addressType]) {
        var val = place.address_components[i][componentForm[addressType]];
        if(addressType === 'premise')
        {
          premise = val;
        } else if(addressType === 'subpremise'){
          sub_premise = val;
          is_sub_premise = true;
        }
        else if(addressType === 'street_number')
        {      
          street_number=val; 
        }
        else if(addressType === 'street_address')
        {
          street_address_1=val;
        }
        else if(addressType === 'route')
        {
          route=val;
        }
        else if(addressType === 'sublocality')
        { 
          document.getElementById('billing_address_2').value += val + " ";
          if( ! isNZAddress ) {
            document.getElementById('billing_city').value = val;
          }
        }
        else if(addressType === "locality"||addressType === 'sublocality_level_1' )
        { 
          if(isUKAddress){
            document.getElementById("billing_address_2").value =val;
          }else{
            document.getElementById('billing_city').value = val;
          }
          
        } 
        else if(addressType === 'sublocality_level_2' )
        {
          document.getElementById('billing_address_2').value += val;
        }
        else if( isVNAddress && addressType === 'neighborhood' )
        { 
          document.getElementById('billing_address_2').value += val;
        }
        else if( ( isNZAddress || isVNAddress  ) && addressType === "administrative_area_level_1"){
          jQuery('#billing_state').val(NZStatesMapping[val]).trigger("change"); 
          if( isVNAddress ){
            document.getElementById('billing_city').value += val;
          }
        }else if( isVNAddress  && addressType === "administrative_area_level_2"){
          document.getElementById('billing_city').value += val + " ";
        }
        else if( isUKAddress && addressType === "postal_town" ){

          document.getElementById('billing_city').value += val;
        }
        else if( isNZAddress && addressType === "postal_code" ) {
          jQuery("#billing_postcode").val(val);
        }
        else if( !isNZAddress && billing_mappingForm[addressType] === 'billing_state')
        { 
          if(val === val.toUpperCase() && jQuery(`#billing_state option[value=${val}]`).length > 0){ // check if option exists in state dropdown.
            jQuery('#billing_state').val(val).trigger("change");
          } 
          if(isUKAddress){
            jQuery('#billing_state').val('');
          }
        }
        else
        {
          if(val !=null && !isNZAddress){
            document.getElementById(billing_mappingForm[addressType]).value = val;
          }
        }
      }
    }
    if( result.street_address_behaviour !== 'default' ){
      document.getElementById('billing_address_1').value=null;
    document.getElementById('billing_address_1').value += street_number + " " + route + " " + street_address_1 ;
    } else if( ["BR", "PL", "IT","SE","AT","DE"].includes( jQuery("#billing_country").val() ) ) {
      document.getElementById('billing_address_1').value = route + " " + street_number + " " + street_address_1 ;
    } else {
      document.getElementById('billing_address_1').value=null;
      if(premise===street_number.trim() + " " + route.trim() || premise === '') {
        document.getElementById('billing_address_1').value = street_number + " " + route + " " + street_address_1 ;
      } else {
        document.getElementById('billing_address_1').value = premise + " " + street_number + " " + route + " " + street_address_1;
      }
    }

    if(result.restrict_street_line=='yes' && document.getElementById('billing_address_2').value != null ){
      document.getElementById('billing_address_2').value=null;
    }

      jQuery("#billing_state").attr("disabled", false);
      jQuery("#billing_postcode").attr("disabled", false);
      jQuery("#billing_city").attr("disabled", false);
      jQuery("#billing_state").attr("disabled", false);
      jQuery("#billing_address_2").attr("disabled", false);
      jQuery("#billing_address_1").attr("disabled", false);
      jQuery("#billing_country").attr("disabled", false);
	var parser = new DOMParser();
	var doc = parser.parseFromString(place.adr_address, "text/html");
	var street_address = doc.getElementsByClassName('street-address')?.[0]?.innerHTML;
  street_address = street_address !== undefined ? street_address : ""
	var addr_com_length = place.address_components.length;
  if( !isNZAddress ) {
    for (var i = 0; i < addr_com_length; i++){
      if ( place.address_components[i].types[0] === 'premise' || place.address_components[i].types[0] === 'subpremise' || place.address_components[i].types[0] === 'street_address' || place.address_components[i].types[0] === 'route' ) {
        if(street_address?.includes(place?.address_components?.[i]?.['short_name'])){
          street_address = street_address.replace(place.address_components[i]['short_name'],place.address_components[i]['long_name']);
        }
      }
    }
    if(is_sub_premise){
      document.getElementById('billing_address_1').value = sub_premise + '/' + street_address;
    }
    }
  }
  jQuery( 'body' ).trigger( 'update_checkout' );
}
// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
function billing_geolocate() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var geolocation = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };
      var circle = new google.maps.Circle({
        center: geolocation,
        radius: position.coords.accuracy
      });
      billing_autocomplete.setBounds(circle.getBounds());
    });
  }
}
//For shipping Address
function shipping_fillInAddress() {
  const NZStatesMapping ={
    "Northland": "NTL",
    "Auckland": "AUK",
    "Waikato": "WKO",
    "Bay of Plenty": "BOP",
    "Taranaki": 'TKI',
    "Gisborne": "GIS",
    "Hawke’s Bay":"HKB",
    "Manawatu-Wanganui": "MWT",
    "Wellington": "WGN",
    "Nelson": "NSN",
    "Marlborough": "MBH",
    "Tasman": "TAS",
    "West Coast": "WTC",
    "Canterbury": "CAN",
    "Otago": "OTA",
    "Southland": "STL",
  }

  var place = shipping_autocomplete.getPlace();
  for (var component in componentForm) {
    if(document.getElementById(shipping_mappingForm[component]) && document.getElementById(shipping_mappingForm[component]).value){
		//to autoclear the fields after every search 
		document.getElementById(shipping_mappingForm[component]).value = '';
		//to enable the input fields after search
		document.getElementById(shipping_mappingForm[component]).disabled = false; 
	 }
	
  }
  // Get each component of the address from the place details
  // and fill the corresponding field on the form.
  //to update the state select field after updating country select fields
  //to update fields with appropiate values recieved from google api
	var is_sub_premise = false;
  for (var i = 0; i < place.address_components.length; i++) {
  var addressType = place.address_components[i].types[0];
    if (componentForm[addressType]) {
      var val = place.address_components[i][componentForm[addressType]];
      if(shipping_mappingForm[addressType] === 'shipping_country' && jQuery("#shipping_country").attr('readonly') !== 'readonly'){
        if(val === "NZ") {
          isNZAddress = true;
          isVNAddress = false;
          isUKAddress = false;
        } else if(val === "VN") {
          isVNAddress = true;
          isNZAddress = false;
          isUKAddress = false;
        } else if(val === "GB"){
          isUKAddress =true;
          isNZAddress =false;
          isVNAddress =false;
        }else {
          isVNAddress = false;
          isNZAddress = false;
          isUKAddress = false;
        }
          jQuery('#shipping_country').val(val).trigger("change");
      }
    }
  }

  let sub_premise = val;
  var street_number='';
  var street_address_1='';
  var route='';
  var premise = '';
  //to update fields with appropiate values recieved from google api
  for (var i = 0; i < place.address_components.length; i++) {
    var addressType = place.address_components[i].types[0];
    if (componentForm[addressType]) {
      var val = place.address_components[i][componentForm[addressType]];
      if(addressType === 'premise')
      {
        premise = val;
      }else if(addressType === 'subpremise'){
    	  sub_premise = val;
    	  is_sub_premise = true;
      }
      else if(addressType === 'street_number' )
      {
        street_number=val; 
      }
      else if(addressType === 'street_address')
      {
        street_address_1=val;
      }
      else if(addressType === 'route')
      {
        route=val;
      }
      else if(addressType === 'sublocality')
      {
        document.getElementById('shipping_address_2').value += val + " ";
        if( ! isNZAddress ) {
          document.getElementById('shipping_city').value = val;
        }
      }
      else if(addressType === "locality"||addressType === 'sublocality_level_1')
      {
        if(isUKAddress){
          document.getElementById("shipping_address_2").value =val;
        }else{
          document.getElementById('shipping_city').value = val;
        }
      }    
      else if(addressType === 'sublocality_level_2')
      {
        document.getElementById('shipping_address_2').value += val;
      }
      else if( isVNAddress && addressType === 'neighborhood' )
      { 
        document.getElementById('shipping_address_2').value += val;
      }
      else if( ( isNZAddress || isVNAddress  ) && addressType === "administrative_area_level_1"){
        jQuery('#shipping_state').val(NZStatesMapping[val]).trigger("change"); 
        if( isVNAddress ){
          document.getElementById('shipping_city').value += val;
        }
      }
      else if( (isUKAddress) && addressType==="postal_town"){
        document.getElementById('shipping_city').value += val;
      }
      else if( isVNAddress  && addressType === "administrative_area_level_2"){
        document.getElementById('shipping_city').value += val + " ";
      }
      else if( isNZAddress && addressType === "postal_code" ) {
        jQuery("#shipping_postcode").val(val);
      }
      else if( !isNZAddress && shipping_mappingForm[addressType] === 'shipping_state')
      {
        if(val === val.toUpperCase() && jQuery(`#shipping_state option[value=${val}]`).length > 0){ // check if option exists in state dropdown.
          jQuery('#shipping_state').val(val).trigger("change");
        }
        if(isUKAddress){
          jQuery('#shipping_state').val('');
        }
      }
      else
      {
          if(val !=null && !isNZAddress) {
			 document.getElementById(shipping_mappingForm[addressType]).value = val;
		  }
      }
    }
  }
  if( result.street_address_behaviour !== 'default' ){
    document.getElementById('shipping_address_1').value=null;
    document.getElementById('shipping_address_1').value += street_number + " " + route + " " + street_address_1 ;
  }else if( ["BR", "PL", "IT","SE","AT","DE"].includes( jQuery("#shipping_country").val() ) ) {
    document.getElementById('shipping_address_1').value = route + " " + street_number + " " + street_address_1;
  } else {
    document.getElementById('shipping_address_1').value=null;
    if(premise===street_number.trim() + " " + route.trim() || premise === '') {
      document.getElementById('shipping_address_1').value = street_number + " " + route + " " + street_address_1 ;
    } else {
      document.getElementById('shipping_address_1').value = premise + " " + street_number + " " + route + " " + street_address_1;
    } 
  }

  if(result.restrict_street_line=='yes' && document.getElementById('shipping_address_2').value != null ){
    document.getElementById('shipping_address_2').value=null;
  }
  
    jQuery("#shipping_state").attr("disabled", false);
    jQuery("#shipping_postcode").attr("disabled", false);
    jQuery("#shipping_city").attr("disabled", false);
    jQuery("#shipping_state").attr("disabled", false);
    jQuery("#shipping_address_2").attr("disabled", false);
    jQuery("#shipping_address_1").attr("disabled", false);
    jQuery("#shipping_country").attr("disabled", false);
	var parser = new DOMParser();
	var doc = parser.parseFromString(place.adr_address, "text/html");
	var street_address = doc.getElementsByClassName('street-address')[0].innerHTML;
	var addr_com_length = place.address_components.length;
  if( !isNZAddress ) {
    for (var i = 0; i < addr_com_length; i++){
      if ( place.address_components[i].types[0] === 'premise' || place.address_components[i].types[0] === 'subpremise' || place.address_components[i].types[0] === 'street_address' || place.address_components[i].types[0] === 'route' ) {
        if(street_address.includes(place.address_components[i]['short_name'])){
          street_address = street_address.replace(place.address_components[i]['short_name'],place.address_components[i]['long_name']);
        }
      }
    }
    if(is_sub_premise){
      document.getElementById('shipping_address_1').value = sub_premise + '/' + street_address;
    }
  }
  jQuery( 'body' ).trigger( 'update_checkout' );
}
// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
function shipping_geolocate() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      var geolocation = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };
      var circle = new google.maps.Circle({
        center: geolocation,
        radius: position.coords.accuracy
      });
      shipping_autocomplete.setBounds(circle.getBounds());
    });
  }
}