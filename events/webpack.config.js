var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    .autoProvideVariables({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
    })
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')
    .copyFiles({
        from: './assets/images',

        // optional target path, relative to the output dir
        to: 'images/[path]/[name].[ext]'

        // if versioning is enabled, add the file hash too
        //to: 'images/[path][name].[hash:8].[ext]',

        // only copy files matching this pattern
        //pattern: /\.(png|jpg|jpeg)$/
    })
    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/js/app.js')
    .addEntry('example', './assets/js/example.js')
    .addEntry('sign_up', './assets/js/sign_up.js')
    .addEntry('user_articles', './assets/js/user_articles.js')
    .addEntry('user', './assets/js/user.js')
    .addEntry('associate', './assets/js/associate.js')
    .addEntry('login', './assets/js/login.js')
    .addEntry('theme', './assets/js/theme.js')
    .addEntry('activity', './assets/js/activity.js')
    .addEntry('gestorEvents', './assets/js/gestor/events.js')
    .addEntry('gestorInstitutions', './assets/js/gestor/institutions.js')
    .addEntry('gestorEditions', './assets/js/gestor/editions.js')
    .addEntry('gestorPrograms', './assets/js/gestor/programs.js')
    .addEntry('gestorSubsections', './assets/js/gestor/subsections.js')
    .addEntry('gestorSpeakers', './assets/js/gestor/speakers.js')
    .addEntry('gestorFiles', './assets/js/gestor/files.js')
    .addEntry('gestorInformatives', './assets/js/gestor/informatives.js')
    .addEntry('gestorEditionDiscounts', './assets/js/gestor/discounts.js')
    .addEntry('gestorEditionPaymentModes', './assets/js/gestor/payments.js')
    .addEntry('gestorThemes', './assets/js/gestor/themes.js')
    .addEntry('panels', './assets/js/panels.js')
    .addEntry('themeEvaluation', './assets/js/theme/evaluation.js')
    .addEntry('panelEvaluation', './assets/js/panel/evaluation.js')
    .addEntry('panelEvaluationAction', './assets/js/panel/evaluationAction.js')
    .addEntry('systemEvaluationSubmissions', './assets/js/system_evaluation/submissions.js')
    .addEntry('systemEvaluationEvaluators', './assets/js/system_evaluation/evaluators.js')
    .addEntry('systemEvaluationConfigurations', './assets/js/system_evaluation/configurations.js')
    .addEntry('systemEvaluationCoordinators', './assets/js/system_evaluation/coordinators.js')
    .addEntry('systemEvaluationDashboard', './assets/js/system_evaluation/dashboard.js')
    .addEntry('systemEvaluationReports', './assets/js/system_evaluation/reports.js')
    .addEntry('systemEvaluationIndication', './assets/js/system_evaluation/indication.js')
    .addEntry('systemEvaluationAverages', './assets/js/system_evaluation/averages.js')
    .addEntry('systemEvaluationAuthorRate', './assets/js/system_evaluation/author_rate.js')
    .addEntry('systemEvaluationThesis', './assets/js/system_evaluation/thesis.js')
    .addEntry('permissions', './assets/js/permissions.js')
    .addEntry('certificate', './assets/js/certificate/certificate.js')
    .addEntry('events/sign_up', './assets/js/events/sign_up.js')
    .addEntry('events/show', './assets/js/events/show.js')
    .addEntry('cookieconsent', './assets/js/cookieconsent.js')
    .addEntry('dashboard/user', './assets/js/dashboard/user.js')
    .addEntry('ensalement/rooms', './assets/js/ensalement/rooms.js')
    .addEntry('ensalement/sessions', './assets/js/ensalement/sessions.js')
    .addEntry('ensalement/slots', './assets/js/ensalement/slots.js')
    .addEntry('ensalement/priority', './assets/js/ensalement/priority.js')
    .addEntry('ensalement/general', './assets/js/ensalement/general.js')
    .addEntry('ensalement/sections', './assets/js/ensalement/sections.js')
    .addEntry('ensalement/sections_search', './assets/js/ensalement/sections_search.js')
    .addEntry('articleEvaluation', './assets/js/article_evaluation.js')
    .addEntry('dashboard/admin', './assets/js/dashboard/admin.js')
    .addEntry('userEdit', './assets/js/user_edit.js')
    .addEntry('permissionsCoordinator', './assets/js/permissions_coordinator.js')
    .addEntry('permissionsCommittee', './assets/js/permissions_committee.js')
    .addEntry('paymentUserAssociation', './assets/js/gestor/payment_user_association.js')
    .addEntry('thesisSubmission', './assets/js/thesis_submission.js')
    .addEntry('userNew', './assets/js/user_new.js')
    .addEntry('certificateNewAwards', './assets/js/certificate_new_awards.js')
    .addEntry('certificateNewManual', './assets/js/certificate_new_manual.js')
    .addEntry('certificateShow', './assets/js/certificate_show.js')
    .addEntry('themeSubmissionConfigForm', './assets/js/gestor/theme_submission_config_form.js')
    .addEntry('themesSubmission', './assets/js/themes_submission.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

// uncomment if you use API Platform Admin (composer require api-admin)
//.enableReactPreset()
//.addEntry('admin', './assets/js/admin.js')
;

module.exports = Encore.getWebpackConfig();
