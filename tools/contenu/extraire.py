"""
Extrait le texte actuel des 7 pages de contenu de la-maison-du-dos.com (API REST publique de WordPress)
et l'enregistre, nettoyé et structuré, dans elementor/contenu/pages.json. build.mjs construit ensuite
les nouvelles mises en page avec ce texte, sans le réécrire.

Usage : python3 -I tools/contenu/extraire.py [dossier-cache]
  dossier-cache : dossier contenant déjà <slug>.json (réponses de l'API) ; sinon les pages sont téléchargées.
"""
import html as H
import json
import os
import re
import sys
import urllib.request

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from nettoyage import corps, propre, texte  # noqa: E402

SITE = 'https://la-maison-du-dos.com'
RACINE = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
SORTIE = os.path.join(RACINE, 'elementor', 'contenu', 'pages.json')
PAGES = {
    'qui-sommes-nous': 'qui-sommes-nous',
    'nos-marques': 'nos-marques',
    'faq': 'matelas-a-eau-faq-complete-35-reponses-dexperts',
    'livraison-paiement': 'livraison-paiement',
    'contact': 'contact',
    'mentions-legales': 'mentions-legales',
    'cgv': 'conditions-generales-de-ventes',
}


def charger(cle, slug, cache):
    if cache and os.path.exists(os.path.join(cache, cle + '.json')):
        with open(os.path.join(cache, cle + '.json'), encoding='utf-8') as f:
            return json.load(f)[0]
    url = SITE + '/wp-json/wp/v2/pages?slug=' + slug + '&_fields=id,slug,link,title,content'
    with urllib.request.urlopen(url, timeout=60) as r:
        return json.loads(r.read().decode('utf-8'))[0]


def ancre(t, vus):
    a = H.unescape(t).lower()
    for x, y in (('àâä', 'a'), ('éèêë', 'e'), ('îï', 'i'), ('ôö', 'o'), ('ùûü', 'u'), ('ç', 'c'), ('œ', 'oe')):
        for c in x:
            a = a.replace(c, y)
    a = re.sub(r'[^a-z0-9]+', '-', a).strip('-')[:48].strip('-') or 'section'
    base, n = a, 2
    while a in vus:
        a, n = '%s-%d' % (base, n), n + 1
    vus.add(a)
    return a


def sans_balise(h, balise):
    m = re.fullmatch(r'\s*<%s>(.*)</%s>\s*' % (balise, balise), h, re.S)
    return m.group(1) if m else h


# ------------------------------------------------------------------ Qui sommes-nous
def qui(p):
    w = [propre(h) for t, h in corps(p['content']['rendered']) if t == 'text-editor']
    h = ''.join(w)
    intro = re.search(r'<h2>(.*?)</h2>', h, re.S).group(1)
    paras = re.findall(r'<p>(.*?)</p>', h, re.S)
    etapes, raisons, raisons_intro = [], [], ''
    for x in paras:
        t = texte(x)
        if t.startswith('Nous livrons'):
            raisons_intro = x
        elif re.match(r'^\d\.', t):
            raisons.append(re.sub(r'^\s*\d\.[,\s-]*', '', x))
        else:
            annee = re.search(r'\b(19[89]\d|20[0-3]\d)\b', t).group(1)
            # Année de la frise : la première citée, sauf « … en 2005 » (dernière phrase du paragraphe).
            if '2005' in t and 'gamme' in t:
                annee = '2005'
            etapes.append({'annee': annee, 'html': x})
    etapes.sort(key=lambda e: e['annee'])
    return {'titre': 'Qui sommes-nous?', 'intro': intro, 'etapes': etapes, 'raisons_intro': raisons_intro, 'raisons': raisons}


# ------------------------------------------------------------------ Nos marques
def marques(p):
    c = p['content']['rendered']
    w = corps(c)
    titres = [texte(h) for t, h in w if t == 'heading']
    textes = [propre(h) for t, h in w if t == 'text-editor']
    liste = next(h for t, h in w if t == 'shortcode')
    noms = [{'nom': H.unescape(re.sub('<[^>]+>', '', n)).strip(), 'url': re.sub(r'https?://[^/]+', '', u)}
            for u, n in re.findall(r'<a href="([^"]+)"[^>]*>(.*?)</a>', liste)]
    galeries = [re.findall(r'src="[^"]*/wp-content/uploads/([^"]+)"', h) for t, h in w if t == 'image-gallery']
    return {'titre': titres[0], 'sous_titre': titres[1], 'texte': textes[0], 'marques': noms,
            'logos': galeries[0], 'anciennes': sans_balise(textes[1], 'p'), 'anciens_logos': galeries[1]}


# ------------------------------------------------------------------ FAQ
def faq(p):
    c = p['content']['rendered']
    tete = corps(c.split('<details')[0])
    titre = texte(next(h for t, h in tete if t == 'heading'))
    bouton = next(h for t, h in tete if t == 'button')
    items = []
    for d in c.split('<details')[1:]:
        q = texte(re.search(r'<summary.*?</summary>', d, re.S).group(0))
        rep = ''.join(propre(h) for t, h in corps(d) if t == 'text-editor')
        # Titre h3 dans la première réponse : gardé, en paragraphe en gras.
        rep = re.sub(r'<h\d>(.*?)</h\d>', r'<p><strong>\1</strong></p>', rep)
        rep = rep.replace('<strong><strong>', '<strong>').replace('</strong></strong>', '</strong>')
        items.append({'q': q, 'r': rep})
    t, intro = titre.split(' - ', 1) if ' - ' in titre else (titre, '')
    return {'titre': t.strip(), 'intro': intro.strip(), 'retour': {'texte': texte(bouton), 'url': re.search(r'href="([^"]+)"', propre(bouton)).group(1)}, 'questions': items}


# ------------------------------------------------------------------ Livraison / Paiement
def livraison(p):
    c = p['content']['rendered']
    w = corps(c)
    tableaux, legendes = [], []
    for t, h in w:
        if t == 'text-editor' and 'ressortissants' in h:
            legendes.append(sans_balise(propre(h), 'p'))
    for tb in re.findall(r'<table.*?</table>', c, re.S):
        lignes = []
        for tr in re.findall(r'<tr.*?</tr>', tb, re.S):
            lignes.append([texte(x[1]) for x in re.findall(r'<t([hd])[^>]*>(.*?)</t\1>', tr, re.S)])
        tableaux.append({'entetes': lignes[0], 'lignes': lignes[1:]})
    for i, l in enumerate(legendes):
        tableaux[i]['legende'] = l
    liv = propre(next(h for t, h in w if t == 'text-editor' and 'Livraisons' in h))
    blocs, cur = [], None
    for m in re.finditer(r'<p>(.*?)</p>', liv, re.S):
        x = m.group(1)
        if re.fullmatch(r'<strong>\d\.-.*?</strong>', x.strip()):
            cur = {'titre': texte(x), 'html': ''}
            blocs.append(cur)
        elif cur is not None:
            cur['html'] += '<p>' + x + '</p>'
    titres = [texte(h) for t, h in w if t == 'heading']
    return {'titre': titres[0], 'paiements': titres[1], 'tableaux': tableaux, 'livraisons': 'Livraisons', 'zones': blocs}


# ------------------------------------------------------------------ Contact
def contact(p):
    w = corps(p['content']['rendered'])
    t = texte(''.join(h for x, h in w if x == 'text-editor'))
    tel = re.search(r'Numéro de téléphone (0[\d ]+\d)', t).group(1)
    mob = re.search(r'Mobile/WhatsApp (0[\d ]+\d)', t).group(1)
    mail = re.search(r'Email (\S+@\S+)', t).group(1)
    titres = [texte(h) for x, h in w if x == 'heading']
    return {'titre': titres[0], 'tel_libelle': 'Numéro de téléphone', 'tel': tel, 'mobile_libelle': 'Mobile/WhatsApp', 'mobile': mob,
            'email_libelle': 'Email', 'email': mail, 'message': titres[1]}


# ------------------------------------------------------------------ Mentions légales
def mentions(p):
    w = corps(p['content']['rendered'])
    titre = texte(w[0][1])
    intro, sections, vus = '', [], set()
    for t, h in w[2:]:
        if t == 'heading':
            tt = texte(h)
            sections.append({'titre': tt, 'id': ancre(tt.rstrip(' :'), vus), 'html': ''})
        elif sections:
            sections[-1]['html'] += propre(h)
        else:
            intro += propre(h)
    for s in sections:
        if not s['html'].lstrip().startswith('<'):
            s['html'] = '<p>' + s['html'] + '</p>'
        if s['html'].count('<div>') != s['html'].count('</div>'):
            s['html'] += '</div>' * (s['html'].count('<div>') - s['html'].count('</div>'))
    return {'titre': titre, 'intro': intro, 'sections': sections}


# ------------------------------------------------------------------ CGV
# Plan d'origine de la page (sommaire en haut des CGV) : ces intertitres deviennent les titres principaux.
CGV_H2 = {
    'Société', 'Propriété Intellectuelle', 'Statut des liens et des contenus', 'Publicités sur les sites', 'Informatique et Libertés',
    'Hébergement', 'Editeur du site', 'Service clients', 'Contact', 'Protection des données du site www.la-maison-du-dos.com',
    'CGV du site www.la-maison-du-dos.com', 'Préambule', 'Définitions', 'Objet', 'Produits', 'Prix', 'Commande et Paiement', 'Livraison',
    'Droit de rétractation', 'Droit de résolution', 'Exécution de la commande', 'Garanties', 'Eco-Participation', 'Informations Nominatives',
    'Conservation et archivage des documents', 'Intégralité du contrat', 'Réserve de propriété', 'Loi applicable', 'Module de gestion des cookies.',
}
CGV_H3 = {
    'Finalités :', 'Le responsable de traitement', 'Formalités CNIL', 'Destinataires des données à caractère personnel', 'Administration des sites',
    'Statut des liens', 'Collecte et usage des données fournies volontairement', 'Protection des données des mineurs', 'Libre choix du visiteur',
    'Confidentialité et sécurité', 'Auto-évaluation', 'Droit d’accès, de rectification, de suppression', 'Notification des changements',
    'Paiement en plusieurs fois avec Alma.', 'Le franco de port', 'Délai de livraison', 'Mondial Relay', 'Colissimo', 'Par transporteur :',
    'Par nos soins :', 'Livraison incomplète ou non-conforme (du fait du transporteur)', 'Livraison incomplète ou non conforme (du fait de la société)',
    'Colis perdu', 'Effets de la rétractation', 'Exceptions au droit de rétractation', 'Garantie légale', 'Garanties contractuelles',
    'Garanties spécifiques sur les soudures des matelas à eau de nos lits à eau :', 'Garantie 10 ans sur les soudures avec dégressivité :',
    'Garanties spécifiques sur l’étanchéité des matelas à suspension(s) à air:',
}
PARTIES = ('Protection des données du site www.la-maison-du-dos.com', 'CGV du site www.la-maison-du-dos.com')


def cgv(p):
    w = corps(p['content']['rendered'])
    titre = texte(w[0][1])
    h = ''.join(propre(x) for t, x in w[1:] if t == 'text-editor')
    # Le sommaire d'origine (listes de liens morts vers l'ancien site) est remplacé par un sommaire cliquable.
    h = h[h.index('Notre site www.la-maison-du-dos.com') - 200:]
    h = h[h.index('<div>'):] if '<div>' in h[:220] else h
    vus, plan = set(), []

    def titre_de(m):
        bal, t = m.group(1), H.unescape(re.sub('<[^>]+>', '', m.group(2))).strip()
        niveau = 2 if t in CGV_H2 else 3 if t in CGV_H3 else 0
        if not niveau:
            return m.group(0)
        if t == 'Société' and any(x['titre'] == 'Société' for x in plan):
            niveau = 2
        i = ancre(t.rstrip(' :.'), vus)
        plan.append({'titre': t, 'id': i, 'niveau': niveau, 'partie': t in PARTIES})
        return '<h%d id="%s">%s</h%d><%s>' % (niveau, i, m.group(2).strip(), niveau, bal)
    h = re.sub(r'<(div|p)>\s*<strong>(.*?)</strong>\s*(?:<br\s*/?>)?', titre_de, h)
    h = re.sub(r'<(p|div)>\s*(?:<br\s*/?>\s*)*</\1>', '', h)
    h = re.sub(r'<(p|div)>\s*<br\s*/?>', r'<\1>', h)
    return {'titre': titre, 'html': h, 'plan': plan}


def main():
    cache = sys.argv[1] if len(sys.argv) > 1 else ''
    f = {'qui-sommes-nous': qui, 'nos-marques': marques, 'faq': faq, 'livraison-paiement': livraison, 'contact': contact,
         'mentions-legales': mentions, 'cgv': cgv}
    out = {'_source': SITE, '_note': 'Texte extrait des pages actuelles (tools/contenu/extraire.py) : ne pas modifier à la main.'}
    for cle, slug in PAGES.items():
        p = charger(cle, slug, cache)
        d = f[cle](p)
        d['slug'] = p['slug']
        out[cle] = d
    with open(SORTIE, 'w', encoding='utf-8') as fh:
        json.dump(out, fh, ensure_ascii=False, indent=1)
    print('Écrit :', SORTIE)


if __name__ == '__main__':
    main()
