/**
 * Génère les modèles Elementor (JSON importables) de la page d'accueil La Maison du Dos.
 * Widgets natifs d'Elementor GRATUIT uniquement (conteneurs Flexbox/Grid, titre, éditeur de texte,
 * bouton, image, boîte d'icône, liste d'icônes, accordéon). Aucun HTML brut, sauf l'iframe
 * du widget officiel de la Société des Avis Garantis (aucun équivalent natif).
 *
 * Usage : node elementor/build.mjs
 */
import { writeFileSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const OUT = path.dirname(fileURLToPath(import.meta.url));
const UP = 'https://prod.la-maison-du-dos.com/wp-content/uploads';

// ---------- Charte ----------
const C = {
  green: '#035C11', greenHover: '#024A0E', greenDark: '#012E08', greenMid: '#2E8B3A', greenText: '#1F7A2C',
  greenLight: '#A9D8B0', tint: '#F5F8F4', greenBg: '#EDF6EE', red: '#C80C25', redHover: '#A50A1E',
  text: '#1B261D', muted: '#56625A', line: '#DFE7E0', white: '#FFFFFF', onDark: '#D3E4D5',
};

// ---------- Utilitaires ----------
let seq = 0x1a2b000;
const id = () => (seq++).toString(16).slice(-7);
const px = (size) => ({ unit: 'px', size, sizes: [] });
const pct = (size) => ({ unit: '%', size, sizes: [] });
const em = (size) => ({ unit: 'em', size, sizes: [] });
const box = (t, r = t, b = t, l = r) => ({ unit: 'px', top: String(t), right: String(r), bottom: String(b), left: String(l), isLinked: t === r && r === b && b === l });
const gap = (n, row = n) => ({ unit: 'px', size: n, column: String(n), row: String(row), isLinked: n === row });
const link = (url, external = false) => ({ url, is_external: external ? 'on' : '', nofollow: '', custom_attributes: '' });
const fa = (name, lib = 'fa-solid') => ({ value: name, library: lib });
const typo = (prefix, { size, sizeT, sizeM, weight, lh, ls, transform } = {}) => {
  const o = { [`${prefix}_typography`]: 'custom' };
  if (size) o[`${prefix}_font_size`] = px(size);
  if (sizeT) o[`${prefix}_font_size_tablet`] = px(sizeT);
  if (sizeM) o[`${prefix}_font_size_mobile`] = px(sizeM);
  if (weight) o[`${prefix}_font_weight`] = String(weight);
  if (lh) o[`${prefix}_line_height`] = em(lh);
  if (ls !== undefined) o[`${prefix}_letter_spacing`] = px(ls);
  if (transform) o[`${prefix}_text_transform`] = transform;
  return o;
};
const shadow = (v = 10, blur = 30, spread = -10, color = 'rgba(1,46,8,0.22)') => ({
  box_shadow_box_shadow_type: 'yes',
  box_shadow_box_shadow: { horizontal: 0, vertical: v, blur, spread, color },
});

const W = (widgetType, settings) => ({ id: id(), elType: 'widget', isInner: false, isLocked: false, widgetType, settings, elements: [] });
// Les conteneurs imbriqués reçoivent un padding nul (Elementor ajoute 10 px par défaut), sauf réglage explicite.
const Con = (settings, elements = [], isInner = true) => ({
  id: id(), elType: 'container', isInner, isLocked: false,
  settings: { content_width: 'full', ...(isInner ? { padding: box(0) } : {}), ...settings }, elements,
});

/** Section pleine largeur au contenu centré sur 1200 px. */
const Section = (settings, elements) => Con({
  content_width: 'boxed', boxed_width: px(1200), html_tag: 'section',
  padding: box(96, 24, 96, 24), padding_tablet: box(72, 24, 72, 24), padding_mobile: box(56, 16, 56, 16),
  flex_direction: 'column', flex_gap: gap(0),
  ...settings,
}, elements, false);

// ---------- Widgets de base ----------
const Eyebrow = (text, color = C.greenText, align) => W('heading', {
  title: text, header_size: 'p', title_color: color, ...(align ? { align } : {}),
  ...typo('typography', { size: 13, weight: 700, ls: 1.6, transform: 'uppercase', lh: 1.3 }),
  _margin: box(0, 0, 12, 0),
});
const H = (text, tag = 'h2', o = {}) => W('heading', {
  title: text, header_size: tag, title_color: o.color || C.green, ...(o.align ? { align: o.align } : {}),
  ...(o.alignM ? { align_mobile: o.alignM } : {}),
  ...(o.link ? { link: link(o.link) } : {}),
  ...typo('typography', {
    size: o.size || { h1: 50, h2: 38, h3: 19 }[tag] || 38,
    sizeT: o.sizeT || { h1: 40, h2: 32 }[tag], sizeM: o.sizeM || { h1: 32, h2: 27, h3: 18 }[tag],
    weight: o.weight || (tag === 'h3' ? 700 : 800), lh: o.lh || (tag === 'h3' ? 1.3 : 1.15), ls: tag === 'h3' ? undefined : -0.5,
  }),
  _margin: o.margin || box(0, 0, tag === 'h3' ? 8 : 16, 0),
});
const Txt = (html, o = {}) => W('text-editor', {
  editor: html, text_color: o.color || C.muted, ...(o.align ? { align: o.align } : {}),
  ...typo('typography', { size: o.size || 17, sizeM: o.sizeM || 16, lh: o.lh || 1.65, weight: o.weight }),
  _margin: o.margin || box(0, 0, 0, 0),
});

const btnBase = (text, url, o = {}) => ({
  text, link: link(url, o.external), size: 'md',
  ...typo('typography', { size: o.fs || 16, weight: 600, lh: 1.2 }),
  border_radius: box(999), text_padding: o.pad || box(16, 28, 16, 28),
  ...(o.icon ? { selected_icon: fa(o.icon, o.iconLib), icon_align: o.iconAfter ? 'row-reverse' : 'row', icon_indent: px(8) } : {}),
  ...(o.align ? { align: o.align } : {}),
  ...(o.alignM ? { align_mobile: o.alignM } : {}),
});
const BtnPrimary = (text, url, o) => W('button', { ...btnBase(text, url, o), background_color: C.green, button_text_color: C.white, button_background_hover_color: C.greenHover, hover_color: C.white, ...shadow() });
const BtnAccent = (text, url, o) => W('button', { ...btnBase(text, url, o), background_color: C.red, button_text_color: C.white, button_background_hover_color: C.redHover, hover_color: C.white });
const BtnGhost = (text, url, o) => W('button', {
  ...btnBase(text, url, o), background_color: 'rgba(0,0,0,0)', button_text_color: C.green,
  border_border: 'solid', border_width: box(2), border_color: C.green, button_background_hover_color: C.green, hover_color: C.white,
  text_padding: o?.pad || box(14, 26, 14, 26),
});
const BtnLight = (text, url, o) => W('button', { ...btnBase(text, url, o), background_color: C.white, button_text_color: C.green, button_background_hover_color: C.greenBg, hover_color: C.green });
const BtnLink = (text, url, o = {}) => W('button', {
  ...btnBase(text, url, { ...o, icon: 'fas fa-arrow-right', iconAfter: true, pad: box(0) }),
  background_color: 'rgba(0,0,0,0)', button_text_color: o.color || C.green, button_background_hover_color: 'rgba(0,0,0,0)', hover_color: o.hover || C.greenMid,
});

/*
 * Images : par défaut, AUCUN téléchargement pendant l'import.
 * Elementor télécharge chaque image d'un modèle importé ; sur un serveur lent, cela dépasse le temps
 * maximal d'exécution PHP (erreur 500). On utilise donc une adresse « a-choisir:<fichier> » qui échoue
 * immédiatement, sans requête réseau : Elementor met alors son image d'attente, et le widget est nommé
 * « Image à choisir : <fichier> » dans le panneau Structure. Les images optimisées sont fournies à part.
 * IMAGES=distantes node elementor/build.mjs  → ancien comportement (téléchargement depuis le site).
 */
const REMOTE_IMAGES = process.env.IMAGES === 'distantes';
const PACK = {
  'altura-pos.jpg': 'lit-a-eau-altura.webp',
  'Havre3-1.webp': 'lit-a-eau-havre.webp',
  'tec-line-standard.webp': 'lit-a-eau-tec-line.webp',
  'matelas-eau-leger.jpg': 'matelas-eau-leger-aqualight.webp',
  'bella-donna-standard-0030-bordeaux.jpg': 'drap-housse-bella-donna.webp',
  'cropped-template_images_logo-boutique-pc.webp': 'logo-la-maison-du-dos.webp',
  'schema-pression-matelas-eau.png': 'schema-pression-matelas-eau.png',
};
export const IMAGE_LIST = []; // [fichier, texte alternatif, emplacement]
const imageValue = (url, alt) => {
  const file = PACK[url.split('/').pop()] || url.split('/').pop();
  return REMOTE_IMAGES ? { url, id: '', alt, source: 'library', size: '' } : { url: 'a-choisir:' + file, id: '', alt, source: 'library', size: '' };
};
const imageTitle = (url) => 'Image à choisir : ' + (PACK[url.split('/').pop()] || url.split('/').pop());

const Img = (url, alt, o = {}) => W('image', {
  image: imageValue(url, alt), image_size: 'full', _title: imageTitle(url),
  width: pct(100), ...(o.height ? { height: px(o.height), height_mobile: px(o.heightM || o.height), 'object-fit': 'cover' } : {}),
  image_border_radius: box(o.radius ?? 22),
  ...(o.link ? { link_to: 'custom', link: link(o.link) } : {}),
  ...(o.shadow ? { image_box_shadow_box_shadow_type: 'yes', image_box_shadow_box_shadow: { horizontal: 0, vertical: 10, blur: 30, spread: -10, color: 'rgba(1,46,8,0.22)' } } : {}),
  ...(o.extra || {}),
});

/** En-tête de section centré : sur-titre + H2 + chapeau. */
const SectionHead = (eyebrow, title, lead) => Con({
  flex_direction: 'column', flex_align_items: 'center', width: pct(100), padding: box(0, 0, 48, 0),
  _title: `En-tête : ${title}`,
}, [
  Eyebrow(eyebrow, C.greenText, 'center'),
  H(title, 'h2', { align: 'center' }),
  ...(lead ? [Txt(`<p>${lead}</p>`, { align: 'center', size: 18 })] : []),
]);

// ================= PAGE D'ACCUEIL =================

// ---- 1. Hero ----
const hero = Section({
  _title: 'Hero', flex_direction: 'row', flex_direction_tablet: 'column', flex_align_items: 'center', flex_gap: gap(64),
  background_background: 'gradient', background_color: C.white, background_color_b: C.tint, background_gradient_angle: { unit: 'deg', size: 180 },
  padding: box(88, 24, 88, 24), padding_mobile: box(40, 16, 48, 16),
}, [
  Con({ _title: 'Hero – texte', width: pct(52), width_tablet: pct(100), flex_direction: 'column', flex_gap: gap(0) }, [
    Eyebrow('Spécialiste du lit à eau depuis 1994'),
    H('Lit à eau et matelas réglables&nbsp;: <span style="color:#2E8B3A">soulagez votre mal de dos</span>', 'h1', { margin: box(0, 0, 20, 0) }),
    Txt("<p>Marque du confort du dos depuis 1988, La Maison du Dos vous accompagne dans le choix d'un lit avec un ou deux matelas à eau, d'un matelas à eau léger ou d'un matelas à fermeté réglable par télécommande.</p>", { size: 19 }),
    Con({ _title: 'Hero – boutons', flex_direction: 'row', flex_direction_mobile: 'column', flex_gap: gap(12), padding: box(28, 0, 32, 0), flex_align_items: 'flex-start', flex_align_items_mobile: 'stretch' }, [
      BtnPrimary('Découvrir nos lits à eau', '/lits-a-eau/', { fs: 17, pad: box(18, 30, 18, 30), alignM: 'justify' }),
      BtnGhost('Demander un devis', '/lits-a-eau/lits-a-eau-en-vente-sur-devis/', { fs: 17, pad: box(16, 28, 16, 28), alignM: 'justify' }),
    ]),
    Con({
      _title: 'Hero – chiffres clés', container_type: 'grid', grid_columns_grid: { unit: 'fr', size: 3 }, grid_columns_grid_mobile: { unit: 'fr', size: 3 },
      grid_rows_grid: { unit: 'fr', size: 1 }, grid_gaps: gap(24, 12), grid_gaps_mobile: gap(12, 12),
      border_border: 'solid', border_width: box(1, 0, 0, 0), border_color: C.line, padding: box(24, 0, 0, 0),
    }, [
      ['Depuis 1988', 'confort du dos'], ['7j/7', 'conseil de 9h à 21h'], ['22', 'stabilisations au choix'],
    ].map(([k, v]) => Con({ flex_direction: 'column', flex_gap: gap(2) }, [
      H(k, 'p', { size: 24, sizeM: 19, weight: 800, lh: 1.2, margin: box(0) }),
      Txt(`<p>${v}</p>`, { size: 14, sizeM: 13, lh: 1.4 }),
    ]))),
  ]),
  Con({ _title: 'Hero – photo', width: pct(48), width_tablet: pct(100), flex_direction: 'column', position: 'relative' }, [
    Img(`${UP}/2025/10/altura-pos.jpg`, 'Lit à eau Altura de Poseïdon avec tête de lit, dans une chambre lumineuse', { height: 500, heightM: 280, shadow: true }),
    W('icon-box', {
      _title: 'Badge installation',
      selected_icon: fa('fas fa-tools'), view: 'stacked', shape: 'square', position: 'left', position_mobile: 'left', text_align_mobile: 'left',
      title_text: 'Installation &amp; réglage', description_text: 'personnalisés, en votre présence', title_size: 'p',
      primary_color: C.greenBg, secondary_color: C.greenMid, icon_size: px(18), icon_padding: px(9), border_radius: box(10), icon_space: px(12),
      title_color: C.green, description_color: C.muted,
      ...typo('title_typography', { size: 16, weight: 700, lh: 1.3 }), ...typo('description_typography', { size: 14, lh: 1.4 }),
      title_bottom_space: px(2),
      _position: 'absolute', _offset_orientation_h: 'start', _offset_x: px(-20), _offset_x_mobile: px(12),
      _offset_orientation_v: 'end', _offset_y: px(28), _offset_y_mobile: px(12),
      _element_width: 'auto', _background_background: 'classic', _background_color: C.white,
      _padding: box(14, 18, 14, 18), _border_radius: box(14), _box_shadow_box_shadow_type: 'yes',
      _box_shadow_box_shadow: { horizontal: 0, vertical: 10, blur: 30, spread: -10, color: 'rgba(1,46,8,0.22)' },
    }),
  ]),
]);

// ---- 2. Engagements ----
const trust = Section({
  _title: 'Engagements', padding: box(28, 24, 28, 24), padding_mobile: box(24, 16, 24, 16),
  border_border: 'solid', border_width: box(1, 0, 1, 0), border_color: C.line, background_background: 'classic', background_color: C.white,
}, [
  Con({ container_type: 'grid', grid_columns_grid: { unit: 'fr', size: 4 }, grid_columns_grid_tablet: { unit: 'fr', size: 2 }, grid_columns_grid_mobile: { unit: 'fr', size: 1 }, grid_rows_grid: { unit: 'fr', size: 1 }, grid_gaps: gap(24, 20) },
    [
      ['fas fa-tools', 'Livraison avec installation', 'et réglages personnalisés'],
      ['fas fa-truck', 'Livraison gratuite', "dès 60 € d'achat"],
      ['fas fa-undo', 'Satisfait ou remboursé', 'voir nos conditions de vente'],
      ['fas fa-lock', 'Facilités de paiement', 'payez à votre rythme'],
    ].map(([icon, title, desc]) => W('icon-box', {
      selected_icon: fa(icon), view: 'stacked', shape: 'square', position: 'left', position_mobile: 'left', text_align_mobile: 'left', title_text: title, description_text: desc, title_size: 'p',
      primary_color: C.greenBg, secondary_color: C.green, icon_size: px(20), icon_padding: px(12), border_radius: box(12), icon_space: px(14),
      title_color: C.green, description_color: C.muted, title_bottom_space: px(2),
      ...typo('title_typography', { size: 16, weight: 700, lh: 1.3 }), ...typo('description_typography', { size: 14, lh: 1.4 }),
    }))),
]);

// ---- 3. Catalogue ----
const catCard = (icon, title, desc, url) => Con({
  _title: `Carte : ${title}`, flex_direction: 'column', flex_gap: gap(0), padding: box(28), padding_mobile: box(24),
  border_border: 'solid', border_width: box(1), border_color: C.line, border_radius: box(22), background_background: 'classic', background_color: C.white,
}, [
  W('icon-box', {
    selected_icon: fa(icon), view: 'stacked', shape: 'square', position: 'top', title_text: title, description_text: desc, title_size: 'h3',
    link: link(url), text_align: 'left', primary_color: C.greenBg, secondary_color: C.greenMid, icon_size: px(26), icon_padding: px(15),
    border_radius: box(16), icon_space: px(20), title_color: C.green, description_color: C.muted, title_bottom_space: px(8),
    ...typo('title_typography', { size: 19, weight: 700, lh: 1.3 }), ...typo('description_typography', { size: 15, lh: 1.6 }),
    _margin: box(0, 0, 16, 0),
  }),
  BtnLink('Découvrir', url, { fs: 15 }),
]);
const catalogue = Section({ _title: 'Catalogue' }, [
  SectionHead('Notre catalogue', 'Trouvez la literie faite pour votre dos', 'Selon vos attentes et vos besoins, nous vous aidons à choisir parmi les articles qui répondent idéalement à vos critères.'),
  Con({ _title: 'Catalogue – cartes', flex_direction: 'row', flex_direction_tablet: 'column', flex_gap: gap(20) }, [
    Con({
      _title: 'Carte : Lits à eau (mise en avant)', width: pct(36), width_tablet: pct(100), flex_direction: 'column', flex_justify_content: 'flex-end', flex_gap: gap(0),
      min_height: px(460), min_height_tablet: px(280), padding: box(32), border_radius: box(22),
      background_background: 'gradient', background_color: C.greenDark, background_color_b: C.greenMid, background_gradient_angle: { unit: 'deg', size: 160 },
    }, [
      W('icon', { selected_icon: fa('fas fa-water'), view: 'stacked', shape: 'square', primary_color: 'rgba(255,255,255,0.12)', secondary_color: C.white, size: px(26), icon_padding: px(15), border_radius: box(16), align: 'left', _margin: box(0, 0, 24, 0) }),
      H('Lits à eau', 'h3', { color: C.white, size: 28, sizeM: 24 }),
      Txt('<p>Un ou deux matelas à eau climatisés (MONO ou DUO), en vente en ligne ou sur devis.</p>', { color: C.onDark, size: 17, margin: box(0, 0, 20, 0) }),
      BtnLink('Voir les lits à eau', '/lits-a-eau/', { color: C.white, hover: C.greenLight }),
    ]),
    Con({
      _title: 'Catalogue – grille', width: pct(64), width_tablet: pct(100), container_type: 'grid',
      grid_columns_grid: { unit: 'fr', size: 2 }, grid_columns_grid_mobile: { unit: 'fr', size: 1 }, grid_rows_grid: { unit: 'fr', size: 2 }, grid_gaps: gap(20),
    }, [
      catCard('fas fa-tint', 'Matelas à eau légers & bébé', 'Le matelas à eau climatisé à poser sur un sommier fixe classique.', '/matelas-reglables/matelas-a-eau-leger-bebe/'),
      catCard('fas fa-sliders-h', 'Matelas à télécommande', 'Fermeté réglable par télécommande, compatibles avec les sommiers de relaxation.', '/matelas-reglables/matelas-a-telecommande/'),
      catCard('fas fa-leaf', 'Linge de lit', 'Couettes, oreillers, surmatelas et draps-housses, dont le linge de lit Hefel conseillé depuis 1997.', '/linge-de-lit/'),
      catCard('fas fa-layer-group', 'Accessoires, SAV & entretien', 'Conditionneurs, anti-algues et accessoires pour faire durer votre literie à eau.', '/product-category/accessoires-produits-dentretien-sav/'),
    ]),
  ]),
]);

// ---- 4. Pourquoi un matelas à eau ----
const why = Section({
  _title: 'Pourquoi un matelas à eau', flex_direction: 'row', flex_direction_tablet: 'column-reverse', flex_align_items: 'center', flex_gap: gap(72),
  background_background: 'classic', background_color: C.tint,
}, [
  Con({ _title: 'Schéma', width: pct(48), width_tablet: pct(100), flex_direction: 'column' }, [
    // Image à remplacer par elementor/images/schema-pression-matelas-eau.png (voir LISEZ-MOI)
    W('image', {
      image: imageValue('schema-pression-matelas-eau.png', "Schéma : sur un matelas classique la pression se concentre sur les épaules et le bassin, sur un matelas à eau elle est répartie uniformément"),
      image_size: 'full', width: pct(100), image_border_radius: box(22),
      image_box_shadow_box_shadow_type: 'yes', image_box_shadow_box_shadow: { horizontal: 0, vertical: 2, blur: 8, spread: 0, color: 'rgba(1,46,8,0.06)' },
      _title: imageTitle('schema-pression-matelas-eau.png'),
    }),
  ]),
  Con({ _title: 'Pourquoi – texte', width: pct(52), width_tablet: pct(100), flex_direction: 'column', flex_gap: gap(0) }, [
    Eyebrow('Le lit à eau, notre spécialité'),
    H('Pourquoi un matelas à eau soulage-t-il le dos&nbsp;?'),
    Txt("<p>Dès que vous vous allongez, l'eau prend exactement la forme de votre corps. Le matelas ne s'affaisse pas&nbsp;: il s'adapte instantanément à votre position, avec une pression d'appui répartie uniformément.</p>", { margin: box(0, 0, 20, 0) }),
    W('icon-list', {
      icon_list: [
        "Un soutien sans usure : l'eau ne s'use pas, et un matelas à eau dure plus de 15 ans selon son entretien et sa qualité.",
        'Une température réglable, tiède en hiver et fraîche en été.',
        '22 stabilisations pour choisir ensemble le soutien adapté à votre morphologie.',
        "Un réglage en eau en votre présence lors de l'installation, pour conserver les garanties des fabricants.",
      ].map((text) => ({ text, selected_icon: fa('fas fa-check-circle'), _id: id() })),
      space_between: px(14), icon_color: C.greenMid, icon_size: px(22), text_color: C.text, text_indent: px(12), icon_self_align: 'left',
      ...typo('icon_typography', { size: 16, lh: 1.55 }),
      _margin: box(0, 0, 32, 0),
    }),
    BtnPrimary('Lire notre guide 2026 du lit à eau', '/lit-a-eau-2026-guide-de-la-maison-du-dos/'),
  ]),
]);

// ---- 5. Produits ----
const product = (img, alt, cat, title, url, priceHtml) => Con({ _title: `Produit : ${title}`, flex_direction: 'column', flex_gap: gap(0) }, [
  Img(img, alt, { height: 280, heightM: 170, radius: 14, link: url, extra: { _margin: box(0, 0, 14, 0) } }),
  Eyebrow(cat, C.greenText),
  H(title, 'h3', { size: 17, sizeM: 15, margin: box(0, 0, 6, 0), link: url }),
  Txt(`<p>${priceHtml}</p>`, { color: C.text, size: 16, sizeM: 15, weight: 700, lh: 1.4 }),
]);
const products = Section({ _title: 'Produits' }, [
  Con({ _title: 'Produits – en-tête', flex_direction: 'row', flex_direction_mobile: 'column', flex_justify_content: 'space-between', flex_align_items: 'flex-end', flex_align_items_mobile: 'flex-start', flex_gap: gap(12), padding: box(0, 0, 36, 0) }, [
    Con({ flex_direction: 'column', width: pct(70), width_mobile: pct(100) }, [Eyebrow('Sélection'), H('Nos lits et matelas à eau', 'h2', { margin: box(0) })]),
    BtnLink('Tous les lits à eau en ligne', '/lits-a-eau/lits-a-eau-en-vente-en-ligne/'),
  ]),
  Con({ _title: 'Produits – grille', container_type: 'grid', grid_columns_grid: { unit: 'fr', size: 4 }, grid_columns_grid_tablet: { unit: 'fr', size: 2 }, grid_columns_grid_mobile: { unit: 'fr', size: 2 }, grid_rows_grid: { unit: 'fr', size: 1 }, grid_gaps: gap(24, 32), grid_gaps_mobile: gap(14, 24) }, [
    product(`${UP}/2026/09/Havre3-1.webp`, 'Lit à eau Havre avec tête de lit capitonnée grise', 'Lit à eau · Pack promotionnel', 'Lit à eau Havre', '/le-lit-a-eau-havre-duo/', '2 229,17 €'),
    product(`${UP}/2024/10/tec-line-standard.webp`, 'Lit à eau Tec-Line avec socle noir', 'Lit à eau', 'Lit à eau Tec-Line', '/lit-a-eau-waterbed-tec-line/', '<del style="font-weight:400;color:#56625A">2 075,83 €</del> <span style="color:#C80C25">1 909,77 €</span>'),
    product(`${UP}/2024/11/matelas-eau-leger.jpg`, 'Matelas à eau léger Aqualight Premium', 'Matelas à eau léger', 'Matelas à eau léger Aqualight Premium', '/matelas-a-eau-leger-aqualight-premium/', '<del style="font-weight:400;color:#56625A">613,33 €</del> <span style="color:#C80C25">582,67 €</span>'),
    product(`${UP}/2024/11/bella-donna-standard-0030-bordeaux.jpg`, 'Drap-housse jersey Bella Donna bordeaux', 'Linge de lit · 46 avis', 'Drap-housse jersey Bella Donna', '/drap-housse-jersey-bella-donna/', '<del style="font-weight:400;color:#56625A">54,13 €</del> <span style="color:#C80C25">52,50 €</span>'),
  ]),
]);

// ---- 6. Matelas à télécommande ----
const feature = Section({
  _title: 'Matelas à télécommande', flex_direction: 'row', flex_direction_tablet: 'column', flex_align_items: 'center', flex_gap: gap(72),
  background_background: 'gradient', background_color: C.greenDark, background_color_b: '#0A4A14', background_gradient_angle: { unit: 'deg', size: 135 },
}, [
  Con({ _title: 'Télécommande – texte', width: pct(50), width_tablet: pct(100), flex_direction: 'column', flex_gap: gap(0) }, [
    Eyebrow('Matelas réglables', C.greenLight),
    H('La fermeté idéale, réglée par télécommande', 'h2', { color: C.white }),
    Txt("<p>Nos matelas à fermeté réglable permettent d'adapter le soutien à vos besoins et s'utilisent aussi avec des sommiers de relaxation, ce qui n'est pas possible avec un matelas à eau.</p>", { color: C.onDark, size: 18, margin: box(0, 0, 28, 0) }),
    BtnAccent('Découvrir les matelas à télécommande', '/matelas-reglables/matelas-a-telecommande/'),
  ]),
  Con({ _title: 'Télécommande – modèles', width: pct(50), width_tablet: pct(100), flex_direction: 'column', flex_gap: gap(16) },
    [
      ['fas fa-sliders-h', 'Matelas Pure', 'Fermeté réglable par télécommande pour une personne.', '/matelas-pure-a-suspension-a-air/'],
      ['fas fa-wind', 'Matelas de santé Life', 'Suspensions à air avec réglage du soutien par télécommande.', '/matelas-de-sante-life-suspensions-a-air/'],
      ['fas fa-heart', 'Matelas Matrair', 'Fermeté réglable et supports lombaires pour soulager le mal de dos.', '/matelas-matrair/'],
    ].map(([icon, title, desc, url]) => W('icon-box', {
      selected_icon: fa(icon), view: 'stacked', shape: 'circle', position: 'left', position_mobile: 'left', text_align_mobile: 'left', title_text: title, description_text: desc, title_size: 'h3', link: link(url),
      primary_color: C.red, secondary_color: C.white, icon_size: px(18), icon_padding: px(12), icon_space: px(18),
      title_color: C.white, description_color: C.onDark, title_bottom_space: px(4),
      ...typo('title_typography', { size: 18, weight: 700, lh: 1.3 }), ...typo('description_typography', { size: 15, lh: 1.5 }),
      _padding: box(22), _background_background: 'classic', _background_color: 'rgba(255,255,255,0.06)',
      _border_border: 'solid', _border_width: box(1), _border_color: 'rgba(255,255,255,0.12)', _border_radius: box(14),
    }))),
]);

// ---- 7. Accompagnement ----
const steps = Section({ _title: 'Accompagnement' }, [
  SectionHead('Accompagnement', 'De votre premier appel à votre première nuit'),
  Con({ _title: 'Étapes', container_type: 'grid', grid_columns_grid: { unit: 'fr', size: 4 }, grid_columns_grid_tablet: { unit: 'fr', size: 2 }, grid_columns_grid_mobile: { unit: 'fr', size: 1 }, grid_rows_grid: { unit: 'fr', size: 1 }, grid_gaps: gap(24) },
    [
      ['01', 'Conseil personnalisé', 'Par téléphone ou WhatsApp, 7 jours sur 7 de 9h à 21h : nous étudions ensemble vos envies et vos besoins de confort.'],
      ['02', 'Choix du matelas', 'Type de lit, dimensions, stabilisation : nous choisissons ensemble le matelas à eau qui vous satisfera pleinement.'],
      ['03', 'Livraison & installation', "Installation d'environ 2 à 3 heures, avec le réglage en eau réalisé en présence des futurs utilisateurs."],
      ['04', 'Entretien & SAV', "Conseils d'entretien à l'installation, produits d'entretien et service client à votre écoute."],
    ].map(([n, title, desc]) => Con({ _title: `Étape ${n}`, flex_direction: 'column', flex_gap: gap(0), padding: box(28, 24, 28, 24), border_radius: box(22), background_background: 'classic', background_color: C.tint }, [
      H(n, 'p', { color: '#A9D8B0', size: 40, sizeM: 34, weight: 800, lh: 1, margin: box(0, 0, 16, 0) }),
      H(title, 'h3'),
      Txt(`<p>${desc}</p>`, { size: 15, lh: 1.6 }),
    ]))),
]);

// ---- 8. Qui sommes-nous ----
const about = Section({
  _title: 'Qui sommes-nous', flex_direction: 'row', flex_direction_tablet: 'column', flex_align_items: 'center', flex_gap: gap(72),
  background_background: 'classic', background_color: C.tint,
}, [
  Con({ _title: 'Qui sommes-nous – texte', width: pct(55), width_tablet: pct(100), flex_direction: 'column', flex_gap: gap(0) }, [
    Eyebrow('Qui sommes-nous'),
    H('Le confort du dos, notre métier depuis 1988'),
    Txt("<p>Créée en 1988, La Maison du Dos propose une literie de qualité pour apporter un supplément de confort à chacun, et particulièrement soulager les personnes qui souffrent du dos. Spécialiste du lit à eau depuis 1994, elle a quitté sa boutique de Mulhouse pour se consacrer à la vente en ligne.</p><p>Depuis la Haute-Marne, nous livrons et installons nos lits à eau en France et en Europe, notamment au Benelux, en Suisse et en Allemagne. Nous vous conseillons aussi le linge de lit <strong>Hefel</strong> depuis 1997.</p>", { margin: box(0, 0, 16, 0) }),
    BtnLink('Découvrir notre histoire', '/qui-sommes-nous/'),
  ]),
  Con({ _title: 'Qui sommes-nous – dates', width: pct(45), width_tablet: pct(100), container_type: 'grid', grid_columns_grid: { unit: 'fr', size: 2 }, grid_columns_grid_mobile: { unit: 'fr', size: 2 }, grid_rows_grid: { unit: 'fr', size: 2 }, grid_gaps: gap(16) },
    [['1988', 'création de La Maison du Dos'], ['1994', 'spécialiste du lit à eau'], ['1997', 'linge de lit Hefel'], ['7j/7', 'service client de 9h à 21h']]
      .map(([k, v]) => Con({ flex_direction: 'column', flex_gap: gap(4), padding: box(28, 24, 28, 24), padding_mobile: box(20), border_radius: box(22), background_background: 'classic', background_color: C.white, ...shadow(2, 8, 0, 'rgba(1,46,8,0.06)') }, [
        H(k, 'p', { size: 36, sizeM: 28, weight: 800, lh: 1.1, margin: box(0) }),
        Txt(`<p>${v}</p>`, { size: 15, sizeM: 14, lh: 1.4 }),
      ]))),
]);

// ---- 9. Avis clients ----
const reviews = Section({ _title: 'Avis clients' }, [
  SectionHead('Avis clients', 'Ils dorment mieux, ils en parlent', "Nos avis sont collectés et contrôlés par la Société des Avis Garantis, auprès d'acheteurs authentifiés."),
  // Seule exception au « 100 % widgets » : le widget officiel des Avis Garantis n'existe qu'en iframe.
  W('html', {
    _title: 'Widget Société des Avis Garantis',
    html: '<iframe src="https://www.societe-des-avis-garantis.fr/wp-content/plugins/ag-core/widgets/iframe/2/h/?id=9649" title="Avis clients La Maison du Dos – Société des Avis Garantis" loading="lazy" width="100%" height="240" style="display:block;border:0;width:100%;height:240px"></iframe>',
    _background_background: 'classic', _background_color: C.white, _border_border: 'solid', _border_width: box(1), _border_color: C.line, _border_radius: box(22),
    _css_classes: 'lmdd-avis',
  }),
]);

// ---- 10. FAQ ----
const faqItems = [
  ['Un matelas à eau est-il bon pour le dos ?', "Dès que vous vous allongez, l'eau prend exactement la forme de votre corps. Le matelas ne s'affaisse pas : il s'adapte instantanément à votre position. Ce soutien, combiné à une faible pression d'appui et à une chaleur agréable, est un bienfait pour votre dos."],
  ['A-t-on le mal de mer sur un matelas à eau ?', "Non. L'eau ne bouge que lorsque vous bougez vous-même, et ce mouvement est immédiatement amorti par la stabilisation intégrée au matelas."],
  ["Quel est le poids d'un lit à eau ?", "Selon le modèle, un lit à eau double pèse jusqu'à 800 kg, soit 150 à 200 kg/m², une charge répartie par le socle. En plus de trente ans d'installations, nous n'avons jamais eu à renoncer à un montage : là où l'on peut installer un congélateur rempli, on peut installer un lit à eau."],
  ['Combien consomme un matelas à eau ?', "Pour un lit à eau dual, comptez environ 1,32 à 1,53 kWh par jour selon la température réglée, l'isolation et la pièce, soit en moyenne une dizaine d'euros par mois sur l'année."],
  ['Comment entretenir un lit à eau ?', 'Purgez bien l\'air des matelas la première semaine, puis ajoutez un <a href="/conditionneur-multi-usage-waterclean-plus/">conditionneur anti-algues</a> une à deux fois par an selon le produit. Il n\'est pas nécessaire de changer l\'eau, sauf en cas de déménagement.'],
];
const faq = Section({ _title: 'FAQ', background_background: 'classic', background_color: C.tint, flex_align_items: 'center' }, [
  SectionHead('Questions fréquentes', 'Tout savoir sur le matelas à eau'),
  W('accordion', {
    tabs: faqItems.map(([q, a]) => ({ tab_title: q, tab_content: `<p>${a}</p>`, _id: id() })),
    faq_schema: 'yes', title_html_tag: 'h3',
    selected_icon: fa('fas fa-chevron-down'), selected_active_icon: fa('fas fa-chevron-up'), icon_align: 'right',
    border_width: px(1), border_color: C.line,
    title_background: C.white, title_color: C.green, tab_active_color: C.greenMid, icon_color: C.greenMid, icon_active_color: C.greenMid,
    ...typo('title_typography', { size: 17, sizeM: 16, weight: 700, lh: 1.4 }), title_padding: box(20, 24, 20, 24),
    content_background_color: C.white, content_color: C.muted, ...typo('content_typography', { size: 16, lh: 1.65 }), content_padding: box(0, 24, 22, 24),
    _element_width: 'initial', _element_custom_width: px(820), _element_width_mobile: 'inherit',
  }),
  Con({ flex_direction: 'row', flex_justify_content: 'center', padding: box(32, 0, 0, 0) }, [
    BtnLink('Lire les 35 réponses de nos experts', '/matelas-a-eau-faq-complete-35-reponses-dexperts/'),
  ]),
]);

// ---- 11. Appel à l'action ----
const cta = Section({
  _title: "Appel à l'action", flex_direction: 'row', flex_direction_tablet: 'column', flex_justify_content: 'space-between', flex_align_items: 'center', flex_align_items_tablet: 'flex-start', flex_gap: gap(32),
  padding: box(64, 24, 64, 24), padding_mobile: box(48, 16, 48, 16),
  background_background: 'gradient', background_color: C.greenDark, background_color_b: C.greenMid, background_gradient_angle: { unit: 'deg', size: 120 },
}, [
  Con({ flex_direction: 'column', width: pct(60), width_tablet: pct(100), flex_gap: gap(0) }, [
    H('Un projet de literie&nbsp;? Parlons-en.', 'h2', { color: C.white, margin: box(0, 0, 8, 0) }),
    Txt('<p>De façon humaine et sincère, un spécialiste vous répond 7 jours sur 7, de 9h à 21h.</p>', { color: '#E3EEE4', size: 18 }),
  ]),
  Con({ flex_direction: 'row', flex_direction_mobile: 'column', flex_justify_content: 'flex-end', flex_justify_content_tablet: 'flex-start', flex_gap: gap(12), flex_align_items_mobile: 'stretch', width: pct(40), width_tablet: pct(100) }, [
    BtnAccent('03 25 04 20 19', 'tel:+33325042019', { icon: 'fas fa-phone', fs: 17, pad: box(18, 28, 18, 28) }),
    BtnLight('WhatsApp', 'https://wa.me/33674393987', { icon: 'fab fa-whatsapp', iconLib: 'fa-brands', fs: 17, pad: box(18, 28, 18, 28), external: true }),
  ]),
]);

const pageContent = [hero, trust, catalogue, why, products, feature, steps, about, reviews, faq, cta];

// ================= PIED DE PAGE =================
const footLink = (items) => W('icon-list', {
  icon_list: items.map(([text, url]) => ({ text, link: link(url), selected_icon: { value: '', library: '' }, _id: id() })),
  space_between: px(10), text_color: '#E3EEE4', text_color_hover: C.white, ...typo('icon_typography', { size: 15, lh: 1.5 }),
});
const footTitle = (text) => H(text, 'p', { color: C.white, size: 16, sizeM: 16, weight: 700, lh: 1.3, margin: box(0, 0, 14, 0) });
const footer = Section({
  _title: 'Pied de page', html_tag: 'footer', padding: box(64, 24, 0, 24), padding_mobile: box(48, 16, 0, 16),
  background_background: 'classic', background_color: C.greenDark,
}, [
  Con({ _title: 'Pied – colonnes', container_type: 'grid', grid_columns_grid: { unit: 'custom', size: '1.5fr 1fr 1fr 1fr', sizes: [] }, grid_columns_grid_tablet: { unit: 'fr', size: 2 }, grid_columns_grid_mobile: { unit: 'fr', size: 1 }, grid_rows_grid: { unit: 'fr', size: 1 }, grid_gaps: gap(40), padding: box(0, 0, 48, 0) }, [
    Con({ flex_direction: 'column', flex_gap: gap(0) }, [
      Img(`${UP}/2024/09/cropped-template_images_logo-boutique-pc.webp`, 'La Maison du Dos – accueil', { radius: 14, link: '/', extra: { width: px(200), _background_background: 'classic', _background_color: C.white, _padding: box(12, 16, 12, 16), _border_radius: box(14), _element_width: 'initial', _element_custom_width: px(232), _margin: box(0, 0, 16, 0) } }),
      Txt('<p>La marque de confort du dos depuis 1988.</p>', { color: '#B3C9B6', size: 15, margin: box(0, 0, 12, 0) }),
      W('icon-list', {
        icon_list: [
          ['5 rue du Moulin, 52270 Epizon, France', '', 'fas fa-map-marker-alt'],
          ['03 25 04 20 19', 'tel:+33325042019', 'fas fa-phone'],
          ['info@la-maison-du-dos.com', 'mailto:info@la-maison-du-dos.com', 'fas fa-envelope'],
        ].map(([text, url, icon]) => ({ text, ...(url ? { link: link(url) } : {}), selected_icon: fa(icon), _id: id() })),
        space_between: px(10), icon_color: C.greenLight, icon_size: px(14), text_color: '#E3EEE4', text_color_hover: C.white, text_indent: px(10),
        ...typo('icon_typography', { size: 15, lh: 1.5 }),
      }),
    ]),
    Con({ flex_direction: 'column', flex_gap: gap(0) }, [footTitle('Nos produits'), footLink([
      ['Lits à eau', '/lits-a-eau/'], ['Matelas à eau légers & bébé', '/matelas-reglables/matelas-a-eau-leger-bebe/'], ['Matelas à télécommande', '/matelas-reglables/matelas-a-telecommande/'],
      ['Linge de lit', '/linge-de-lit/'], ['Accessoires, SAV & entretien', '/product-category/accessoires-produits-dentretien-sav/'], ['Nos marques', '/nos-marques/'],
    ])]),
    Con({ flex_direction: 'column', flex_gap: gap(0) }, [footTitle('Aide & conseils'), footLink([
      ['Qui sommes-nous', '/qui-sommes-nous/'], ['Matelas à eau : FAQ', '/matelas-a-eau-faq-complete-35-reponses-dexperts/'], ['Conseils & guides', '/blog'],
      ['Livraison & paiement', '/livraison-paiement/'], ['Contact', '/contact/'],
    ])]),
    Con({ flex_direction: 'column', flex_gap: gap(0), flex_align_items: 'flex-start' }, [
      footTitle('Service client'),
      Txt('<p>7 jours sur 7<br>de 9h à 21h</p>', { color: '#B3C9B6', size: 15, margin: box(0, 0, 16, 0) }),
      BtnAccent('Écrire sur WhatsApp', 'https://wa.me/33674393987', { icon: 'fab fa-whatsapp', iconLib: 'fa-brands', fs: 15, pad: box(12, 20, 12, 20), external: true }),
    ]),
  ]),
  Con({ _title: 'Pied – bas', flex_direction: 'row', flex_direction_mobile: 'column', flex_justify_content: 'space-between', flex_align_items: 'center', flex_align_items_mobile: 'flex-start', flex_gap: gap(12), padding: box(20, 0, 20, 0), border_border: 'solid', border_width: box(1, 0, 0, 0), border_color: 'rgba(255,255,255,0.1)' }, [
    Txt('<p>© 2026 La Maison du Dos. Tous droits réservés.</p>', { color: '#B3C9B6', size: 14 }),
    W('icon-list', {
      view: 'inline', icon_list: [['Mentions légales', '/mentions-legales/'], ['Conditions générales de vente', '/conditions-generales-de-ventes/'], ['Protection des données', '/conditions-generales-de-ventes/']]
        .map(([text, url]) => ({ text, link: link(url), selected_icon: { value: '', library: '' }, _id: id() })),
      space_between: px(20), text_color: '#E3EEE4', text_color_hover: C.white, ...typo('icon_typography', { size: 14, lh: 1.5 }),
    }),
  ]),
]);

// ---------- Écriture ----------
const tpl = (title, type, content, page_settings = []) => ({ content, page_settings, version: '0.4', title, type });
writeFileSync(path.join(OUT, 'accueil-la-maison-du-dos.json'), JSON.stringify(tpl("Accueil – La Maison du Dos", 'page', pageContent, { template: 'elementor_header_footer', hide_title: 'yes' }), null, 1));
writeFileSync(path.join(OUT, 'pied-de-page-la-maison-du-dos.json'), JSON.stringify(tpl('Pied de page – La Maison du Dos', 'section', [footer]), null, 1));

const count = (els) => els.reduce((a, e) => a + 1 + count(e.elements || []), 0);
console.log('Accueil :', count(pageContent), 'éléments ; pied de page :', count([footer]), 'éléments');
