const
    mix = require('laravel-mix'),
    VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin')

mix.webpackConfig({
    resolve: {
        alias: {
            '~': __dirname,
            '@bo': path.resolve(__dirname, 'resources', 'assets', 'back'),
            '@fo': path.resolve(__dirname, 'resources', 'assets', 'front'),
        },
    },
    plugins: [
        new VuetifyLoaderPlugin()
    ],
    module: {},
});

mix
// Сборка back
    .js('resources/assets/back/app.js', 'public/back/js')
    .sass('resources/assets/back/sass/app.scss', 'public/back/css')
    .sourceMaps(false, 'inline-source-map')
    .copyDirectory('resources/assets/back/img', 'public/back/img')

mix.version()

if (process.env.NODE_ENV === 'development' && process.env.BROWSER_SYNC_PORT) {
    mix.browserSync({
        proxy: 'http://192.168.31.144:' + process.env.BROWSER_SYNC_PORT,
        port: Number(process.env.BROWSER_SYNC_PORT) + 1,
    })
}
