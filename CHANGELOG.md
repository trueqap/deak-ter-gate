# Changelog

## [1.4.0] - 2026-10-05

### Added
- Checkout-validáció: a blokkolt címre leadott rendelés létre sem jön (nincs rendelés-levél, nincs készletfoglalás). Classic checkout: `woocommerce_after_checkout_validation`; Blocks / Store API: `woocommerce_store_api_checkout_update_order_from_request` (csak leadáskor, POST).
- A banki átutalás (`bacs`) és a csekk (`cheque`) státusz-filtere is `failed`-re irányít (eddig csak az utánvét).

### Changed
- Rugalmasabb címfelismerés: ékezetek nélkül, kisbetűvel, az írásjeleket szóközzé alakítva hasonlít (`Deák F. tér 1`, `Deák Ferenc-tér 1`, `Deak Ferenc ter 1.`).

### Fixed
- A banki átutalással leadott spam rendelések átcsúsztak, mert `on-hold` státuszba kerültek: a safety net a `woocommerce_order_status_on-hold`-ot is figyeli.
- A `Deák Ferenc tér 12` már nem minősül blokkolt címnek.

## [1.3.0] - 2026-02-28

### Fixed
- Az utánvétes (COD) fizetés visszaállította a `failed` rendelést `processing`-re: a `woocommerce_cod_process_payment_order_status` filter közvetlenül `failed`-re irányítja a blokkolt rendelést, a `woocommerce_order_status_processing` safety net pedig minden más úton átcsúszót elkap.

## [1.2.0] - 2026-02-28

### Added
- Rendelés-megjegyzés a blokkolt címmel.

### Fixed
- Az utánvétes (COD) fizetés felülírta a `failed` státuszt: a `_dtg_checked` jelölő helyett a `failed` státusz ellenőrzése véd a végtelen ciklus ellen.

## [1.1.0] - 2026-02-27

### Added
- Első kiadás: a „Deák Ferenc tér 1” (és variációi) számlázási vagy szállítási címre leadott rendelés automatikusan `failed` státuszt kap. Classic checkout, Blocks / Store API checkout és egy `processing` fallback (API, admin).
