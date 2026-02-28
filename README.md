# Deák Tér Gate

WooCommerce bővítmény, amely automatikusan **sikertelenre (`failed`)** állítja a spam rendeléseket blokkolt cím alapján.

## Letöltés

**[deak-ter-gate-1.3.0.zip](https://github.com/trueqap/deak-ter-gate/releases/download/v1.3.0/deak-ter-gate-1.3.0.zip)** – közvetlenül telepíthető WordPress adminból.

## Telepítés

1. Töltsd le a zip fájlt
2. WordPress admin → **Bővítmények → Új hozzáadása → Bővítmény feltöltése**
3. Aktiváld

## Hogyan működik?

A plugin három hookon figyel, hogy semmilyen checkout útvonalat ne lehessen kikerülni:

| Hook | Mikor fut |
|------|-----------|
| `woocommerce_checkout_order_created` | Classic checkout |
| `woocommerce_store_api_checkout_order_processed` | Blocks / Store API checkout |
| `woocommerce_order_status_processing` | Fallback (API, admin, egyéb) |

Ha a számlázási vagy szállítási cím tartalmazza a blokkolt mintát (ékezetes és ékezet nélküli variációk), a rendelés azonnal `failed` státuszba kerül, és egy megjegyzés kerül a rendeléshez:

> Automatikusan elutasítva: blokkolt cím (Deák Ferenc tér 1).

## Blokkolt minták

- `deak ferenc ter 1`
- `deák ferenc tér 1`
- `deak ferenc tér 1`
- `deák ferenc ter 1`

## Követelmények

- WordPress 6.0+
- WooCommerce 8.0+
- PHP 8.0+
