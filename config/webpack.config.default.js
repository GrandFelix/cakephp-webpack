const ExtractTextPlugin = require("extract-text-webpack-plugin");
let path = require('path');
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
                use: ExtractTextPlugin.extract('css-loader!sass-loader')
            }
        ]
    },
    resolve: {
        extensions: ['.js'],
        alias: config.aliases,
    },
    plugins: [
        new ExtractTextPlugin({ filename: "webroot/css/[name].css", allChunks: false }),
    ],
    devtool: 'source-map'
};
