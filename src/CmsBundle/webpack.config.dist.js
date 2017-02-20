const webpack = require('webpack');
const path = require('path');
const autoprefixer = require('autoprefixer');
// const CopyWebpackPlugin = require('copy-webpack-plugin');

const BUILD_DIR = path.resolve(__dirname, 'Resources/public/dist');
const APP_DIR = path.resolve(__dirname, 'Resources/xms/src');

module.exports = {
  debug: false,
  entry: [
    path.join(APP_DIR, '/index.jsx'),
  ],
  output: {
    path: BUILD_DIR,
    filename: 'bundle.js',
    publicPath: '/dist/',
  },
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: JSON.stringify('production'),
        DEBUG: false,
      },
    }),
    new webpack.optimize.DedupePlugin(),
    new webpack.optimize.UglifyJsPlugin(),
    new webpack.NoErrorsPlugin(),
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
      'window.jQuery': 'jquery',
      'window.Tether': 'tether',
    }),
  ],
  stats: {
    progress: true,
    colors: true,
  },
  module: {
    loaders: [
      {
        test: /\.jsx?/,
        include: [
          APP_DIR,
          /opifer-rcs/,
        ],
        exclude: [
          /opifer-rcs\/node_modules/,
        ],
        loader: 'babel',
      },
      { test: /\.json$/, loader: 'json' },
      { test: /\.scss$/, loaders: ['style', 'css', 'postcss', 'sass'] },
      { test: /\.css$/, loaders: ['style', 'css', 'postcss'] },
      { test: /\.(jpe?g|png|gif|svg)$/i, loader: 'file?name=img/[hash].[ext]' },
      { test: /fonts\/\*.svg$/, loader: 'url?limit=65000&mimetype=image/svg+xml&name=[name].[ext]' },
      { test: /\.woff$/, loader: 'url?limit=65000&mimetype=application/font-woff&name=[name].[ext]' },
      { test: /\.woff2$/, loader: 'url?limit=65000&mimetype=application/font-woff2&name=[name].[ext]' },
      { test: /\.[ot]tf$/, loader: 'url?limit=65000&mimetype=application/octet-stream&name=[name].[ext]' },
      { test: /\.eot$/, loader: 'url?limit=65000&mimetype=application/vnd.ms-fontobject&name=[name].[ext]' },
      { test: /tether\/dist\/js\/umd\//, loader: 'expose?Tether' },
      { test: /bootstrap\/dist\/js\/umd\//, loader: 'imports?jQuery=jquery' },
    ],
  },
  resolve: {
    extensions: ['', '.jsx', '.js', '.scss', '.json'],
  },
  postcss: [autoprefixer],
  node: {
    fs: 'empty',
  },
};
