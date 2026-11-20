"""Write cache/build.json: which commit is live, when, and when each project last changed.

The home page shows this ("Laatste deploy ...", "Bijgewerkt ...", the "nieuw" label).

    python3 build_info.py <linked.tsv> cache/build.json

linked.tsv has one line per linked repo: folder, url, commit, commit date.
Other project folders get the date of the last commit in this repo that touched them.
Run from the repo root, with full git history (actions/checkout fetch-depth: 0).
"""

import json
import os
import subprocess
import sys
from datetime import datetime, timezone

linked_file, out_file = sys.argv[1], sys.argv[2]
MARKERS = ("index.php", "index.html", "project.json")


def git(*args):
    return subprocess.run(["git", *args], capture_output=True, text=True, check=True).stdout


folders, repos = {}, {}
if os.path.exists(linked_file):
    with open(linked_file, encoding="utf-8") as f:
        for line in f:
            parts = line.rstrip("\n").split("\t")
            if len(parts) == 4:
                folder, url, sha, date = parts
                folders[folder] = date
                repos[url] = sha

for path in git("ls-files", "-z").split("\0"):
    folder, _, name = path.rpartition("/")
    if folder and name in MARKERS and folder not in folders:
        # changes to project.json alone (title, description) do not count as work on the project
        date = git("log", "-1", "--format=%cI", "--", folder, f":(exclude){folder}/project.json").strip()
        date = date or git("log", "-1", "--format=%cI", "--", folder).strip()
        if date:
            folders[folder] = date

build = {
    "sha": os.environ.get("GITHUB_SHA") or git("rev-parse", "HEAD").strip(),
    "time": datetime.now(timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ"),
    "event": os.environ.get("GITHUB_EVENT_NAME", "local"),
    "folders": dict(sorted(folders.items())),
    "repos": repos,
}
os.makedirs(os.path.dirname(out_file) or ".", exist_ok=True)
with open(out_file, "w", encoding="utf-8") as f:
    json.dump(build, f, indent=2)
print(json.dumps(build, indent=2))
