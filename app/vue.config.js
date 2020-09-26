const CompressionPlugin = require('compression-webpack-plugin')
const CopyWebpackPlugin = require('copy-webpack-plugin')

module.exports = {
  lintOnSave: false,
  productionSourceMap: false,
  devServer: {
    disableHostCheck: true
  },
  configureWebpack: config => {
    if (!process.env.VUE_CLI_MODERN_BUILD) {
      config.entry.app.unshift('whatwg-fetch')
      config.entry.app.unshift('intersection-observer')
    }

    config.plugins.push(new CopyWebpackPlugin({
      patterns: [
        { from: 'src/assets/images/arch(icon|logo).svg', to: 'img/[name].[ext]' }
      ]
    }))

    if (process.env.NODE_ENV === 'production') {
      config.plugins.push(new CompressionPlugin({ filename: '[path][base].gz', algorithm: 'gzip' }))
      config.plugins.push(new CompressionPlugin({ filename: '[path][base].br', algorithm: 'brotliCompress' }))
    }
  },
  chainWebpack: config => {
    config.resolve.alias.set('bootstrap-vue$', 'bootstrap-vue/src/index.js')
  },
  transpileDependencies: ['bootstrap-vue']
}
