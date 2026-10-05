# Deák Tér Gate

WooCommerce bővítmény, amely blokkolt cím alapján megakadályozza a spam rendeléseket: a checkout elutasítja őket, ami mégis átjut, az **sikertelen (`failed`)** státuszt kap.

## Letöltés

**[deak-ter-gate-1.4.0.zip](https://github.com/trueqap/deak-ter-gate/releases/download/v1.4.0/deak-ter-gate-1.4.0.zip)** – közvetlenül telepíthető WordPress adminból.

## Telepítés

1. Töltsd le a zip fájlt
2. WordPress admin → **Bővítmények → Új hozzáadása → Bővítmény feltöltése**
3. Aktiváld

## Hogyan működik?

A plugin három rétegben figyel, hogy semmilyen checkout útvonalat ne lehessen kikerülni:

1. **Checkout-validáció** – a rendelés létre sem jön (nincs rendelés-levél, nincs készletfoglalás), a vásárló ezt látja: *„A megadott címre nem tudunk rendelést fogadni.”*
   - Classic checkout: `woocommerce_after_checkout_validation`
   - Blocks / Store API checkout: `woocommerce_store_api_checkout_update_order_from_request` (csak a leadáskor, POST)
2. **Fizetési módok státusz-filterei** – utánvét (`cod`), banki átutalás (`bacs`) és csekk (`cheque`) esetén a rendelés `failed` státuszt kap a `processing` / `on-hold` helyett.
3. **Safety net** – ha egy rendelés bármilyen úton `processing` vagy `on-hold` státuszba kerül (API, admin, más fizetési mód), utólag `failed`-re állítja, és megjegyzést fűz hozzá:

> Automatikusan elutasítva: blokkolt cím (Deák Ferenc tér 1).

A számlázási és a szállítási cím első sorát vizsgálja.

## Blokkolt minták

A címet normalizálva hasonlítja: ékezetek nélkül, kisbetűvel, az írásjeleket szóközzé alakítva. Így ezek mind egyeznek:

- `Deak Ferenc ter 1`, `Deák Ferenc tér 1.`, `DEAK  FERENC TER 1`
- `Deák F. tér 1`, `Deák Ferenc-tér 1`, `Deák Ferenc tér 1/a`

A `Deák Ferenc tér 12` vagy a `Deák Ferenc utca 1` nem egyezik.

## Követelmények

- WordPress 6.0+
- WooCommerce 8.0+
- PHP 8.0+
