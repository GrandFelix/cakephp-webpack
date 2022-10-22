let path = require('path');
let fs = require('fs');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

let config = {};

try {
    config = require('./webpack.config.json');
} catch (e) {
    console.error(e.message);
    return;
}

module.exports = {
    entry: config.paths,
    output: {
        path: __dirname,
        filename: "./webroot/js/[name].js"
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                loader: "babel-loader"
            },
            {
                test: /\.scss/,
                use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader'],
            }
        ]
    },
    resolve: {
        extensions: ['.js'],
        alias: config.aliases,
    },
    plugins: [
        new MiniCssExtractPlugin({filename: "webroot/css/[name].css"}),
    ],
    devtool: 'source-map'
};
