const
    path = require('path'),
    { VueLoaderPlugin } = require('vue-loader'),
    VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin');

module.exports = {
    devServer: {
        https: true,
        port: 9000,
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
                test: /\.s(c|a)ss$/,
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
    ],
    entry: {
        welcome: './resources/js/pages/welcome.js',
        home: './resources/js/pages/home.js',
    },
    output: {
        filename: '[name].bundle.js',
        path: path.resolve(__dirname, 'public/dist'),
    },
    devtool: 'source-map',
};
