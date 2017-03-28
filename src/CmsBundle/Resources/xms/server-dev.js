const webpack = require('webpack');
const WebpackDevServer = require('webpack-dev-server');
const config = require('./webpack.config');

new WebpackDevServer(
  webpack(config),
  {
    debug: config.debug,
    devtool: config.devtool,
    publicPath: config.output.publicPath,
    contentBase: config.devServer.contentBase,
    hot: true,
    quiet: false,
    noInfo: false,
    historyApiFallback: true,
    stats: {
      progress: true,
      colors: true,
    },
  }
).listen(
  3333,
  '0.0.0.0',
  (err) => {
    if (err) {
      console.log(err);
    }
    console.log('Listening at 0.0.0.0:3333');
  }
);
