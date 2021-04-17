const mix = require('laravel-mix');

require('laravel-mix-imagemin');
require('laravel-mix-purgecss');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .options({
        extractVueStyles: true
    })
    .webpackConfig({
        resolve: {
            alias: {
                '#': path.resolve('resources/sass')
            }
        },
        devServer: {
            overlay: {
                warnings: true,
                errors: true
            }
        }
    })
    .js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .extract([
        'vue',
        'vue-router',
        'vuex',
        'bootstrap',
        'moment',
        'popper.js',
    ])
    .purgeCss()
    .sourceMaps();

if (mix.inProduction()) {
    mix.version();
}

// Mix.listen('configReady', (webpackConfig) => {
//     if (Mix.isUsing('hmr')) {
//         // Remove leading '/' from entry keys
//         webpackConfig.entry = Object.keys(webpackConfig.entry).reduce((entries, entry) => {
//             entries[entry.replace(/^\//, '')] = webpackConfig.entry[entry];
//             return entries;
//         }, {});
//
//         // Remove leading '/' from ExtractTextPlugin instances
//         webpackConfig.plugins.forEach((plugin) => {
//             if (plugin.constructor.name === 'ExtractTextPlugin') {
//                 plugin.filename = plugin.filename.replace(/^\//, '');
//             }
//         });
//     }
// });
