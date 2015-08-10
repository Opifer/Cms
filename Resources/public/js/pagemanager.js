var pagemanager;

$(document).ready(function(){

    //
    // PageManager: general encapsulation, you know, to organise and stuff
    //
    pagemanager = (function() {
        var sortables;

        var onReady = function() {
            // Toggle view between Content, Preview and Layout
            $('input[name="viewmode"]:radio').change(function () {
                console.log('change', $(this));
                if ($('input[name="viewmode"]:checked').val() == 'CONTENT') {
                    $('.pm-preview').removeClass('pm-viewmode-layout').addClass('pm-viewmode-content');
                    $('.pm-tools-blockset').addClass('hidden');
                    $('#pm-tools-blocks').removeClass('hidden');
                } else if ($('input[name="viewmode"]:checked').val() == 'LAYOUT') {
                    $('.pm-preview').removeClass('pm-viewmode-content').addClass('pm-viewmode-layout');
                    $('.pm-tools-blockset').addClass('hidden');
                    $('#pm-tools-layouts').removeClass('hidden');
                } else {
                    $('.pm-preview').removeClass('pm-viewmode-content').removeClass('pm-viewmode-layout');
                    $('.pm-tools-blockset').addClass('hidden');
                }
            });

            // Split page library
            $('.split-pane').splitPane();

            // Resize iframe based on their contents (for block editing view)
            //$('#pm-block-edit-iframe').iFrameResize();
            //$('#pm-block-edit-iframe').bind('load', function () { pagemanager.isNotLoading(); });

            $(document).ajaxStart(function(e) {
                pagemanager.isLoading();
            });

            $(document).ajaxComplete(function(e) {
                pagemanager.isNotLoading();
                angular.bootstrap($('#pm-block-edit form'), ["MainApp"]);
            });

            pagemanager.bindSortable();

            $(document).on('submit', '#pm-block-edit form', function(e) {
                e.preventDefault();
                var blockId = $('#pm-block-edit form').attr('data-pm-block-id');
                pagemanager.postBlockForm($(this), function(data) {
                    $('#pm-block-edit').html(data);
                    pagemanager.refreshBlock(blockId);
                });

                return false;
            });
        };

        // Start loading indicator
        var isLoading = function() {
            $('#pm-navbar').addClass('isloading');
        };

        // Stop loading indicator
        var isNotLoading = function() {
            $('#pm-navbar').removeClass('isloading');
        };

        var refreshBlock = function(id) {
            $.get(Routing.generate('opifer_content_api_pagemanager_view_block', {id: id})).done(function (data) {
                $('div[data-pm-block-id="'+id+'"]').replaceWith(data.view);
            });
        };

        var paintEmptyPlaceholders = function() {
            $('.pm-placeholder').each(function(index) {
                if ($(this).children().length) {
                    $(this).removeClass('pm-empty');
                } else {
                    $(this).addClass('pm-empty');
                }
            });
        };

        //
        // Create a block by dropping in a placeholder
        //
        var bindSortable = function() {
            sortables = $('.pm-placeholder').sortable({
                handle: '.pm-toolbar',
                revert: true,
                distance: 10,
                connectWith: '.pm-placeholder',
                //greedy: true,
                placeholder: 'pm-placeholder-droparea',
                tolerance: "pointer",
                receive: function( event, ui ) {
                    console.log('Received:', ui.item);
                    // Create new block
                    if ( $(ui.item).hasClass('pm-block-item') ) {
                        var element = $(this).find('.pm-block-item');
                        var type = $(ui.item).attr('data-pm-block-type');
                        var parent = $(this).parent().closest('.pm-layout').attr('data-pm-block-id');
                        var placeholderKey = $(this).closest('.pm-placeholder').attr('data-pm-placeholder-key');
                        var data = $(ui.item).attr('data-pm-block-data');

                        $(ui.item).attr('data-pm-block-id', '0');
                        var sortOrder = $(this).sortable('toArray', {attribute: 'data-pm-block-id'});

                        console.log('Creating new block', sortOrder);
                        console.log('Creating new block', {type: type, parentId: parent, placeholder: placeholderKey}, {data: data, sort: sortOrder});
                        $.post(Routing.generate('opifer_content_api_pagemanager_create_block', {type: type, parentId: parent, placeholder: placeholderKey}), {data: data, sort: sortOrder}).done(function (data, textStatus, request) {
                            var viewUrl = request.getResponseHeader('Location');
                            console.log("item dropped!", type, parent, sortOrder, data);
                            $.get(viewUrl).done(function (data) {
                                element.replaceWith(data.view);

                                // unbind and rebind sortable to allow new layouts with placeholders.
                                pagemanager.bindSortable();
                                pagemanager.paintEmptyPlaceholders();
                            });
                        });
                    }
                    // Sort existing block
                    if ( $(ui.item).hasClass('pm-block') ) {
                        console.log('Sorting existing block', ui.item);
                    }
                },
                over: function( event, ui ) {
                    $(this).addClass('pm-placeholder-accept').closest('.pm-layout').addClass('pm-layout-accept');
                },
                out: function( event, ui ) {
                    $(this).removeClass('pm-placeholder-accept').closest('.pm-layout').removeClass('pm-layout-accept');
                },
                start: function() {
                    $('.pm-preview').addClass('pm-dragging');
                },
                stop: function( event, ui ) {
                    console.log('Stopped sorting:', ui.placeholder);
                    $('.pm-preview').removeClass('pm-dragging');
                    $('.pm-layout').removeClass('pm-layout-accept'); // cleaning up just to be sure
                    pagemanager.paintEmptyPlaceholders();

                    if (!$(ui.item).hasClass('pm-block-item')) {
                        // Push order of blocks to backend service
                        var sortOrder = $(ui.item).closest('.pm-placeholder').sortable('toArray', {attribute: 'data-pm-block-id'});
                        var blockId = $(ui.item).attr('data-pm-block-id');
                        var parentId = $(ui.item).parent().closest('.pm-layout').attr('data-pm-block-id');
                        var placeholderKey = $(ui.item).closest('.pm-placeholder').attr('data-pm-placeholder-key');
                        console.log('Posting re-sort of items to backend service', sortOrder, blockId, parentId, placeholderKey);

                        $.post(Routing.generate('opifer_content_api_pagemanager_move_block'), {sort: sortOrder, id: blockId, parent: parentId, placeholder: placeholderKey}).done(function (data, textStatus, request) {
                            console.log("Block moved", data);
                        });
                    }
                }
            });

            return this;
        };

        var unbindSortable = function() {
            sortables.sortable('destroy');

            return this;
        };

        var postBlockForm = function( $form, callback ){
            var values = {};
            $.each( $form.serializeArray(), function(i, field) {
                values[field.name] = field.value;
            });

            $.ajax({
                type        : $form.attr( 'method' ),
                url         : $form.attr( 'action' ),
                data        : values,
                success     : function(data) {
                    callback( data );
                }
            });
        };

        return {
            onReady : onReady,
            isLoading : isLoading,
            isNotLoading : isNotLoading,
            refreshBlock : refreshBlock,
            paintEmptyPlaceholders : paintEmptyPlaceholders,
            bindSortable : bindSortable,
            unbindSortable : unbindSortable,
            postBlockForm : postBlockForm
        };
    })();

    pagemanager.onReady();

    $('.pm-block-item').draggable({
        appendTo: '#pm-list-group-container',
        helper: 'clone',
        connectToSortable: '.pm-placeholder',
        start: function() {
            $('.pm-preview').addClass('pm-dragging');
        },
        stop: function() {
            $('.pm-preview').removeClass('pm-dragging');
            $('.pm-layout').removeClass('pm-layout-accept'); // cleaning up just to be sure
        }
    });

    //
    // Open block edit
    //
    $(document).on('click', '.pm-block .btn-delete', function(e) {
        e.preventDefault();
        var element = $(this);
        var id = $(this).closest('.pm-block').attr('data-pm-block-id');
        $.ajax({
            url: Routing.generate('opifer_content_api_pagemanager_remove_block', {id: id}),
            type: 'DELETE',
            success: function (data) {
                element.closest('.pm-block').remove();
                pagemanager.paintEmptyPlaceholders();
            }
        });
    });

    //
    // Open block edit
    //
    $(document).on('click', '.pm-block .btn-edit', function(e) {
        e.preventDefault();
        var id = $(this).closest('.pm-block').attr('data-pm-block-id');
        console.log(id, Routing.generate('opifer_content_pagemanager_edit_block', {'id': id}));
        $.get(Routing.generate('opifer_content_pagemanager_edit_block', {id: id})).success(function(data) {
            $('#pm-block-edit').html(data);
        });
        $('#pm-block-edit').removeClass('hidden');

    });


    $(document).on('click', '#pm-block-edit #btn-cancel', function(e) {
        $('#pm-block-edit').addClass('hidden');
    });

    ////
    //// Block edit events in iframe
    ////
    //$('#pm-block-edit-iframe').load(function(){
    //    var iframe = $('#pm-block-edit-iframe').contents();
    //
    //    iframe.find("form").submit(function(){
    //        pagemanager.isLoading();
    //    });
    //
    //    iframe.find("#btn-cancel").click(function(e){
    //        e.preventDefault();
    //    });
    //});
});

(function( jQuery ) {
    var matched,
        userAgent = navigator.userAgent || "";

    // Use of jQuery.browser is frowned upon.
    // More details: http://api.jquery.com/jQuery.browser
    // jQuery.uaMatch maintained for back-compat
    jQuery.uaMatch = function( ua ) {
        ua = ua.toLowerCase();

        var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
            /(webkit)[ \/]([\w.]+)/.exec( ua ) ||
            /(opera)(?:.*version)?[ \/]([\w.]+)/.exec( ua ) ||
            /(msie) ([\w.]+)/.exec( ua ) ||
            ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+))?/.exec( ua ) ||
            [];

        return {
            browser: match[ 1 ] || "",
            version: match[ 2 ] || "0"
        };
    };

    matched = jQuery.uaMatch( userAgent );

    jQuery.browser = {};

    if ( matched.browser ) {
        jQuery.browser[ matched.browser ] = true;
        jQuery.browser.version = matched.version;
    }

    // Deprecated, use jQuery.browser.webkit instead
    // Maintained for back-compat only
    if ( jQuery.browser.webkit ) {
        jQuery.browser.safari = true;
    }

}( jQuery ));