const webpack = require('webpack');
const path = require('path');

const BUILD_DIR = path.resolve(__dirname, './src/CmsBundle/Resources/public');
const APP_DIR = path.resolve(__dirname, './client');

module.exports = {
  mode: 'development',
  entry: {
    contentbrowser: [
      path.join(APP_DIR, '/ContentBrowser.jsx'),
    ]
  },
  output: {
    path: BUILD_DIR,
    filename: '[name].js',
    publicPath: '/dist/',
    sourceMapFilename: '[file].map',
    pathinfo: true,
  },
  devServer: {
    contentBase: 'web/',
    headers: {
      'Access-Control-Allow-Origin': 'localhost',
      'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, PATCH, OPTIONS',
      'Access-Control-Allow-Headers': 'X-Requested-With, content-type, Authorization'
    }
  },
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: JSON.stringify('development'),
        DEBUG: true,
      },
    }),
  ],
  stats: {
    colors: true,
  },
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader'
        }
      },
      {
        test: /\.css$/i,
        use: ['style-loader', 'css-loader'],
      },
    ],
  },
  resolve: {
    extensions: ['.jsx', '.js', '.scss'],
  },
};
