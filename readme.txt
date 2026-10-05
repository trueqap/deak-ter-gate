=== Deák Tér Gate ===
Contributors: trueqap
Tags: woocommerce, spam, checkout, orders
Requires at least: 6.0
Requires PHP: 8.0
Stable tag: 1.4.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Megakadályozza a „Deák Ferenc tér 1” (és variációi) címre leadott spam WooCommerce-rendeléseket.

== Description ==

A checkout elutasítja a blokkolt címre leadott rendelést; ami mégis átjut, az sikertelen (`failed`) státuszt kap. Három rétegben figyel: checkout-validáció (classic és Blocks / Store API), a fizetési módok (utánvét, banki átutalás, csekk) státusz-filterei, és egy safety net a `processing` / `on-hold` státuszváltásra. A számlázási és a szállítási cím első sorát vizsgálja.

Követelmény: WooCommerce 8.0+.

== Installation ==

1. Töltsd le a `deak-ter-gate-<verzió>.zip` fájlt a GitHub legfrissebb release-éből.
2. WordPress admin → Bővítmények → Új hozzáadása → Bővítmény feltöltése.
3. Aktiváld.

== Changelog ==

= 1.4.0 =
* New: Checkout-validáció: a blokkolt címre leadott rendelés létre sem jön (nincs rendelés-levél, nincs készletfoglalás). Classic checkout: `woocommerce_after_checkout_validation`; Blocks / Store API: `woocommerce_store_api_checkout_update_order_from_request` (csak leadáskor, POST).
* New: A banki átutalás (`bacs`) és a csekk (`cheque`) státusz-filtere is `failed`-re irányít (eddig csak az utánvét).
* Change: Rugalmasabb címfelismerés: ékezetek nélkül, kisbetűvel, az írásjeleket szóközzé alakítva hasonlít (`Deák F. tér 1`, `Deák Ferenc-tér 1`, `Deak Ferenc ter 1.`).
* Fix: A banki átutalással leadott spam rendelések átcsúsztak, mert `on-hold` státuszba kerültek: a safety net a `woocommerce_order_status_on-hold`-ot is figyeli.
* Fix: A `Deák Ferenc tér 12` már nem minősül blokkolt címnek.

= 1.3.0 =
* Fix: Az utánvétes (COD) fizetés visszaállította a `failed` rendelést `processing`-re: a `woocommerce_cod_process_payment_order_status` filter közvetlenül `failed`-re irányítja a blokkolt rendelést, a `woocommerce_order_status_processing` safety net pedig minden más úton átcsúszót elkap.

= 1.2.0 =
* New: Rendelés-megjegyzés a blokkolt címmel.
* Fix: Az utánvétes (COD) fizetés felülírta a `failed` státuszt: a `_dtg_checked` jelölő helyett a `failed` státusz ellenőrzése véd a végtelen ciklus ellen.

= 1.1.0 =
* New: Első kiadás: a „Deák Ferenc tér 1” (és variációi) számlázási vagy szállítási címre leadott rendelés automatikusan `failed` státuszt kap. Classic checkout, Blocks / Store API checkout és egy `processing` fallback (API, admin).
