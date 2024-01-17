const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .js('resources/js/admin/index.js', 'js/admin.js')
    .sass('resources/css/admin.sass', 'css/admin.css')
    .setPublicPath('public/assets')
    .options({
        processCssUrls: false,
        terser: {
            extractComments: false,
        }
    })
    .react()
    mix.webpackConfig({
        devtool: "inline-source-map"
    })
    .sourceMaps()
;
