var pagemanagerClient;
var jq = jQuery.noConflict( true )
jq(document).ready(function() {
    //
    // PageManager: general encapsulation, you know, to organise and stuff
    //
    pagemanagerClient = (function() {

        var onReady = function() {
            setViewMode('content');

            $(document).on('click', '.pm-block .btn-edit', function(e) {
                e.preventDefault();
                var id = $(this).closest('.pm-block').attr('data-pm-block-id');
                server().editBlock(id);
            });

            // Delete block (click)
            $(document).on('click', '.pm-block .btn-delete', function(e) {
                e.preventDefault();
                var id = $(this).closest('.pm-block').attr('data-pm-block-id');
                server().deleteBlock(id);
            });


            $('.pm-block').mouseover(function() {
                $('.pm-block').removeClass('hovered');
                $(this).addClass('hovered');
            });
            //
            //// create clone when dragging to retrieve original style
            //$(document).on('mousedown', '.pm-placeholder .pm-toolbar', function() {
            //    server().setDragFloat( $(this).closest('.pm-block').css('float') );
            //});

            console.log('Pagemanager client ready.');
            server().onClientReady();
        };

        var server = function() {
            return parent.pagemanager;
        };

        var setViewMode = function(mode) {
            jq('body').removeClass (function (index, css) {
                return (css.match (/(^|\s)pm-viewmode-\S+/g) || []).join(' ');
            }).addClass('pm-viewmode-'+mode);

            return this;
        };

        return {
            onReady : onReady,
            setViewMode : setViewMode
        };
    })();


    pagemanagerClient.onReady();
});
