# Audit performance — https://prod.la-maison-du-dos.com/

Profil : **desktop** · 2026-09-30T06:26:51.116Z

## Synthèse

| Indicateur | Valeur | Objectif |
|---|---|---|
| TTFB (réponse serveur) | 3833 ms | < 800 ms |
| First Contentful Paint | 6944 ms | < 1 800 ms |
| Largest Contentful Paint | 6944 ms | < 2 500 ms |
| Total Blocking Time (approx.) | 2575 ms | < 200 ms |
| Cumulative Layout Shift | 0.001 | < 0,1 |
| Événement load | 10354 ms | — |
| Requêtes | 241 | < 50 |
| Poids total transféré | 1952.5 Ko | < 1 500 Ko |
| HTML (compressé) | 116.6 Ko | < 60 Ko |
| Nœuds DOM / profondeur | 2438 / 35 | < 1 500 / < 32 |

Élément LCP : `h1 `

Générateurs : WordPress 7.1.2 · Site Kit by Google 1.188.0 · Elementor 4.3.2; features: e_font_icon_svg, additional_custom_breakpoints; settings: css_print_method-external, google_font-enabled, font_display-swap

## Poids par plugin / thème / service tiers

| Source | Req. | Total | JS | CSS | Images | Polices |
|---|---:|---:|---:|---:|---:|---:|
| `tiers:www.googletagmanager.com` | 4 | 725.2 Ko | 725.2 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `uploads` | 52 | 304.6 Ko | 0.0 Ko | 0.0 Ko | 304.6 Ko | 0.0 Ko |
| `tiers:my.zadarma.com` | 9 | 133.0 Ko | 122.3 Ko | 10.7 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:accounts.google.com` | 2 | 101.1 Ko | 99.7 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:royal-elementor-addons` | 10 | 84.3 Ko | 2.1 Ko | 82.3 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:cdn-cookieyes.com` | 9 | 83.6 Ko | 67.7 Ko | 0.0 Ko | 5.2 Ko | 0.0 Ko |
| `theme:howes` | 2 | 65.1 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 65.1 Ko |
| `tiers:widget.xpay.sh` | 9 | 63.8 Ko | 63.8 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:wp-user-avatar` | 6 | 34.0 Ko | 7.1 Ko | 26.9 Ko | 0.0 Ko | 0.0 Ko |
| `wp-core` | 44 | 33.4 Ko | 33.4 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `elementor:generated-css` | 20 | 32.9 Ko | 0.0 Ko | 32.9 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:challenges.cloudflare.com` | 1 | 27.9 Ko | 27.9 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:scripts.clarity.ms` | 1 | 25.6 Ko | 25.6 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:nehl6uu58j.execute-api.us-east-1.amazonaws.com` | 1 | 23.2 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:alma-gateway-for-woocommerce` | 5 | 22.4 Ko | 0.3 Ko | 22.1 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:woocommerce` | 9 | 17.8 Ko | 1.8 Ko | 16.1 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:ag-core` | 4 | 12.9 Ko | 3.4 Ko | 0.0 Ko | 7.7 Ko | 0.0 Ko |
| `plugin:woo-guaranteed-reviews-company` | 3 | 11.3 Ko | 0.3 Ko | 5.1 Ko | 5.9 Ko | 0.0 Ko |
| `plugin:prisna-wp-translate` | 4 | 5.5 Ko | 0.6 Ko | 4.9 Ko | 0.0 Ko | 0.0 Ko |
| `theme:royal-elementor-kit` | 1 | 4.9 Ko | 0.0 Ko | 4.9 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:elementor` | 10 | 4.2 Ko | 1.2 Ko | 3.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:cdn.jsdelivr.net` | 1 | 3.9 Ko | 0.0 Ko | 3.9 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:google-site-kit` | 5 | 2.9 Ko | 2.9 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:wpb-accordion-menu-or-category` | 4 | 2.9 Ko | 0.9 Ko | 2.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:fonts.googleapis.com` | 1 | 2.6 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `wp-core:jquery` | 5 | 1.5 Ko | 1.5 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:j.clarity.ms` | 4 | 1.3 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:www.clarity.ms` | 1 | 1.2 Ko | 1.2 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:c.clarity.ms` | 1 | 1.0 Ko | 0.0 Ko | 0.0 Ko | 1.0 Ko | 0.0 Ko |
| `tiers:p.typekit.net` | 1 | 0.4 Ko | 0.0 Ko | 0.4 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:log.cookieyes.com` | 1 | 0.3 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:flexible-shipping` | 1 | 0.3 Ko | 0.0 Ko | 0.3 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:woocommerce-google-adwords-conversion-tracking-tag` | 1 | 0.3 Ko | 0.3 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:agentic-commerce-for-woocommerce` | 1 | 0.3 Ko | 0.3 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:wp-consent-api` | 1 | 0.3 Ko | 0.3 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:agent-commerce.xpay.sh` | 1 | 0.2 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:pagead2.googlesyndication.com` | 2 | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:www.google-analytics.com` | 3 | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |

## Ressources bloquant le rendu (dans le `<head>`)

### JavaScript synchrone (15)

- `wp-core:jquery` — https://prod.la-maison-du-dos.com/wp-includes/js/jquery/jquery.min.js?ver=3.7.1
- `wp-core:jquery` — https://prod.la-maison-du-dos.com/wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1
- `wp-core` — https://prod.la-maison-du-dos.com/wp-includes/js/dist/hooks.min.js?ver=f0f188028580e8dc1255
- `plugin:woocommerce` — https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/js/jquery-blockui/jquery.blockUI.min.js?ver=2.7.0-wc.11.1.2
- `plugin:woocommerce` — https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/js/js-cookie/js.cookie.min.js?ver=2.1.4-wc.11.1.2
- `plugin:wp-user-avatar` — https://prod.la-maison-du-dos.com/wp-content/plugins/wp-user-avatar/assets/flatpickr/flatpickr.min.js?ver=4.17.5
- `plugin:wp-user-avatar` — https://prod.la-maison-du-dos.com/wp-content/plugins/wp-user-avatar/assets/select2/select2.min.js?ver=4.17.5
- `plugin:royal-elementor-addons` — https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/js/lib/perfect-scrollbar/perfect-scrollbar.min.js?ver=0.4.9
- `plugin:woocommerce-google-adwords-conversion-tracking-tag` — https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce-google-adwords-conversion-tracking-tag/js/public/free/pmw-public.p1.min.js?ver=1.69.1
- `tiers:widget.xpay.sh` — https://widget.xpay.sh/widget/v1/shell/runtime.js
- `tiers:widget.xpay.sh` — https://widget.xpay.sh/widget/v1/shell/features/fab.js
- `tiers:widget.xpay.sh` — https://widget.xpay.sh/widget/v1/shell/features/pill.js
- `tiers:widget.xpay.sh` — https://widget.xpay.sh/widget/v1/shell/features/scroll-collapse.js
- `tiers:widget.xpay.sh` — https://widget.xpay.sh/widget/v1/shell/features/scrim.js
- `tiers:widget.xpay.sh` — https://widget.xpay.sh/widget/v1/shell/features/chip-stack.js

### CSS (37)

- `tiers:cdn.jsdelivr.net` — https://cdn.jsdelivr.net/npm/@alma/widgets@4.x.x/dist/widgets.min.css?ver=6.7.0
- `plugin:alma-gateway-for-woocommerce` — https://prod.la-maison-du-dos.com/wp-content/plugins/alma-gateway-for-woocommerce/assets/css/frontend/alma-widget.css?ver=6.7.0
- `plugin:alma-gateway-for-woocommerce` — https://prod.la-maison-du-dos.com/wp-content/plugins/alma-gateway-for-woocommerce/assets/css/frontend/alma-checkout.css?ver=6.7.0
- `plugin:alma-gateway-for-woocommerce` — https://prod.la-maison-du-dos.com/wp-content/plugins/alma-gateway-for-woocommerce/build/alma-gateway-block/alma-gateway-block.css?ver=6.7.0
- `plugin:alma-gateway-for-woocommerce` — https://prod.la-maison-du-dos.com/wp-content/plugins/alma-gateway-for-woocommerce/build/alma-gateway-block/style-alma-gateway-block.css?ver=6.7.0
- `plugin:wpb-accordion-menu-or-category` — https://prod.la-maison-du-dos.com/wp-content/plugins/wpb-accordion-menu-or-category/assets/css/wpb_wmca_style.css?ver=1.0
- `plugin:prisna-wp-translate` — https://prod.la-maison-du-dos.com/wp-content/plugins/prisna-wp-translate/styles/blocks.css?ver=1.17.3
- `plugin:prisna-wp-translate` — https://prod.la-maison-du-dos.com/wp-content/plugins/prisna-wp-translate/styles/translator-s.css?ver=1.17.3
- `plugin:woocommerce` — https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/css/woocommerce-layout.css?ver=11.1.2
- `plugin:woocommerce` — https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/css/woocommerce.css?ver=11.1.2
- `plugin:wp-user-avatar` — https://prod.la-maison-du-dos.com/wp-content/plugins/wp-user-avatar/assets/css/frontend.min.css?ver=4.17.5
- `plugin:wp-user-avatar` — https://prod.la-maison-du-dos.com/wp-content/plugins/wp-user-avatar/assets/flatpickr/flatpickr.min.css?ver=4.17.5
- `plugin:wp-user-avatar` — https://prod.la-maison-du-dos.com/wp-content/plugins/wp-user-avatar/assets/select2/select2.min.css?ver=7.1.2
- `tiers:fonts.googleapis.com` — https://fonts.googleapis.com/css?family=Open+Sans:600,400,400i|Oswald:700&ver=7.1.2
- `plugin:woo-guaranteed-reviews-company` — https://prod.la-maison-du-dos.com/wp-content/plugins/woo-guaranteed-reviews-company/assets/css/main.css?ver=1.3.0
- `elementor:generated-css` — https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/custom-frontend.min.css?ver=1790748725
- `elementor:generated-css` — https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-5318.css?ver=1790749030
- `elementor:generated-css` — https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-209.css?ver=1790748721
- `plugin:elementor` — https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/css/widget-heading.min.css?ver=4.3.2
- `plugin:elementor` — https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/css/widget-image.min.css?ver=4.3.2
- `elementor:generated-css` — https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-212.css?ver=1790748722
- `theme:royal-elementor-kit` — https://prod.la-maison-du-dos.com/wp-content/themes/royal-elementor-kit/style.css?ver=1.0
- `plugin:flexible-shipping` — https://prod.la-maison-du-dos.com/wp-content/plugins/flexible-shipping/assets/dist/css/free-shipping.css?ver=6.13.0.34
- `elementor:generated-css` — https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-6.css?ver=1790748724
- `plugin:elementor` — https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/css/widget-spacer.min.css?ver=4.3.2
- `elementor:generated-css` — https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/custom-widget-image-box.min.css?ver=1790748725
- `plugin:elementor` — https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/lib/swiper/v8/css/swiper.min.css?ver=8.4.5
- `plugin:elementor` — https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/css/conditionals/e-swiper.min.css?ver=4.3.2
- `elementor:generated-css` — https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/custom-widget-image-gallery.min.css?ver=1790748725
- `plugin:royal-elementor-addons` — https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/css/lib/animations/wpr-link-animations.min.css?ver=1.7.1068
- `plugin:royal-elementor-addons` — https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/css/lib/animations/text-animations.min.css?ver=1.7.1068
- `plugin:royal-elementor-addons` — https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/css/frontend.min.css?ver=1.7.1068
- `plugin:elementor` — https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/lib/font-awesome/css/all.min.css?ver=1.7.1068
- `elementor:generated-css` — https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/css/poppins.css?ver=1742312054
- `elementor:generated-css` — https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/css/roboto.css?ver=1742292385
- `elementor:generated-css` — https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/css/robotoslab.css?ver=1742292388
- `tiers:accounts.google.com` — https://accounts.google.com/gsi/style

## Code chargé mais inutilisé sur cette page

| Type | Source | Taille | Inutilisé | Fichier |
|---|---|---:|---:|---|
| CSS | `plugin:royal-elementor-addons` | 438.0 Ko | 98 % | /wp-content/plugins/royal-elementor-addons/assets/css/frontend.min.css |
| JS | `tiers:www.googletagmanager.com` | 585.6 Ko | 50 % | https://www.googletagmanager.com/gtag/js |
| JS | `tiers:www.googletagmanager.com` | 566.0 Ko | 48 % | https://www.googletagmanager.com/gtag/js |
| JS | `tiers:www.googletagmanager.com` | 575.4 Ko | 46 % | https://www.googletagmanager.com/gtag/js |
| JS | `tiers:www.googletagmanager.com` | 467.8 Ko | 54 % | https://www.googletagmanager.com/gtm.js |
| JS | `tiers:my.zadarma.com` | 276.0 Ko | 88 % | https://my.zadarma.com/callmewidget/v2.0.9/jssip.min.js |
| JS | `tiers:accounts.google.com` | 267.4 Ko | 82 % | https://accounts.google.com/gsi/client |
| CSS | `elementor:generated-css` | 105.5 Ko | 100 % | /wp-content/uploads/elementor/google-fonts/css/roboto.css |
| CSS | `plugin:wp-user-avatar` | 98.5 Ko | 100 % | /wp-content/plugins/wp-user-avatar/assets/css/frontend.min.css |
| CSS | `plugin:woocommerce` | 90.0 Ko | 96 % | /wp-content/plugins/woocommerce/assets/css/woocommerce.css |
| CSS | `plugin:alma-gateway-for-woocommerce` | 87.0 Ko | 95 % | /wp-content/plugins/alma-gateway-for-woocommerce/build/alma-gateway-block/style-alma-gateway-block.css |
| JS | `tiers:challenges.cloudflare.com` | 84.7 Ko | 90 % | https://challenges.cloudflare.com/turnstile/v0/api.js |
| JS | `tiers:cdn-cookieyes.com` | 97.0 Ko | 53 % | https://cdn-cookieyes.com/client_data/0ea5729425dc239df232d1a8/script.js |
| JS | `tiers:my.zadarma.com` | 87.4 Ko | 53 % | https://my.zadarma.com/callbackWidget/js/jquery-3.5.1.min.js |
| JS | `tiers:cdn-cookieyes.com` | 99.7 Ko | 46 % | https://cdn-cookieyes.com/client_data/0ea5729425dc239df232d1a8/banner.js |
| CSS | `elementor:generated-css` | 53.6 Ko | 84 % | /wp-content/uploads/elementor/css/custom-frontend.min.css |
| JS | `tiers:scripts.clarity.ms` | 73.3 Ko | 46 % | https://scripts.clarity.ms/0.8.70/clarity.js |
| CSS | `plugin:prisna-wp-translate` | 31.7 Ko | 100 % | /wp-content/plugins/prisna-wp-translate/styles/translator-s.css |
| CSS | `tiers:my.zadarma.com` | 28.6 Ko | 100 % | https://my.zadarma.com/callmewidget/v2.0.9/style.min.css |
| JS | `tiers:my.zadarma.com` | 29.4 Ko | 93 % | https://my.zadarma.com/callbackWidget/js/combine.min.js |
| JS | `tiers:my.zadarma.com` | 23.8 Ko | 99 % | https://my.zadarma.com/callmewidget/v2.0.9/widget.min.js |
| JS | `plugin:wp-user-avatar` | 22.1 Ko | 100 % | /wp-content/plugins/wp-user-avatar/assets/js/frontend.min.js |
| CSS | `elementor:generated-css` | 30.2 Ko | 73 % | /wp-content/uploads/elementor/css/post-209.css |
| CSS | `site` | 30.2 Ko | 73 % | / |
| JS | `tiers:my.zadarma.com` | 24.3 Ko | 86 % | https://my.zadarma.com/callbackWidget/js/main.min.js |
| CSS | `tiers:fonts.googleapis.com` | 18.9 Ko | 100 % | https://fonts.googleapis.com/css |
| JS | `tiers:widget.xpay.sh` | 40.2 Ko | 44 % | https://widget.xpay.sh/v1/storefront.js |
| CSS | `plugin:wp-user-avatar` | 14.6 Ko | 100 % | /wp-content/plugins/wp-user-avatar/assets/select2/select2.min.css |
| CSS | `plugin:woo-guaranteed-reviews-company` | 15.4 Ko | 94 % | /wp-content/plugins/woo-guaranteed-reviews-company/assets/css/main.css |
| CSS | `plugin:royal-elementor-addons` | 13.9 Ko | 100 % | /wp-content/plugins/royal-elementor-addons/assets/css/lib/animations/text-animations.min.css |
| CSS | `tiers:cdn.jsdelivr.net` | 13.2 Ko | 96 % | https://cdn.jsdelivr.net/npm/@alma/widgets@4.x.x/dist/widgets.min.css |
| CSS | `plugin:wp-user-avatar` | 12.6 Ko | 100 % | /wp-content/plugins/wp-user-avatar/assets/flatpickr/flatpickr.min.css |
| CSS | `theme:royal-elementor-kit` | 13.3 Ko | 84 % | /wp-content/themes/royal-elementor-kit/style.css |
| JS | `tiers:my.zadarma.com` | 20.7 Ko | 53 % | https://my.zadarma.com/callmewidget/v2.0.9/detectWebRTC.min.js |
| JS | `wp-core` | 12.9 Ko | 81 % | /wp-includes/js/dist/dom.min.js |
| JS | `tiers:widget.xpay.sh` | 28.0 Ko | 34 % | https://widget.xpay.sh/widget/v1/track.js |
| CSS | `elementor:generated-css` | 26.3 Ko | 35 % | /wp-content/uploads/elementor/css/post-5318.css |
| CSS | `elementor:generated-css` | 10.0 Ko | 88 % | /wp-content/uploads/elementor/css/custom-widget-icon-list.min.css |
| JS | `tiers:widget.xpay.sh` | 25.2 Ko | 35 % | https://widget.xpay.sh/widget/v1/shell/features/pill.js |
| CSS | `plugin:wpb-accordion-menu-or-category` | 7.8 Ko | 100 % | /wp-content/plugins/wpb-accordion-menu-or-category/assets/css/wpb_wmca_style.css |
| JS | `plugin:ag-core` | 10.5 Ko | 71 % | https://www.societe-des-avis-garantis.fr/wp-content/plugins/ag-core/widgets/JsWidget.js |
| CSS | `site` | 19.2 Ko | 38 % | / |
| CSS | `site` | 5.6 Ko | 99 % | / |
| CSS | `plugin:royal-elementor-addons` | 5.2 Ko | 99 % | /wp-content/plugins/royal-elementor-addons/assets/css/lib/animations/wpr-link-animations.min.css |
| JS | `wp-core` | 5.6 Ko | 87 % | /wp-includes/js/dist/autop.min.js |
| CSS | `site` | 9.4 Ko | 47 % | / |
| CSS | `site` | 13.2 Ko | 33 % | / |
| CSS | `elementor:generated-css` | 13.2 Ko | 33 % | /wp-content/uploads/elementor/css/post-12333.css |
| JS | `wp-core` | 6.8 Ko | 64 % | /wp-includes/js/dist/style-engine.min.js |
| JS | `site` | 3.8 Ko | 94 % | / |
| CSS | `plugin:alma-gateway-for-woocommerce` | 3.0 Ko | 100 % | /wp-content/plugins/alma-gateway-for-woocommerce/build/alma-gateway-block/alma-gateway-block.css |
| JS | `tiers:widget.xpay.sh` | 13.0 Ko | 22 % | https://widget.xpay.sh/widget/v1/shell/features/chip-stack.js |
| JS | `tiers:widget.xpay.sh` | 9.2 Ko | 29 % | https://widget.xpay.sh/widget/v1/shell/runtime.js |
| JS | `wp-core` | 5.0 Ko | 51 % | /wp-includes/js/dist/hooks.min.js |
| JS | `wp-core` | 22.2 Ko | 11 % | /wp-includes/js/wp-emoji-release.min.js |
| CSS | `site` | 9.5 Ko | 25 % | / |
| JS | `plugin:google-site-kit` | 2.6 Ko | 61 % | /wp-content/plugins/google-site-kit/dist/assets/js/sign-in-with-google-5a9e012872c712a23a12.js |
| JS | `wp-core` | 3.0 Ko | 50 % | /wp-includes/js/wp-emoji-loader.min.js |
| CSS | `site` | 4.7 Ko | 30 % | / |
| CSS | `site` | 3.6 Ko | 31 % | / |
| CSS | `elementor:generated-css` | 3.6 Ko | 31 % | /wp-content/uploads/elementor/css/post-12296.css |
| CSS | `site` | 3.6 Ko | 31 % | / |
| JS | `wp-core` | 2.0 Ko | 53 % | /wp-includes/js/dist/primitives.min.js |
| JS | `tiers:widget.xpay.sh` | 6.1 Ko | 14 % | https://widget.xpay.sh/widget/v1/shell/features/fab.js |
| JS | `site` | 2.2 Ko | 12 % | / |
| JS | `site` | 2.1 Ko | 0 % | / |

## Toutes les requêtes (triées par poids)

| Poids | Durée | Source | URL |
|---:|---:|---|---|
| 191.4 Ko | 2842 ms | `tiers:www.googletagmanager.com` | https://www.googletagmanager.com/gtag/js?id=G-HH41XH2SV1 |
| 187.3 Ko | 178 ms | `tiers:www.googletagmanager.com` | https://www.googletagmanager.com/gtag/js?id=AW-969053143&cx=c&gtm=4e69s1 |
| 186.7 Ko | 2675 ms | `tiers:www.googletagmanager.com` | https://www.googletagmanager.com/gtag/js?id=G-VJSQ20QJ4W |
| 159.8 Ko | 825 ms | `tiers:www.googletagmanager.com` | https://www.googletagmanager.com/gtm.js?id=GTM-PF26W689 |
| 99.7 Ko | 2600 ms | `tiers:accounts.google.com` | https://accounts.google.com/gsi/client |
| 78.6 Ko | 2069 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/css/frontend.min.css?ver=1.… |
| 63.0 Ko | 1283 ms | `tiers:my.zadarma.com` | https://my.zadarma.com/callmewidget/v2.0.9/jssip.min.js |
| 34.3 Ko | 169 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/client_data/0ea5729425dc239df232d1a8/script.js |
| 33.4 Ko | 423 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/client_data/0ea5729425dc239df232d1a8/banner.js |
| 33.2 Ko | 274 ms | `theme:howes` | https://www.societe-des-avis-garantis.fr/wp-content/themes/howes/fonts/Oswald-Bold.woff2 |
| 31.9 Ko | 406 ms | `theme:howes` | https://www.societe-des-avis-garantis.fr/wp-content/themes/howes/fonts/Oswald-Regular.woff2 |
| 30.7 Ko | 1398 ms | `tiers:my.zadarma.com` | https://my.zadarma.com/callbackWidget/js/jquery-3.5.1.min.js?v=1.15.4 |
| 27.9 Ko | 3299 ms | `tiers:challenges.cloudflare.com` | https://challenges.cloudflare.com/turnstile/v0/api.js |
| 25.6 Ko | 645 ms | `tiers:scripts.clarity.ms` | https://scripts.clarity.ms/0.8.70/clarity.js |
| 23.2 Ko | 376 ms | `tiers:nehl6uu58j.execute-api.us-east-1.amazonaws.com` | https://nehl6uu58j.execute-api.us-east-1.amazonaws.com/merchant/widget-config/public/la-maison-du-dos-com |
| 20.4 Ko | 968 ms | `plugin:wp-user-avatar` | https://prod.la-maison-du-dos.com/wp-content/plugins/wp-user-avatar/assets/css/frontend.min.css?ver=4.17.5 |
| 19.3 Ko | 785 ms | `plugin:alma-gateway-for-woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/alma-gateway-for-woocommerce/build/alma-gateway-block/sty… |
| 17.3 Ko | 3020 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/v1/storefront.js?ver=0.7.1 |
| 15.5 Ko | 808 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/css/woocommerce.css?ver=11.1.2 |
| 13.0 Ko | 764 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/widget/v1/track.js |
| 10.9 Ko | 149 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/widget/v1/shell/features/pill.js |
| 10.7 Ko | 808 ms | `tiers:my.zadarma.com` | https://my.zadarma.com/callmewidget/v2.0.9/style.min.css |
| 9.7 Ko | 576 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/10/logo-Aqua-flair.jpg |
| 9.6 Ko | 216 ms | `tiers:my.zadarma.com` | https://my.zadarma.com/callbackWidget/js/combine.min.js?v=1.15.4 |
| 9.5 Ko | 1357 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/custom-frontend.min.css?ver=1790748725 |
| 9.5 Ko | 438 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-cropped-template_images_logo-boutique-pc-… |
| 9.5 Ko | 726 ms | `uploads` | https://la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-cropped-template_images_logo-boutique-pc-192x1… |
| 8.2 Ko | 524 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/matelas-eau-leger-1-300x300.jpg |
| 8.2 Ko | 1945 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/css/roboto.css?ver=1742292385 |
| 7.7 Ko | 146 ms | `plugin:ag-core` | https://www.societe-des-avis-garantis.fr/wp-content/plugins/ag-core/images/widgets/cocarde.png |
| 6.9 Ko | 118 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/widget/v1/shell/features/chip-stack.js |
| 6.8 Ko | 765 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/wp-emoji-release.min.js?ver=7.1.2 |
| 6.5 Ko | 5356 ms | `plugin:wp-user-avatar` | https://prod.la-maison-du-dos.com/wp-content/plugins/wp-user-avatar/assets/js/frontend.min.js?ver=4.17.5 |
| 6.4 Ko | 659 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-cropped-template_images_logo-boutique-pc-… |
| 6.3 Ko | 1449 ms | `tiers:my.zadarma.com` | https://my.zadarma.com/callbackWidget/js/main.min.js?v=1.15.4 |
| 6.2 Ko | 233 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/client_data/0ea5729425dc239df232d1a8/config/t8mN_D0E.json |
| 6.0 Ko | 3490 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/dom.min.js?ver=c95f94cbbc1ac3fde84f |
| 5.9 Ko | 2378 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-template_images_logo-boutique-pc.webp |
| 5.9 Ko | 2336 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/09/screenshot-la-maison-du-dos-com-2025-09-08-11-18-… |
| 5.9 Ko | 2518 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2026/09/matelas-reglable.webp |
| 5.9 Ko | 2759 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/Lyra_01.webp |
| 5.9 Ko | 5805 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/10/paiements.jpg |
| 5.9 Ko | 359 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/09/screenshot-la-maison-du-dos-com-2025-09-08-11-18-… |
| 5.9 Ko | 601 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/07/1_akva-Nordic-frene-tiroirs.jpg |
| 5.9 Ko | 602 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/07/1_Highline-SPLIT-AVEC-TIROIRS-HT-40.jpg |
| 5.9 Ko | 3098 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/07/Lyra_DIA.jpg |
| 5.9 Ko | 3164 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/07/vega-DIA.jpg |
| 5.9 Ko | 3241 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/07/modulex-dia.jpg |
| 5.9 Ko | 3242 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/07/1_akva-cloe-frene-tiroirs1.jpg |
| 5.9 Ko | 2574 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-template_images_logo-boutique-pc.webp |
| 5.9 Ko | 192 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/matelaseau2.webp |
| 5.9 Ko | 542 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/10/hefel_softbausch.jpg |
| 5.9 Ko | 456 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/10/Carbon-iQ-Akva.jpg |
| 5.9 Ko | 210 ms | `plugin:woo-guaranteed-reviews-company` | https://prod.la-maison-du-dos.com/wp-content/plugins/woo-guaranteed-reviews-company/assets/images/star_off.png |
| 5.9 Ko | 179 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/matelas-eau-leger-300x300.jpg |
| 5.9 Ko | 180 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/Babymadras1-300x300.jpg |
| 5.9 Ko | 228 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/tribrid-fluid-300x300.jpg |
| 5.9 Ko | 182 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/02/pure-300x213.webp |
| 5.9 Ko | 200 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/02/Matelas_reglable_emotion1-300x300.webp |
| 5.9 Ko | 189 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/02/Matelas_reglable_life1-300x300.webp |
| 5.9 Ko | 177 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/02/website-pagina-slapen-op-lucht_Page_1_Image_0002-… |
| 5.9 Ko | 213 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-template_images_logo-boutique-pc-300x92.w… |
| 5.9 Ko | 198 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/Formesse_LogoSlogan_RGB.webp |
| 5.9 Ko | 547 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-Bodyform_logo.webp |
| 5.9 Ko | 961 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/ATT00136logo-marque-1442234847.webp |
| 5.9 Ko | 993 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/10/logo2-akva.jpg |
| 5.9 Ko | 1070 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/LOGO-MATRAIR.webp |
| 5.9 Ko | 1058 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/LogoTTIEWPlogo-marque-1448474026.webp |
| 5.9 Ko | 687 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logologo-marque-1402648459.webp |
| 5.9 Ko | 285 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logokirstenlogo-marque-1396450046.webp |
| 5.9 Ko | 260 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logobodytonelogo-marque-1399716526.webp |
| 5.9 Ko | 440 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logo_profine.webp |
| 5.9 Ko | 638 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/09/logo-1-e1757664098298.jpg |
| 5.9 Ko | 533 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/09/logo-e1757664451940.jpg |
| 5.9 Ko | 954 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/Conditionneur-Multi-Usage-Waterclean-Plus-300x300… |
| 5.9 Ko | 609 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/bella-donna-standard-0030-bordeaux-300x300.jpg |
| 5.9 Ko | 492 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2018/10/waterbed-basic-line.webp |
| 5.9 Ko | 589 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/icn1.png |
| 5.9 Ko | 591 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/icn4.png |
| 5.8 Ko | 1179 ms | `tiers:my.zadarma.com` | https://my.zadarma.com/callmewidget/v2.0.9/detectWebRTC.min.js |
| 5.7 Ko | 1450 ms | `tiers:my.zadarma.com` | https://my.zadarma.com/callmewidget/v2.0.9/widget.min.js |
| 5.3 Ko | 611 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/widget/v1/shell/runtime.js |
| 5.3 Ko | 1039 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/akva-long-life-300x300.jpg |
| 5.1 Ko | 1109 ms | `plugin:woo-guaranteed-reviews-company` | https://prod.la-maison-du-dos.com/wp-content/plugins/woo-guaranteed-reviews-company/assets/css/main.css?ver=1.… |
| 4.9 Ko | 1471 ms | `theme:royal-elementor-kit` | https://prod.la-maison-du-dos.com/wp-content/themes/royal-elementor-kit/style.css?ver=1.0 |
| 4.9 Ko | 718 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/LOGOlogo-marque-1386846181.webp |
| 4.7 Ko | 198 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/widget/v1/shell/features/fab.js |
| 4.7 Ko | 1285 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-209.css?ver=1790748721 |
| 4.6 Ko | 979 ms | `plugin:prisna-wp-translate` | https://prod.la-maison-du-dos.com/wp-content/plugins/prisna-wp-translate/styles/translator-s.css?ver=1.17.3 |
| 4.4 Ko | 1032 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/10/logo-TASSO-CARRE.jpg |
| 3.9 Ko | 133 ms | `tiers:cdn.jsdelivr.net` | https://cdn.jsdelivr.net/npm/@alma/widgets@4.x.x/dist/widgets.min.css?ver=6.7.0 |
| 3.5 Ko | 975 ms | `plugin:wp-user-avatar` | https://prod.la-maison-du-dos.com/wp-content/plugins/wp-user-avatar/assets/flatpickr/flatpickr.min.css?ver=4.1… |
| 3.4 Ko | 2951 ms | `plugin:ag-core` | https://www.societe-des-avis-garantis.fr/wp-content/plugins/ag-core/widgets/JsWidget.js?ver=1.3.0 |
| 3.0 Ko | 1182 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-5318.css?ver=1790749030 |
| 3.0 Ko | 4797 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/style-engine.min.js?ver=914befb08774033e6265 |
| 3.0 Ko | 1357 ms | `plugin:wp-user-avatar` | https://prod.la-maison-du-dos.com/wp-content/plugins/wp-user-avatar/assets/select2/select2.min.css?ver=7.1.2 |
| 2.9 Ko | 417 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/widget/v1/shell/features/scroll-collapse.js |
| 2.8 Ko | 549 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/widget/v1/shell/features/scrim.js |
| 2.7 Ko | 3297 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/autop.min.js?ver=4e10a18cb6f21a043fc0 |
| 2.7 Ko | 592 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logolunalifelogo-marque-1367070312.webp |
| 2.6 Ko | 377 ms | `tiers:fonts.googleapis.com` | https://fonts.googleapis.com/css?family=Open+Sans:600,400,400i|Oswald:700&ver=7.1.2 |
| 2.4 Ko | 2208 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/hooks.min.js?ver=f0f188028580e8dc1255 |
| 2.3 Ko | 1854 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/css/lib/animations/text-ani… |
| 2.3 Ko | 227 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/client_data/0ea5729425dc239df232d1a8/audit-table/E4-F0PLf.json |
| 2.1 Ko | 181 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/assets/images/poweredbtcky.svg |
| 2.0 Ko | 888 ms | `plugin:wpb-accordion-menu-or-category` | https://prod.la-maison-du-dos.com/wp-content/plugins/wpb-accordion-menu-or-category/assets/css/wpb_wmca_style.… |
| 1.8 Ko | 2550 ms | `plugin:google-site-kit` | https://prod.la-maison-du-dos.com/wp-content/plugins/google-site-kit/dist/assets/js/sign-in-with-google-5a9e01… |
| 1.8 Ko | 640 ms | `plugin:ag-core` | https://www.societe-des-avis-garantis.fr/wp-content/plugins/ag-core/widgets/cache/jsv2/9649.html |
| 1.7 Ko | 139 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/assets/images/revisit.svg |
| 1.7 Ko | 3089 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/custom-widget-icon-list.min.css?ver=1790748… |
| 1.5 Ko | 237 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/client_data/0ea5729425dc239df232d1a8/translations/WYHDtpqk.json |
| 1.4 Ko | 4608 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/primitives.min.js?ver=44cc5a35c7b9fe07a838 |
| 1.4 Ko | 397 ms | `tiers:accounts.google.com` | https://accounts.google.com/gsi/style |
| 1.4 Ko | 3102 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-12333.css?ver=1790748722 |
| 1.4 Ko | 1695 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/css/lib/animations/wpr-link… |
| 1.3 Ko | 672 ms | `plugin:alma-gateway-for-woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/alma-gateway-for-woocommerce/build/alma-gateway-block/alm… |
| 1.3 Ko | 155 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/assets/images/close.svg |
| 1.2 Ko | 197 ms | `tiers:www.clarity.ms` | https://www.clarity.ms/tag/qffm4e3fjy?ref=gtm2 |
| 1.0 Ko | 557 ms | `tiers:c.clarity.ms` | https://c.clarity.ms/c.gif |
| 1.0 Ko | 2745 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-12296.css?ver=1790748722 |
| 1.0 Ko | 415 ms | `plugin:alma-gateway-for-woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/alma-gateway-for-woocommerce/assets/css/frontend/alma-che… |
| 0.9 Ko | 1289 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/css/widget-heading.min.css?ver=4.3.2 |
| 0.8 Ko | 628 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/icn2.png |
| 0.8 Ko | 1490 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-6.css?ver=1790748724 |
| 0.7 Ko | 1594 ms | `tiers:my.zadarma.com` | https://my.zadarma.com/callmewidget/v2.0.9/loader.js |
| 0.7 Ko | 1652 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/css/widget-spacer.min.css?ver=4.3.2 |
| 0.7 Ko | 250 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/client_data/0ea5729425dc239df232d1a8/HKxm_GZM.json |
| 0.6 Ko | 534 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/icn3.png |
| 0.6 Ko | 290 ms | `tiers:my.zadarma.com` | https://my.zadarma.com/callback/widget/initialize?callback=jQuery351023460513714266273_1790749600496&linkId=84… |
| 0.5 Ko | 1303 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/css/widget-image.min.css?ver=4.3.2 |
| 0.5 Ko | 310 ms | `plugin:alma-gateway-for-woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/alma-gateway-for-woocommerce/assets/css/frontend/alma-wid… |
| 0.4 Ko | 170 ms | `tiers:p.typekit.net` | https://p.typekit.net/p.css?s=1&k=lsl5vay&ht=tk&f=49383.49384.49387.51204.51206.51207&a=135436349&app=typekit&… |
| 0.3 Ko | 798 ms | `tiers:log.cookieyes.com` | https://log.cookieyes.com/api/v1/log |
| 0.3 Ko | 239 ms | `tiers:j.clarity.ms` | https://j.clarity.ms/collect |
| 0.3 Ko | 287 ms | `tiers:j.clarity.ms` | https://j.clarity.ms/collect |
| 0.3 Ko | 363 ms | `tiers:j.clarity.ms` | https://j.clarity.ms/collect |
| 0.3 Ko | 149 ms | `tiers:j.clarity.ms` | https://j.clarity.ms/collect |
| 0.3 Ko | 508 ms | `plugin:prisna-wp-translate` | https://prod.la-maison-du-dos.com/wp-content/plugins/prisna-wp-translate/styles/blocks.css?ver=1.17.3 |
| 0.3 Ko | 667 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/css/woocommerce-layout.css?ver=11.1.2 |
| 0.3 Ko | 1372 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-212.css?ver=1790748722 |
| 0.3 Ko | 1478 ms | `plugin:flexible-shipping` | https://prod.la-maison-du-dos.com/wp-content/plugins/flexible-shipping/assets/dist/css/free-shipping.css?ver=6… |
| 0.3 Ko | 1643 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/custom-widget-image-box.min.css?ver=1790748… |
| 0.3 Ko | 1685 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/lib/swiper/v8/css/swiper.min.css?ver=8.4… |
| 0.3 Ko | 1674 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/css/conditionals/e-swiper.min.css?ver=4.… |
| 0.3 Ko | 1689 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/custom-widget-image-gallery.min.css?ver=179… |
| 0.3 Ko | 1880 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/lib/font-awesome/css/all.min.css?ver=1.7… |
| 0.3 Ko | 1923 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/css/poppins.css?ver=1742312054 |
| 0.3 Ko | 1942 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/css/robotoslab.css?ver=1742292388 |
| 0.3 Ko | 2039 ms | `wp-core:jquery` | https://prod.la-maison-du-dos.com/wp-includes/js/jquery/jquery.min.js?ver=3.7.1 |
| 0.3 Ko | 2173 ms | `wp-core:jquery` | https://prod.la-maison-du-dos.com/wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1 |
| 0.3 Ko | 2211 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/js/jquery-blockui/jquery.blockUI.min.j… |
| 0.3 Ko | 2223 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/js/js-cookie/js.cookie.min.js?ver=2.1.… |
| 0.3 Ko | 2241 ms | `plugin:wp-user-avatar` | https://prod.la-maison-du-dos.com/wp-content/plugins/wp-user-avatar/assets/flatpickr/flatpickr.min.js?ver=4.17… |
| 0.3 Ko | 2349 ms | `plugin:wp-user-avatar` | https://prod.la-maison-du-dos.com/wp-content/plugins/wp-user-avatar/assets/select2/select2.min.js?ver=4.17.5 |
| 0.3 Ko | 2363 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/js/lib/perfect-scrollbar/pe… |
| 0.3 Ko | 2390 ms | `plugin:woocommerce-google-adwords-conversion-tracking-tag` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce-google-adwords-conversion-tracking-tag/js/pub… |
| 0.3 Ko | 6171 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/css/woocommerce-smallscreen.css?ver=11… |
| 0.3 Ko | 2753 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-12291.css?ver=1790748722 |
| 0.3 Ko | 2756 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-12330.css?ver=1790748722 |
| 0.3 Ko | 3301 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-11019.css?ver=1790748722 |
| 0.3 Ko | 3191 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-12338.css?ver=1790748723 |
| 0.3 Ko | 3440 ms | `wp-core:jquery` | https://prod.la-maison-du-dos.com/wp-includes/js/jquery/ui/core.min.js?ver=1.14.2 |
| 0.3 Ko | 3440 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/i18n.min.js?ver=1dfe7db3940c23ea9216 |
| 0.3 Ko | 3525 ms | `plugin:alma-gateway-for-woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/alma-gateway-for-woocommerce/assets/js/frontend/alma-chec… |
| 0.3 Ko | 3525 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/vendor/react.min.js?ver=18.3.1.1 |
| 0.3 Ko | 3971 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/vendor/react-jsx-runtime.min.js?ver=18.3.1 |
| 0.3 Ko | 4006 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/blob.min.js?ver=c7582a735ddd2edc9731 |
| 0.3 Ko | 4006 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/block-serialization-default-parser.min.js?ver=4c6f3dd400… |
| 0.3 Ko | 4006 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/deprecated.min.js?ver=fe587bac92b7d0ef760e |
| 0.3 Ko | 4055 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/vendor/react-dom.min.js?ver=18.3.1.1 |
| 0.3 Ko | 4128 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/escape-html.min.js?ver=87ebe53e97bba59805a5 |
| 0.3 Ko | 4128 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/element.min.js?ver=4a4370b2b349066fd440 |
| 0.3 Ko | 4130 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/is-shallow-equal.min.js?ver=7ad271045c1fe60f5496 |
| 0.3 Ko | 4130 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/keycodes.min.js?ver=d0b4204e4bbeb412df6e |
| 0.3 Ko | 4131 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/priority-queue.min.js?ver=6c0aa59b65d55dfd509b |
| 0.3 Ko | 4131 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/private-apis.min.js?ver=eb85f28c4c729bb4f002 |
| 0.3 Ko | 4131 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/undo-manager.min.js?ver=4554fce6276d8910a4ae |
| 0.3 Ko | 4131 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/compose.min.js?ver=0e8bde2a499ea6073b42 |
| 0.3 Ko | 4131 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/redux-routine.min.js?ver=acca2b4857d83ad1790e |
| 0.3 Ko | 4134 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/data.min.js?ver=14a216e0932d72c22976 |
| 0.3 Ko | 4135 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/html-entities.min.js?ver=a976ff3a0f00bc2999a3 |
| 0.3 Ko | 4135 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/dom-ready.min.js?ver=3fe927cab37bf38d6a23 |
| 0.3 Ko | 4135 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/a11y.min.js?ver=31c6cec5a4ff7aff483d |
| 0.3 Ko | 4815 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/rich-text.min.js?ver=3e5852e42cee1c239bae |
| 0.3 Ko | 4190 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/shortcode.min.js?ver=f6273476300cc5fad4cd |
| 0.3 Ko | 4813 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/warning.min.js?ver=a0978839debc564a6608 |
| 0.3 Ko | 4813 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/blocks.min.js?ver=c0e57a630a0b6f6c3bb5 |
| 0.3 Ko | 4816 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/vendor/moment.min.js?ver=2.30.1 |
| 0.3 Ko | 4813 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/date.min.js?ver=8173fc0fc12b7bb7eaf0 |
| 0.3 Ko | 4814 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/theme.min.js?ver=56a75cc08ae66c1fcb7a |
| 0.3 Ko | 4814 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/components.min.js?ver=9b751f17060211272a5c |
| 0.3 Ko | 4815 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/keyboard-shortcuts.min.js?ver=37da95806f2339bc80d0 |
| 0.3 Ko | 4815 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/url.min.js?ver=7b0de086d4ae11d55704 |
| 0.3 Ko | 4816 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/api-fetch.min.js?ver=6f2a4faeee3c722b1e57 |
| 0.3 Ko | 4816 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/preferences-persistence.min.js?ver=a34abbdacd8f50f9acb1 |
| 0.3 Ko | 4816 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/preferences.min.js?ver=5a169e3fc0e657f74172 |
| 0.3 Ko | 4816 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/commands.min.js?ver=1a4910212c7ed2355300 |
| 0.3 Ko | 4816 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/notices.min.js?ver=c09a068fdab0eb465e14 |
| 0.3 Ko | 4816 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/token-list.min.js?ver=e86ab419d8302d57822c |
| 0.3 Ko | 5145 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/upload-media.min.js?ver=a2c026d433c295fd5145 |
| 0.3 Ko | 5142 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/block-editor.min.js?ver=37e58da384b2558bd479 |
| 0.3 Ko | 5144 ms | `plugin:prisna-wp-translate` | https://prod.la-maison-du-dos.com/wp-content/plugins/prisna-wp-translate/javascript/blocks.class.js?ver=1.17.3 |
| 0.3 Ko | 5157 ms | `wp-core:jquery` | https://prod.la-maison-du-dos.com/wp-includes/js/jquery/ui/mouse.min.js?ver=1.14.2 |
| 0.3 Ko | 5161 ms | `wp-core:jquery` | https://prod.la-maison-du-dos.com/wp-includes/js/jquery/ui/draggable.min.js?ver=1.14.2 |
| 0.3 Ko | 5162 ms | `plugin:prisna-wp-translate` | https://prod.la-maison-du-dos.com/wp-content/plugins/prisna-wp-translate/javascript/translator.js?ver=1.17.3 |
| 0.3 Ko | 5163 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/js/frontend/woocommerce.min.js?ver=11.… |
| 0.3 Ko | 5165 ms | `plugin:woo-guaranteed-reviews-company` | https://prod.la-maison-du-dos.com/wp-content/plugins/woo-guaranteed-reviews-company/assets/js/main.js?ver=1.3.… |
| 0.3 Ko | 5183 ms | `plugin:agentic-commerce-for-woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/agentic-commerce-for-woocommerce/js/xpay-attribution.js?v… |
| 0.3 Ko | 5176 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/js/webpack.runtime.min.js?ver=4.3.2 |
| 0.3 Ko | 5189 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/js/frontend-modules.min.js?ver=4.3.2 |
| 0.3 Ko | 5229 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/js/frontend.min.js?ver=4.3.2 |
| 0.3 Ko | 5311 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/js/lib/particles/particles.… |
| 0.3 Ko | 5359 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/js/lib/jarallax/jarallax.mi… |
| 0.3 Ko | 5359 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/js/lib/parallax/parallax.mi… |
| 0.3 Ko | 5390 ms | `plugin:wpb-accordion-menu-or-category` | https://prod.la-maison-du-dos.com/wp-content/plugins/wpb-accordion-menu-or-category/assets/js/jquery.cookie.js… |
| 0.3 Ko | 5415 ms | `plugin:wpb-accordion-menu-or-category` | https://prod.la-maison-du-dos.com/wp-content/plugins/wpb-accordion-menu-or-category/assets/js/jquery.navgoco.m… |
| 0.3 Ko | 5512 ms | `plugin:wpb-accordion-menu-or-category` | https://prod.la-maison-du-dos.com/wp-content/plugins/wpb-accordion-menu-or-category/assets/js/accordion-init.j… |
| 0.3 Ko | 5534 ms | `plugin:google-site-kit` | https://prod.la-maison-du-dos.com/wp-content/plugins/google-site-kit/dist/assets/js/googlesitekit-consent-mode… |
| 0.3 Ko | 5531 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/js/sourcebuster/sourcebuster.min.js?ve… |
| 0.3 Ko | 5545 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/js/frontend/order-attribution.min.js?v… |
| 0.3 Ko | 5888 ms | `plugin:wp-consent-api` | https://prod.la-maison-du-dos.com/wp-content/plugins/wp-consent-api/assets/js/wp-consent-api.min.js?ver=2.1.0 |
| 0.3 Ko | 5605 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/js/frontend/wp-consent-api-integration… |
| 0.3 Ko | 5731 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/lib/swiper/v8/swiper.min.js?ver=8.4.5 |
| 0.3 Ko | 5848 ms | `plugin:google-site-kit` | https://prod.la-maison-du-dos.com/wp-content/plugins/google-site-kit/dist/assets/js/googlesitekit-events-provi… |
| 0.3 Ko | 5888 ms | `plugin:google-site-kit` | https://prod.la-maison-du-dos.com/wp-content/plugins/google-site-kit/dist/assets/js/googlesitekit-events-provi… |
| 0.3 Ko | 5888 ms | `plugin:google-site-kit` | https://prod.la-maison-du-dos.com/wp-content/plugins/google-site-kit/dist/assets/js/googlesitekit-events-provi… |
| 0.3 Ko | 5730 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/js/frontend.min.js?ver=1.7.… |
| 0.3 Ko | 5739 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/js/modal-popups.min.js?ver=… |
| 0.3 Ko | 138 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/js/lib/perfect-scrollbar/pe… |
| 0.2 Ko | 428 ms | `tiers:agent-commerce.xpay.sh` | https://agent-commerce.xpay.sh/widget/track |
| 0.0 Ko | 1020 ms | `elementor:generated-css` | https://la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/fonts/roboto-kfo7cnqeu92fr1me7ksn66agld… |
| 0.0 Ko | 1616 ms | `plugin:ag-core` | https://www.societe-des-avis-garantis.fr/wp-content/plugins/ag-core/widgets/iframe/2/h/?id=9649 |
| 0.0 Ko | 209 ms | `tiers:pagead2.googlesyndication.com` | https://pagead2.googlesyndication.com/ccm/collect?rcb=4&frm=0&apvc=1&ae=g&en=page_view&dl=https%3A%2F%2Fprod.l… |
| 0.0 Ko | 114 ms | `tiers:www.google-analytics.com` | https://www.google-analytics.com/g/collect?v=2&tid=G-VJSQ20QJ4W&gtm=45je69s1h1v880674583za200zd880674583xf1&_p… |
| 0.0 Ko | 228 ms | `tiers:www.google-analytics.com` | https://www.google-analytics.com/g/collect?v=2&tid=G-HH41XH2SV1&gtm=45je69s1v9206600813za200zd9206600813xf1&_p… |
| 0.0 Ko | 194 ms | `tiers:www.google-analytics.com` | https://www.google-analytics.com/g/collect?v=2&tid=G-HT4NBE35GW&gtm=45be69s1v9218560706z89207186614za20gzb9207… |
| 0.0 Ko | 222 ms | `tiers:pagead2.googlesyndication.com` | https://pagead2.googlesyndication.com/ccm/collect?rcb=15&frm=0&apvc=0&tid=AW-969053143&en=page_view&dl=https%3… |
| 0.0 Ko | 678 ms | `elementor:generated-css` | https://la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/fonts/roboto-kfo5cnqeu92fr1mu53zec9_vu3… |
| 0.0 Ko | 166 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/api/widget-events |
| 0.0 Ko | 744 ms | `elementor:generated-css` | https://la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/fonts/roboto-kfo7cnqeu92fr1me7ksn66agld… |

## Scripts inline

| Taille | id | Début |
|---:|---|---|
| 59.1 Ko | wp-block-editor-js-translations | `( function( domain, translations ) { var localeData = translations.locale_data[ domain ] ` |
| 13.9 Ko | wp-components-js-translations | `( function( domain, translations ) { var localeData = translations.locale_data[ domain ] ` |
| 5.5 Ko | wp-upload-media-js-translations | `( function( domain, translations ) { var localeData = translations.locale_data[ domain ] ` |
| 3.8 Ko |  | `jQuery(document).ready(function($) { // Parcourir chaque produit dans la liste $("` |
| 3.4 Ko | rocket-preload-links-js-after | `(function() { "use strict";var r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterat` |
| 3.3 Ko | googlesitekit-events-provider-woocommerce-js-before | `window._googlesitekit.wcdata = window._googlesitekit.wcdata \|\| {}; window._googlesitekit.w` |
| 2.6 Ko | elementor-frontend-js-before | `var elementorFrontendConfig = {"environmentMode":{"edit":false,"wpPreview":false,"isScript` |
| 2.2 Ko |  | `// Seuil de déclenchement (768px = Tablettes et ordinateurs) if (window.innerWidth >= ` |
| 2.1 Ko | rocket-browser-checker-js-after | `"use strict";var _createClass=function(){function defineProperties(target,props){for(var i` |
| 2.1 Ko |  | `window.pmwDataLayer = window.pmwDataLayer \|\| {}; window.pmwDataLayer = Object.assign(wi` |
| 1.3 Ko | xpay-side-cart-js-after | `(function () { if (window.location.hash !== '#open-cart') { return; } var touched = fa` |
| 1.2 Ko |  | `document.addEventListener('DOMContentLoaded', function() { function force` |
| 1.2 Ko | wp-api-fetch-js-translations | `( function( domain, translations ) { var localeData = translations.locale_data[ domain ] ` |
| 1.2 Ko | google_gtagjs-js-consent-mode-data-layer | `window.dataLayer = window.dataLayer \|\| [];function gtag(){dataLayer.push(arguments);} gtag` |
| 1.0 Ko | wp-commands-js-translations | `( function( domain, translations ) { var localeData = translations.locale_data[ domain ] ` |
| 1.0 Ko | wp-blocks-js-translations | `( function( domain, translations ) { var localeData = translations.locale_data[ domain ] ` |
| 0.9 Ko |  | `( () => { const lazyloadRunObserver = () => { const lazyloadBackgrounds = docum` |
| 0.9 Ko | wp-date-js-after | `wp.date.setSettings( {"l10n":{"locale":"fr_FR","months":["janvier","f\u00e9vrier","mars","` |
| 0.9 Ko |  | `window.onload = function() { const div = document.querySelector(".woocommerce-Tabs-pan` |
| 0.8 Ko | wpr-addons-js-js-extra | `var WprConfig = {"ajaxurl":"https://prod.la-maison-du-dos.com/wp-admin/admin-ajax.php","re` |
| 0.8 Ko | google_gtagjs-js-after | `window.dataLayer = window.dataLayer \|\| [];function gtag(){dataLayer.push(arguments);} gtag` |
| 0.7 Ko | wp-preferences-js-translations | `( function( domain, translations ) { var localeData = translations.locale_data[ domain ] ` |
| 0.7 Ko |  | `(window.pmwDataLayer = window.pmwDataLayer \|\| {}).products = window.pmwData` |
| 0.7 Ko | wc-order-attribution-js-extra | `var wc_order_attribution = {"params":{"lifetime":1.0e-5,"session":30,"base64":false,"ajaxu` |
| 0.7 Ko |  | `(window.pmwDataLayer = window.pmwDataLayer \|\| {}).products = window.pmwData` |
| 0.7 Ko |  | `(window.pmwDataLayer = window.pmwDataLayer \|\| {}).products = window.pmwData` |
| 0.7 Ko |  | `(window.pmwDataLayer = window.pmwDataLayer \|\| {}).products = window.pmwData` |
| 0.7 Ko |  | `(window.pmwDataLayer = window.pmwDataLayer \|\| {}).products = window.pmwData` |
| 0.7 Ko |  | `(window.pmwDataLayer = window.pmwDataLayer \|\| {}).products = window.pmwData` |
| 0.7 Ko |  | `(window.pmwDataLayer = window.pmwDataLayer \|\| {}).products = window.pmwData` |
| 0.7 Ko |  | `(window.pmwDataLayer = window.pmwDataLayer \|\| {}).products = window.pmwData` |
| 0.7 Ko |  | `(window.pmwDataLayer = window.pmwDataLayer \|\| {}).products = window.pmwData` |
| 0.6 Ko |  | `(window.pmwDataLayer = window.pmwDataLayer \|\| {}).products = window.pmwData` |
| 0.6 Ko |  | `(window.pmwDataLayer = window.pmwDataLayer \|\| {}).products = window.pmwData` |
| 0.6 Ko | wp-keycodes-js-translations | `( function( domain, translations ) { var localeData = translations.locale_data[ domain ] ` |
| 0.6 Ko | wp-rich-text-js-translations | `( function( domain, translations ) { var localeData = translations.locale_data[ domain ] ` |
| 0.6 Ko |  | `(window.pmwDataLayer = window.pmwDataLayer \|\| {}).products = window.pmwData` |
| 0.6 Ko | wp-a11y-js-translations | `( function( domain, translations ) { var localeData = translations.locale_data[ domain ] ` |
| 0.5 Ko | moment-js-after | `moment.updateLocale( 'fr_FR', {"months":["janvier","f\u00e9vrier","mars","avril","mai","ju` |
| 0.5 Ko | rocket-preload-links-js-extra | `var RocketPreloadLinksConfig = {"excludeUris":"/(?:.+/)?feed(?:/(?:.+/?)?)?$\|/(?:.+/)?embe` |
| 0.5 Ko |  | `var ZCallbackWidgetLinkId = '8413fce37974ddaa037c96f51bd179d9'; var ZCallbackWidgetDomain` |
| 0.5 Ko | prisna-wp-translate-js-after | `if(typeof _prisna_translate=="undefined")_prisna_translate={layout:"dropdown",size:"s",fro` |
| 0.4 Ko |  | `( function( w, d, s, l, i ) { w[l] = w[l] \|\| []; w[l].push( {'gtm.start': new Date` |
| 0.4 Ko | wp-api-fetch-js-after | `wp.apiFetch.use( wp.apiFetch.createRootURLMiddleware( "https://prod.la-maison-du-dos.com/w` |
| 0.4 Ko | ppress-frontend-script-js-extra | `var pp_ajax_form = {"ajaxurl":"https://prod.la-maison-du-dos.com/wp-admin/admin-ajax.php",` |
| 0.3 Ko | wp-preferences-js-after | `( function() { var serverData = false; var userId = "0"; var persistenceLayer ` |
| 0.3 Ko |  | `jQuery(document).ready(function($) { if ($('.posted_in a:contains("Lits à eau en vente` |
| 0.3 Ko |  | `(function(a,e,b,f,g,c,d){a[b]=a[b]\|\|function(){(a[b].q=a[b].q\|\|[]).push(arguments)};c=e.cr` |
| 0.3 Ko | pmw-js-before | `window.pmw = Object.assign(window.pmw \|\| {}, {"ajax_url":"https://prod.la-maison-du-dos.co` |
| 0.2 Ko | woocommerce-js-extra | `var woocommerce_params = {"ajax_url":"/wp-admin/admin-ajax.php","wc_ajax_url":"/?wc-ajax=%` |
| 0.2 Ko | googlesitekit-events-provider-content-events-js-before | `window._googlesitekit = window._googlesitekit \|\| {}; window._googlesitekit.contentEvents =` |
| 0.2 Ko |  | `(function() { var script = document.createElement('script'); script.src = 'https://my.za` |
| 0.2 Ko | wp-data-js-after | `( function() { var userId = 0; var storageKey = "WP_DATA_USER_" + userId; wp.data .us` |
| 0.2 Ko | wp-consent-api-js-extra | `var consent_api = {"consent_type":"","waitfor_consent_hook":"","cookie_expiration":"30","c` |
| 0.1 Ko |  | `window.dataLayer = window.dataLayer \|\| []; function gtag(){dataLayer.push(arguments);} ` |
| 0.1 Ko |  | `(function () { var c = document.body.className; c = c.replace(/woocommerce-no-js/, '` |
| 0.1 Ko | xpay-attribution-js-extra | `var XpayAttr = {"endpoint":"https://prod.la-maison-du-dos.com/wp-json/xpay/v1/classify"}; ` |
| 0.1 Ko | wp-consent-api-integration-js-before | `window.wc_order_attribution.params.consentCategory = "marketing"; //# sourceURL=wp-consent` |
| 0.1 Ko | cloudflare-turnstile-js-after | `document.addEventListener( 'wpcf7submit', e => turnstile.reset() ); //# sourceURL=cloudfla` |
| 0.1 Ko | wp-i18n-js-after | `wp.i18n.setLocaleData( { 'text direction\u0004ltr': [ 'ltr' ] } ); //# sourceURL=wp-i18n-j` |
| 0.1 Ko |  | `var urlCertificate = "https://www.societe-des-avis-garantis.fr/la-maisondu-dos/";` |
| 0.1 Ko | jquery-ui-core-js-before | `jQuery.uiBackCompat = true; //# sourceURL=jquery-ui-core-js-before` |
| 0.0 Ko |  | `var agSiteId="9649";` |

## Images

| Rendu | Naturel | width/height | loading | alt | URL |
|---|---|---|---|---|---|
| 0x0 | 36x36 | **non (CLS)** | auto | Revisit consent button | https://cdn-cookieyes.com/assets/images/revisit.svg |
| 10x10 | 10x10 | **non (CLS)** | auto | Close | https://cdn-cookieyes.com/assets/images/close.svg |
| 78x13 | 78x13 | **non (CLS)** | auto | Cookieyes logo | https://cdn-cookieyes.com/assets/images/poweredbtcky.svg |
| 0x0 | 0x0 | **non (CLS)** | auto | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-template_images_logo- |
| 807x1130 | 0x0 | oui | auto | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/09/screenshot-la-maison-du-dos-c |
| 0x0 | 0x0 | oui | auto | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/09/screenshot-la-maison-du-dos-c |
| 1336x500 | 0x0 | **non (CLS)** | auto | Akva Nordic | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/07/1_akva-Nordic-frene-tiroirs.j |
| 1336x500 | 0x0 | **non (CLS)** | auto | Highline | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/07/1_Highline-SPLIT-AVEC-TIROIRS |
| 1336x500 | 0x0 | **non (CLS)** | auto | Akva Lyra | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/07/Lyra_DIA.jpg |
| 1336x500 | 0x0 | **non (CLS)** | auto | Akva Vega | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/07/vega-DIA.jpg |
| 1336x500 | 0x0 | **non (CLS)** | auto | Akva Modulex | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/07/modulex-dia.jpg |
| 1336x500 | 0x0 | **non (CLS)** | auto | Clone | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/07/1_akva-cloe-frene-tiroirs1.jp |
| 804x450 | 0x0 | oui | auto | matelas réglable pour soutenir idéalement votre colonne vertébrale | https://prod.la-maison-du-dos.com/wp-content/uploads/2026/09/matelas-reglable.webp |
| 216x150 | 0x0 | oui | auto | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/Lyra_01.webp |
| 216x150 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/matelaseau2.webp |
| 216x150 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/10/hefel_softbausch.jpg |
| 172x150 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/10/Carbon-iQ-Akva.jpg |
| 1140x1140 | 300x300 | oui | lazy | Le matelas à eau Léger climatisé à poser sur un sommier fixe classique | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/matelas-eau-leger-1-300x300.j |
| 1140x1140 | 0x0 | oui | lazy | Matelas à eau léger Aqualight Premium de fabrication européenne | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/matelas-eau-leger-300x300.jpg |
| 1140x1140 | 0x0 | oui | lazy | Le matelas à eau pour Bébé d'Akva favorise un développement harmonieux et sain de son enfant | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/Babymadras1-300x300.jpg |
| 1140x1140 | 0x0 | oui | lazy | Le matelas à eau léger Tribrid® Fluid est un concept de matelas à eau avec ou sans chauffage | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/tribrid-fluid-300x300.jpg |
| 1140x809 | 0x0 | oui | lazy | matelas à fermeté réglable | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/02/pure-300x213.webp |
| 1140x1140 | 0x0 | oui | lazy | matelas ergonomique réglable e-motion luxe de Dynaglobe | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/02/Matelas_reglable_emotion1-300 |
| 1140x1140 | 0x0 | oui | lazy | matelas de santé Life | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/02/Matelas_reglable_life1-300x30 |
| 1140x1140 | 0x0 | oui | lazy | matelas réglable Matrair | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/02/website-pagina-slapen-op-luch |
| 300x92 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-template_images_logo- |
| 120x56 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/Formesse_LogoSlogan_RGB.webp |
| 120x48 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-Bodyform_logo.webp |
| 120x66 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/ATT00136logo-marque-144223484 |
| 150x178 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/10/logo2-akva.jpg |
| 215x194 | 215x194 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/10/logo-TASSO-CARRE.jpg |
| 120x38 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/LOGO-MATRAIR.webp |
| 105x100 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/LogoTTIEWPlogo-marque-1448474 |
| 120x65 | 120x65 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logolunalifelogo-marque-13670 |
| 120x44 | 120x44 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/LOGOlogo-marque-1386846181.we |
| 120x48 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logologo-marque-1402648459.we |
| 120x48 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logokirstenlogo-marque-139645 |
| 120x56 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logobodytonelogo-marque-13997 |
| 120x37 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logo_profine.webp |
| 100x27 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/09/logo-1-e1757664098298.jpg |
| 120x36 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/09/logo-e1757664451940.jpg |
| 227x175 | 227x175 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/10/logo-Aqua-flair.jpg |
| 1140x1140 | 300x300 | oui | lazy | LONG LIFE  : Anti-algues du fabricant Akva waterbeds | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/akva-long-life-300x300.jpg |
| 1140x1140 | 0x0 | oui | lazy | Conditionneur Multi-Usage Waterclean Plus | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/Conditionneur-Multi-Usage-Wat |
| 1140x1140 | 0x0 | oui | lazy | Drap-housse jersey Bella donna | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/bella-donna-standard-0030-bor |
| 1140x722 | 0x0 | oui | lazy | lit à eau Basic Line | https://prod.la-maison-du-dos.com/wp-content/uploads/2018/10/waterbed-basic-line.webp |
| 51x51 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/icn1.png |
| 51x51 | 51x51 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/icn2.png |
| 51x51 | 51x51 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/icn3.png |
| 51x51 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/icn4.png |
| 0x0 | 0x0 | **non (CLS)** | auto | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-template_images_logo- |
| 219x27 | 0x0 | oui | auto | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/10/paiements.jpg |

## Polices chargées

- Oswald 400 normal
- Oswald 700 normal

## SEO

```json
{
  "title": "Accueil - La Maison du Dos",
  "description": "La Maison du Dos vous conseille des lits à eau ou « waterbeds », avec un ou deux matelas à eau climatisés, offrant la meilleure qualité de soutien du dos & sans usure.",
  "canonical": null,
  "robots": "noindex, nofollow",
  "ogImage": null,
  "h1": [
    "La Maison du Dos®",
    "Pour soulager un mal de dos, améliorer durablement votre sommeil et votre qualité de vie!",
    "Marque du confort du dos depuis 1988 et spécialiste du lit à eau depuis 1994, La Maison du Dos vous acompagne dans votre choix d’un lit avec un ou deux matelas à eau ou « waterbed » , d’un matelas à eau léger ou d’un matelas à fermeté réglable par télécommande."
  ],
  "h2Count": 106,
  "lang": "fr-FR",
  "jsonLdTypes": [
    "HomeGoodsStore/Organization",
    "WebSite",
    "ImageObject",
    "WebPage",
    "Person",
    "Article",
    "ItemList"
  ]
}
```