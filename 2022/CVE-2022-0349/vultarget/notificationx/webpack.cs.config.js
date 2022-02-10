const path = require("path");
const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const MiniCSSExtractPlugin = require("mini-css-extract-plugin");

const isProduction = process.env.NODE_ENV === "production";

const plugins = defaultConfig.plugins.filter(
    (plugin) =>
        plugin.constructor.name != "MiniCssExtractPlugin" &&
        plugin.constructor.name != "CleanWebpackPlugin"
);

const config = {
    ...defaultConfig,
    entry: {
        crossSite: path.resolve(
            __dirname,
            "nxdev/notificationx/frontend/crossSite.tsx"
        ),
    },
    module: {
        ...defaultConfig.module,
        rules: [
            ...defaultConfig.module.rules,
            {
                test: /\.tsx?$/,
                use: "ts-loader",
                exclude: /node_modules/,
            },
        ],
    },
    resolve: {
        ...defaultConfig.resolve,
        extensions: [".tsx", ".ts", ".js", ".jsx"],
    },
    output: {
        ...defaultConfig.output,
        filename: (pathData) => {
            // if (!isProduction) {
            //     return "[name].js";
            // }
            return pathData.chunk.name == "admin"
                ? "admin/js/[name].js"
                : "public/js/[name].js";
        },
        path: path.resolve(process.cwd(), isProduction ? "assets" : "build"),
    },
    plugins: [
        new CleanWebpackPlugin({
            // dry: true,
            cleanOnceBeforeBuildPatterns: [
                "public/css/crossSite.css",
                "public/css/crossSite.css.map",
                "public/js/crossSite.js",
                "public/js/crossSite.js.map",
                "public/js/crossSite.asset.php",
            ],
        }),
        new MiniCSSExtractPlugin({
            filename: ({ chunk }) => {
                // if (!isProduction) {
                //     return `${chunk.name}.css`;
                // }
                return chunk.name == "admin"
                    ? `admin/css/${chunk.name}.css`
                    : `public/css/${chunk.name}.css`;
            },
        }),
        ...plugins,
    ],
    // externalsType: 'script',
    externals: [
        function ({ context, request, contextInfo }, callback) {
            const ext = {
                // lodash                                 : '_',
                moment                                 : 'moment',
                react                                  : 'React',
                'react-dom'                            : 'ReactDOM',
                'moment-timezone/moment-timezone'      : 'moment',
                'moment-timezone/moment-timezone-utils': 'moment',
            };
            if (Object.keys(ext).includes(request)) {
                // Externalize to a commonjs module using the request path
                // console.log(context, request, contextInfo);
                return callback(null, ext[request]);
            }

            // Continue without externalizing the import
            callback();
        },
    ]
};

module.exports = config;
