<?php

$inline_css_path = __DIR__ . '/../assets/css/inline.min.css';

if (is_readable($inline_css_path)) {
    echo '<script>document.documentElement.classList.replace("no-js", "js");</script>';
    echo '<style id="crimewiki-inline-css">';
    readfile($inline_css_path);
    echo '</style>';
} else {
    // Keep pages styled if the local build has not been run yet.
    echo '<link rel="stylesheet" href="/assets/css/selectric.css">';
    echo '<link rel="stylesheet" href="/assets/css/style.css">';
}
