<?php
/**
 *
 * File for popup html.
 *
 * @package Address Validation & Google Address Auto Complete Plugin for WooCommerce
 */

if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
	$device = preg_match( '/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i', sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) );
}

if ( $device ) {
	echo "<div id='xa_myModal' class='xa-modal' style='z-index:9999999 !important'>
				    <div class='xa-modal-content' >
				      	<div class='xa-container xa-white'>
                            <span class='xa-closebtn'>&times;</span>
					      	<table class='xa-popup' border='0'>
								<tr>
								    <th><center><bold>Original Address</bold></center></th>
								</tr>
                                <tr>
                                    <td id='original' class='xa-center'></td>
                                </tr>
                                <tr>
                                    <td><center><button class='xa-btn xa-white xa-round-large xa-border use_original'>Place Order with Original Address</button></center></td>
                                </tr>
                                <tr>
                                    <th id='right_title'></th>
                                </tr>
                                <tr>
                                    <td id='validated' class='xa-center'></td>
                                </tr>
                                <tr>
                                    <td id='right_button'></td>
                                </tr>
                            </table>
				    	</div>
				  	</div>
				</div>";
} else {
	echo "<div id='xa_myModal' class='xa-modal' style='z-index:9999999 !important'>
                    <div class='xa-modal-content'>
                        <div class='xa-container xa-white '>
                            <div class='xa-container xa-white'>
                                <span class='xa-closebtn'>&times;</span>
                            </div>
                            <table class='xa-popup' border='0'>
                                <tr>
                                    <th><center><bold>Original Address</bold></center></th>
                                    <th id='right_title'></th>
                                </tr>
                                <tr>
                                    <td id='original' class='xa-center'></td>
                                    <td id='validated' class='xa-center'></td>
                                </tr>
                                <tr>
                                    <td><center><button class='xa-btn xa-white xa-round-large xa-border use_original'>Place Order with Original Address</button></center></td>
                                    <td id='right_button'></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>";
}
