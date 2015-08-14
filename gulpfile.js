//npm install -g gulp
//npm install --save-dev gulp gulp-if gulp-uglify gulp-uglifycss gulp-less gulp-concat gulp-sourcemaps gulp-watch gulp-livereload gulp-imagemin imagemin-pngquant

var gulp = require('gulp'),
    gulpif = require('gulp-if'),
    uglify = require('gulp-uglify'),
    uglifycss = require('gulp-uglifycss'),
    imagemin = require('gulp-imagemin'),
    pngquant = require('imagemin-pngquant'),
    less = require('gulp-less'),
    concat = require('gulp-concat'),
    sourcemaps = require('gulp-sourcemaps'),
    livereload = require('gulp-livereload'),
    watch = require('gulp-watch'),
    env = process.env.SYMFONY_ENV;

// JAVASCRIPT TASK: write one minified js file out of jquery.js, bootstrap.js and all of my custom js files
gulp.task('js', function () {
    return gulp.src([
        'Resources/public/components/ng-file-upload/angular-file-upload-shim.min.js',
        'Resources/public/components/jquery/dist/jquery.js',
        'Resources/public/components/jquery-ui/ui/jquery-ui.js',
        'Resources/public/components/speakingurl/lib/speakingurl.js',
        'Resources/public/components/jquery-slugify/dist/slugify.js',
        'Resources/public/components/underscore/underscore.js',
        'Resources/public/components/angular/angular.js',
        'Resources/public/components/angular-route/angular-route.js',
        'Resources/public/components/angular-resource/angular-resource.js',
        'Resources/public/components/angular-google-maps/dist/angular-google-maps.js',
        'Resources/public/components/angular-inview/angular-inview.js',
        'Resources/public/components/angular-ui-sortable/sortable.js',
        'Resources/public/components/angular-loading-bar/build/loading-bar.js',
        'Resources/public/components/bootstrap/dist/js/bootstrap.js',
        'Resources/public/components/dropzone/dist/dropzone.js',
        'Resources/public/components/iframe-resizer/src/iframeResizer.js',
        'Resources/public/components/ngInfiniteScroll/build/ng-infinite-scroll.js',
        'Resources/public/components/afkl-lazy-image/release/lazy-image.js',
        'Resources/public/components/bootbox.js/bootbox.js',
        'Resources/public/components/mprogress/mprogress.min.js',
        'Resources/public/components/ng-file-upload/angular-file-upload.min.js',
        'Resources/public/components/typeahead.js/dist/typeahead.bundle.js',
        'vendor/braincrafted/bootstrap-bundle/Braincrafted/Bundle/BootstrapBundle/Resources/js/bc-bootstrap-collection.js',
        'vendor/infinite-networks/form-bundle/Resources/public/js/collections.js',
        'Resources/public/js/split-pane.js',
        'Resources/public/js/main.js',
        'Resources/public/js/jquery-sortable-zindex.js',
        'Resources/public/js/pagemanager.js',
        'Resources/public/app/app.js',

        'vendor/opifer/content-bundle/Resources/public/app/content/content.js',
        'vendor/opifer/content-bundle/Resources/public/app/presentationeditor/presentationeditor.js',
        'vendor/opifer/media-bundle/Resources/public/js/dropzone.js',
        'vendor/opifer/crud-bundle/Resources/public/js/main.js',
        'vendor/opifer/eav-bundle/Resources/public/app/googleaddress/googleaddress.js',
        'vendor/opifer/eav-bundle/Resources/public/app/nestedcontent/nestedcontent.js',
        'vendor/opifer/media-bundle/Resources/public/app/modal/modal.js',
        'vendor/opifer/media-bundle/Resources/public/app/medialibrary/medialibrary.js',
        'vendor/opifer/rulesengine-bundle/Resources/public/app/ruleeditor/ruleeditor.js',

        'Resources/public/components/ckeditor/ckeditor.js'])
        .pipe(concat('app.js'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('Resources/public/dist/js'));
});


// CSS TASK: write one minified css file out of bootstrap.less and all of my custom less files
gulp.task('css', function () {
    return gulp.src([
        'vendor/opifer/media-bundle/Resources/public/css/main.less',
        'vendor/opifer/media-bundle/Resources/public/css/dropzone.less',
        'Resources/public/components/angular-loading-bar/build/loading-bar.css',
        'Resources/public/less/main.less'
        ])
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
        'Resources/public/js/pagemanager-client.js',
    ])
        .pipe(concat('client.js'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('Resources/public/dist/js'));
});
gulp.task('pagemanager-client-css', function () {
    return gulp.src([
        'Resources/public/less/pagemanager-client.less',
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
