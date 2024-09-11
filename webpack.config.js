const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // Directory where compiled assets will be stored
    .setOutputPath('public/build/')

    // Public path used by the web server to access the output path
    .setPublicPath('/build')

    // Enables Sass/SCSS support
    .enableSassLoader()

    // Each entry will result in one JavaScript file (e.g. app.js)
    // and one CSS file (e.g. app.scss) if your JavaScript imports CSS.
    .addEntry('app', './assets/js/app.js') // Ensure this points to your app.js

    // Compile custom.scss into a separate file
    .addStyleEntry('css/custom', './assets/styles/custom.scss')

    // Split files into smaller pieces for optimization
    .splitEntryChunks()

    // Enables Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // Will require an extra script tag for runtime.js
    .enableSingleRuntimeChunk()

    // Clean up the output directory before each build
    .cleanupOutputBeforeBuild()

    // Enable build notifications
    .enableBuildNotifications()

    // Enable source maps for non-production environments
    .enableSourceMaps(!Encore.isProduction())

    // Enables hashed filenames (e.g. app.abc123.css) for production builds
    .enableVersioning(Encore.isProduction())

    // Configure Babel for JavaScript
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    });

// Export the final Webpack configuration
module.exports = Encore.getWebpackConfig();
