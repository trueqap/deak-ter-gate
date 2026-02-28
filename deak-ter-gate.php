<?php
/**
 * Plugin Name: Deák Tér Gate
 * Description: Automatikusan sikertelenre állítja a rendeléseket, ha a számlázási vagy szállítási cím "Deak Ferenc ter 1" vagy hasonló variáció.
 * Version: 1.3.0
 * Author: TrueQAP
 * Author URI: https://github.com/trueqap/deak-ter-gate
 * Requires Plugins: woocommerce
 * Text Domain: deak-ter-gate
 */

defined( 'ABSPATH' ) || exit;

// COD gateway filter: megakadályozza, hogy a rendelés processing-re kerüljön.
add_filter( 'woocommerce_cod_process_payment_order_status', 'dtg_block_cod_status', 10, 2 );

// Safety net: bármilyen úton processing-re kerülő rendeléseket is elkap.
add_action( 'woocommerce_order_status_processing', 'dtg_check_order_address_by_id', 10, 1 );

/**
 * Ellenőrzi, hogy a cím blokkolt-e.
 */
function dtg_is_blocked_address( $order ) {
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
				return $address;
			}
		}
	}

	return false;
}

/**
 * COD fizetésnél failed-re irányítja a rendelést processing helyett.
 */
function dtg_block_cod_status( $status, $order ) {
	$matched = dtg_is_blocked_address( $order );

	if ( $matched ) {
		$order->add_order_note(
			sprintf(
				'[Deák Tér Gate] Blokkolt cím észlelve: "%s". Rendelés failed státuszra állítva.',
				$matched
			)
		);
		return 'failed';
	}

	return $status;
}

/**
 * Safety net: ha bármilyen úton processing-re kerül, utólag lekapja.
 */
function dtg_check_order_address_by_id( $order_id ) {
	$order = wc_get_order( $order_id );

	if ( ! $order ) {
		return;
	}

	$matched = dtg_is_blocked_address( $order );

	if ( $matched ) {
		$order->add_order_note(
			sprintf(
				'[Deák Tér Gate] Blokkolt cím észlelve: "%s". Rendelés failed státuszra állítva.',
				$matched
			)
		);
		$order->update_status( 'failed', __( 'Automatikusan elutasítva: blokkolt cím (Deák Ferenc tér 1).', 'deak-ter-gate' ) );
	}
}
