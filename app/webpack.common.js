const { VueLoaderPlugin } = require('vue-loader')
const CopyPlugin = require('copy-webpack-plugin')
const HtmlPlugin = require('html-webpack-plugin')
const webpack = require('webpack')

module.exports = {
  entry: { app: ['./src/main.js'] },
  output: { publicPath: '/' },
  resolve: {
    extensions: ['.js', '.vue']
  },

  module: {
    rules: [
      { test: /\.vue$/, loader: 'vue-loader' },
      { test: /\.svg$/, type: 'asset/resource', generator: { filename: 'img/[name].[contenthash].[ext]' } }
    ]
  },

  plugins: [
    new webpack.DefinePlugin({
      __VUE_OPTIONS_API__: false,
      __VUE_PROD_DEVTOOLS__: false
    }),
    new VueLoaderPlugin(),
    new CopyPlugin({
      patterns: [
        { from: 'public', globOptions: { ignore: ['**/index.html'] } },
        { from: 'src/assets/images/arch(icon|logo).svg', to: 'img/[name][ext]' }
      ]
    }),
    new HtmlPlugin({
      template: 'public/index.html',
      title: process.env.npm_package_name
    })
  ]
}
