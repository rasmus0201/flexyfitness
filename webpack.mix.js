let mix = require('laravel-mix');

mix.js('resources/assets/js/app.js', 'public/assets/js/')
    .sass('resources/assets/scss/app.scss', 'public/assets/css/')
    .version();
