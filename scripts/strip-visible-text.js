import fs from "fs";
import glob from "glob";
import cheerio from "cheerio";

const FILES = [
  "public/**/*.html",
  "public/**/*.php",
  "src/**/*.php",
];

const SKIP_TAGS = new Set([
  "script",
  "style",
  "code",
  "pre",
  "noscript"
]);

function isInsideSkippedTag(elem) {
  let current = elem.parent;
  while (current) {
    if (
      current.tagName &&
      SKIP_TAGS.has(current.tagName.toLowerCase())
    ) {
      return true;
    }
    current = current.parent;
  }
  return false;
}

for (const pattern of FILES) {
  const files = glob.sync(pattern, { nodir: true });

  for (const file of files) {
    const html = fs.readFileSync(file, "utf8");
    const $ = cheerio.load(html, {
      decodeEntities: false,
      xmlMode: false
    });

    $("*").contents().each((_, node) => {
      if (node.type === "text" && !isInsideSkippedTag(node)) {
        node.data = "";
      }
    });

    fs.writeFileSync(file, $.html());
  }
}

console.log("✔ Visible HTML text stripped (CI-only)");