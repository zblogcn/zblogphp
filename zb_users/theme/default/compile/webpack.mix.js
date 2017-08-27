const mix = require('laravel-mix')
const fs = require('fs')
const os = require('os')
const path = require('path')
const CopyWebpackPlugin = require('copy-webpack-plugin')
mix.pug = require('laravel-mix-pug')

const styles = []
const getPath = str => path.join(__dirname, str)

if (fs.existsSync(getPath('dist'))) {
  fs.unlinkSync(getPath('dist'))
}
if (os.platform() === 'win32') {
  fs.symlinkSync('..', 'dist', 'junction')
} else {
  fs.linkSync('..', 'dist')
}

// Global
mix.setPublicPath('dist')
  .disableNotifications()
  .options({
    processCssUrls: false
  })

if (mix.config.inProduction) {
  mix.version()
}

// Sass / Less / Stylus
if (fs.existsSync(getPath('style/app.scss'))) {
  mix.sass('style/app.scss', 'style/app.sass.css')
  styles.push('dist/style/app.sass.css')
}
if (fs.existsSync(getPath('style/app.less'))) {
  mix.less('style/app.less', 'style/app.less.css')
  styles.push('dist/style/app.less.css')
}
if (fs.existsSync(getPath('style/app.styl'))) {
  mix.less('style/app.styl', 'style/app.styl.css')
  styles.push('dist/style/app.styl.css')
}
if (styles.length > 0) {
  mix.styles(styles, 'dist/style/app.css')
}

// Copy styles
mix.webpackConfig({
  plugins: [
    new CopyWebpackPlugin([{
      from: 'style/**/*',
      to: ''
    }], {
      ignore: [
        '*.sass',
        '*.scss',
        '*.less',
        '*.styl'
      ]
    })
  ]
})

// CoffeeScript / JavaScript
if (fs.existsSync(getPath('script/app.coffee'))) {
  mix.js('script/app.coffee', 'script')
    .webpackConfig({
      module: {
        rules: [
          { test: /\.coffee$/, loader: 'coffee-loader' }
        ]
      }
    })
}
else if (fs.existsSync(getPath('script/app.js'))) {
  mix.js('script/app.js', 'script')
}

// Pug / Raw PHP
mix
  .pug('php/*.pug', 'dist/template', {})
  .copy('php/*.php', 'dist/template')

mix
  .webpackConfig({
    externals: {
      jquery: 'jQuery',
      zbp: 'zbp'
    }
  })

// Full API
// mix.js(src, output);
// mix.react(src, output); <-- Identical to mix.js(), but registers React Babel compilation.
// mix.extract(vendorLibs);
// mix.sass(src, output);
// mix.standaloneSass('src', output); <-- Faster, but isolated from Webpack.
// mix.fastSass('src', output); <-- Alias for mix.standaloneSass().
// mix.less(src, output);
// mix.stylus(src, output);
// mix.postCss(src, output, [require('postcss-some-plugin')()]);
// mix.browserSync('my-site.dev');
// mix.combine(files, destination);
// mix.babel(files, destination); <-- Identical to mix.combine(), but also includes Babel compilation.
// mix.copy(from, to);
// mix.copyDirectory(fromDir, toDir);
// mix.minify(file);
// mix.sourceMaps(); // Enable sourcemaps
// mix.version(); // Enable versioning.
// mix.disableNotifications();
// mix.setPublicPath('path/to/public');
// mix.setResourceRoot('prefix/for/resource/locators');
// mix.autoload({}); <-- Will be passed to Webpack's ProvidePlugin.
// mix.webpackConfig({}); <-- Override webpack.config.js, without editing the file directly.
// mix.then(function () {}) <-- Will be triggered each time Webpack finishes building.
// mix.options({
//   extractVueStyles: false, // Extract .vue component styling to file, rather than inline.
//   processCssUrls: true, // Process/optimize relative stylesheet url()'s. Set to false, if you don't want them touched.
//   purifyCss: false, // Remove unused CSS selectors.
//   uglify: {}, // Uglify-specific options. https://webpack.github.io/docs/list-of-plugins.html#uglifyjsplugin
//   postCss: [] // Post-CSS options: https://github.com/postcss/postcss/blob/master/docs/plugins.md
// });
