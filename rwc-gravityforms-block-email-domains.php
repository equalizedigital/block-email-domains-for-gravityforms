<?php
/*
Plugin Name: Gravity Forms Block Email Domains
Plugin URI: http://roadwarriorcreative.com
Description: Block email domains from gmail, yahoo, hotmail, me, outlook, icloud and comcast.
Author: Road Warrior Creative
Version: 1.0.0
Author URI: http://roadwarriorcreative.com
*/

defined( 'ABSPATH' ) or die( 'No direct file access allowed!' );

/*
Define Custiom Setting
*/
add_action( 'gform_field_advanced_settings', 'rwc_advanced_settings', 10, 2 );
function rwc_advanced_settings( $position, $form_id ) {
 
	//create settings on position 425 (right after visibility)
	if ( $position == 425 ) {
		?>
		<li class="block_domains_setting field_setting">
				<!-- <label for="field_admin_label">
						<?php esc_html_e( 'Block Domains', 'gravityforms' ); ?>
						
				</label> -->
				<input type="checkbox" id="field_block_domains_value" onclick="SetFieldProperty('blockDomains', this.checked);" /> Block Personal Email Domains <?php gform_tooltip( 'form_field_block_domains_value' ) ?>
		</li>
		<?php
	}

}

/*
Add Custom Setting to Email Fields
*/
add_action( 'gform_editor_js', 'editor_script' );
function editor_script(){
		?>
		<script type='text/javascript'>
				//adding setting to fields of type "text"
				fieldSettings.email += ', .block_domains_setting';
 
				//binding to the load field settings event to initialize the checkbox
				jQuery(document).bind('gform_load_field_settings', function(event, field, form){
						jQuery('#field_block_domains_value').attr('checked', field.blockDomains == true);
				});
		</script>
		<?php
}

/*
Custom Setting Tooltip
*/
add_filter( 'gform_tooltips', 'add_block_domains_tooltips' );
function add_block_domains_tooltips( $tooltips ) {
	 $tooltips['form_field_block_domains_value'] = "<h6>Block Domains</h6>Check this box to block personal email domains: gmail, yahoo, hotmail, me, outlook, icloud and comcast.";
	 return $tooltips;
}

/*
Custom Email Field Validation
*/
add_filter( 'gform_field_validation', function ( $result, $value, $form, $field ) {

	if ( $field->type == 'email' ) {

		$blocked_domains = array('gmail.com','yahoo.com','hotmail.com','me.com','outlook.com','icloud.com','comcast.net');

		$domain = substr(strrchr($value, "@"), 1);

		if($field["blockDomains"] == true && in_array($domain, $blocked_domains)){
			$result['is_valid'] = false;
			$result['message']  = empty( $field->errorMessage ) ? __( 'Sorry, '.$domain.' emails are not accepted on this form. Please provide a work email address and try again.', 'gravityforms' ) : $field->errorMessage;
		}else{
			$result['is_valid'] = true;
			$result['message']  = '';
		}

	}

	return $result;
}, 10, 4 );

?>