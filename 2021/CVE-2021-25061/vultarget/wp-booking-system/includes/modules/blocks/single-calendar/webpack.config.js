module.exports = {
  // Entry.
  entry: "./assets/js/src/script-block-single-calendar.js", // Import all JavaScript dependencies in this file.

  // Output.
  output: {
    path: __dirname, // Path to produce the output. Set to the current directory.
    filename: "assets/js/build/script-block-single-calendar.js" // Filename of the file that webpack builds.
  },

  // Loaders.
  // The configuration below has defined a rules property for a single module with
  // two required properties: test and use. This tells webpack's compiler the following:
  // "Hey webpack compiler, when you come across a '.js' or '.jsx' file inside of a
  // require()/import statement, use the babel-loader to transform it before you add
  // it to the bundle."
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/, // Identifies which file or files should be transformed.
        use: { loader: "babel-loader" }, // Babel loader to transpile modern JavaScript.
        exclude: /(node_modules|bower_components)/ // JavaScript files to be ignored.
      }
    ]
  },

  externals : {
    'react'     : 'React',
    'react-dom' : 'ReactDOM'
  }

};