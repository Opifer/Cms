//npm install -g gulp
//npm install --save-dev gulp gulp-util gulp-if gulp-uglify gulp-uglifycss gulp-less gulp-concat gulp-sourcemaps gulp-watch gulp-livereload gulp-imagemin imagemin-pngquant

var gulp = require('gulp'),
    gulpif = require('gulp-if'),
    uglify = require('gulp-uglify'),
    util = require('gulp-util'),
    uglifycss = require('gulp-uglifycss'),
    imagemin = require('gulp-imagemin'),
    pngquant = require('imagemin-pngquant'),
    less = require('gulp-less'),
    fs = require('fs'),
    concat = require('gulp-concat'),
    sourcemaps = require('gulp-sourcemaps'),
    livereload = require('gulp-livereload'),
    watch = require('gulp-watch'),
    env = process.env.SYMFONY_ENV;

// JAVASCRIPT TASK: write one minified js file out of jquery.js, bootstrap.js and all of my custom js files
gulp.task('js', function () {
    var files = [
        'Resources/public/components/ng-file-upload/angular-file-upload-shim.min.js',
        'Resources/public/components/jquery/dist/jquery.min.js',
        'Resources/public/components/jquery-ui/jquery-ui.js',
        'Resources/public/components/js-cookie/src/js.cookie.js',
        'Resources/public/components/speakingurl/lib/speakingurl.js',
        'Resources/public/components/jquery-slugify/dist/slugify.js',
        'Resources/public/components/underscore/underscore.js',
        'Resources/public/components/angular/angular.min.js',
        'Resources/public/components/angular-cookies/angular-cookies.js',
        'Resources/public/components/angular-route/angular-route.js',
        'Resources/public/components/angular-resource/angular-resource.js',
        'Resources/public/components/angular-inview/angular-inview.js',
        'Resources/public/components/angular-ui-sortable/sortable.js',
        'Resources/public/components/angular-ui-tree/dist/angular-ui-tree.js',
        'Resources/public/components/angular-loading-bar/build/loading-bar.js',
        'Resources/public/components/bootstrap/dist/js/bootstrap.js',
        'Resources/public/components/moment/min/moment.min.js',
        'Resources/public/components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
        'Resources/public/components/bootstrap-iconpicker/bootstrap-iconpicker/js/iconset/iconset-materialdesign-2.2.1.js',
        'Resources/public/components/bootstrap-iconpicker/bootstrap-iconpicker/js/bootstrap-iconpicker.js',
        'Resources/public/components/dropzone/dist/dropzone.js',
        'Resources/public/components/ngInfiniteScroll/build/ng-infinite-scroll.js',
        'Resources/public/components/afkl-lazy-image/release/lazy-image.js',
        'Resources/public/components/bootbox.js/bootbox.js',
        'Resources/public/components/mprogress/mprogress.min.js',
        'Resources/public/components/ng-file-upload/angular-file-upload.min.js',
        'Resources/public/components/typeahead.js/dist/typeahead.bundle.js',
        '../../../../braincrafted/bootstrap-bundle/Braincrafted/Bundle/BootstrapBundle/Resources/js/bc-bootstrap-collection.js',
        'Resources/public/js/split-pane.js',
        'Resources/public/js/main.js',
        'Resources/public/js/pagemanager.js',
        'Resources/public/angular/app.js',
        '../ContentBundle/Resources/public/js/app.js',
        '../ContentBundle/Resources/public/app/content/content.js',
        '../MediaBundle/Resources/public/js/dropzone.js',
        '../MediaBundle/Resources/public/app/modal/modal.js',
        '../MediaBundle/Resources/public/app/medialibrary/medialibrary.js',

        'Resources/public/components/ckeditor/ckeditor.js'
    ];

    files.forEach(function(file) {
        if (file.indexOf('*') !== -1) {
            return;
        }

        fs.stat(file, function(err, stat) {
            if (err) {
                throw new util.PluginError({
                    plugin: 'deploy',
                    message: file + ' does not exist'
                });
            }
        });
    });

    return gulp.src(files)
        .pipe(concat('app.js'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('Resources/public/js'));
});


// CSS TASK: write one minified css file out of bootstrap.less and all of my custom less files
gulp.task('css', function () {
    var files = [
        '../MediaBundle/Resources/public/css/main.less',
        '../MediaBundle/Resources/public/css/dropzone.less',
        'Resources/public/components/angular-loading-bar/build/loading-bar.css',
        'Resources/public/components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css',
        'Resources/public/components/bootstrap-iconpicker/bootstrap-iconpicker/css/bootstrap-iconpicker.css',
        'Resources/public/less/main.less'
    ];

    files.forEach(function(file) {
        if (file.indexOf('*') !== -1) {
            return;
        }

        fs.stat(file, function(err, stat) {
            if (err) {
                throw new util.PluginError({
                    plugin: 'deploy',
                    message: file + ' does not exist'
                });
            }
        });
    });

    return gulp.src(files)
        .pipe(gulpif(/[.]less/, less()))
        .pipe(concat('app.css'))
        .pipe(uglifycss())
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('Resources/public/css'));
});


// Pagemanager client side assets
gulp.task('pagemanager-client-js', function () {
    return gulp.src([
        'Resources/public/components/jquery/dist/jquery.js',
        'Resources/public/components/jquery-ui/ui/jquery-ui.js',
        'Resources/public/js/pagemanager-client.js'
    ])
        .pipe(concat('client.js'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('Resources/public/js'));
});
gulp.task('pagemanager-client-css', function () {
    return gulp.src([
        'Resources/public/less/pagemanager-client.less'
    ])
        .pipe(gulpif(/[.]less/, less()))
        .pipe(concat('pagemanager-client.css'))
        //.pipe(uglifycss())
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('Resources/public/css'));
});


//define executable tasks when running "gulp" command
gulp.task('pagemanager', ['pagemanager-client-js', 'pagemanager-client-css']);
gulp.task('default', ['js', 'css', 'pagemanager']);

//watch less files for changes
gulp.task('watch', function() {
    gulp.watch('Resources/public/less/*.less', ['default']);
    gulp.watch('Resources/public/js/*.js', ['default']);
});
