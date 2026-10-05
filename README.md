# Deák Tér Gate

WooCommerce bővítmény, amely blokkolt cím alapján megakadályozza a spam rendeléseket: a checkout elutasítja őket, ami mégis átjut, az **sikertelen (`failed`)** státuszt kap.

## Letöltés

A telepíthető zip a **[legfrissebb release](https://github.com/trueqap/deak-ter-gate/releases/latest)** alatt van (`deak-ter-gate-<verzió>.zip`) – közvetlenül telepíthető WordPress adminból. A zipet a GitHub Action állítja elő minden új verzióhoz: a `main`-re pusholt verzióemelés (`Version:` a `deak-ter-gate.php` fejlécében) automatikusan taget, release-t és zipet készít.

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

## Változások

Lásd: [CHANGELOG.md](CHANGELOG.md).

## Kiadás

Új verzióhoz ugyanazt a számot írd mind a négy helyre: `deak-ter-gate.php` `Version:`, `readme.txt` `Stable tag:` és `== Changelog ==`, valamint egy új `CHANGELOG.md`-bejegyzés. A CI ellenőrzi, hogy egyeznek. A `main`-re pusholt verzióemelés után a Release workflow elkészíti a `v<verzió>` taget, a release-t és a telepíthető zipet, a release leírása pedig a verzió CHANGELOG-bejegyzése lesz.

## Követelmények

- WordPress 6.0+
- WooCommerce 8.0+
- PHP 8.0+
