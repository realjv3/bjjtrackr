const
    path = require('path'),
    { VueLoaderPlugin } = require('vue-loader');

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
			}
		]
	},
	resolve: {
		alias: {
			'vue$': 'vue/dist/vue.esm.js'
		},
		extensions: ['*', '.js', '.vue', '.json']
	},
    plugins: [
        new VueLoaderPlugin()
    ],
    entry: {
        app: './resources/js/app.js',
    },
    output: {
        filename: '[name].bundle.js',
        path: path.resolve(__dirname, 'public/dist'),
    }
};
