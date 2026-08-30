'use strict';

const fs = require('fs');
const path = require('path');
const postcss = require('postcss');
const cssnano = require('cssnano');

const projectRoot = path.resolve(__dirname, '..');
const stylePath = path.join(projectRoot, 'assets', 'css', 'style.css');
const selectricPath = path.join(projectRoot, 'assets', 'css', 'selectric.css');
const outputPath = path.join(projectRoot, 'assets', 'css', 'inline.min.css');

function removeEmbeddedSourceMaps(css) {
  return css.replace(/\/\*# sourceMappingURL=data:[\s\S]*?\*\//g, '');
}

function makeInlineAssetPathsAbsolute(css) {
  return css.replace(/url\((["']?)\.\.\/icons\/([^"')]+)\1\)/g, 'url($1/assets/icons/$2$1)');
}

async function build() {
  const selectricCss = fs.readFileSync(selectricPath, 'utf8');
  const styleCss = makeInlineAssetPathsAbsolute(
    removeEmbeddedSourceMaps(fs.readFileSync(stylePath, 'utf8'))
  );
  const source = `${selectricCss}\n${styleCss}`;
  const result = await postcss([cssnano({ preset: 'default' })]).process(source, {
    from: undefined,
  });

  fs.writeFileSync(outputPath, `${result.css}\n`);
  console.log(`Wrote ${path.relative(projectRoot, outputPath)} (${result.css.length} bytes)`);
}

build().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});
