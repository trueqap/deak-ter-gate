<?php
/**
 * Plugin Name: Deák Tér Gate
 * Description: Megakadályozza a "Deak Ferenc ter 1" (és hasonló variációk) címre leadott spam rendeléseket: a checkout elutasítja őket, ami mégis átjut, az sikertelen (failed) státuszt kap.
 * Version: 1.4.0
 * Author: TrueQAP
 * Author URI: https://github.com/trueqap/deak-ter-gate
 * Requires Plugins: woocommerce
 * Text Domain: deak-ter-gate
 */

defined( 'ABSPATH' ) || exit;

// 1. réteg: a rendelés létre sem jön (nincs levél, nincs készletfoglalás).
// Classic checkout.
add_action( 'woocommerce_after_checkout_validation', 'dtg_validate_classic_checkout', 10, 2 );
// Blocks / Store API checkout.
add_action( 'woocommerce_store_api_checkout_update_order_from_request', 'dtg_validate_store_api_checkout', 10, 2 );

// 2. réteg: a fizetési módok státusz-filterei (utánvét, átutalás, csekk) failed-re irányítanak.
add_filter( 'woocommerce_cod_process_payment_order_status', 'dtg_block_payment_status', 10, 2 );
add_filter( 'woocommerce_bacs_process_payment_order_status', 'dtg_block_payment_status', 10, 2 );
add_filter( 'woocommerce_cheque_process_payment_order_status', 'dtg_block_payment_status', 10, 2 );

// 3. réteg (safety net): bármilyen úton processing / on-hold státuszba kerülő rendelést utólag lekap.
add_action( 'woocommerce_order_status_processing', 'dtg_check_order_address_by_id', 10, 1 );
add_action( 'woocommerce_order_status_on-hold', 'dtg_check_order_address_by_id', 10, 1 );

/**
 * Egy címsorról eldönti, hogy blokkolt-e.
 *
 * Normalizálás: ékezetek le, kisbetű, minden nem betű/szám karakter szóköz.
 * Így a "Deák Ferenc tér 1.", a "DEAK  FERENC TER 1" és a "Deák F. tér 1" is egyezik,
 * a "Deák Ferenc tér 12" viszont nem.
 */
function dtg_address_line_is_blocked( $address ) {
	$normalized = strtolower( remove_accents( (string) $address ) );
	$normalized = trim( preg_replace( '/[^a-z0-9]+/', ' ', $normalized ) );

	return 1 === preg_match( '/\bdeak (ferenc|f) ter 1\b/', $normalized );
}

/**
 * Az első blokkolt címsor a felsoroltak közül, vagy false.
 */
function dtg_find_blocked_address( array $addresses ) {
	foreach ( $addresses as $address ) {
		if ( dtg_address_line_is_blocked( $address ) ) {
			return $address;
		}
	}

	return false;
}

/**
 * Ellenőrzi, hogy a rendelés címe blokkolt-e.
 */
function dtg_is_blocked_address( $order ) {
	return dtg_find_blocked_address(
		array(
			$order->get_billing_address_1(),
			$order->get_shipping_address_1(),
		)
	);
}

/**
 * A vásárlónak mutatott hibaüzenet.
 */
function dtg_rejection_message() {
	return __( 'A megadott címre nem tudunk rendelést fogadni.', 'deak-ter-gate' );
}

/**
 * Classic checkout: validációs hibát ad, így a rendelés nem jön létre.
 */
function dtg_validate_classic_checkout( $data, $errors ) {
	$matched = dtg_find_blocked_address(
		array(
			$data['billing_address_1'] ?? '',
			$data['shipping_address_1'] ?? '',
		)
	);

	if ( $matched ) {
		$errors->add( 'dtg_blocked_address', dtg_rejection_message() );
	}
}

/**
 * Blocks / Store API checkout: a rendelés leadásakor (POST) kivétellel elutasít.
 * A címmezők szerkesztése közbeni PATCH-kéréseket nem bántja.
 */
function dtg_validate_store_api_checkout( $order, $request ) {
	if ( 'POST' !== $request->get_method() ) {
		return;
	}

	if ( dtg_is_blocked_address( $order ) ) {
		throw new \Automattic\WooCommerce\StoreApi\Exceptions\RouteException(
			'dtg_blocked_address',
			dtg_rejection_message(),
			400
		);
	}
}

/**
 * Fizetési mód státusz-filter: blokkolt címnél failed-re irányítja a rendelést.
 */
function dtg_block_payment_status( $status, $order ) {
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
 * Safety net: ha bármilyen úton processing / on-hold státuszba kerül, utólag lekapja.
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
