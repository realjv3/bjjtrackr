require('dotenv').config()

const
    path = require('path'),
    { VueLoaderPlugin } = require('vue-loader'),
    VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin'),
    webpack = require("webpack");

module.exports = {
    devServer: {
        https: true,
        port: 9090,
        disableHostCheck: true,
    },
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader'
				}
			},
			{
				test: /\.vue$/,
				loader: 'vue-loader'
			},
            {
                test: /\.css$/,
                use: ["vue-style-loader", "css-loader"],
            },
            {
                test: /\.sass$/,
                use: [
                    'vue-style-loader',
                    'css-loader',
                    {
                        loader: 'sass-loader',
                        // Requires sass-loader@^7.0.0
                        options: {
                            implementation: require('sass'),
                            fiber: require('fibers'),
                            indentedSyntax: true // optional
                        },
                        // Requires sass-loader@^8.0.0
                        options: {
                            implementation: require('sass'),
                            sassOptions: {
                                fiber: require('fibers'),
                                indentedSyntax: true // optional
                            },
                        },
                    },
                ],
            },
		]
	},
	resolve: {
		alias: {
			'vue$': 'vue/dist/vue.esm.js',
            'components': path.resolve(__dirname, 'resources/js/components')
		},
		extensions: ['*', '.js', '.vue', '.json']
	},
    plugins: [
        new VueLoaderPlugin(),
        new VuetifyLoaderPlugin(),
        new webpack.DefinePlugin({
            API_KEY: JSON.stringify(process.env.API_KEY),
            STRIPE_PUB_KEY: JSON.stringify(process.env.STRIPE_PUB_KEY),
        }),
    ],
    entry: {
        signup: './resources/js/pages/signup.js',
        payment_method: './resources/js/pages/payment_method.js',
        welcome: './resources/js/pages/welcome.js',
        send_reset: './resources/js/pages/send-reset.js',
        reset: './resources/js/pages/reset.js',
        home: './resources/js/pages/home.js',
    },
    output: {
        filename: '[name].bundle.js',
        path: path.resolve(__dirname, 'public/dist'),
    },
    devtool: 'source-map',
};
