const CompressionPlugin = require('compression-webpack-plugin')
const CopyWebpackPlugin = require('copy-webpack-plugin')
const WorkboxPlugin = require('workbox-webpack-plugin')

module.exports = {
  lintOnSave: false,
  productionSourceMap: false,
  devServer: {
    disableHostCheck: true
  },
  configureWebpack: config => {
    config.plugins.push(new CopyWebpackPlugin({
      patterns: [
        { from: 'src/assets/images/arch(icon|logo).svg', to: 'img/[name].[ext]' }
      ]
    }))

    config.plugins.push(new WorkboxPlugin.GenerateSW({
      cacheId: 'app',
      exclude: [/robots\.txt$/],
      cleanupOutdatedCaches: true,
      dontCacheBustURLsMatching: /\.[a-f0-9]+\./,
      navigateFallback: '/index.html',
      navigateFallbackAllowlist: [
        new RegExp('^/$')
      ],
      runtimeCaching: [
        {
          urlPattern: new RegExp('^https?://[^/]+/api/'),
          handler: 'StaleWhileRevalidate',
          options: { cacheName: 'api', expiration: { maxAgeSeconds: 24 * 60 * 60 } }
        }
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
