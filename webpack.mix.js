let mix = require('laravel-mix');
const webpack = require('webpack');
mix.webpackConfig({
    plugins: [
      new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/)
    ]
});
mix.js('resources/assets/js/app.js', 'public/vuejs');

mix.js('resources/tripclues/accounts.js', 'public/js');