# Audit performance — https://prod.la-maison-du-dos.com/

Profil : **mobile** (4G lente, CPU ×4) · 2026-09-30T06:27:44.441Z

## Synthèse

| Indicateur | Valeur | Objectif |
|---|---|---|
| TTFB (réponse serveur) | 3131 ms | < 800 ms |
| First Contentful Paint | 7712 ms | < 1 800 ms |
| Largest Contentful Paint | 9660 ms | < 2 500 ms |
| Total Blocking Time (approx.) | 21396 ms | < 200 ms |
| Cumulative Layout Shift | 0.003 | < 0,1 |
| Événement load | 28261 ms | — |
| Requêtes | 251 | < 50 |
| Poids total transféré | 3880.5 Ko | < 1 500 Ko |
| HTML (compressé) | 126.5 Ko | < 60 Ko |
| Nœuds DOM / profondeur | 2517 / 35 | < 1 500 / < 32 |

Élément LCP : `h1.elementor-heading-title.elementor-size-default `

Générateurs : WordPress 7.1.2 · Site Kit by Google 1.188.0 · Elementor 4.3.2; features: e_font_icon_svg, additional_custom_breakpoints; settings: css_print_method-external, google_font-enabled, font_display-swap

## Poids par plugin / thème / service tiers

| Source | Req. | Total | JS | CSS | Images | Polices |
|---|---:|---:|---:|---:|---:|---:|
| `wp-core` | 44 | 1176.5 Ko | 1176.5 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:www.googletagmanager.com` | 5 | 916.7 Ko | 916.7 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `uploads` | 26 | 421.8 Ko | 0.0 Ko | 0.0 Ko | 421.8 Ko | 0.0 Ko |
| `plugin:elementor` | 19 | 181.7 Ko | 143.0 Ko | 25.5 Ko | 0.0 Ko | 13.2 Ko |
| `plugin:royal-elementor-addons` | 9 | 177.5 Ko | 105.9 Ko | 71.7 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:my.zadarma.com` | 8 | 102.3 Ko | 91.6 Ko | 10.7 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:accounts.google.com` | 2 | 101.2 Ko | 99.8 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:cdn-cookieyes.com` | 9 | 83.6 Ko | 67.7 Ko | 0.0 Ko | 5.2 Ko | 0.0 Ko |
| `plugin:wp-user-avatar` | 6 | 72.0 Ko | 47.2 Ko | 24.9 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:woocommerce-google-adwords-conversion-tracking-tag` | 7 | 66.0 Ko | 66.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:widget.xpay.sh` | 9 | 63.8 Ko | 63.8 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:prisna-wp-translate` | 6 | 61.0 Ko | 30.7 Ko | 5.4 Ko | 25.0 Ko | 0.0 Ko |
| `wp-core:jquery` | 5 | 55.5 Ko | 55.5 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:google-site-kit` | 5 | 39.9 Ko | 39.9 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `elementor:generated-css` | 24 | 38.3 Ko | 0.0 Ko | 38.3 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:woocommerce` | 9 | 36.1 Ko | 15.5 Ko | 20.7 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:challenges.cloudflare.com` | 1 | 27.8 Ko | 27.8 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:scripts.clarity.ms` | 1 | 25.5 Ko | 25.5 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:nehl6uu58j.execute-api.us-east-1.amazonaws.com` | 1 | 23.2 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:alma-gateway-for-woocommerce` | 5 | 20.7 Ko | 1.0 Ko | 19.7 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:woo-guaranteed-reviews-company` | 3 | 9.7 Ko | 1.2 Ko | 5.1 Ko | 3.5 Ko | 0.0 Ko |
| `plugin:wpb-accordion-menu-or-category` | 4 | 9.6 Ko | 7.6 Ko | 2.0 Ko | 0.0 Ko | 0.0 Ko |
| `wp-ajax` | 2 | 7.3 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:googleads.g.doubleclick.net` | 2 | 6.7 Ko | 6.7 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:ag-core` | 3 | 5.2 Ko | 3.4 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `theme:royal-elementor-kit` | 1 | 4.9 Ko | 0.0 Ko | 4.9 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:cdn.jsdelivr.net` | 1 | 3.9 Ko | 0.0 Ko | 3.9 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:fonts.googleapis.com` | 1 | 2.7 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:agentic-commerce-for-woocommerce` | 1 | 2.2 Ko | 2.2 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:a.clarity.ms` | 6 | 1.9 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:stats.g.doubleclick.net` | 2 | 1.6 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `plugin:wp-consent-api` | 1 | 1.3 Ko | 1.3 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:www.clarity.ms` | 1 | 1.2 Ko | 1.2 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:www.google.com` | 7 | 1.1 Ko | 0.0 Ko | 0.0 Ko | 1.1 Ko | 0.0 Ko |
| `plugin:flexible-shipping` | 1 | 1.1 Ko | 0.0 Ko | 1.1 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:c.clarity.ms` | 1 | 1.0 Ko | 0.0 Ko | 0.0 Ko | 1.0 Ko | 0.0 Ko |
| `tiers:log.cookieyes.com` | 1 | 0.4 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:p.typekit.net` | 1 | 0.4 Ko | 0.0 Ko | 0.4 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:agent-commerce.xpay.sh` | 2 | 0.3 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:www.google-analytics.com` | 4 | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:pagead2.googlesyndication.com` | 1 | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:analytics.google.com` | 2 | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |
| `tiers:ad.doubleclick.net` | 1 | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko | 0.0 Ko |

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

### CSS (38)

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
- `plugin:elementor` — https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/css/conditionals/dialog.min.css?ver=4.3.2
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
| JS | `wp-core` | 1378.4 Ko | 79 % | /wp-includes/js/dist/block-editor.min.js |
| JS | `wp-core` | 817.1 Ko | 76 % | /wp-includes/js/dist/components.min.js |
| CSS | `plugin:royal-elementor-addons` | 438.0 Ko | 99 % | /wp-content/plugins/royal-elementor-addons/assets/css/frontend.min.css |
| JS | `tiers:www.googletagmanager.com` | 585.6 Ko | 67 % | https://www.googletagmanager.com/gtag/js |
| JS | `tiers:www.googletagmanager.com` | 585.6 Ko | 49 % | https://www.googletagmanager.com/gtag/js |
| JS | `plugin:royal-elementor-addons` | 296.3 Ko | 95 % | /wp-content/plugins/royal-elementor-addons/assets/js/frontend.min.js |
| JS | `tiers:www.googletagmanager.com` | 575.4 Ko | 45 % | https://www.googletagmanager.com/gtag/js |
| JS | `tiers:www.googletagmanager.com` | 566.0 Ko | 45 % | https://www.googletagmanager.com/gtag/js |
| JS | `tiers:www.googletagmanager.com` | 467.8 Ko | 53 % | https://www.googletagmanager.com/gtm.js |
| JS | `tiers:my.zadarma.com` | 276.0 Ko | 88 % | https://my.zadarma.com/callmewidget/v2.0.9/jssip.min.js |
| JS | `tiers:accounts.google.com` | 267.7 Ko | 82 % | https://accounts.google.com/gsi/client |
| JS | `plugin:elementor` | 140.3 Ko | 97 % | /wp-content/plugins/elementor/assets/lib/swiper/v8/swiper.min.js |
| JS | `wp-core` | 128.7 Ko | 87 % | /wp-includes/js/dist/vendor/react-dom.min.js |
| CSS | `elementor:generated-css` | 105.5 Ko | 100 % | /wp-content/uploads/elementor/google-fonts/css/roboto.css |
| JS | `wp-core` | 143.0 Ko | 69 % | /wp-includes/js/dist/blocks.min.js |
| CSS | `plugin:wp-user-avatar` | 98.5 Ko | 100 % | /wp-content/plugins/wp-user-avatar/assets/css/frontend.min.css |
| CSS | `plugin:woocommerce` | 90.0 Ko | 96 % | /wp-content/plugins/woocommerce/assets/css/woocommerce.css |
| JS | `plugin:woocommerce-google-adwords-conversion-tracking-tag` | 128.1 Ko | 67 % | /wp-content/plugins/woocommerce-google-adwords-conversion-tracking-tag/js/public/free/pmw-public.p1.min.js |
| CSS | `plugin:alma-gateway-for-woocommerce` | 87.0 Ko | 95 % | /wp-content/plugins/alma-gateway-for-woocommerce/build/alma-gateway-block/style-alma-gateway-block.css |
| JS | `tiers:challenges.cloudflare.com` | 84.7 Ko | 90 % | https://challenges.cloudflare.com/turnstile/v0/api.js |
| CSS | `plugin:elementor` | 58.0 Ko | 99 % | /wp-content/plugins/elementor/assets/lib/font-awesome/css/all.min.css |
| JS | `plugin:google-site-kit` | 71.2 Ko | 75 % | /wp-content/plugins/google-site-kit/dist/assets/js/googlesitekit-consent-mode-755f1678e260138d789e.js |
| JS | `tiers:cdn-cookieyes.com` | 97.0 Ko | 53 % | https://cdn-cookieyes.com/client_data/0ea5729425dc239df232d1a8/script.js |
| JS | `plugin:wp-user-avatar` | 67.9 Ko | 75 % | /wp-content/plugins/wp-user-avatar/assets/select2/select2.min.js |
| JS | `wp-core` | 55.9 Ko | 84 % | /wp-includes/js/dist/commands.min.js |
| JS | `plugin:wp-user-avatar` | 51.5 Ko | 91 % | /wp-content/plugins/wp-user-avatar/assets/flatpickr/flatpickr.min.js |
| JS | `plugin:elementor` | 76.5 Ko | 61 % | /wp-content/plugins/elementor/assets/js/chunks/lightbox-lightbox.min.js |
| JS | `tiers:cdn-cookieyes.com` | 99.7 Ko | 46 % | https://cdn-cookieyes.com/client_data/0ea5729425dc239df232d1a8/banner.js |
| CSS | `elementor:generated-css` | 53.6 Ko | 85 % | /wp-content/uploads/elementor/css/custom-frontend.min.css |
| JS | `wp-core` | 49.3 Ko | 88 % | /wp-includes/js/dist/upload-media.min.js |
| JS | `wp-core` | 57.4 Ko | 75 % | /wp-includes/js/dist/vendor/moment.min.js |
| JS | `plugin:royal-elementor-addons` | 42.0 Ko | 98 % | /wp-content/plugins/royal-elementor-addons/assets/js/lib/particles/particles.js |
| JS | `wp-core` | 42.2 Ko | 87 % | /wp-includes/js/dist/rich-text.min.js |
| JS | `wp-core` | 59.7 Ko | 55 % | /wp-includes/js/dist/theme.min.js |
| JS | `plugin:elementor` | 51.8 Ko | 62 % | /wp-content/plugins/elementor/assets/js/frontend-modules.min.js |
| JS | `wp-core:jquery` | 85.5 Ko | 36 % | /wp-includes/js/jquery/jquery.min.js |
| JS | `tiers:scripts.clarity.ms` | 73.3 Ko | 41 % | https://scripts.clarity.ms/0.8.70/clarity.js |
| CSS | `tiers:my.zadarma.com` | 28.6 Ko | 100 % | https://my.zadarma.com/callmewidget/v2.0.9/style.min.css |
| JS | `tiers:my.zadarma.com` | 29.4 Ko | 93 % | https://my.zadarma.com/callbackWidget/js/combine.min.js |
| JS | `plugin:woocommerce-google-adwords-conversion-tracking-tag` | 31.8 Ko | 80 % | /wp-content/plugins/woocommerce-google-adwords-conversion-tracking-tag/js/public/free/pixel-google.dd64da84.chunk.min.js |
| CSS | `elementor:generated-css` | 24.6 Ko | 100 % | /wp-content/uploads/elementor/google-fonts/css/robotoslab.css |
| CSS | `plugin:prisna-wp-translate` | 31.7 Ko | 76 % | /wp-content/plugins/prisna-wp-translate/styles/translator-s.css |
| JS | `tiers:my.zadarma.com` | 23.8 Ko | 99 % | https://my.zadarma.com/callmewidget/v2.0.9/widget.min.js |
| JS | `plugin:elementor` | 58.6 Ko | 39 % | /wp-content/plugins/elementor/assets/js/frontend.min.js |
| JS | `wp-core` | 29.4 Ko | 77 % | /wp-includes/js/dist/compose.min.js |
| CSS | `elementor:generated-css` | 30.2 Ko | 75 % | /wp-content/uploads/elementor/css/post-209.css |
| CSS | `site` | 30.2 Ko | 75 % | / |
| JS | `tiers:my.zadarma.com` | 24.3 Ko | 86 % | https://my.zadarma.com/callbackWidget/js/main.min.js |
| JS | `plugin:wp-user-avatar` | 22.1 Ko | 88 % | /wp-content/plugins/wp-user-avatar/assets/js/frontend.min.js |
| CSS | `site` | 19.2 Ko | 100 % | / |
| CSS | `elementor:generated-css` | 19.2 Ko | 100 % | /wp-content/uploads/elementor/css/post-11019.css |
| CSS | `tiers:fonts.googleapis.com` | 19.1 Ko | 100 % | https://fonts.googleapis.com/css |
| CSS | `plugin:woocommerce` | 19.7 Ko | 94 % | /wp-content/plugins/woocommerce/assets/css/woocommerce-layout.css |
| JS | `tiers:widget.xpay.sh` | 40.2 Ko | 44 % | https://widget.xpay.sh/v1/storefront.js |
| CSS | `elementor:generated-css` | 16.5 Ko | 100 % | /wp-content/uploads/elementor/google-fonts/css/poppins.css |
| JS | `plugin:woocommerce-google-adwords-conversion-tracking-tag` | 23.6 Ko | 69 % | /wp-content/plugins/woocommerce-google-adwords-conversion-tracking-tag/js/public/free/consent-management.0f558e13.chunk.min.js |
| JS | `plugin:royal-elementor-addons` | 17.5 Ko | 93 % | /wp-content/plugins/royal-elementor-addons/assets/js/lib/perfect-scrollbar/perfect-scrollbar.min.js |
| JS | `plugin:royal-elementor-addons` | 17.5 Ko | 93 % | /wp-content/plugins/royal-elementor-addons/assets/js/lib/perfect-scrollbar/perfect-scrollbar.min.js |
| CSS | `plugin:elementor` | 16.1 Ko | 100 % | /wp-content/plugins/elementor/assets/css/conditionals/dialog.min.css |
| CSS | `plugin:elementor` | 16.1 Ko | 100 % | /wp-content/plugins/elementor/assets/lib/swiper/v8/css/swiper.min.css |
| JS | `wp-core:jquery` | 17.9 Ko | 88 % | /wp-includes/js/jquery/ui/draggable.min.js |
| JS | `wp-core` | 26.0 Ko | 58 % | /wp-includes/js/dist/data.min.js |
| CSS | `plugin:wp-user-avatar` | 14.6 Ko | 100 % | /wp-content/plugins/wp-user-avatar/assets/select2/select2.min.css |
| CSS | `plugin:woo-guaranteed-reviews-company` | 15.4 Ko | 93 % | /wp-content/plugins/woo-guaranteed-reviews-company/assets/css/main.css |
| CSS | `plugin:royal-elementor-addons` | 13.9 Ko | 100 % | /wp-content/plugins/royal-elementor-addons/assets/css/lib/animations/text-animations.min.css |
| JS | `plugin:royal-elementor-addons` | 16.9 Ko | 79 % | /wp-content/plugins/royal-elementor-addons/assets/js/lib/parallax/parallax.min.js |
| CSS | `site` | 13.2 Ko | 100 % | / |
| CSS | `elementor:generated-css` | 13.2 Ko | 100 % | /wp-content/uploads/elementor/css/post-12333.css |
| CSS | `tiers:cdn.jsdelivr.net` | 13.2 Ko | 96 % | https://cdn.jsdelivr.net/npm/@alma/widgets@4.x.x/dist/widgets.min.css |
| CSS | `plugin:wp-user-avatar` | 12.6 Ko | 100 % | /wp-content/plugins/wp-user-avatar/assets/flatpickr/flatpickr.min.css |
| JS | `wp-core:jquery` | 18.2 Ko | 65 % | /wp-includes/js/jquery/ui/core.min.js |
| JS | `plugin:royal-elementor-addons` | 15.0 Ko | 78 % | /wp-content/plugins/royal-elementor-addons/assets/js/lib/jarallax/jarallax.min.js |
| CSS | `elementor:generated-css` | 11.0 Ko | 100 % | /wp-content/uploads/elementor/css/custom-lightbox.min.css |
| CSS | `theme:royal-elementor-kit` | 13.3 Ko | 83 % | /wp-content/themes/royal-elementor-kit/style.css |
| JS | `tiers:my.zadarma.com` | 20.7 Ko | 53 % | https://my.zadarma.com/callmewidget/v2.0.9/detectWebRTC.min.js |
| JS | `plugin:royal-elementor-addons` | 11.3 Ko | 95 % | /wp-content/plugins/royal-elementor-addons/assets/js/modal-popups.min.js |
| JS | `plugin:elementor` | 22.6 Ko | 47 % | /wp-content/plugins/elementor/assets/js/chunks/background-slideshow.min.js |
| JS | `wp-core` | 12.9 Ko | 81 % | /wp-includes/js/dist/dom.min.js |
| CSS | `elementor:generated-css` | 10.0 Ko | 100 % | /wp-content/uploads/elementor/css/custom-widget-icon-list.min.css |
| JS | `plugin:google-site-kit` | 11.3 Ko | 85 % | /wp-content/plugins/google-site-kit/dist/assets/js/googlesitekit-events-provider-content-events-c7172f3fd0e4d5ad62ef.js |
| JS | `tiers:widget.xpay.sh` | 28.0 Ko | 34 % | https://widget.xpay.sh/widget/v1/track.js |
| CSS | `site` | 9.4 Ko | 100 % | / |
| CSS | `elementor:generated-css` | 9.4 Ko | 100 % | /wp-content/uploads/elementor/css/post-12338.css |
| JS | `plugin:elementor` | 11.3 Ko | 83 % | /wp-content/plugins/elementor/assets/lib/dialog/dialog.min.js |
| JS | `wp-core` | 141.4 Ko | 6 % | /wp-includes/js/dist/date.min.js |
| JS | `plugin:elementor` | 20.2 Ko | 42 % | /wp-content/plugins/elementor/assets/js/chunks/text-editor.min.js |
| CSS | `plugin:wpb-accordion-menu-or-category` | 7.8 Ko | 100 % | /wp-content/plugins/wpb-accordion-menu-or-category/assets/css/wpb_wmca_style.css |
| JS | `plugin:woocommerce` | 9.4 Ko | 82 % | /wp-content/plugins/woocommerce/assets/js/jquery-blockui/jquery.blockUI.min.js |
| CSS | `plugin:woocommerce` | 8.5 Ko | 91 % | /wp-content/plugins/woocommerce/assets/css/woocommerce-smallscreen.css |
| JS | `plugin:wpb-accordion-menu-or-category` | 11.5 Ko | 65 % | /wp-content/plugins/wpb-accordion-menu-or-category/assets/js/jquery.navgoco.min.js |
| JS | `plugin:ag-core` | 10.5 Ko | 68 % | https://www.societe-des-avis-garantis.fr/wp-content/plugins/ag-core/widgets/JsWidget.js |
| JS | `wp-core` | 10.5 Ko | 64 % | /wp-includes/js/dist/vendor/react.min.js |
| JS | `plugin:google-site-kit` | 7.2 Ko | 93 % | /wp-content/plugins/google-site-kit/dist/assets/js/googlesitekit-events-provider-contact-form-7-fac2a8013ba5ba355cd0.js |
| JS | `wp-core` | 9.7 Ko | 59 % | /wp-includes/js/dist/redux-routine.min.js |
| JS | `wp-core:jquery` | 13.3 Ko | 42 % | /wp-includes/js/jquery/jquery-migrate.min.js |
| JS | `plugin:woocommerce-google-adwords-conversion-tracking-tag` | 7.1 Ko | 78 % | /wp-content/plugins/woocommerce-google-adwords-conversion-tracking-tag/js/public/free/deprecated-functions.6e8ae3ec.chunk.min.js |
| JS | `wp-core` | 12.2 Ko | 44 % | /wp-includes/js/dist/element.min.js |
| CSS | `plugin:royal-elementor-addons` | 5.2 Ko | 100 % | /wp-content/plugins/royal-elementor-addons/assets/css/lib/animations/wpr-link-animations.min.css |
| CSS | `plugin:elementor` | 5.4 Ko | 97 % | /wp-content/plugins/elementor/assets/css/conditionals/e-swiper.min.css |
| CSS | `site` | 5.6 Ko | 90 % | / |
| JS | `wp-core` | 5.6 Ko | 87 % | /wp-includes/js/dist/autop.min.js |
| CSS | `site` | 4.7 Ko | 100 % | / |
| CSS | `elementor:generated-css` | 4.7 Ko | 100 % | /wp-content/uploads/elementor/css/post-12330.css |
| JS | `wp-core` | 7.8 Ko | 59 % | /wp-includes/js/dist/preferences.min.js |
| JS | `wp-core` | 6.8 Ko | 64 % | /wp-includes/js/dist/style-engine.min.js |
| JS | `tiers:widget.xpay.sh` | 25.2 Ko | 17 % | https://widget.xpay.sh/widget/v1/shell/features/pill.js |
| JS | `wp-core` | 7.3 Ko | 55 % | /wp-includes/js/dist/api-fetch.min.js |
| JS | `wp-core` | 5.6 Ko | 71 % | /wp-includes/js/dist/preferences-persistence.min.js |
| CSS | `elementor:generated-css` | 26.3 Ko | 15 % | /wp-content/uploads/elementor/css/post-5318.css |
| CSS | `site` | 3.6 Ko | 100 % | / |
| CSS | `elementor:generated-css` | 3.6 Ko | 100 % | /wp-content/uploads/elementor/css/post-12296.css |
| CSS | `site` | 3.6 Ko | 100 % | / |
| CSS | `elementor:generated-css` | 3.6 Ko | 100 % | /wp-content/uploads/elementor/css/post-12291.css |
| JS | `plugin:wpb-accordion-menu-or-category` | 4.6 Ko | 72 % | /wp-content/plugins/wpb-accordion-menu-or-category/assets/js/accordion-init.js |
| CSS | `plugin:alma-gateway-for-woocommerce` | 3.0 Ko | 100 % | /wp-content/plugins/alma-gateway-for-woocommerce/build/alma-gateway-block/alma-gateway-block.css |
| JS | `tiers:widget.xpay.sh` | 13.0 Ko | 22 % | https://widget.xpay.sh/widget/v1/shell/features/chip-stack.js |
| JS | `wp-core` | 5.7 Ko | 49 % | /wp-includes/js/dist/i18n.min.js |
| JS | `tiers:widget.xpay.sh` | 9.2 Ko | 29 % | https://widget.xpay.sh/widget/v1/shell/runtime.js |
| JS | `plugin:wpb-accordion-menu-or-category` | 3.1 Ko | 86 % | /wp-content/plugins/wpb-accordion-menu-or-category/assets/js/jquery.cookie.js |
| JS | `plugin:woocommerce` | 4.2 Ko | 61 % | /wp-content/plugins/woocommerce/assets/js/frontend/woocommerce.min.js |
| JS | `wp-core` | 10.2 Ko | 25 % | /wp-includes/js/dist/url.min.js |
| JS | `wp-core:jquery` | 3.2 Ko | 79 % | /wp-includes/js/jquery/ui/mouse.min.js |
| JS | `wp-core` | 22.2 Ko | 11 % | /wp-includes/js/wp-emoji-release.min.js |
| JS | `wp-core` | 3.3 Ko | 73 % | /wp-includes/js/dist/shortcode.min.js |
| JS | `wp-core` | 3.9 Ko | 63 % | /wp-includes/js/dist/notices.min.js |
| CSS | `plugin:prisna-wp-translate` | 2.9 Ko | 82 % | /wp-content/plugins/prisna-wp-translate/styles/blocks.css |
| JS | `plugin:google-site-kit` | 2.6 Ko | 87 % | /wp-content/plugins/google-site-kit/dist/assets/js/googlesitekit-events-provider-woocommerce-86c5a3ff303665006998.js |
| JS | `site` | 3.8 Ko | 58 % | / |
| JS | `tiers:googleads.g.doubleclick.net` | 5.1 Ko | 41 % | https://googleads.g.doubleclick.net/pagead/viewthroughconversion/969053143/ |
| JS | `tiers:googleads.g.doubleclick.net` | 5.1 Ko | 41 % | https://googleads.g.doubleclick.net/pagead/viewthroughconversion/969053143/ |
| JS | `site` | 2.2 Ko | 95 % | / |
| JS | `plugin:wp-consent-api` | 2.2 Ko | 94 % | /wp-content/plugins/wp-consent-api/assets/js/wp-consent-api.min.js |
| JS | `wp-core` | 3.4 Ko | 59 % | /wp-includes/js/dist/priority-queue.min.js |
| CSS | `elementor:generated-css` | 9.5 Ko | 21 % | /wp-content/uploads/elementor/css/post-212.css |
| CSS | `site` | 9.5 Ko | 21 % | / |
| JS | `plugin:woocommerce` | 2.4 Ko | 75 % | /wp-content/plugins/woocommerce/assets/js/frontend/order-attribution.min.js |
| JS | `plugin:woocommerce-google-adwords-conversion-tracking-tag` | 2.5 Ko | 71 % | /wp-content/plugins/woocommerce-google-adwords-conversion-tracking-tag/js/public/free/consent-api.e8630f08.chunk.min.js |
| JS | `wp-core` | 3.6 Ko | 48 % | /wp-includes/js/dist/keyboard-shortcuts.min.js |
| JS | `wp-core` | 2.5 Ko | 69 % | /wp-includes/js/dist/block-serialization-default-parser.min.js |
| JS | `plugin:google-site-kit` | 2.6 Ko | 61 % | /wp-content/plugins/google-site-kit/dist/assets/js/sign-in-with-google-5a9e012872c712a23a12.js |
| JS | `wp-core` | 5.0 Ko | 32 % | /wp-includes/js/dist/hooks.min.js |
| JS | `wp-core` | 3.0 Ko | 51 % | /wp-includes/js/dist/keycodes.min.js |
| JS | `wp-core` | 3.0 Ko | 50 % | /wp-includes/js/wp-emoji-loader.min.js |
| CSS | `elementor:generated-css` | 2.4 Ko | 61 % | /wp-content/uploads/elementor/css/custom-widget-image-gallery.min.css |
| JS | `plugin:elementor` | 2.7 Ko | 48 % | /wp-content/plugins/elementor/assets/lib/share-link/share-link.min.js |
| JS | `plugin:woocommerce-google-adwords-conversion-tracking-tag` | 2.3 Ko | 53 % | /wp-content/plugins/woocommerce-google-adwords-conversion-tracking-tag/js/public/free/wc-hooks.61457d30.chunk.min.js |
| JS | `tiers:widget.xpay.sh` | 6.1 Ko | 18 % | https://widget.xpay.sh/widget/v1/shell/features/fab.js |
| JS | `wp-core` | 2.9 Ko | 32 % | /wp-includes/js/dist/private-apis.min.js |
| JS | `wp-core` | 2.0 Ko | 45 % | /wp-includes/js/dist/primitives.min.js |
| JS | `plugin:agentic-commerce-for-woocommerce` | 3.5 Ko | 25 % | /wp-content/plugins/agentic-commerce-for-woocommerce/js/xpay-attribution.js |
| JS | `wp-core` | 2.6 Ko | 22 % | /wp-includes/js/dist/a11y.min.js |
| JS | `site` | 2.1 Ko | 0 % | / |
| JS | `plugin:prisna-wp-translate` | 56.0 Ko | 0 % | /wp-content/plugins/prisna-wp-translate/javascript/translator.js |

## Toutes les requêtes (triées par poids)

| Poids | Durée | Source | URL |
|---:|---:|---|---|
| 522.2 Ko | 18020 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/block-editor.min.js?ver=37e58da384b2558bd479 |
| 314.2 Ko | 16840 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/components.min.js?ver=9b751f17060211272a5c |
| 200.2 Ko | 7728 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/09/screenshot-la-maison-du-dos-com-2025-09-08-11-18-… |
| 191.5 Ko | 5690 ms | `tiers:www.googletagmanager.com` | https://www.googletagmanager.com/gtag/js?id=G-HH41XH2SV1&cx=c&gtm=4e69s1h1 |
| 191.4 Ko | 10850 ms | `tiers:www.googletagmanager.com` | https://www.googletagmanager.com/gtag/js?id=G-HH41XH2SV1 |
| 187.3 Ko | 5224 ms | `tiers:www.googletagmanager.com` | https://www.googletagmanager.com/gtag/js?id=AW-969053143&cx=c&gtm=4e69s1 |
| 186.7 Ko | 10571 ms | `tiers:www.googletagmanager.com` | https://www.googletagmanager.com/gtag/js?id=G-VJSQ20QJ4W |
| 159.8 Ko | 7625 ms | `tiers:www.googletagmanager.com` | https://www.googletagmanager.com/gtm.js?id=GTM-PF26W689 |
| 99.8 Ko | 6349 ms | `tiers:accounts.google.com` | https://accounts.google.com/gsi/client |
| 72.1 Ko | 14631 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/js/frontend.min.js?ver=1.7.… |
| 68.0 Ko | 3050 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/css/frontend.min.css?ver=1.… |
| 63.0 Ko | 4476 ms | `tiers:my.zadarma.com` | https://my.zadarma.com/callmewidget/v2.0.9/jssip.min.js |
| 58.8 Ko | 3696 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2026/09/matelas-reglable.webp |
| 51.3 Ko | 8918 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/blocks.min.js?ver=c0e57a630a0b6f6c3bb5 |
| 48.4 Ko | 7050 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/vendor/react-dom.min.js?ver=18.3.1.1 |
| 45.7 Ko | 13596 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/lib/swiper/v8/swiper.min.js?ver=8.4.5 |
| 45.4 Ko | 3597 ms | `plugin:woocommerce-google-adwords-conversion-tracking-tag` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce-google-adwords-conversion-tracking-tag/js/pub… |
| 34.6 Ko | 2663 ms | `wp-core:jquery` | https://prod.la-maison-du-dos.com/wp-includes/js/jquery/jquery.min.js?ver=3.7.1 |
| 34.3 Ko | 1657 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/client_data/0ea5729425dc239df232d1a8/script.js |
| 33.4 Ko | 1327 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/client_data/0ea5729425dc239df232d1a8/banner.js |
| 31.1 Ko | 506 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/js/chunks/lightbox-lightbox.min.js?ver=4… |
| 29.5 Ko | 12448 ms | `plugin:google-site-kit` | https://prod.la-maison-du-dos.com/wp-content/plugins/google-site-kit/dist/assets/js/googlesitekit-consent-mode… |
| 29.1 Ko | 10388 ms | `plugin:prisna-wp-translate` | https://prod.la-maison-du-dos.com/wp-content/plugins/prisna-wp-translate/javascript/translator.js?ver=1.17.3 |
| 27.9 Ko | 8165 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/date.min.js?ver=8173fc0fc12b7bb7eaf0 |
| 27.8 Ko | 5004 ms | `tiers:challenges.cloudflare.com` | https://challenges.cloudflare.com/turnstile/v0/api.js |
| 25.5 Ko | 1294 ms | `tiers:scripts.clarity.ms` | https://scripts.clarity.ms/0.8.70/clarity.js |
| 25.5 Ko | 8508 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/theme.min.js?ver=56a75cc08ae66c1fcb7a |
| 24.4 Ko | 613 ms | `plugin:prisna-wp-translate` | https://prod.la-maison-du-dos.com/wp-content/plugins/prisna-wp-translate/images/all-s.png?ver=1.17.3 |
| 23.4 Ko | 3140 ms | `plugin:wp-user-avatar` | https://prod.la-maison-du-dos.com/wp-content/plugins/wp-user-avatar/assets/select2/select2.min.js?ver=4.17.5 |
| 23.2 Ko | 405 ms | `tiers:nehl6uu58j.execute-api.us-east-1.amazonaws.com` | https://nehl6uu58j.execute-api.us-east-1.amazonaws.com/merchant/widget-config/public/la-maison-du-dos-com |
| 22.5 Ko | 9317 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/commands.min.js?ver=1a4910212c7ed2355300 |
| 22.0 Ko | 7830 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/vendor/moment.min.js?ver=2.30.1 |
| 21.6 Ko | 10943 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/js/frontend.min.js?ver=4.3.2 |
| 21.1 Ko | 683 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/matelas-eau-leger-300x300.jpg |
| 19.0 Ko | 10823 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/js/frontend-modules.min.js?ver=4.3.2 |
| 18.6 Ko | 9653 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/upload-media.min.js?ver=a2c026d433c295fd5145 |
| 18.4 Ko | 1436 ms | `plugin:wp-user-avatar` | https://prod.la-maison-du-dos.com/wp-content/plugins/wp-user-avatar/assets/css/frontend.min.css?ver=4.17.5 |
| 17.3 Ko | 4769 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/v1/storefront.js?ver=0.7.1 |
| 17.2 Ko | 2961 ms | `plugin:wp-user-avatar` | https://prod.la-maison-du-dos.com/wp-content/plugins/wp-user-avatar/assets/flatpickr/flatpickr.min.js?ver=4.17… |
| 17.0 Ko | 7279 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/rich-text.min.js?ver=3e5852e42cee1c239bae |
| 16.9 Ko | 1049 ms | `plugin:alma-gateway-for-woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/alma-gateway-for-woocommerce/build/alma-gateway-block/sty… |
| 14.5 Ko | 2213 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/lib/font-awesome/css/all.min.css?ver=1.7… |
| 14.5 Ko | 884 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/tribrid-fluid-300x300.jpg |
| 14.2 Ko | 1034 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/css/woocommerce.css?ver=11.1.2 |
| 13.5 Ko | 325 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/02/website-pagina-slapen-op-lucht_Page_1_Image_0002-… |
| 13.3 Ko | 3266 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-template_images_logo-boutique-pc.webp |
| 13.3 Ko | 6452 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/compose.min.js?ver=0e8bde2a499ea6073b42 |
| 13.2 Ko | 967 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/lib/font-awesome/webfonts/fa-regular-400… |
| 13.0 Ko | 513 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/widget/v1/track.js |
| 11.2 Ko | 6558 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/data.min.js?ver=14a216e0932d72c22976 |
| 11.1 Ko | 10777 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/js/lib/particles/particles.… |
| 10.9 Ko | 355 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/widget/v1/shell/features/pill.js |
| 10.7 Ko | 1313 ms | `tiers:my.zadarma.com` | https://my.zadarma.com/callmewidget/v2.0.9/style.min.css |
| 9.6 Ko | 926 ms | `tiers:my.zadarma.com` | https://my.zadarma.com/callbackWidget/js/combine.min.js?v=1.15.4 |
| 9.4 Ko | 899 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/Babymadras1-300x300.jpg |
| 9.4 Ko | 948 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/02/Matelas_reglable_emotion1-300x300.webp |
| 9.3 Ko | 375 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/js/chunks/background-slideshow.min.js?ve… |
| 9.1 Ko | 320 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/matelaseau2.webp |
| 9.1 Ko | 1379 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/custom-frontend.min.css?ver=1790748725 |
| 9.0 Ko | 706 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/10/Carbon-iQ-Akva.jpg |
| 8.5 Ko | 291 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/02/Matelas_reglable_life1-300x300.webp |
| 8.4 Ko | 714 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/js/chunks/text-editor.min.js?ver=4.3.2 |
| 8.4 Ko | 313 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/10/hefel_softbausch.jpg |
| 7.6 Ko | 466 ms | `plugin:woocommerce-google-adwords-conversion-tracking-tag` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce-google-adwords-conversion-tracking-tag/js/pub… |
| 7.6 Ko | 4185 ms | `wp-core:jquery` | https://prod.la-maison-du-dos.com/wp-includes/js/jquery/ui/core.min.js?ver=1.14.2 |
| 7.6 Ko | 10440 ms | `plugin:woocommerce-google-adwords-conversion-tracking-tag` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce-google-adwords-conversion-tracking-tag/js/pub… |
| 7.5 Ko | 503 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/10/logo2-akva.jpg |
| 6.9 Ko | 791 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/02/pure-300x213.webp |
| 6.9 Ko | 254 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/widget/v1/shell/features/chip-stack.js |
| 6.8 Ko | 403 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/wp-emoji-release.min.js?ver=7.1.2 |
| 6.6 Ko | 1201 ms | `wp-ajax` | https://prod.la-maison-du-dos.com/wp-admin/admin-ajax.php |
| 6.5 Ko | 9974 ms | `plugin:wp-user-avatar` | https://prod.la-maison-du-dos.com/wp-content/plugins/wp-user-avatar/assets/js/frontend.min.js?ver=4.17.5 |
| 6.5 Ko | 2811 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/js/lib/perfect-scrollbar/pe… |
| 6.3 Ko | 2096 ms | `tiers:my.zadarma.com` | https://my.zadarma.com/callbackWidget/js/main.min.js?v=1.15.4 |
| 6.2 Ko | 11191 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/js/lib/parallax/parallax.mi… |
| 6.2 Ko | 10840 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/js/lib/jarallax/jarallax.mi… |
| 6.2 Ko | 254 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/client_data/0ea5729425dc239df232d1a8/config/t8mN_D0E.json |
| 6.0 Ko | 9645 ms | `wp-core:jquery` | https://prod.la-maison-du-dos.com/wp-includes/js/jquery/ui/draggable.min.js?ver=1.14.2 |
| 6.0 Ko | 5596 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/element.min.js?ver=4a4370b2b349066fd440 |
| 6.0 Ko | 5248 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/dom.min.js?ver=c95f94cbbc1ac3fde84f |
| 5.8 Ko | 1437 ms | `tiers:my.zadarma.com` | https://my.zadarma.com/callmewidget/v2.0.9/detectWebRTC.min.js |
| 5.7 Ko | 2433 ms | `wp-core:jquery` | https://prod.la-maison-du-dos.com/wp-includes/js/jquery/jquery-migrate.min.js?ver=3.4.1 |
| 5.7 Ko | 11561 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/js/sourcebuster/sourcebuster.min.js?ve… |
| 5.7 Ko | 3082 ms | `tiers:my.zadarma.com` | https://my.zadarma.com/callmewidget/v2.0.9/widget.min.js |
| 5.4 Ko | 1879 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/lib/swiper/v8/css/swiper.min.css?ver=8.4… |
| 5.3 Ko | 538 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/widget/v1/shell/runtime.js |
| 5.1 Ko | 3347 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/Lyra_01.webp |
| 5.1 Ko | 1128 ms | `plugin:woo-guaranteed-reviews-company` | https://prod.la-maison-du-dos.com/wp-content/plugins/woo-guaranteed-reviews-company/assets/css/main.css?ver=1.… |
| 5.0 Ko | 4520 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/vendor/react.min.js?ver=18.3.1.1 |
| 4.9 Ko | 1657 ms | `theme:royal-elementor-kit` | https://prod.la-maison-du-dos.com/wp-content/themes/royal-elementor-kit/style.css?ver=1.0 |
| 4.8 Ko | 8287 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/url.min.js?ver=7b0de086d4ae11d55704 |
| 4.7 Ko | 488 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/widget/v1/shell/features/fab.js |
| 4.7 Ko | 1409 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-209.css?ver=1790748721 |
| 4.6 Ko | 479 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/lib/dialog/dialog.min.js?ver=4.9.3 |
| 4.6 Ko | 677 ms | `plugin:prisna-wp-translate` | https://prod.la-maison-du-dos.com/wp-content/plugins/prisna-wp-translate/styles/translator-s.css?ver=1.17.3 |
| 4.5 Ko | 841 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/css/woocommerce-layout.css?ver=11.1.2 |
| 4.4 Ko | 498 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/10/logo-TASSO-CARRE.jpg |
| 4.2 Ko | 663 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/LogoTTIEWPlogo-marque-1448474026.webp |
| 4.2 Ko | 2454 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/js/jquery-blockui/jquery.blockUI.min.j… |
| 4.2 Ko | 13237 ms | `plugin:google-site-kit` | https://prod.la-maison-du-dos.com/wp-content/plugins/google-site-kit/dist/assets/js/googlesitekit-events-provi… |
| 4.1 Ko | 13137 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/10/paiements.jpg |
| 4.1 Ko | 6380 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/redux-routine.min.js?ver=acca2b4857d83ad1790e |
| 3.9 Ko | 11191 ms | `plugin:wpb-accordion-menu-or-category` | https://prod.la-maison-du-dos.com/wp-content/plugins/wpb-accordion-menu-or-category/assets/js/jquery.navgoco.m… |
| 3.9 Ko | 8603 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/preferences.min.js?ver=5a169e3fc0e657f74172 |
| 3.9 Ko | 261 ms | `tiers:cdn.jsdelivr.net` | https://cdn.jsdelivr.net/npm/@alma/widgets@4.x.x/dist/widgets.min.css?ver=6.7.0 |
| 3.9 Ko | 8230 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/api-fetch.min.js?ver=6f2a4faeee3c722b1e57 |
| 3.8 Ko | 12449 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/js/modal-popups.min.js?ver=… |
| 3.5 Ko | 1255 ms | `plugin:wp-user-avatar` | https://prod.la-maison-du-dos.com/wp-content/plugins/wp-user-avatar/assets/flatpickr/flatpickr.min.css?ver=4.1… |
| 3.5 Ko | 700 ms | `plugin:woo-guaranteed-reviews-company` | https://prod.la-maison-du-dos.com/wp-content/plugins/woo-guaranteed-reviews-company/assets/images/star_off.png |
| 3.4 Ko | 3735 ms | `plugin:ag-core` | https://www.societe-des-avis-garantis.fr/wp-content/plugins/ag-core/widgets/JsWidget.js?ver=1.3.0 |
| 3.4 Ko | 231 ms | `tiers:googleads.g.doubleclick.net` | https://googleads.g.doubleclick.net/pagead/viewthroughconversion/969053143/?random=1790749640926&cv=11&fst=179… |
| 3.4 Ko | 248 ms | `tiers:googleads.g.doubleclick.net` | https://googleads.g.doubleclick.net/pagead/viewthroughconversion/969053143/?random=1790749640760&cv=11&fst=179… |
| 3.1 Ko | 2156 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/css/roboto.css?ver=1742292385 |
| 3.0 Ko | 1314 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-5318.css?ver=1790749030 |
| 3.0 Ko | 4349 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/i18n.min.js?ver=1dfe7db3940c23ea9216 |
| 3.0 Ko | 8849 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/style-engine.min.js?ver=914befb08774033e6265 |
| 3.0 Ko | 1091 ms | `plugin:wp-user-avatar` | https://prod.la-maison-du-dos.com/wp-content/plugins/wp-user-avatar/assets/select2/select2.min.css?ver=7.1.2 |
| 2.9 Ko | 13066 ms | `plugin:google-site-kit` | https://prod.la-maison-du-dos.com/wp-content/plugins/google-site-kit/dist/assets/js/googlesitekit-events-provi… |
| 2.9 Ko | 324 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/widget/v1/shell/features/scroll-collapse.js |
| 2.8 Ko | 475 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/widget/v1/shell/features/scrim.js |
| 2.7 Ko | 4834 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/autop.min.js?ver=4e10a18cb6f21a043fc0 |
| 2.7 Ko | 8445 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/preferences-persistence.min.js?ver=a34abbdacd8f50f9acb1 |
| 2.7 Ko | 408 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/custom-lightbox.min.css?ver=4.3.2 |
| 2.7 Ko | 461 ms | `tiers:fonts.googleapis.com` | https://fonts.googleapis.com/css?family=Open+Sans:600,400,400i|Oswald:700&ver=7.1.2 |
| 2.6 Ko | 235 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logokirstenlogo-marque-1396450046.webp |
| 2.4 Ko | 2418 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/hooks.min.js?ver=f0f188028580e8dc1255 |
| 2.3 Ko | 1971 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/css/lib/animations/text-ani… |
| 2.3 Ko | 220 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/client_data/0ea5729425dc239df232d1a8/audit-table/E4-F0PLf.json |
| 2.3 Ko | 220 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/css/conditionals/dialog.min.css?ver=4.3.… |
| 2.2 Ko | 379 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/ATT00136logo-marque-1442234847.webp |
| 2.2 Ko | 9965 ms | `plugin:agentic-commerce-for-woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/agentic-commerce-for-woocommerce/js/xpay-attribution.js?v… |
| 2.2 Ko | 3871 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-11019.css?ver=1790748722 |
| 2.2 Ko | 8764 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/notices.min.js?ver=c09a068fdab0eb465e14 |
| 2.1 Ko | 242 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/assets/images/poweredbtcky.svg |
| 2.1 Ko | 7750 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/keyboard-shortcuts.min.js?ver=37da95806f2339bc80d0 |
| 2.1 Ko | 5659 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/priority-queue.min.js?ver=6c0aa59b65d55dfd509b |
| 2.1 Ko | 6793 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/shortcode.min.js?ver=f6273476300cc5fad4cd |
| 2.1 Ko | 9639 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/js/frontend/woocommerce.min.js?ver=11.… |
| 2.0 Ko | 719 ms | `plugin:wpb-accordion-menu-or-category` | https://prod.la-maison-du-dos.com/wp-content/plugins/wpb-accordion-menu-or-category/assets/css/wpb_wmca_style.… |
| 2.0 Ko | 5575 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/keycodes.min.js?ver=d0b4204e4bbeb412df6e |
| 2.0 Ko | 227 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-Bodyform_logo.webp |
| 2.0 Ko | 892 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/css/woocommerce-smallscreen.css?ver=11… |
| 2.0 Ko | 228 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/Formesse_LogoSlogan_RGB.webp |
| 1.9 Ko | 11098 ms | `plugin:wpb-accordion-menu-or-category` | https://prod.la-maison-du-dos.com/wp-content/plugins/wpb-accordion-menu-or-category/assets/js/jquery.cookie.js… |
| 1.8 Ko | 278 ms | `plugin:woocommerce-google-adwords-conversion-tracking-tag` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce-google-adwords-conversion-tracking-tag/js/pub… |
| 1.8 Ko | 11218 ms | `plugin:wpb-accordion-menu-or-category` | https://prod.la-maison-du-dos.com/wp-content/plugins/wpb-accordion-menu-or-category/assets/js/accordion-init.j… |
| 1.8 Ko | 549 ms | `plugin:ag-core` | https://www.societe-des-avis-garantis.fr/wp-content/plugins/ag-core/widgets/cache/jsv2/9649.html |
| 1.8 Ko | 1599 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-212.css?ver=1790748722 |
| 1.8 Ko | 3559 ms | `plugin:google-site-kit` | https://prod.la-maison-du-dos.com/wp-content/plugins/google-site-kit/dist/assets/js/sign-in-with-google-5a9e01… |
| 1.7 Ko | 221 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/assets/images/revisit.svg |
| 1.7 Ko | 3829 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/custom-widget-icon-list.min.css?ver=1790748… |
| 1.6 Ko | 4747 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/block-serialization-default-parser.min.js?ver=4c6f3dd400… |
| 1.6 Ko | 254 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logobodytonelogo-marque-1399716526.webp |
| 1.6 Ko | 717 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/lib/share-link/share-link.min.js?ver=4.3… |
| 1.6 Ko | 5901 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/private-apis.min.js?ver=eb85f28c4c729bb4f002 |
| 1.6 Ko | 9258 ms | `plugin:prisna-wp-translate` | https://prod.la-maison-du-dos.com/wp-content/plugins/prisna-wp-translate/javascript/blocks.class.js?ver=1.17.3 |
| 1.6 Ko | 11503 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/js/frontend/order-attribution.min.js?v… |
| 1.6 Ko | 231 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/client_data/0ea5729425dc239df232d1a8/translations/WYHDtpqk.json |
| 1.5 Ko | 9316 ms | `wp-core:jquery` | https://prod.la-maison-du-dos.com/wp-includes/js/jquery/ui/mouse.min.js?ver=1.14.2 |
| 1.5 Ko | 13377 ms | `plugin:google-site-kit` | https://prod.la-maison-du-dos.com/wp-content/plugins/google-site-kit/dist/assets/js/googlesitekit-events-provi… |
| 1.5 Ko | 6757 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/a11y.min.js?ver=31c6cec5a4ff7aff483d |
| 1.4 Ko | 7307 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/primitives.min.js?ver=44cc5a35c7b9fe07a838 |
| 1.4 Ko | 457 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logo_profine.webp |
| 1.4 Ko | 264 ms | `plugin:woocommerce-google-adwords-conversion-tracking-tag` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce-google-adwords-conversion-tracking-tag/js/pub… |
| 1.4 Ko | 287 ms | `tiers:accounts.google.com` | https://accounts.google.com/gsi/style |
| 1.4 Ko | 3772 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-12333.css?ver=1790748722 |
| 1.4 Ko | 1893 ms | `plugin:royal-elementor-addons` | https://prod.la-maison-du-dos.com/wp-content/plugins/royal-elementor-addons/assets/css/lib/animations/wpr-link… |
| 1.4 Ko | 5916 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/undo-manager.min.js?ver=4554fce6276d8910a4ae |
| 1.3 Ko | 693 ms | `plugin:alma-gateway-for-woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/alma-gateway-for-woocommerce/build/alma-gateway-block/alm… |
| 1.3 Ko | 229 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/assets/images/close.svg |
| 1.3 Ko | 2149 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/css/robotoslab.css?ver=1742292388 |
| 1.3 Ko | 234 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-cropped-template_images_logo-boutique-pc-… |
| 1.3 Ko | 1871 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/css/conditionals/e-swiper.min.css?ver=4.… |
| 1.3 Ko | 11475 ms | `plugin:wp-consent-api` | https://prod.la-maison-du-dos.com/wp-content/plugins/wp-consent-api/assets/js/wp-consent-api.min.js?ver=2.1.0 |
| 1.3 Ko | 6189 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/html-entities.min.js?ver=a976ff3a0f00bc2999a3 |
| 1.2 Ko | 303 ms | `plugin:woocommerce-google-adwords-conversion-tracking-tag` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce-google-adwords-conversion-tracking-tag/js/pub… |
| 1.2 Ko | 661 ms | `uploads` | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/LOGO-MATRAIR.webp |
| 1.2 Ko | 2463 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/js/js-cookie/js.cookie.min.js?ver=2.1.… |
| 1.2 Ko | 9888 ms | `plugin:woo-guaranteed-reviews-company` | https://prod.la-maison-du-dos.com/wp-content/plugins/woo-guaranteed-reviews-company/assets/js/main.js?ver=1.3.… |
| 1.2 Ko | 6459 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/dom-ready.min.js?ver=3fe927cab37bf38d6a23 |
| 1.2 Ko | 8863 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/token-list.min.js?ver=e86ab419d8302d57822c |
| 1.2 Ko | 4869 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/deprecated.min.js?ver=fe587bac92b7d0ef760e |
| 1.2 Ko | 891 ms | `tiers:www.clarity.ms` | https://www.clarity.ms/tag/qffm4e3fjy?ref=gtm2 |
| 1.2 Ko | 3992 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-12338.css?ver=1790748723 |
| 1.1 Ko | 6821 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/warning.min.js?ver=a0978839debc564a6608 |
| 1.1 Ko | 2099 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/css/poppins.css?ver=1742312054 |
| 1.1 Ko | 4598 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/blob.min.js?ver=c7582a735ddd2edc9731 |
| 1.1 Ko | 1629 ms | `plugin:flexible-shipping` | https://prod.la-maison-du-dos.com/wp-content/plugins/flexible-shipping/assets/dist/css/free-shipping.css?ver=6… |
| 1.0 Ko | 4442 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/vendor/react-jsx-runtime.min.js?ver=18.3.1 |
| 1.0 Ko | 3757 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-12330.css?ver=1790748722 |
| 1.0 Ko | 5133 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/escape-html.min.js?ver=87ebe53e97bba59805a5 |
| 1.0 Ko | 4341 ms | `plugin:alma-gateway-for-woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/alma-gateway-for-woocommerce/assets/js/frontend/alma-chec… |
| 1.0 Ko | 5625 ms | `wp-core` | https://prod.la-maison-du-dos.com/wp-includes/js/dist/is-shallow-equal.min.js?ver=7ad271045c1fe60f5496 |
| 1.0 Ko | 3524 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-12291.css?ver=1790748722 |
| 1.0 Ko | 3549 ms | `tiers:c.clarity.ms` | https://c.clarity.ms/c.gif |
| 1.0 Ko | 3537 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-12296.css?ver=1790748722 |
| 1.0 Ko | 398 ms | `plugin:alma-gateway-for-woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/alma-gateway-for-woocommerce/assets/css/frontend/alma-che… |
| 0.9 Ko | 264 ms | `plugin:woocommerce-google-adwords-conversion-tracking-tag` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce-google-adwords-conversion-tracking-tag/js/pub… |
| 0.9 Ko | 585 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/js/chunks/section-stretched-section.min.… |
| 0.9 Ko | 1465 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/css/widget-heading.min.css?ver=4.3.2 |
| 0.8 Ko | 251 ms | `tiers:stats.g.doubleclick.net` | https://stats.g.doubleclick.net/g/collect?v=2&tid=G-VJSQ20QJ4W&cid=777122633.1790749634&gtm=45je69s1h1v8806745… |
| 0.8 Ko | 481 ms | `tiers:stats.g.doubleclick.net` | https://stats.g.doubleclick.net/g/collect?v=2&ngs=1&ibt=1&tid=G-HH41XH2SV1&cid=777122633.1790749634&gtm=45je69… |
| 0.8 Ko | 1643 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/post-6.css?ver=1790748724 |
| 0.8 Ko | 606 ms | `plugin:prisna-wp-translate` | https://prod.la-maison-du-dos.com/wp-content/plugins/prisna-wp-translate/styles/blocks.css?ver=1.17.3 |
| 0.8 Ko | 1814 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/custom-widget-image-box.min.css?ver=1790748… |
| 0.7 Ko | 11715 ms | `plugin:woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/woocommerce/assets/js/frontend/wp-consent-api-integration… |
| 0.7 Ko | 1885 ms | `elementor:generated-css` | https://prod.la-maison-du-dos.com/wp-content/uploads/elementor/css/custom-widget-image-gallery.min.css?ver=179… |
| 0.7 Ko | 2490 ms | `tiers:my.zadarma.com` | https://my.zadarma.com/callmewidget/v2.0.9/loader.js |
| 0.7 Ko | 1678 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/css/widget-spacer.min.css?ver=4.3.2 |
| 0.7 Ko | 216 ms | `tiers:cdn-cookieyes.com` | https://cdn-cookieyes.com/client_data/0ea5729425dc239df232d1a8/HKxm_GZM.json |
| 0.7 Ko | 3088 ms | `wp-ajax` | https://prod.la-maison-du-dos.com/wp-json/wp/v2/users/me?context=edit&_locale=user |
| 0.6 Ko | 219 ms | `plugin:prisna-wp-translate` | https://prod.la-maison-du-dos.com/wp-content/plugins/prisna-wp-translate/images/loading-s.gif?ver=1.17.3 |
| 0.6 Ko | 650 ms | `tiers:my.zadarma.com` | https://my.zadarma.com/callback/widget/initialize?callback=jQuery3710062013843561753657_1790749625480&linkId=8… |
| 0.6 Ko | 237 ms | `tiers:www.google.com` | https://www.google.com/pagead/1p-user-list/969053143/?random=1790749640760&cv=11&fst=1790748000000&bg=ffffff&g… |
| 0.6 Ko | 226 ms | `tiers:www.google.com` | https://www.google.com/pagead/1p-user-list/969053143/?random=1790749640926&cv=11&fst=1790748000000&bg=ffffff&g… |
| 0.5 Ko | 1751 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/css/widget-image.min.css?ver=4.3.2 |
| 0.5 Ko | 416 ms | `plugin:alma-gateway-for-woocommerce` | https://prod.la-maison-du-dos.com/wp-content/plugins/alma-gateway-for-woocommerce/assets/css/frontend/alma-wid… |
| 0.4 Ko | 10152 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/js/webpack.runtime.min.js?ver=4.3.2 |
| 0.4 Ko | 320 ms | `tiers:log.cookieyes.com` | https://log.cookieyes.com/api/v1/log |
| 0.4 Ko | 240 ms | `tiers:p.typekit.net` | https://p.typekit.net/p.css?s=1&k=lsl5vay&ht=tk&f=49383.49384.49387.51204.51206.51207&a=135436349&app=typekit&… |
| 0.3 Ko | 316 ms | `tiers:a.clarity.ms` | https://a.clarity.ms/collect |
| 0.3 Ko | 976 ms | `tiers:a.clarity.ms` | https://a.clarity.ms/collect |
| 0.3 Ko | 341 ms | `tiers:a.clarity.ms` | https://a.clarity.ms/collect |
| 0.3 Ko | 236 ms | `tiers:a.clarity.ms` | https://a.clarity.ms/collect |
| 0.3 Ko | 553 ms | `tiers:a.clarity.ms` | https://a.clarity.ms/collect |
| 0.3 Ko | 474 ms | `tiers:a.clarity.ms` | https://a.clarity.ms/collect |
| 0.3 Ko | 5562 ms | `plugin:elementor` | https://prod.la-maison-du-dos.com/wp-content/plugins/elementor/assets/js/chunks/background-video.min.js?ver=4.… |
| 0.2 Ko | 642 ms | `tiers:agent-commerce.xpay.sh` | https://agent-commerce.xpay.sh/widget/track |
| 0.2 Ko | 296 ms | `tiers:agent-commerce.xpay.sh` | https://agent-commerce.xpay.sh/widget/track |
| 0.0 Ko | 735 ms | `elementor:generated-css` | https://la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/fonts/roboto-kfo7cnqeu92fr1me7ksn66agld… |
| 0.0 Ko | 1505 ms | `plugin:ag-core` | https://www.societe-des-avis-garantis.fr/wp-content/plugins/ag-core/widgets/iframe/2/h/?id=9649 |
| 0.0 Ko | 696 ms | `elementor:generated-css` | https://la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/fonts/poppins-pxieyp8kv8jhgfvrjjfecg.wo… |
| 0.0 Ko | 1082 ms | `elementor:generated-css` | https://la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/fonts/poppins-pxibyp8kv8jhgfvrlej6z1xlf… |
| 0.0 Ko | 1096 ms | `elementor:generated-css` | https://la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/fonts/poppins-pxibyp8kv8jhgfvrlbt5z1xlf… |
| 0.0 Ko | 338 ms | `tiers:www.google-analytics.com` | https://www.google-analytics.com/g/collect?v=2&tid=G-VJSQ20QJ4W&gtm=45je69s1h1v880674583za200zd880674583xf1&_p… |
| 0.0 Ko | 405 ms | `tiers:www.google-analytics.com` | https://www.google-analytics.com/g/collect?v=2&tid=G-HH41XH2SV1&gtm=45je69s1v9206600813za200zb880674583zd88067… |
| 0.0 Ko | 267 ms | `tiers:pagead2.googlesyndication.com` | https://pagead2.googlesyndication.com/ccm/collect?rcb=13&frm=0&apvc=1&ae=g&en=page_view&dl=https%3A%2F%2Fprod.… |
| 0.0 Ko | 264 ms | `tiers:analytics.google.com` | https://analytics.google.com/g/collect?v=2&tid=G-VJSQ20QJ4W&gtm=45je69s1h1v880674583za200zd880674583xf1&_p=179… |
| 0.0 Ko | 250 ms | `tiers:analytics.google.com` | https://analytics.google.com/g/collect?v=2&tid=G-HH41XH2SV1&gtm=45je69s1v9206600813za200zb880674583zd880674583… |
| 0.0 Ko | 465 ms | `tiers:www.google.com` | https://www.google.com/ccm/collect?rcb=13&frm=0&apvc=1&ae=g&auid=393783374.1790749638&dt=Accueil%20-%20La%20Ma… |
| 0.0 Ko | 405 ms | `tiers:ad.doubleclick.net` | https://ad.doubleclick.net/ccm/s/collect?auid=393783374.1790749638&gtm=45He69s1v9207186614za200zd9207186614xea… |
| 0.0 Ko | 218 ms | `tiers:www.google-analytics.com` | https://www.google-analytics.com/g/collect?v=2&tid=G-HT4NBE35GW&gtm=45be69s1v9218560706z89207186614za20gzb9207… |
| 0.0 Ko | 217 ms | `tiers:www.google.com` | https://www.google.com/rmkt/collect/969053143/?random=1790749640760&cv=11&fst=1790749640760&fmt=8&bg=ffffff&gu… |
| 0.0 Ko | 409 ms | `tiers:www.google.com` | https://www.google.com/rmkt/collect/969053143/?random=1790749640926&cv=11&fst=1790749640926&fmt=8&bg=ffffff&gu… |
| 0.0 Ko | 450 ms | `tiers:www.google.com` | https://www.google.com/ccm/collect?rcb=12&frm=0&apvc=0&auid=393783374.1790749638&dt=Accueil%20-%20La%20Maison%… |
| 0.0 Ko | 464 ms | `tiers:www.google.com` | https://www.google.com/ccm/collect?rcb=12&frm=0&apvc=0&auid=393783374.1790749638&dt=Accueil%20-%20La%20Maison%… |
| 0.0 Ko | 214 ms | `tiers:www.google-analytics.com` | https://www.google-analytics.com/g/collect?v=2&tid=G-HT4NBE35GW&gtm=45be69s1v9218560706za20gzb9207186614zd9207… |
| 0.0 Ko | 618 ms | `elementor:generated-css` | https://la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/fonts/roboto-kfo5cnqeu92fr1mu53zec9_vu3… |
| 0.0 Ko | 163 ms | `tiers:widget.xpay.sh` | https://widget.xpay.sh/api/widget-events |
| 0.0 Ko | 600 ms | `elementor:generated-css` | https://la-maison-du-dos.com/wp-content/uploads/elementor/google-fonts/fonts/roboto-kfo7cnqeu92fr1me7ksn66agld… |

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
| 14x11 | 14x11 | **non (CLS)** | auto | Français | data:image/gif;base64,R0lGODlhDgALAIAAAAAAAAAAACH5BAEAAAEALAAAAAAOAAsAAAILjI+py+0Po5y0hgIA |
| 0x0 | 14x11 | **non (CLS)** | auto | Français | data:image/gif;base64,R0lGODlhDgALAIAAAAAAAAAAACH5BAEAAAEALAAAAAAOAAsAAAILjI+py+0Po5y0hgIA |
| 0x0 | 14x11 | **non (CLS)** | auto | English | data:image/gif;base64,R0lGODlhDgALAIAAAAAAAAAAACH5BAEAAAEALAAAAAAOAAsAAAILjI+py+0Po5y0hgIA |
| 0x0 | 14x11 | **non (CLS)** | auto | Deutsch | data:image/gif;base64,R0lGODlhDgALAIAAAAAAAAAAACH5BAEAAAEALAAAAAAOAAsAAAILjI+py+0Po5y0hgIA |
| 0x0 | 14x11 | **non (CLS)** | auto | Dutch | data:image/gif;base64,R0lGODlhDgALAIAAAAAAAAAAACH5BAEAAAEALAAAAAAOAAsAAAILjI+py+0Po5y0hgIA |
| 0x0 | 14x11 | **non (CLS)** | auto | Italiano | data:image/gif;base64,R0lGODlhDgALAIAAAAAAAAAAACH5BAEAAAEALAAAAAAOAAsAAAILjI+py+0Po5y0hgIA |
| 0x0 | 14x11 | **non (CLS)** | auto | Español | data:image/gif;base64,R0lGODlhDgALAIAAAAAAAAAAACH5BAEAAAEALAAAAAAOAAsAAAILjI+py+0Po5y0hgIA |
| 0x0 | 14x11 | **non (CLS)** | auto | Português | data:image/gif;base64,R0lGODlhDgALAIAAAAAAAAAAACH5BAEAAAEALAAAAAAOAAsAAAILjI+py+0Po5y0hgIA |
| 0x0 | 14x11 | **non (CLS)** | auto | Dansk | data:image/gif;base64,R0lGODlhDgALAIAAAAAAAAAAACH5BAEAAAEALAAAAAAOAAsAAAILjI+py+0Po5y0hgIA |
| 0x0 | 14x11 | **non (CLS)** | auto | Shqip | data:image/gif;base64,R0lGODlhDgALAIAAAAAAAAAAACH5BAEAAAEALAAAAAAOAAsAAAILjI+py+0Po5y0hgIA |
| 140x43 | 212x65 | **non (CLS)** | auto | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-template_images_logo- |
| 0x0 | 360x504 | oui | auto | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/09/screenshot-la-maison-du-dos-c |
| 0x0 | 360x504 | oui | auto | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/09/screenshot-la-maison-du-dos-c |
| 0x0 | 0x0 | **non (CLS)** | auto | Akva Nordic |  |
| 0x0 | 0x0 | **non (CLS)** | auto | Highline |  |
| 0x0 | 0x0 | **non (CLS)** | auto | Akva Lyra |  |
| 0x0 | 0x0 | **non (CLS)** | auto | Akva Vega |  |
| 0x0 | 0x0 | **non (CLS)** | auto | Akva Modulex |  |
| 0x0 | 0x0 | **non (CLS)** | auto | Clone |  |
| 320x179 | 360x201 | oui | auto | matelas réglable pour soutenir idéalement votre colonne vertébrale | https://prod.la-maison-du-dos.com/wp-content/uploads/2026/09/matelas-reglable.webp |
| 240x150 | 180x135 | oui | auto | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/Lyra_01.webp |
| 198x150 | 180x120 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/matelaseau2.webp |
| 240x150 | 232x150 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/10/hefel_softbausch.jpg |
| 217x150 | 285x223 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/10/Carbon-iQ-Akva.jpg |
| 158x158 | 0x0 | oui | lazy | Le matelas à eau Léger climatisé à poser sur un sommier fixe classique | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/matelas-eau-leger-1-300x300.j |
| 158x158 | 300x300 | oui | lazy | Matelas à eau léger Aqualight Premium de fabrication européenne | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/matelas-eau-leger-300x300.jpg |
| 158x158 | 300x300 | oui | lazy | Le matelas à eau pour Bébé d'Akva favorise un développement harmonieux et sain de son enfant | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/Babymadras1-300x300.jpg |
| 158x158 | 300x300 | oui | lazy | Le matelas à eau léger Tribrid® Fluid est un concept de matelas à eau avec ou sans chauffage | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/tribrid-fluid-300x300.jpg |
| 158x112 | 300x213 | oui | lazy | matelas à fermeté réglable | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/02/pure-300x213.webp |
| 158x158 | 300x300 | oui | lazy | matelas ergonomique réglable e-motion luxe de Dynaglobe | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/02/Matelas_reglable_emotion1-300 |
| 158x158 | 300x300 | oui | lazy | matelas de santé Life | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/02/Matelas_reglable_life1-300x30 |
| 158x158 | 300x300 | oui | lazy | matelas réglable Matrair | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/02/website-pagina-slapen-op-luch |
| 300x92 | 300x91 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-template_images_logo- |
| 120x56 | 120x56 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/Formesse_LogoSlogan_RGB.webp |
| 120x48 | 120x48 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-Bodyform_logo.webp |
| 120x66 | 120x66 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/ATT00136logo-marque-144223484 |
| 150x178 | 150x178 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/10/logo2-akva.jpg |
| 215x194 | 215x194 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/10/logo-TASSO-CARRE.jpg |
| 120x38 | 120x38 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/LOGO-MATRAIR.webp |
| 105x100 | 105x100 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/LogoTTIEWPlogo-marque-1448474 |
| 120x65 | 0x0 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logolunalifelogo-marque-13670 |
| 120x44 | 120x44 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/LOGOlogo-marque-1386846181.we |
| 120x48 | 120x48 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logologo-marque-1402648459.we |
| 120x48 | 120x48 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logokirstenlogo-marque-139645 |
| 120x56 | 120x56 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logobodytonelogo-marque-13997 |
| 120x37 | 120x37 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/logo_profine.webp |
| 100x25 | 100x25 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/09/logo-1-e1757664098298.jpg |
| 120x36 | 120x36 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/09/logo-e1757664451940.jpg |
| 227x175 | 227x175 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2025/10/logo-Aqua-flair.jpg |
| 158x158 | 300x300 | oui | lazy | LONG LIFE  : Anti-algues du fabricant Akva waterbeds | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/akva-long-life-300x300.jpg |
| 158x158 | 300x300 | oui | lazy | Conditionneur Multi-Usage Waterclean Plus | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/Conditionneur-Multi-Usage-Wat |
| 158x158 | 300x300 | oui | lazy | Drap-housse jersey Bella donna | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/11/bella-donna-standard-0030-bor |
| 158x100 | 210x133 | oui | lazy | lit à eau Basic Line | https://prod.la-maison-du-dos.com/wp-content/uploads/2018/10/waterbed-basic-line.webp |
| 35x35 | 51x51 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/icn1.png |
| 41x41 | 51x51 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/icn2.png |
| 47x47 | 51x51 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/icn3.png |
| 31x31 | 51x51 | oui | lazy | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/icn4.png |
| 213x65 | 212x65 | **non (CLS)** | auto | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/09/cropped-template_images_logo- |
| 246x30 | 246x30 | oui | auto | (vide) | https://prod.la-maison-du-dos.com/wp-content/uploads/2024/10/paiements.jpg |

## Polices chargées

- Font Awesome 5 Free 400 normal

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