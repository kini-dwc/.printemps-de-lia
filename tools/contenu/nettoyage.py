"""Nettoyage du HTML rendu par Elementor : on garde le texte et les liens, on enlève l'habillage."""
import html as H
import re

SITE = re.compile(r'https?://(?:www\.|prod\.)?la-maison-du-dos\.com')
LIENS_MORTS = ('conditions.php', 'mentions_legales.php', '_wp_link_placeholder', 'superadmin/')


def corps(rendered):
    """Contenu des widgets « éditeur de texte » et « titre », dans l'ordre (sans les conteneurs Elementor)."""
    out = []
    for m in re.finditer(r'data-widget_type="([a-z-]+)\.default"[^>]*>\s*<div class="elementor-widget-container">(.*?)</div>\s*</div>(?=\s*(?:<div class="elementor-element|</div>|<section|</section|$))', rendered, re.S):
        out.append((m.group(1), m.group(2)))
    return out


def propre(h):
    h = re.sub(r'<(script|style)[^>]*>.*?</\1>', '', h, flags=re.S)
    h = re.sub(r'<img[^>]*class="[^"]*emoji[^"]*"[^>]*alt="([^"]*)"[^>]*>', r'\1', h)
    h = re.sub(r'<img[^>]*alt="([^"]*)"[^>]*s\.w\.org/images/core/emoji[^>]*>', r'\1', h)
    h = re.sub(r'<img[^>]*s\.w\.org/images/core/emoji[^>]*alt="([^"]*)"[^>]*>', r'\1', h)
    # Liens : adresses du site en relatif, liens morts retirés (le texte reste).
    def lien(m):
        href = re.search(r'href="([^"]*)"', m.group(1))
        href = H.unescape(href.group(1)) if href else ''
        if not href or any(x in href for x in LIENS_MORTS):
            return '<a>'
        href = SITE.sub('', href) or '/'
        ext = ' target="_blank" rel="noopener"' if href.startswith('http') else ''
        return '<a href="%s"%s>' % (H.escape(href, quote=True), ext)
    h = re.sub(r'<a\b([^>]*)>', lien, h)
    h = re.sub(r'<a>(.*?)</a>', r'\1', h, flags=re.S)
    # Lettres écrites en blanc (protection anti-robots des coordonnées) : gardées, mais masquées quel que soit le fond.
    h = re.sub(r'<span style="[^"]*color:\s*(?:#fff\b|#ffffff\b|white\b)[^"]*">(.*?)</span>', r'<span class="lmdd-masque">\1</span>', h, flags=re.S | re.I)
    h = h.replace('<span class="lmdd-masque">', '\x00M')
    # Attributs de mise en forme retirés.
    h = re.sub(r'\s(?:style|class|id|data-[\w-]+|title|role|dir|aria-[\w-]+)="[^"]*"', '', h)
    h = re.sub(r'<span>(.*?)</span>', r'\1', h, flags=re.S)
    h = re.sub(r'<span>(.*?)</span>', r'\1', h, flags=re.S)
    h = re.sub(r'<(/?)b>', r'<\1strong>', h)
    h = h.replace('\x00M', '<span aria-hidden="true" style="color:transparent">')
    h = h.replace('&nbsp;', ' ').replace(' ', ' ')
    h = re.sub(r'[ \t\r\n]+', ' ', h)
    h = re.sub(r'<(p|div|li|h\d)>\s+', r'<\1>', h)
    h = re.sub(r'\s+</(p|div|li|h\d)>', r'</\1>', h)
    h = re.sub(r'<strong>\s*</strong>', '', h)
    h = re.sub(r'<(p|div)>\s*(?:<br\s*/?>\s*)*</\1>', '', h)
    return h.strip()


def texte(h):
    return re.sub(r'\s+', ' ', H.unescape(re.sub(r'<[^>]+>', ' ', h))).strip()
