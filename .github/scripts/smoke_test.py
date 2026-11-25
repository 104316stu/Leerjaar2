"""Check the live site after a deploy.

Opens the home page, the page of each vak and folder (?vak=...), every project
they link to, the pages inside each project (one click deeper, same folder only)
and every file those pages load
(CSS, JS, images, audio, and url() inside the CSS).
Exits with 1 when something returns 4xx/5xx or a page shows a PHP error,
so the GitHub run fails and you get an email.

    python3 smoke_test.py https://104316.stu.sd-lab.nl
"""

import os
import re
import sys
import urllib.error
import urllib.request
from concurrent.futures import ThreadPoolExecutor
from html.parser import HTMLParser
from urllib.parse import quote, urldefrag, urljoin, urlparse

SITE = sys.argv[1].rstrip("/") + "/"
HOST = urlparse(SITE).netloc
UA = "leerjaar2-smoke-test"
MAX_PAGES = 80
PHP_ERROR = re.compile(r"<b>(Fatal error|Parse error)</b>|Fatal error: |Uncaught \w+Exception")
PHP_WARNING = re.compile(r"<b>(Warning|Notice|Deprecated)</b>:")
FILE_EXT = re.compile(r"\.(png|jpe?g|gif|webp|svg|ico|pdf|mp3|mp4|webm|zip|js|json|txt|md|docx|pptx|xlsx)$", re.I)
CSS_URL = re.compile(r"""url\(\s*['"]?([^'")]+)['"]?\s*\)""")
# Links that change something when opened are never followed. We are not logged in,
# but a check must never be able to delete anything or log anyone out.
UNSAFE = re.compile(r"verwijder|delete|remove|logout|uitlog|afmeld", re.I)


class Links(HTMLParser):
    """Collects page links (<a href>) and loaded files (src, stylesheets, icons)."""

    def __init__(self):
        super().__init__()
        self.pages, self.files = set(), set()

    def handle_starttag(self, tag, attrs):
        a = dict(attrs)
        if tag == "a" and a.get("href"):
            self.pages.add(a["href"])
        elif tag == "link" and a.get("href") and a.get("rel", "") in ("stylesheet", "icon"):
            self.files.add(a["href"])
        elif a.get("src") and tag in ("img", "script", "source", "audio", "video", "iframe"):
            self.files.add(a["src"])


def fetch(url, method="GET"):
    """Returns (status, body text or '', content type)."""
    req = urllib.request.Request(url, method=method, headers={"User-Agent": UA})
    try:
        with urllib.request.urlopen(req, timeout=25) as r:
            ctype = r.headers.get("Content-Type", "")
            body = r.read().decode("utf-8", "replace") if method == "GET" else ""
            return r.status, body, ctype
    except urllib.error.HTTPError as e:
        if method == "HEAD" and e.code in (403, 405, 501):
            return fetch(url, "GET")
        return e.code, "", ""
    except Exception as e:  # timeouts, DNS, connection reset
        print(f"  {url}: {e}")
        return 0, "", ""


def resolve(base, ref):
    """Absolute URL on this site, or None for anchors, mail links and other sites."""
    ref = ref.strip()
    if not ref or ref.startswith(("#", "%23", "mailto:", "tel:", "javascript:", "data:")):
        return None
    # encode spaces and other characters, but keep what is already encoded
    url = quote(urldefrag(urljoin(base, ref))[0], safe=":/?&=%+;,@!$'()*~#")
    p = urlparse(url)
    return url if p.scheme in ("http", "https") and p.netloc == HOST else None


def kind_of(url):
    path = urlparse(url).path
    if path.lower().endswith(".css"):
        return "css"
    return "file" if FILE_EXT.search(path) else "page"


def folder_of(url):
    return urlparse(url).path.rsplit("/", 1)[0] + "/"


errors, warnings, checked = [], [], set()


def check(item):
    """item: (url, kind, depth, scope). Returns the new items found on it."""
    url, kind, depth, scope = item
    status, body, ctype = fetch(url, "HEAD" if kind == "file" else "GET")
    print(f"{status} {url}")
    if status == 403:
        # usually a link to a folder without a page; worth fixing, not worth failing the deploy
        warnings.append(f"403 (geen toegang) {url}")
        return []
    if status == 0 or status >= 400:
        errors.append(f"{status or 'geen antwoord'} {url}")
        return []

    found = []
    if kind == "page" and "html" in ctype:
        if PHP_ERROR.search(body):
            errors.append(f"PHP-fout op {url}")
        elif PHP_WARNING.search(body):
            warnings.append(f"PHP-waarschuwing op {url}")
        parser = Links()
        parser.feed(body)
        for ref in parser.files:
            u = resolve(url, ref)
            if u:
                found.append((u, "css" if kind_of(u) == "css" else "file", depth, scope))
        # depth 0 = home page and the ?vak= pages, 1 = a project, 2 = a page inside it (not followed further)
        if depth < 2:
            for ref in parser.pages:
                u = resolve(url, ref)
                if not u or UNSAFE.search(u):
                    continue
                if depth == 0 and u.startswith(SITE + "?vak="):
                    found.append((u, "page", 0, "/"))
                elif depth == 0:
                    found.append((u, kind_of(u), 1, folder_of(u)))
                elif urlparse(u).path.startswith(scope):
                    found.append((u, kind_of(u), 2, scope))
    elif kind == "css":
        for ref in CSS_URL.findall(body):
            u = resolve(url, ref)
            if u:
                found.append((u, "file", depth, scope))
    return found


# Breadth first: the home page, then the projects, then pages and files inside them.
queue = [(SITE, "page", 0, "/")]
pages_seen = 0
with ThreadPoolExecutor(max_workers=8) as pool:
    while queue:
        batch = []
        for item in queue:
            if item[0] in checked:
                continue
            if item[1] == "page":
                if pages_seen >= MAX_PAGES:
                    continue
                pages_seen += 1
            checked.add(item[0])
            batch.append(item)
        queue = [x for result in pool.map(check, batch) for x in result]

lines = [f"**{len(checked)} adressen gecontroleerd** ({pages_seen} pagina's), "
         f"{len(errors)} fouten, {len(warnings)} waarschuwingen.", ""]
lines += [f"- ❌ {e}" for e in errors] + [f"- ⚠️ {w}" for w in warnings]
summary = os.environ.get("GITHUB_STEP_SUMMARY")
if summary:
    with open(summary, "a", encoding="utf-8") as f:
        f.write("\n".join(lines) + "\n")
for w in warnings:
    print(f"::warning::{w}")
for e in errors:
    print(f"::error::{e}")
print(lines[0])
sys.exit(1 if errors else 0)
