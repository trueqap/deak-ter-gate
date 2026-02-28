<?php
/**
 * Plugin Name: Deák Tér Gate
 * Description: Automatikusan sikertelenre állítja a rendeléseket, ha a számlázási vagy szállítási cím "Deak Ferenc ter 1" vagy hasonló variáció.
 * Version: 1.2.0
 * Author: TrueQAP
 * Author URI: https://github.com/trueqap/deak-ter-gate
 * Requires Plugins: woocommerce
 * Text Domain: deak-ter-gate
 */

defined( 'ABSPATH' ) || exit;

// Classic checkout.
add_action( 'woocommerce_checkout_order_created', 'dtg_check_order_address', 10, 1 );

// Blocks / Store API checkout.
add_action( 'woocommerce_store_api_checkout_order_processed', 'dtg_check_order_address', 10, 1 );

// Safety net: COD és egyéb gateway-ek felülírhatják a státuszt a fenti hookok után,
// ezért processing-re váltáskor újra ellenőrzünk.
add_action( 'woocommerce_order_status_processing', 'dtg_check_order_address_by_id', 10, 1 );

/**
 * Rendelés objektumból ellenőriz.
 */
function dtg_check_order_address( $order ) {
	if ( 'failed' === $order->get_status() ) {
		return;
	}

	$blocked_patterns = array(
		'deak ferenc ter 1',
		'deák ferenc tér 1',
		'deak ferenc tér 1',
		'deák ferenc ter 1',
	);

	$addresses_to_check = array(
		$order->get_billing_address_1(),
		$order->get_shipping_address_1(),
	);

	foreach ( $addresses_to_check as $address ) {
		$normalized = mb_strtolower( trim( $address ) );

		foreach ( $blocked_patterns as $pattern ) {
			if ( str_contains( $normalized, $pattern ) ) {
				$matched_address = $address;
				$order->add_order_note(
					sprintf(
						'[Deák Tér Gate] Blokkolt cím észlelve: "%s". Rendelés failed státuszra állítva.',
						$matched_address
					)
				);
				$order->update_status( 'failed', __( 'Automatikusan elutasítva: blokkolt cím (Deák Ferenc tér 1).', 'deak-ter-gate' ) );
				return;
			}
		}
	}
}

/**
 * Order ID-ből ellenőriz (status change hook).
 */
function dtg_check_order_address_by_id( $order_id ) {
	$order = wc_get_order( $order_id );

	if ( ! $order ) {
		return;
	}

	dtg_check_order_address( $order );
}
