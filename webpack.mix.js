const mix = require('laravel-mix');
require('laravel-mix-purgecss');
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

// Sass
mix.sass('resources/sass/app.scss', 'public/css').purgeCss().sourceMaps();

// JS
mix.js('resources/js/app.js', 'public/js').sourceMaps();

mix.version();

if (!mix.inProduction()) {
	// Browsersync
	mix.browserSync('api.loadorderlibrary.localhost');
}