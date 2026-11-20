"""List the projects that need a new cover screenshot.

Reads the live home page. Each project that can get an automatic cover has
data-shot="<slug>" and data-url="<link>"; data-has-shot means one exists already.
Prints "<slug>\t<absolute url>" per project: the ones without a cover,
or all of them when the second argument is "true" (full run).

    python3 covers.py https://104316.stu.sd-lab.nl false
"""

import sys
import urllib.request
from html.parser import HTMLParser
from urllib.parse import urljoin

site = sys.argv[1].rstrip("/") + "/"
redo_all = len(sys.argv) > 2 and sys.argv[2] == "true"


class Shots(HTMLParser):
    def __init__(self):
        super().__init__()
        self.items = []

    def handle_starttag(self, tag, attrs):
        a = dict(attrs)
        if "data-shot" in a and a.get("data-url"):
            self.items.append((a["data-shot"], a["data-url"], "data-has-shot" in a))


req = urllib.request.Request(site, headers={"User-Agent": "leerjaar2-covers"})
with urllib.request.urlopen(req, timeout=25) as r:
    html = r.read().decode("utf-8", "replace")

parser = Shots()
parser.feed(html)
for slug, url, has in parser.items:
    if redo_all or not has:
        print(f"{slug}\t{urljoin(site, url)}")
