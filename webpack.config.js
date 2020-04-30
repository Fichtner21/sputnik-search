var path = require("path");
var webpack = require("webpack");

module.exports = {
  devtool: "eval",
  entry: {
    app: path.resolve(__dirname, "react/main.js"),
  },
  resolve: {
    extensions: [".js", ".jsx", ".scss"],
  },
  module: {
    loaders: [
      {
        test: /\.less$/,
        exclude: /node_modules/,
        loader: "style!css!less",
      },
      {
        test: /\.scss$/,
        exclude: /node_modules/,
        loaders: [
          "style-loader",
          "css-loader",
          "postcss-loader",
          "sass-loader",
        ],
      },
      {
        test: /\.css$/,
        loader: "style-loader!css-loader",
      },
      {
        test: /\.jsx?$/,
        loader: "babel-loader",
        exclude: /node_modules/,
        query: {
          presets: ["es2015", "stage-0", "react"],
        },
      },
      {
        test: /\.(png|woff|woff2|eot|ttf|svg)$/,
        exclude: /node_modules/,
        loader: "url-loader?limit=100000",
      },
      {
        test: /\.json$/,
        loader: "json",
      },
      {
        test: /\.(ttf|ijmap|eot|svg|cur|gif|png|woff|jpg)(.*)$/,
        loader: "file-loader",
      },
    ],
  },
  output: {
    path: path.resolve(__dirname, "react"),
    filename: "sputnik-wordpress-search.build.js",
  },
};
