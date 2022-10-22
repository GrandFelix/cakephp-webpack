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

module.exports = (env) => {
    return {
        entry: config.paths,
        output: {
            path: __dirname + '/webroot/',
            filename: "./js/[name].js"
        }
        ,
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    loader: "babel-loader"
                },
                {
                    test: /\.scss/,
                    use: [MiniCssExtractPlugin.loader, 'css-loader'],
                }
            ]
        }
        ,
        resolve: {
            extensions: ['.js'],
            alias: config.aliases,
        },
        plugins: [
            new MiniCssExtractPlugin({filename: "css/[name].css"}),
        ],
        mode: env.production ? 'production' : 'development',
        devtool: env.production ? false : 'source-map'
    }
};
