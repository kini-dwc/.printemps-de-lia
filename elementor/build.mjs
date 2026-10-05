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
      catCard('fas fa-layer-group', 'Accessoires, SAV & entretien', 'Conditionneurs, anti-algues et accessoires pour faire durer votre literie à eau.', '/accessoires-produits-dentretien-sav/'),
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
      ['Linge de lit', '/linge-de-lit/'], ['Accessoires, SAV & entretien', '/accessoires-produits-dentretien-sav/'], ['Nos marques', '/nos-marques/'],
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

// ================= EN-TÊTE =================
/*
 * Widgets Royal Elementor Addons (version gratuite, déjà installée sur le site) pour ce qu'Elementor
 * gratuit ne sait pas faire : menu déroulant + menu mobile (wpr-nav-menu), recherche (wpr-search),
 * panier avec compteur (wpr-product-mini-cart). Le reste en widgets Elementor natifs.
 * Le menu WordPress « lmdd-menu-principal » est créé par l'extension d'import (MENU ci-dessous).
 */
export const MENU_SLUG = 'lmdd-menu-principal';
// Mêmes onglets que l'en-tête actuel du site, dans le même ordre. Le 4e champ désigne le méga-menu affiché sur ordinateur ;
// les sous-éléments servent au menu mobile (et de repli si les méga-menus sont désactivés).
export const MENU = [
  ['Lits à eau', '/lits-a-eau/', [
    ['Lits à eau en vente en ligne', '/lits-a-eau/lits-a-eau-en-vente-en-ligne/'],
    ['Lits à eau en vente sur devis', '/lits-a-eau/lits-a-eau-en-vente-sur-devis/'],
  ], 'lits'],
  ['Matelas réglables', '/matelas-reglables/', [
    ['Matelas à eau léger / bébé', '/matelas-reglables/matelas-a-eau-leger-bebe/'],
    ['Matelas à télécommande', '/matelas-reglables/matelas-a-telecommande/'],
  ], 'matelas'],
  ['Accessoires', '/accessoires-produits-dentretien-sav/', [
    ['Produits d\'entretien / SAV', '/accessoires-produits-dentretien-sav/produits-dentretien-sav/'],
    ['Accessoires literie à eau', '/accessoires-produits-dentretien-sav/accessoires-literie-a-eau/'],
    ['Têtes & cadres de lits', '/accessoires-produits-dentretien-sav/tetes-cadres-de-lits/'],
  ], 'accessoires'],
  ['Linge de lit', '/linge-de-lit/', [
    ['Couettes hiver', '/linge-de-lit/couettes-hiver/'], ['Couettes été', '/linge-de-lit/couettes-ete/'],
    ['Couettes toutes saisons', '/linge-de-lit/couettes-toutes-saisons/'], ['Couettes doubles', '/linge-de-lit/couettes-doubles/'],
    ['Couettes eider', '/linge-de-lit/couettes-eider/'], ['Couettes bio', '/linge-de-lit/couettes-bio/'],
    ['Draps-housses jersey', '/linge-de-lit/draps-housses-jersey/'], ['Housses de couette / taies', '/linge-de-lit/housses-couettes-taies/'],
    ['Alèses / protections', '/linge-de-lit/aleses-protections/'], ['Bella Donna : couverture / dessus de lit d\'été', '/linge-de-lit/bella-donna-couverture-dessus-de-lit-dete/'],
    ['Oreillers', '/linge-de-lit/oreillers/'], ['Surmatelas', '/linge-de-lit/surmatelas/'],
  ], 'linge'],
  ['Couettes Hefel', '/product-tag/hefel/', [
    ['Toutes les couettes Hefel', '/product-tag/hefel/'],
    ['Couettes doubles 4 saisons', '/linge-de-lit/couettes-doubles/'],
    ['Catalogue Hefel 2024-2025 (PDF)', '/wp-content/uploads/2025/04/HEFEL_Bettwarenkatalog-2024-2025_FR.pdf'],
  ], 'hefel'],
  ['Marques', '/nos-marques/', [
    ['La Maison du Dos', '/product-tag/la-maison-du-dos/'], ['Moosburger', '/product-tag/moosburger/'], ['Akva', '/product-tag/akva/'],
    ['Hefel', '/product-tag/hefel/'], ['Tasso', '/product-tag/tasso/'], ['Profine', '/product-tag/profine/'],
    ['Matrair', '/product-tag/matrair/'], ['Formesse', '/product-tag/formesse/'], ['Mr. Sandman', '/product-tag/mr-sandman/'],
    ['Poseïdon – Lunalife', '/product-tag/poseidon-lunalife/'], ['Kirstenbalk', '/product-tag/kirstenbalk/'], ['Dynaglobe', '/product-tag/dynaglobe/'],
  ], 'marques'],
];

const withSettings = (el, settings) => ({ ...el, settings: { ...el.settings, ...settings } });
const HIDE_DESKTOP = { hide_desktop: 'hidden-desktop' };
const HIDE_MOBILE_TABLET = { hide_tablet: 'hidden-tablet', hide_mobile: 'hidden-mobile' };
const topList = (items, o = {}) => W('icon-list', {
  view: 'inline',
  icon_list: items.map(([text, url, icon, lib]) => ({ text, ...(url ? { link: link(url, url.startsWith('http')) } : {}), selected_icon: icon ? fa(icon, lib) : { value: '', library: '' }, _id: id() })),
  space_between: px(22), icon_color: C.greenLight, icon_size: px(13), text_indent: px(7), text_color: C.white, text_color_hover: C.greenLight,
  ...typo('icon_typography', { size: 13, lh: 1.4, weight: 500 }),
  ...o,
});
const headIcon = (icon, url, label, o = {}) => W('icon', {
  _title: label, selected_icon: fa(icon, 'fa-regular'), link: { ...link(url), custom_attributes: `aria-label|${label}` },
  primary_color: C.green, hover_primary_color: C.red, size: px(21), size_mobile: px(20), align: 'center',
  _element_width: 'auto', ...o,
});

const headerTop = Con({
  _title: 'En-tête – barre du haut', content_width: 'boxed', boxed_width: px(1200),
  flex_direction: 'row', flex_justify_content: 'space-between', flex_justify_content_tablet: 'center', flex_align_items: 'center', flex_wrap: 'nowrap', flex_wrap_mobile: 'wrap',
  flex_gap: gap(22), flex_gap_mobile: gap(14, 4), padding: box(8, 24, 8, 24), padding_mobile: box(7, 12, 7, 12),
  background_background: 'classic', background_color: C.greenDark,
}, [
  // « grow » : prend la place restante et pousse les contacts à droite (ordinateur).
  topList([['Livraison gratuite dès 60 €', '/livraison-paiement/', 'fas fa-truck']], { _title: 'Livraison', _flex_size: 'grow', _flex_size_tablet: 'none', _element_width: 'auto' }),
  topList([['03 25 04 20 19', 'tel:+33325042019', 'fas fa-phone-alt']], { _title: 'Téléphone', _element_width: 'auto', _flex_size: 'none' }),
  topList([['WhatsApp', 'https://wa.me/33674393987', 'fab fa-whatsapp', 'fa-brands'], ['Service client 7j/7 de 9h à 21h', '', 'far fa-clock', 'fa-regular'], ['Contact', '/contact/', 'far fa-envelope', 'fa-regular']], {
    _title: 'WhatsApp & horaires', _element_width: 'auto', _flex_size: 'none', ...HIDE_MOBILE_TABLET,
  }),
], false);

const navMenu = (title, o) => W('wpr-nav-menu', {
  _title: title, menu_select: MENU_SLUG, menu_layout: 'horizontal', menu_align: 'center',
  menu_items_pointer: 'underline', pointer_animation_line: 'fade', pointer_height: px(2), pointer_color_hover: C.red,
  menu_items_submenu_icon: 'caret-down', menu_items_submenu_trigger: 'hover', menu_items_submenu_entrance: 'fade',
  menu_item_color: C.text, menu_item_color_hover: C.green,
  menu_items_padding_hr: px(16), menu_items_padding_vr: px(13),
  ...typo('menu_items_typography', { size: 15, weight: 600, lh: 1.3 }),
  sub_menu_color: C.text, sub_menu_color_bg: C.white, sub_menu_color_hover: C.green, sub_menu_color_bg_hover: C.greenBg,
  ...typo('sub_menu_typography', { size: 14, weight: 500, lh: 1.4 }),
  sub_menu_border_radius: box(10),
  mob_menu_display: 'tablet', mob_menu_stretch: 'full-width', mob_menu_item_align: 'left', toggle_btn_style: 'hamburger', toggle_btn_burger: 'v1',
  toggle_btn_align: 'right', toggle_btn_color: C.green, toggle_btn_color_hover: C.red,
  mobile_menu_color: C.text, mobile_menu_bg_color: C.white, mobile_menu_color_focus: C.green, mobile_menu_bg_color_focus: C.greenBg,
  mobile_menu_divider_color: C.line,
  ...o,
});

const headerMain = Con({
  _title: 'En-tête – logo, recherche, actions', content_width: 'boxed', boxed_width: px(1200), html_tag: 'header',
  flex_direction: 'row', flex_align_items: 'center', flex_justify_content: 'space-between', flex_wrap: 'nowrap',
  flex_gap: gap(28), flex_gap_tablet: gap(18), flex_gap_mobile: gap(14), padding: box(14, 24, 14, 24), padding_mobile: box(10, 16, 10, 16),
  background_background: 'classic', background_color: C.white,
}, [
  Img(`${UP}/2024/09/cropped-template_images_logo-boutique-pc.webp`, 'La Maison du Dos – accueil', {
    radius: 0, link: '/', extra: { width: px(180), width_mobile: px(132), align: 'left', _element_width: 'auto', _flex_size: 'none', _flex_size_tablet: 'grow' },
  }),
  W('wpr-search', {
    _title: 'Recherche (ordinateur)', search_query: 'all', search_placeholder: 'Lit à eau, couette Hefel, conditionneur…', search_aria_label: 'Rechercher un produit',
    search_btn: 'yes', search_btn_style: 'inner', search_btn_type: 'icon', search_btn_icon: fa('fas fa-search'),
    _flex_size: 'grow', _element_width: 'auto',
    input_color: C.text, input_bg_color: C.tint, input_placeholder_color: C.muted, input_border_color: C.line, input_focus_border_color: C.green,
    input_border_size: box(1), input_border_radius: box(999), input_padding: box(12, 20, 12, 20),
    ...typo('input_typography', { size: 15, lh: 1.3 }),
    btn_text_color: C.green, btn_bg_color: 'rgba(0,0,0,0)', btn_hv_text_color: C.red, btn_hv_bg_color: 'rgba(0,0,0,0)', btn_width: px(48),
    ...HIDE_MOBILE_TABLET,
  }),
  withSettings(BtnAccent('Demander un devis', '/lits-a-eau/lits-a-eau-en-vente-sur-devis/', { fs: 15, pad: box(12, 22, 12, 22) }), { _title: 'Bouton devis', _element_width: 'auto', _flex_size: 'none', ...HIDE_MOBILE_TABLET }),
  headIcon('far fa-heart', '/my-wishlist/', 'Ma liste de souhaits', { hide_mobile: 'hidden-mobile', _flex_size: 'none' }),
  headIcon('far fa-user', '/my-account/', 'Mon compte', { _flex_size: 'none' }),
  W('wpr-product-mini-cart', {
    _title: 'Panier', icon: 'bag-medium', toggle_text: 'none', mini_cart_style: 'none',
    toggle_btn_icon_color: C.green, toggle_btn_icon_color_hover: C.red, toggle_btn_icon_size: px(22),
    toggle_btn_item_count_color: C.white, toggle_btn_item_count_bg_color: C.red,
    _element_width: 'auto', _flex_size: 'none',
  }),
  // Menu burger : tablette et mobile uniquement.
  navMenu('Menu (tablette et mobile)', { _element_width: 'auto', _flex_size: 'none', ...HIDE_DESKTOP }),
], false);

const headerNav = Con({
  _title: 'En-tête – méga-menu (ordinateur)', content_width: 'boxed', boxed_width: px(1200), html_tag: 'nav',
  flex_direction: 'row', flex_justify_content: 'center', padding: box(0, 24, 0, 24),
  background_background: 'classic', background_color: C.white,
  border_border: 'solid', border_width: box(1, 0, 1, 0), border_color: C.line,
  ...HIDE_MOBILE_TABLET,
}, [
  W('wpr-mega-menu', {
    ...navMenu('', {}).settings, _title: 'Méga-menu (ordinateur)', _element_width: 'auto',
    sub_mega_menu_color_bg: C.white, sub_mega_menu_border_radius: box(0, 0, 16, 16), menu_items_sub_offset: px(0),
    sub_mega_menu_box_shadow_box_shadow_type: 'yes', sub_mega_menu_box_shadow_box_shadow: { horizontal: 0, vertical: 18, blur: 40, spread: -12, color: 'rgba(1,46,8,0.25)' },
    sub_mega_menu_border_border: 'solid', sub_mega_menu_border_width: box(3, 0, 0, 0), sub_mega_menu_border_color: C.green,
  }),
], false);

const headerSearchMobile = Con({
  _title: 'En-tête – recherche (tablette et mobile)', content_width: 'boxed', boxed_width: px(1200),
  padding: box(10, 16, 10, 16), background_background: 'classic', background_color: C.tint, border_border: 'solid', border_width: box(0, 0, 1, 0), border_color: C.line,
  ...HIDE_DESKTOP,
}, [
  W('wpr-search', {
    _title: 'Recherche (mobile)', search_query: 'all', search_placeholder: 'Lit à eau, couette Hefel, conditionneur…', search_aria_label: 'Rechercher un produit',
    search_btn: 'yes', search_btn_style: 'inner', search_btn_type: 'icon', search_btn_icon: fa('fas fa-search'),
    input_color: C.text, input_bg_color: C.white, input_placeholder_color: C.muted, input_border_color: C.line, input_focus_border_color: C.green,
    input_border_size: box(1), input_border_radius: box(999), input_padding: box(11, 18, 11, 18),
    ...typo('input_typography', { size: 15, lh: 1.3 }),
    btn_text_color: C.green, btn_bg_color: 'rgba(0,0,0,0)', btn_hv_text_color: C.red, btn_hv_bg_color: 'rgba(0,0,0,0)', btn_width: px(46),
  }),
], false);

// ---------- Méga-menus (ordinateur) ----------
/*
 * Un modèle Elementor par onglet, affiché par le widget « Méga-menu » de Royal Elementor Addons (gratuit),
 * en pleine largeur sous la barre de menu. Contenu centré sur 1200 px, widgets Elementor natifs.
 */
const MEGA_LINK = { text_color: C.text, text_color_hover: C.green };
const megaTitle = (text, url) => W('heading', {
  title: text, header_size: 'p', title_color: C.greenText, ...(url ? { link: link(url) } : {}),
  ...typo('typography', { size: 12, weight: 700, ls: 1.4, transform: 'uppercase', lh: 1.3 }),
  _margin: box(0, 0, 12, 0), _padding: box(0, 0, 10, 0), _border_border: 'solid', _border_width: box(0, 0, 1, 0), _border_color: C.line,
});
const megaLinks = (items, o = {}) => W('icon-list', {
  icon_list: items.map(([text, url]) => ({ text, link: link(url, /\.pdf$/.test(url)), selected_icon: { value: '', library: '' }, _id: id() })),
  space_between: px(o.space ?? 9), ...MEGA_LINK, ...typo('icon_typography', { size: o.size || 14.5, lh: 1.4, weight: 500 }),
});
/** Carte cliquable : toute la carte est un lien (balise <a>), sans lien imbriqué. */
const megaCard = (icon, title, desc, url) => Con({
  _title: `Carte : ${title}`, html_tag: 'a', link: link(url), flex_direction: 'column', flex_gap: gap(6), 
  padding: box(22, 22, 20, 22),
  background_background: 'classic', background_color: C.tint, background_hover_background: 'classic', background_hover_color: C.greenBg,
  border_border: 'solid', border_width: box(1), border_color: C.line, border_hover_border: 'solid', border_hover_width: box(1), border_hover_color: C.greenLight,
  border_radius: box(14),
}, [
  W('icon', {
    selected_icon: fa(icon), view: 'stacked', shape: 'circle', primary_color: C.white, secondary_color: C.green,
    size: px(18), icon_padding: px(11), align: 'left', _margin: box(0, 0, 8, 0),
  }),
  H(title, 'p', { size: 17, sizeM: 16, weight: 700, lh: 1.3, color: C.green, margin: box(0) }),
  Txt(`<p>${desc}</p>`, { size: 14, sizeM: 14, lh: 1.55 }),
  H('Découvrir →', 'p', { size: 14, sizeM: 14, weight: 600, lh: 1.3, color: C.red, margin: box(6, 0, 0, 0) }),
]);
/** Encart vert foncé : conseil + bouton. */
const megaAside = (title, text, btnText, btnUrl, o = {}) => Con({
  _title: 'Encart conseil', flex_direction: 'column', flex_gap: gap(10), flex_justify_content: 'center', 
  padding: box(24), border_radius: box(14),
  background_background: 'gradient', background_color: C.greenDark, background_color_b: C.green, background_gradient_angle: { unit: 'deg', size: 135 },
}, [
  H(title, 'p', { size: 18, sizeM: 17, weight: 700, lh: 1.3, color: C.white, margin: box(0) }),
  Txt(`<p>${text}</p>`, { size: 14, lh: 1.55, color: C.onDark }),
  withSettings(BtnLight(btnText, btnUrl, { fs: 14, pad: box(11, 18, 11, 18), icon: o.icon, external: o.external }), { _margin: box(6, 0, 0, 0) }),
]);
/** Visuel : photo + bouton « Tout voir ». */
const megaVisual = (img, alt, btnText, btnUrl) => Con({
  _title: 'Visuel', flex_direction: 'column', flex_gap: gap(12),
}, [
  // Sans lien : le bouton juste dessous y mène (une image liée s'affiche en « inline-block » et perd sa largeur).
  Img(img, alt, { height: 170, radius: 14 }),
  withSettings(BtnPrimary(btnText, btnUrl, { fs: 14, pad: box(12, 18, 12, 18), icon: 'fas fa-arrow-right', iconAfter: true }), { box_shadow_box_shadow_type: '', align: 'justify' }),
]);
/** Panneau en grille : `cols` = colonnes CSS (ex. « 1fr 1fr 260px »), pour des largeurs stables. */
const megaPanel = (title, cols, children) => Con({
  _title: `Méga-menu : ${title}`, content_width: 'boxed', boxed_width: px(1200),
  container_type: 'grid', grid_columns_grid: { unit: 'custom', size: cols, sizes: [] }, grid_rows_grid: { unit: 'fr', size: 1 },
  grid_gaps: gap(24), grid_align_items: 'stretch', padding: box(28, 24, 32, 24),
  background_background: 'classic', background_color: C.white,
}, children, false);
const megaCol = (title, children) => Con({ _title: `Colonne : ${title}`, flex_direction: 'column', flex_gap: gap(0) }, children);

const MEGA = {
  lits: ['Lits à eau', megaPanel('Lits à eau', '1fr 1fr 260px', [
    megaCard('fas fa-shopping-cart', 'Lits à eau en vente en ligne', 'Des modèles prêts à commander, livrés et installés chez vous avec des réglages personnalisés.', '/lits-a-eau/lits-a-eau-en-vente-en-ligne/'),
    megaCard('fas fa-file-signature', 'Lits à eau en vente sur devis', 'Un lit pensé pour votre dos et votre chambre : un spécialiste établit votre devis selon vos besoins.', '/lits-a-eau/lits-a-eau-en-vente-sur-devis/'),
    megaVisual(`${UP}/2024/09/altura-pos.jpg`, 'Lit à eau Altura de Poseïdon', 'Tous les lits à eau', '/lits-a-eau/'),
  ])],
  matelas: ['Matelas réglables', megaPanel('Matelas réglables', '1fr 1fr 260px', [
    megaCard('fas fa-feather-alt', 'Matelas à eau léger / bébé', 'Le confort d\'un matelas à eau, dans une version légère.', '/matelas-reglables/matelas-a-eau-leger-bebe/'),
    megaCard('fas fa-sliders-h', 'Matelas à télécommande', 'Une fermeté réglable à volonté, par simple télécommande.', '/matelas-reglables/matelas-a-telecommande/'),
    megaVisual(`${UP}/2024/09/matelas-eau-leger.jpg`, 'Matelas à eau léger Aqualight Premium', 'Tous les matelas réglables', '/matelas-reglables/'),
  ])],
  accessoires: ['Accessoires', megaPanel('Accessoires', '1fr 1fr 1fr 240px', [
    megaCard('fas fa-tint', 'Produits d\'entretien / SAV', 'Conditionneurs, anti-algues et produits pour faire durer votre literie à eau.', '/accessoires-produits-dentretien-sav/produits-dentretien-sav/'),
    megaCard('fas fa-tools', 'Accessoires literie à eau', 'Tout pour équiper et entretenir votre lit ou matelas à eau.', '/accessoires-produits-dentretien-sav/accessoires-literie-a-eau/'),
    megaCard('fas fa-bed', 'Têtes & cadres de lits', 'Pour habiller votre lit à eau selon vos goûts.', '/accessoires-produits-dentretien-sav/tetes-cadres-de-lits/'),
    megaAside('Une question d\'entretien ?', 'Nos spécialistes vous conseillent 7 jours sur 7, de 9h à 21h.', '03 25 04 20 19', 'tel:+33325042019', { width: 240, icon: 'fas fa-phone-alt' }),
  ])],
  linge: ['Linge de lit', megaPanel('Linge de lit', '1fr 1fr 1fr 260px', [
    megaCol('Couettes', [megaTitle('Couettes'), megaLinks([
      ['Couettes hiver', '/linge-de-lit/couettes-hiver/'], ['Couettes été', '/linge-de-lit/couettes-ete/'], ['Couettes toutes saisons', '/linge-de-lit/couettes-toutes-saisons/'],
      ['Couettes doubles', '/linge-de-lit/couettes-doubles/'], ['Couettes eider', '/linge-de-lit/couettes-eider/'], ['Couettes bio', '/linge-de-lit/couettes-bio/'],
    ])]),
    megaCol('Draps & protections', [megaTitle('Draps & protections'), megaLinks([
      ['Draps-housses jersey', '/linge-de-lit/draps-housses-jersey/'], ['Housses de couette / taies', '/linge-de-lit/housses-couettes-taies/'],
      ['Alèses / protections', '/linge-de-lit/aleses-protections/'], ['Bella Donna : couverture / dessus de lit d\'été', '/linge-de-lit/bella-donna-couverture-dessus-de-lit-dete/'],
    ])]),
    megaCol('Oreillers & surmatelas', [megaTitle('Oreillers & surmatelas'), megaLinks([
      ['Oreillers', '/linge-de-lit/oreillers/'], ['Surmatelas', '/linge-de-lit/surmatelas/'],
    ])]),
    megaVisual(`${UP}/2024/09/bella-donna-standard-0030-bordeaux.jpg`, 'Drap-housse jersey Bella Donna bordeaux', 'Tout le linge de lit', '/linge-de-lit/'),
  ])],
  hefel: ['Couettes Hefel', megaPanel('Couettes Hefel', '1fr 200px', [
    Con({ _title: 'Couettes Hefel – colonnes', container_type: 'grid',
      grid_columns_grid: { unit: 'fr', size: 5 }, grid_rows_grid: { unit: 'fr', size: 1 }, grid_gaps: gap(22), grid_auto_flow: 'row' }, [
      megaCol('Couettes doubles 4 saisons', [megaTitle('Couettes doubles 4 saisons', '/linge-de-lit/couettes-doubles/'), megaLinks([
        ['Bio Bois', '/couette-double-bio-bois-4-saisons-legere/'], ['Maïs / Tencel™ Lyocell', '/couette-double-soft-4-saisons-legere/'],
        ['Tencel™ Lyocell / housse Tencel', '/couette-double-klimacontrol-comfort4-saisons-legere/'], ['Tencel™ Lyocell / housse coton', '/couette-double-edition-101-4-saisons-2/'],
      ], { size: 13.5, space: 7 })]),
      megaCol('Fibres animales', [megaTitle('Fibres animales'), megaLinks([
        ['Soie sauvage / housse Tencel', '/couette-pure-soie-ete/'], ['Soie sauvage / housse coton', '/couette-soie-dream-ete/'],
        ['Cachemire / laine', '/couette-cachemire-deluxe-toutes-saisons/'], ['Poils de chameau', '/couette-camel-dreamtoutes-saisons/'],
        ['Laine vierge', '/couette-pure-wool-toutes-saisons/'], ['Bio laine', '/couette-bio-laine-toutes-saisons/'],
        ['Laine vierge / pin cimbre', '/couette-wellness-pin-cimbretoutes-saisons-legere/'],
      ], { size: 13.5, space: 7 })]),
      megaCol('Fibres végétales', [megaTitle('Fibres végétales'), megaLinks([
        ['Bio Bois', '/couette-bio-bois-toutes-saisons/'], ['Tencel™ Lyocell', '/couette-klimacontrol-fair-toutes-saisons/'],
        ['Maïs', '/couette-pure-mais-toutes-saisons/'], ['Maïs / Tencel™ Lyocell', '/couette-soft-toutes-saisons-legere/'],
        ['Bambou / maïs', '/couette-pure-bambou-toutes-saisons/'], ['Microfibres Tencel', '/couette-edition-101-toutes-saisons/'],
        ['Tencel programme cool', '/couette-ete-programme-cool-hefel/'],
      ], { size: 13.5, space: 7 })]),
      megaCol('Fibres synthétiques', [megaTitle('Fibres synthétiques'), megaLinks([
        ['Softbausch fibres creuses PES', '/couette-softbausch-home-toutes-saisons/'], ['Tencel™ Lyocell / viscose Celliant®', '/couette-toutes-saisons-wellness-retreat-hefel/'],
        ['Tencel™ Lyocell / Softbausch', '/couette-softbausch-home-toutes-saisons/'], ['Vitasan fibres PES', '/couette-wellness-vitasan-toutes-saisons/'],
      ], { size: 13.5, space: 7 })]),
      megaCol('Duvets', [megaTitle('Duvets'), megaLinks([
        ['Canard 90 % CUIN 700', '/couette-en-duvet-de-canard-alaska-toutes-saisons/'], ['Oie 90 % CUIN 600', '/couette-en-duvet-doie-silver-down-toutes-saisons/'],
        ['Oie 90 % CUIN 700', '/couette-en-duvet-mont-blanc-toutes-saisons-legere/'], ['Oie 100 % CUIN 700', '/couette-en-duvet-doie-100-arlberg-toutes-saisons-2/'],
        ['Oie 100 % CUIN 700 / housse Outlast®', '/couette-duvet-doie-outlast-proactive-nexgen-toutes-saisons-2/'], ['Oie 100 % CUIN 750', '/couette-en-duvet-doie-platinum-down-toutes-saisons/'],
        ['Oie 100 % CUIN 800 / housse Tencel / Celliant®', '/couette-retreat-down-duvet-toutes-saisons/'], ['Oie 100 % CUIN 850+', '/couette-duvet-doie-de-luxe-down-toutes-saisons-legere/'],
        ['Oie 100 % CUIN 850+ / housse Tencel / cachemire', '/couette-duvet-doie-opulence-toutes-saisons/'],
        ['Eider / housse nano coton 70 g/m²', '/couette-duvet-eider-toutes-saisons-legere-housse-100-coton-nano-batiste-pour-duvet/'],
        ['Eider / housse jacquard de soie 120 g/m²', '/couette-duvet-eider-toutes-saisons-legere/'],
      ], { size: 13.5, space: 7 })]),
    ]),
    Con({ _title: 'Catalogue & promotions', flex_direction: 'column', flex_gap: gap(12), flex_align_items: 'stretch' }, [
      // Miniature (214 px) du catalogue, chargée en différé, au lieu de l'image de 807 px de l'ancien menu.
      W('image', {
        _title: 'Catalogue Hefel (PDF)', image: { url: `${UP}/2025/09/screenshot-la-maison-du-dos-com-2025-09-08-11-18-36-214x300.png`, id: '', alt: 'Catalogue des couettes Hefel 2024-2025', source: 'library', size: '' },
        image_size: 'full', width: pct(100), link_to: 'custom', link: link(`${UP}/2025/04/HEFEL_Bettwarenkatalog-2024-2025_FR.pdf`, true),
        image_border_radius: box(10), image_border_border: 'solid', image_border_width: box(1), image_border_color: C.line,
      }),
      H('Catalogue Hefel 2024-2025 (PDF)', 'p', { size: 13, weight: 600, lh: 1.35, color: C.green, align: 'center', margin: box(0), link: `${UP}/2025/04/HEFEL_Bettwarenkatalog-2024-2025_FR.pdf` }),
      Txt('<p>Profitez de nos promotions sur les couettes doubles, les couettes hiver et toutes saisons.</p>', { size: 13, lh: 1.5, align: 'center', color: C.red, weight: 600 }),
    ]),
  ])],
  marques: ['Marques', megaPanel('Marques', '1fr 250px', [
    Con({ _title: 'Marques – grille', flex_direction: 'column', flex_gap: gap(14) }, [
      megaTitle('Nos marques partenaires', '/nos-marques/'),
      Con({ _title: 'Logos', container_type: 'grid', grid_columns_grid: { unit: 'fr', size: 6 }, grid_rows_grid: { unit: 'fr', size: 2 }, grid_gaps: gap(10) },
        MENU.find((m) => m[3] === 'marques')[2].map(([name, url]) => withSettings(BtnGhost(name, url, { fs: 13.5, pad: box(12, 10, 12, 10) }), {
          _title: `Marque : ${name}`, align: 'justify', border_width: box(1), border_color: C.line, button_text_color: C.text, border_radius: box(10),
          hover_color: C.green, button_background_hover_color: C.greenBg, button_hover_border_color: C.green,
        }))),
    ]),
    megaAside('Toutes nos marques', 'Découvrez les fabricants dont nous distribuons la literie et le linge de lit.', 'Voir les marques', '/nos-marques/', { width: 250 }),
  ])],
};
export const MEGA_MENUS = Object.fromEntries(Object.entries(MEGA).map(([k, [label, panel]]) => [k, { title: `Méga-menu – ${label}`, content: [panel] }]));

const headerContent = [headerTop, headerMain, headerNav, headerSearchMobile];

// ================= FICHE PRODUIT (modèle Royal « product_single ») =================
/*
 * Un seul modèle pour tous les produits : il s'adapte tout seul.
 * - Produit vendu sur devis (catégorie « …sur-devis… » ou forcé dans le produit) : badge « Sur devis », prix masqué,
 *   options de configuration conservées, bouton « Demander un devis » qui ouvre le panneau (extension LMDD – Devis & SAV).
 * - Produit vendu en ligne : prix, options, « Ajouter au panier », réassurance livraison / paiement.
 * Les codes courts [lmdd_…] viennent de l'extension LMDD – Devis & SAV. Les deux emplacements « Avis » reçoivent,
 * à l'installation, les codes courts de la Société des Avis Garantis repris de votre modèle actuel.
 */
const SC = (shortcode, title, o = {}) => W('shortcode', { shortcode, _title: title, ...o });
export const AVIS_RESUME = 'Avis (résumé) – code court repris du modèle actuel';
export const AVIS_LISTE = 'Avis (liste) – code court repris du modèle actuel';

const productTemplate = [
  Con({
    _title: 'Fiche – images et achat', content_width: 'boxed', boxed_width: px(1200), html_tag: 'section',
    flex_direction: 'column', flex_gap: gap(18), padding: box(20, 24, 64, 24), padding_mobile: box(12, 16, 40, 16),
  }, [
    SC('[lmdd_fil_ariane]', 'Fil d\'Ariane'),
    Con({ _title: 'Deux colonnes', flex_direction: 'row', flex_direction_tablet: 'column', flex_gap: gap(56), flex_gap_tablet: gap(28), flex_align_items: 'flex-start', flex_align_items_tablet: 'stretch' }, [
      Con({ _title: 'Images (restent visibles au défilement)', width: pct(54), width_tablet: pct(100), css_classes: 'lmdd-sticky', flex_direction: 'column' }, [
        W('wpr-product-media', {
          product_media_sales_badge: '', product_media_lightbox: 'yes', lightbox_extra_icon: 'yes',
          lightbox_popup_download: '', lightbox_popup_sharing: '', lightbox_popup_autoplay: '',
          gallery_slider_nav: 'yes', gallery_slider_thumbs: 'yes', gallery_slider_thumbs_type: 'stacked', gallery_slider_thumb_cols: 5,
          media_border_radius: box(18), gallery_thumb_border_radius: box(10),
          gallery_thumb_nav_gutter_hr: px(10), gallery_thumb_nav_gutter_vr: px(10), product_media_vertical_distance: px(12),
          gallery_slider_nav_color: C.green, gallery_slider_nav_bg_color: C.white, gallery_slider_nav_border_color: C.white,
          gallery_slider_nav_hover_color: C.white, gallery_slider_nav_hover_bg_color: C.green, gallery_slider_nav_hover_border_color: C.green,
          gallery_slider_nav_border_radius: box(999), lightbox_color: C.green, lightbox_bg_color: C.white, lightbox_radius: box(999),
        }),
      ]),
      Con({ _title: 'Achat', width: pct(46), width_tablet: pct(100), flex_direction: 'column', flex_gap: gap(14) }, [
        SC('[lmdd_produit_entete]', 'Badges, catégorie et marque'),
        SC('[lmdd_titre]', 'Titre (titre court du produit, sinon son nom)'),
        SC('', AVIS_RESUME),
        W('wpr-product-price', { _title: 'Prix (masqué sur les produits sur devis)', price_color: C.green, price_sale_color: C.muted, ...typo('price_typography', { size: 28, weight: 800, lh: 1.2 }) }),
        W('wpr-product-excerpt', { _title: 'Description courte', excerpt_color: C.muted, ...typo('excerpt_typography', { size: 16, lh: 1.65 }) }),
        SC('[lmdd_points_cles]', 'Points clés (champ « Points clés » du produit)'),
        W('wpr-product-add-to-cart', {
          _title: 'Options et ajout au panier (sur devis : bouton « Demander un devis »)', _margin: box(8, 0, 0, 0),
          add_to_cart_layout: 'column', add_to_cart_button_alignment: 'justify', add_to_cart_buttons_layout: 'row',
          add_to_cart_color: C.white, add_to_cart_bg_color: C.green, add_to_cart_border_color: C.green,
          add_to_cart_color_hr: C.white, add_to_cart_bg_color_hr: C.greenHover, add_to_cart_border_color_hr: C.greenHover,
          add_to_cart_height: px(54), add_to_cart_radius: box(999), ...typo('add_to_cart_typography', { size: 17, weight: 700 }),
          quantity_radius: box(999), quantity_border_color: C.line, add_to_cart_quantity_height: px(54),
        }),
        SC('[lmdd_reassurance]', 'Réassurance (livraison, paiement, conseil)'),
      ]),
    ]),
  ], false),
  Con({
    _title: 'Fiche – description et questions', content_width: 'boxed', boxed_width: px(1200), html_tag: 'section',
    padding: box(56, 24, 56, 24), padding_mobile: box(36, 16, 36, 16), background_background: 'classic', background_color: C.tint, flex_direction: 'column',
  }, [
    W('wpr-product-tabs', {
      tabs_position: 'above',
      tab_text_color: C.muted, tab_bg_color: 'rgba(0,0,0,0)', tabs_border_color: 'rgba(0,0,0,0)',
      hover_tab_text_color: C.green, hover_tab_bg_color: 'rgba(0,0,0,0)', active_tab_text_color: C.green, active_tab_bg_color: C.white, tab_active_border_color: C.line,
      tab_border_radius: box(12, 12, 0, 0), tab_padding: box(14, 24, 14, 24), ...typo('tab_typography', { size: 16, weight: 700 }),
      text_color: C.text, tab_content_bg_color: C.white, content_border_color: C.line, panel_padding: box(36, 40, 36, 40),
      panel_border_radius: box(0, 16, 16, 16), ...typo('content_typography', { size: 16, lh: 1.7 }), title_color: C.green,
    }),
  ], false),
  Con({ _title: 'Fiche – avis', content_width: 'boxed', boxed_width: px(1200), html_tag: 'section', padding: box(48, 24, 16, 24), padding_mobile: box(32, 16, 8, 16), flex_direction: 'column' }, [
    SC('', AVIS_LISTE),
  ], false),
  Con({ _title: 'Fiche – produits similaires', content_width: 'boxed', boxed_width: px(1200), html_tag: 'section', padding: box(40, 24, 72, 24), padding_mobile: box(28, 16, 48, 16), flex_direction: 'column' }, [
    SC('[related_products limit="4" columns="4"]', 'Produits liés (WooCommerce, titre « Produits similaires »)'),
  ], false),
];

// ================= PAGE SAV =================
const SAV_FORM_ID = 'formulaire-sav';
const brandCard = (title, sub, models) => Con({ _title: `Marque : ${title}`, flex_direction: 'column', flex_gap: gap(6), padding: box(22), border_border: 'solid', border_width: box(1), border_color: C.line, border_radius: box(16), background_background: 'classic', background_color: C.white }, [
  H(title, 'h3', { size: 19 }),
  Txt(`<p>${sub}</p>`, { size: 14.5, margin: box(0, 0, 6, 0) }),
  W('icon-list', {
    view: 'inline', icon_list: models.map((m) => ({ text: m, selected_icon: { value: '', library: '' }, _id: id() })),
    space_between: px(8), text_color: C.text, ...typo('icon_typography', { size: 13.5, weight: 600 }),
  }),
]);

const savPage = [
  Section({
    _title: 'SAV – introduction', flex_direction: 'row', flex_direction_tablet: 'column', flex_align_items: 'center', flex_gap: gap(56),
    background_background: 'gradient', background_color: C.greenDark, background_color_b: C.green, background_gradient_angle: { unit: 'deg', size: 135 },
    padding: box(80, 24, 80, 24), padding_mobile: box(44, 16, 48, 16),
  }, [
    Con({ _title: 'Texte', width: pct(60), width_tablet: pct(100), flex_direction: 'column', flex_gap: gap(0) }, [
      Eyebrow('SAV lits à eau — toutes marques', C.greenLight),
      H('Votre lit à eau a un problème ? <span style="color:#A9D8B0">Il a sûrement une solution.</span>', 'h1', { color: C.white, size: 46, sizeT: 38, sizeM: 30 }),
      Txt('<p>Fuite, matelas déformé, chauffage en panne, eau trouble, déménagement : nous intervenons sur tous les types et toutes les marques de lits à eau vendus en France, y compris les marques qui n\'existent plus. Décrivez-nous la situation, nous vous rappelons avec une solution et, si besoin, un devis.</p>', { color: C.onDark, size: 18, margin: box(0, 0, 26, 0) }),
      Con({ _title: 'Boutons', flex_direction: 'row', flex_wrap: 'wrap', flex_gap: gap(12) }, [
        BtnLight('Décrire mon problème', `#${SAV_FORM_ID}`, { icon: 'fas fa-arrow-down', iconAfter: true }),
        withSettings(BtnGhost('03 25 04 20 19', 'tel:+33325042019', { icon: 'fas fa-phone-alt' }), { button_text_color: C.white, border_color: 'rgba(255,255,255,0.6)', button_background_hover_color: 'rgba(255,255,255,0.12)', hover_color: C.white }),
      ]),
      Txt('<p>Spécialiste du lit à eau depuis 1994 · service joignable 7 jours sur 7, de 9h à 21h</p>', { color: '#9FC2A4', size: 14, margin: box(22, 0, 0, 0) }),
    ]),
    Con({
      _title: 'Urgence', width: pct(40), width_tablet: pct(100), flex_direction: 'column', flex_gap: gap(10), padding: box(26),
      background_background: 'classic', background_color: 'rgba(255,255,255,0.08)', border_border: 'solid', border_width: box(1), border_color: 'rgba(233,162,59,0.7)', border_radius: box(18),
    }, [
      W('icon', { selected_icon: fa('fas fa-exclamation-triangle'), primary_color: '#E9A23B', size: px(26), align: 'left' }),
      H('De l\'eau qui s\'écoule, maintenant ?', 'p', { color: C.white, size: 21, weight: 800, lh: 1.25, margin: box(0) }),
      Txt('<p>Ne remplissez pas de formulaire. Coupez le chauffage du lit, épongez avec des serviettes, et appelez-nous : nous vous guidons pour limiter les dégâts avant de parler de pièces.</p>', { color: C.onDark, size: 15.5 }),
      withSettings(BtnAccent('Appeler le 03 25 04 20 19', 'tel:+33325042019', { icon: 'fas fa-phone-alt', fs: 16 }), { align: 'justify', _margin: box(6, 0, 0, 0) }),
    ]),
  ]),
  Section({ _title: 'SAV – symptômes' }, [
    SectionHead('Le diagnostic', 'Ce qui arrive aux lits à eau', 'Trouvez votre situation : pour chacune, ce que c\'est en général, ce que nous faisons, et les pièces concernées avec leur prix.'),
    SC('[lmdd_sav_symptomes formulaire="#' + SAV_FORM_ID + '"]', 'Fiches symptômes (prix des pièces en direct)'),
  ]),
  Section({ _title: 'SAV – marques', background_background: 'classic', background_color: C.tint }, [
    SectionHead('Toutes marques', 'Même si le fabricant n\'existe plus', 'C\'est le matelas qu\'il faut identifier, pas la marque. Votre marque n\'est pas dans la liste, ou l\'étiquette est illisible ? Ce n\'est pas bloquant : dites-le dans le formulaire.'),
    Con({ _title: 'Marques – grille', container_type: 'grid', grid_columns_grid: { unit: 'fr', size: 4 }, grid_columns_grid_tablet: { unit: 'fr', size: 2 }, grid_columns_grid_mobile: { unit: 'fr', size: 1 }, grid_rows_grid: { unit: 'fr', size: 1 }, grid_gaps: gap(18) }, [
      brandCard('Akva', 'Fabricant danois · nous en sommes revendeur', ['Carla', 'Modulex', 'Nordic', 'Vega', 'Lyra', 'Boxbed', 'Soft']),
      brandCard('Poseïdon · Lunalife', 'Ex-Luna-Rest : même fabricant, plusieurs noms selon l\'année', ['Easy Box', 'Océan', 'Smile', 'Lunaflex', 'Pacific', 'Altura']),
      brandCard('Tasso', 'Softside, hardside et berceau', ['Softside', 'Hardside', 'Berceau bébé']),
      brandCard('Et les autres', 'Matelas de remplacement sur mesure', ['Highline', 'Mesamoll II', 'Tribrid®', 'Sur mesure']),
    ]),
  ]),
  Section({ _title: 'SAV – entretien et garantie', flex_direction: 'row', flex_direction_tablet: 'column', flex_gap: gap(56), flex_align_items: 'center' }, [
    Con({ _title: 'Texte', width: pct(58), width_tablet: pct(100), flex_direction: 'column', flex_gap: gap(0) }, [
      Eyebrow('Entretien & garantie'),
      H('Un geste par an décide de la durée de vie du matelas', 'h2'),
      Txt('<p>La garantie d\'un matelas à eau porte sur les soudures. Elle suppose un entretien régulier : une dose de <strong>conditionneur</strong> ajoutée dans l\'eau chaque année. Le négliger peut suffire à l\'annuler.</p><p>Vérifiez aussi l\'étanchéité trois à quatre fois par an, en passant une lingette humide sur le matelas, les raccords et la valve : un micro-défaut peut passer inaperçu plusieurs semaines.</p><p>Les durées de garantie dépendent du modèle : elles sont précisées dans nos <a href="/conditions-generales-de-ventes/">conditions générales de vente</a>.</p>', { margin: box(0, 0, 22, 0) }),
      BtnPrimary('Voir les produits d\'entretien', '/accessoires-produits-dentretien-sav/produits-dentretien-sav/', { icon: 'fas fa-arrow-right', iconAfter: true }),
    ]),
    Con({ _title: 'Rappel', width: pct(42), width_tablet: pct(100), flex_direction: 'column', flex_gap: gap(14), padding: box(28), border_radius: box(18), background_background: 'classic', background_color: C.greenBg }, [
      ...[['fas fa-calendar-check', 'Chaque année', 'Une dose de conditionneur dans chaque matelas.'], ['fas fa-search', '3 à 4 fois par an', 'Contrôle des soudures, raccords et valve, à la lingette humide.'], ['fas fa-thermometer-half', 'En cas de lit froid', 'Le chauffage se remplace, souvent sans vider le lit.']].map(([icon, t, d]) => W('icon-box', {
        selected_icon: fa(icon), view: 'stacked', shape: 'circle', position: 'left', title_text: t, description_text: d, title_size: 'p',
        primary_color: C.green, secondary_color: C.white, icon_size: px(18), icon_padding: px(12), icon_space: px(14), text_align: 'left',
        title_color: C.green, description_color: C.muted, ...typo('title_typography', { size: 16, weight: 700 }), ...typo('description_typography', { size: 14.5, lh: 1.5 }),
      })),
    ]),
  ]),
  Section({ _title: 'SAV – formulaire', _element_id: SAV_FORM_ID, css_id: SAV_FORM_ID, background_background: 'classic', background_color: C.tint, flex_align_items: 'stretch' }, [
    SectionHead('Demande SAV', 'Décrivez-nous la situation', 'Ce sont les questions que nous posons au téléphone, pour vous proposer un matelas identique ou 100 % compatible. Répondez de votre chambre, à l\'heure qui vous arrange : « je ne sais pas » est une réponse acceptée partout.'),
    Con({ _title: 'Carte du formulaire', boxed_width: px(880), content_width: 'boxed', padding: box(36, 40, 36, 40), padding_mobile: box(22, 16, 22, 16), border_radius: box(20), background_background: 'classic', background_color: C.white, ...shadow(18, 40, -18) }, [
      SC('[lmdd_sav_formulaire]', 'Formulaire SAV en étapes'),
    ]),
  ]),
];

// ---------- Écriture ----------
const tpl = (title, type, content, page_settings = []) => ({ content, page_settings, version: '0.4', title, type });
writeFileSync(path.join(OUT, 'en-tete-la-maison-du-dos.json'), JSON.stringify(tpl('En-tête – La Maison du Dos', 'section', headerContent), null, 1));
writeFileSync(path.join(OUT, 'menu-principal-la-maison-du-dos.json'), JSON.stringify({ slug: MENU_SLUG, name: 'Menu principal – La Maison du Dos', items: MENU.map(([title, url, children, mega]) => ({ title, url, mega: mega || '', children: children.map(([t, u]) => ({ title: t, url: u })) })) }, null, 1));
writeFileSync(path.join(OUT, 'mega-menus-la-maison-du-dos.json'), JSON.stringify(MEGA_MENUS, null, 1));
writeFileSync(path.join(OUT, 'accueil-la-maison-du-dos.json'), JSON.stringify(tpl("Accueil – La Maison du Dos", 'page', pageContent, { template: 'elementor_header_footer', hide_title: 'yes' }), null, 1));
writeFileSync(path.join(OUT, 'fiche-produit-la-maison-du-dos.json'), JSON.stringify(tpl('Fiche produit – La Maison du Dos', 'section', productTemplate), null, 1));
writeFileSync(path.join(OUT, 'sav-la-maison-du-dos.json'), JSON.stringify(tpl('SAV lit à eau – La Maison du Dos', 'page', savPage, { template: 'elementor_header_footer', hide_title: 'yes' }), null, 1));
writeFileSync(path.join(OUT, 'pied-de-page-la-maison-du-dos.json'), JSON.stringify(tpl('Pied de page – La Maison du Dos', 'section', [footer]), null, 1));

const count = (els) => els.reduce((a, e) => a + 1 + count(e.elements || []), 0);
console.log('Accueil :', count(pageContent), 'éléments ; en-tête :', count(headerContent), 'éléments ; pied de page :', count([footer]), 'éléments ; fiche produit :', count(productTemplate), '; page SAV :', count(savPage));
