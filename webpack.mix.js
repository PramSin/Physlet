const mix = require('laravel-mix');
const CompressionPlugin = require('compression-webpack-plugin');
const zopfli = require('@gfx/zopfli');

require('laravel-mix-polyfill');
require('laravel-mix-bundle-analyzer');

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

mix.disableSuccessNotifications();

mix
    .options({
        processCssUrls: true,
        purifyCss: false,
        imgLoaderOptions: {
            enabled: false
        },
        uglify: {
            uglifyOptions: {
                compress: {
                    warnings: false
                },
                mangle: {
                    safari10: true
                },
                parallel: true
            }
        }
    })
    .polyfill({
        enabled: true,
        useBuiltIns: "usage",
        targets: "iOS 7"
    })
    .styles(['resources/css/bootstrap.css'], 'public/css/bootstrap.css')
    .js('resources/js/main.js', 'public/js')
    .extract([
        '@fortawesome/free-solid-svg-icons',
        'core-js',
        'lodash',
        'vue',
        'vue-router',
        'vue-cookies',
        'axios',
        'vuex',
        'element-ui',

        'prosemirror-view',
        'prosemirror-model',
        'prosemirror-tables',
        'prosemirror-transform',
        'prosemirror-state',
        'prosemirror-commands',
        'prosemirror-history',
        'prosemirror-schema-list',
        'prosemirror-inputrules',
        'prosemirror-gapcursor',
        'prosemirror-collab',
        'prosemirror-dropcursor',
        'prosemirror-keymap',

        'tiptap',
        'tiptap-commands',
        'tiptap-extensions',
    ]);

if (mix.inProduction()) {
    mix.sourceMaps(false)
        .webpackConfig({
            plugins: [
                new CompressionPlugin({
                    //algorithm: 'gzip',
                    algorithm(input, compressionOptions, callback) {
                        return zopfli.gzip(input, compressionOptions, callback);
                    },
                    compressionOptions: {
                        numiterations: 15,
                    },
                    test: /\.js$|\.css$|\.html$|\.svg$/,
                    threshold: 8192,
                }),
            ],
            externals: {
                'font-awesome':'fontAwesome',
            },
        })
        .version();
} else {
    // mix.bundleAnalyzer();
}
