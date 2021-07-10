const mix = require('laravel-mix');

mix
    .postCss("resources/css/app.css", "public/vendor/eduka-nereus/css", [
        require("tailwindcss"),
    ]);