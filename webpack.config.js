const Encore = require('@symfony/webpack-encore')
const CompressionPlugin = require('compression-webpack-plugin')
const path = require('path')

Encore
  .setOutputPath((process.env.PUBLIC_PATH || 'public') + '/build')
  .setPublicPath('/build')
  .addAliases({ '@': path.resolve(__dirname, 'assets') })
  .addAliases({ jquery: 'jquery/dist/jquery.slim' })
  .addEntry('base', './assets/js/base.js')
  .addEntry('index', './assets/js/index.js')
  .copyFiles({ from: 'assets/images', to: 'images/[path][name].[hash:8].[ext]' })
  .splitEntryChunks()
  .enableSingleRuntimeChunk()
  .enableSassLoader()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
  .enablePostCssLoader()
  .configureBabel(() => {}, { useBuiltIns: 'usage', corejs: 3 })

if (Encore.isProduction()) {
  Encore.addPlugin(new CompressionPlugin({ filename: '[path].gz[query]', algorithm: 'gzip' }))
  Encore.addPlugin(new CompressionPlugin({ filename: '[path].br[query]', algorithm: 'brotliCompress' }))
} else {
  Encore.cleanupOutputBeforeBuild()
}

module.exports = Encore.getWebpackConfig()
