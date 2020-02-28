const webpack = require('webpack');
const path = require('path');

const BUILD_DIR = path.resolve(__dirname, './src/CmsBundle/Resources/public/js');
const APP_DIR = path.resolve(__dirname, './client');

module.exports = {
  mode: 'production',
  entry: {
    contentbrowser: [
      path.join(APP_DIR, '/ContentBrowser.jsx'),
    ]
  },
  output: {
    path: BUILD_DIR,
    filename: '[name].js',
  },
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: JSON.stringify('production'),
      },
    }),
  ],
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
