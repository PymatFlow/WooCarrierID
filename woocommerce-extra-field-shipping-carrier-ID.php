<?php
/**
 * Plugin Name: Carrier ID field for shipping methods for WooCommerce
 * Plugin URI: https://www.invinciblebrands.com/
 * Description: Add a new text input field (“Carrier ID”) for the shipping methods for WooCommerce
 * Version: v 1.0
 * Author: Kazem Gheysari
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



add_action('woocommerce_init', 'Kazem_shipping_fields_filters');

function Kazem_shipping_fields_filters()
{
    $shipping_methods = WC()
        ->shipping
        ->get_shipping_methods();
    foreach ($shipping_methods as $shipping_method_id)
    {
        add_filter('woocommerce_shipping_instance_form_fields_' . $shipping_method_id->id, 'shipping_carrierID_form_add_extra_fields');
    }
}

// Define the setting of the input field
function shipping_carrierID_form_add_extra_fields($settings)
{
    $settings['carrier_id_extra_field_in_shipping'] = [
        'title' => 'Carrier ID', 
        'type' => 'text', 
        'class' => array('form-row-wide') ,
        'placeholder' => 'shipping',
        'required' => false,
        'description' => 'In this field, you can specify the Carrier ID'];
    return $settings;
}

add_action('woocommerce_thankyou', 'woocommerce_update_post_meta', 10, 1);
function woocommerce_update_post_meta($order_id)
{
    if (!$order_id)
    {
        return;
    }
    $order = wc_get_order($order_id);

    if ($order->get_status() == 'processing')
    {

        $carrier_id_value = $_POST['carrier_id_extra_field_in_shipping'];

        add_action('woocommerce_checkout_update_order_meta', function ($order_id, $posted)
        {
            update_post_meta($order_id, '_carrier_id', sanitize_text_field($carrier_id_value));
        }
        , 10, 2);
    }
}

?>
