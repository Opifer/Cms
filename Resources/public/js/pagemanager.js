var pagemanager;
var CKEDITOR_BASEPATH = '/bundles/opifercms/components/ckeditor/';


$(document).ready(function() {
    //
    // PageManager: general encapsulation, you know, to organise and stuff
    //
    pagemanager = (function () {
        var ownerType = 'template';
        var ownerId = 0;
        var mprogress = null;
        var hasUnsavedChanges = false;
        var version = 0;
        var version_published = 0;

        var onReady = function () {
            mprogress = new Mprogress({
                template: 3
            });
            isLoading();

            // Toggle view between Content, Preview and Layout
            $('input[name="viewmode"]:radio').change(function () {
                pagemanager.unselectBlock();
                if ($('input[name="viewmode"]:checked').val() == 'CONTENT') {
                    client().setViewMode('content');
                    $('.pm-tools-blockset').addClass('hidden');
                    $('#pm-tools-blocks').removeClass('hidden');
                } else if ($('input[name="viewmode"]:checked').val() == 'LAYOUT') {
                    client().setViewMode('layout');
                    $('.pm-tools-blockset').addClass('hidden');
                    $('#pm-tools-layouts').removeClass('hidden');
                } else {
                    client().setViewMode('preview');
                    $('.pm-tools-blockset').addClass('hidden');
                }
            });

            $('input[name="screenwidth"]:radio').change(function () {
                var val = $('input[name="screenwidth"]:checked').val();
                var width = '100%';
                var scrollBarWidth = 15;
                switch(val) {
                    case 'xs':
                        width = 320 + scrollBarWidth;
                        break;
                    case 'sm':
                        width = 768 + scrollBarWidth;
                        break;
                    case 'md':
                        width = 992 + scrollBarWidth;
                        break;
                }

                $('#pm-iframe').css('width', width);
            });

            ownerId = $('#pm-document').attr('data-pm-id');
            version = $('#pm-document').attr('data-pm-version');
            version_published = $('#pm-document').attr('data-pm-version-published');

            // Split page library
            $('.split-pane').splitPane();

            $(document).ajaxStart(function (e) {
                isLoading();
            });

            // Resize iframe based on their contents (for block editing view)
            //$('#pm-iframe').iFrameResize();
            $('#pm-iframe').bind('load', function () {
                console.log(client());
                pagemanager.isNotLoading();
                sortables();
            });

            $(document).ajaxComplete(function (e) {
                isNotLoading();
            });


            $(document).on('submit', '#pm-block-edit form', function (e) {
                e.preventDefault();
                var blockId = $('#pm-block-edit form').attr('data-pm-block-id');
                postBlockForm($(this), function (data) {
                    $('#pm-block-edit').html(data);
                    // Bootstrap AngularJS app (media library etc) after altering DOM
                    angular.bootstrap($('#pm-block-edit form'), ["MainApp"]);
                    if (blockId) {
                        refreshBlock(blockId);
                    }
                });

                return false;
            });

            $(document).on('click', '.pm-block .btn-edit', function (e) {
                e.preventDefault();
                editBlock($(this).closest('.pm-block').attr('data-pm-block-id'));
            });

            // Edit block (click)
            $(document).on('click', '#pm-block-edit #btn-cancel', function (e) {
                e.preventDefault();
                closeEditBlock($(this).closest('.pm-block').attr('data-pm-block-id'));
            });

            // Version picker (click)
            $(document).on('click', '.pm-version-link', function (e) {
                //e.preventDefault();
                loadVersion($(this).attr('data-pm-version'));
            });

            $(document).on('click', '#pm-btn-publish', function(e) {
                e.preventDefault();
                publish();
            });

            $(document).on('click', '#pm-btn-properties', function(e) {
                e.preventDefault();
                editProperties($(this).attr('href'));
            });

            window.onbeforeunload = function() {
                if (hasUnsavedChanges) {
                    return "Attention: you will possible lose changes made.";
                }
            }
        };

        var client = function () {
            return document.getElementById('pm-iframe').contentWindow['pagemanagerClient'];
        };

        // Start loading indicator
        var isLoading = function () {
            mprogress.start();
            $('#pm-navbar').addClass('isloading');
        };

        // Stop loading indicator
        var isNotLoading = function () {
            mprogress.end();
            $('#pm-navbar').removeClass('isloading');
        };

        var refreshBlock = function (id) {
            $.get(Routing.generate('opifer_content_api_contenteditor_view_block', {id: id, rootVersion: version})).done(function (data) {
                getBlockElement(id).replaceWith(data.view);
            });
        };

        var paintEmptyPlaceholders = function () {
            $('#pm-iframe').contents().find('.pm-placeholder').each(function (index) {
                if ($(this).children().length) {
                    $(this).removeClass('pm-empty');
                } else {
                    $(this).addClass('pm-empty');
                }
            });
        };

        var getBlockElement = function (id) {
            return $('#pm-iframe').contents().find('div[data-pm-block-id="' + id + '"]');
        };

        var selectBlock = function (id) {
            $('#pm-iframe').contents().find('.pm-block').removeClass('selected');
            getBlockElement(id).addClass('selected');
        };

        var unselectBlock = function (id) {
            $('#pm-iframe').contents().find('.pm-block').removeClass('selected');
        };

        //
        // Call API to request an edit view
        //
        var editBlock = function (id) {
            if ($('#pm-block-edit').attr('data-pm-block-id') != id) {
                $('#pm-block-edit').attr('data-pm-block-id', id);
                isLoadingEdit();
                selectBlock(id);

                console.debug(id, Routing.generate('opifer_content_contenteditor_edit_block', {'id': id, rootVersion: version}));
                $.get(Routing.generate('opifer_content_contenteditor_edit_block', {id: id, rootVersion: version})).success(function (data) {
                    $('#pm-block-edit').html(data);
                    updateVersionPicker();

                    // Bootstrap AngularJS app (media library etc) after altering DOM
                    angular.bootstrap($('#pm-block-edit form'), ["MainApp"]);
                });
            }

            $('#pm-block-edit').removeClass('hidden');

            return this;
        };

        var editProperties = function (url) {
            $('#pm-block-edit').attr('data-pm-block-id', '');
            isLoadingEdit();

            $.get(url).success(function (data) {
                $('#pm-block-edit').html(data);
            });

            $('#pm-block-edit').removeClass('hidden');

            return this;
        };

        var isLoadingEdit = function() {
            $('#pm-block-edit').html('<div class="loading panel-body"><span>Loading…</span></div>');
        };

        var closeEditBlock = function (id) {
            unselectBlock(id);
            $('#pm-block-edit').addClass('hidden');
        };

        // Delete block
        var deleteBlock = function (id) {
            $.ajax({
                url: Routing.generate('opifer_content_api_contenteditor_remove_block', {id: id, rootVersion: version}),
                type: 'DELETE',
                dataType: 'json', // Choosing a JSON datatype
                success: function (data) {
                    getBlockElement(id).remove();
                    paintEmptyPlaceholders();
                    pagemanager.closeEditBlock(id);
                    updateVersionPicker();
                }
            }).error(function(data){
                showAPIError(data);
            });
        };

        //
        // Call API to create a new block
        //
        var createBlock = function (block, reference) {
            console.log('Creating new block', pagemanager.ownerId, block);

            $.post(Routing.generate('opifer_content_api_contenteditor_create_block', {ownerId: ownerId, rootVersion: version}), block).done(function (data, textStatus, request) {
                var viewUrl = request.getResponseHeader('Location');
                var id = data.id;

                console.debug("Block created:", id, block);

                $.get(viewUrl).done(function (data) {
                    reference.replaceWith(data.view);
                    // unbind and rebind sortable to allow new layouts with placeholders.
                    editBlock(id);
                    sortables();
                    paintEmptyPlaceholders();
                    updateVersionPicker();
                }).fail(function(data){
                    reference.remove();
                    showAPIError(data);
                });
            }).fail(function(data){
                reference.remove();
                showAPIError(data);
            });;
        };

        var showAPIError = function(data) {
            bootbox.dialog({
                title: data.statusText,
                message: '<code>'+data.responseJSON.error+'</code>',
                buttons: {
                    ok: {
                        label: 'Ok',
                        className: 'btn-primary'
                    }
                }
            });
        };

        var onClientReady = function () {
            $('.pm-placeholder', $('#pm-iframe').contents()).on('mousemove mouseup', function (event) {
                $(parent.document).trigger(event);
            });

            $('.pm-block-item').draggable({
                appendTo: '#pm-list-group-container',
                helper: 'clone',
                iframeFix: true,
                connectToSortable: sortables(),
                start: function (event, ui) {
                    ui.helper.animate({
                        width: 330,
                        height: 80
                    });
                    //$('.pm-preview').addClass('pm-dragging');
                },
                stop: function () {
                    $(document).scrollTop(0); // Fix for dissappearing .navbar.
                    //$('.pm-preview').removeClass('pm-dragging');
                    //$('.pm-layout').removeClass('pm-layout-accept'); // cleaning up just to be sure
                },
                drag: function (event, ui) {
                    //ui.position.top += 200;
                    //console.log(ui.offset.top, ui.offset.left, ui);
                }
            });

            //$('.pm-block-item').on('dragstop',autoResizeFrame);

            isNotLoading();
        };

        //
        // Create a block by dropping in a placeholder
        //
        var sortables = function () {
            return $('#pm-iframe').contents().find('.pm-placeholder').sortable({
                handle: '.pm-toolbar',
                revert: false,
                distance: 10,
                connectWith: $('#pm-iframe').contents().find('.pm-placeholder'),
                //greedy: true,
                iframeFix: true,
                placeholder: 'pm-placeholder-droparea',
                forcePlaceholderSize: true,
                tolerance: "pointer",
                cursorAt: { top: 5, left: 5 },
                receive: function (event, ui) {
                    console.log('Received:', ui.item);
                    // Create new block
                    if ($(ui.item).hasClass('pm-block-item')) {
                        var reference = $(this).find('.pm-block-item');
                        var type = $(ui.item).attr('data-pm-block-type');
                        var parent = $(this).parent().closest('.pm-layout').attr('data-pm-block-id');
                        var placeholderKey = $(this).closest('.pm-placeholder').attr('data-pm-placeholder-key');
                        var data = $(ui.item).attr('data-pm-block-data');

                        $(ui.item).attr('data-pm-block-id', '0'); // Set default so toArray won't trip and fall below
                        var sortOrder = $(this).sortable('toArray', {attribute: 'data-pm-block-id'});

                        createBlock({type: type, parent: parent, placeholder: placeholderKey, sort: sortOrder, data: data}, reference);
                    }
                },
                over: function (event, ui) {
                    //if ($.ui.ddmanager.current)
                    //    $.ui.ddmanager.prepareOffsets($.ui.ddmanager.current, null);
                    $('#pm-iframe').contents().find('.pm-block, .pm-placeholder').removeClass('pm-accept');
                    $(this).addClass('pm-accept').closest('.pm-layout').addClass('pm-accept');

                    var layoutId = $(this).addClass('pm-accept').closest('.pm-layout').attr('data-pm-block-id');
                    highlightPlaceholders(layoutId);
                },
                out: function (event, ui) {
                    //if ($.ui.ddmanager.current)
                    //    $.ui.ddmanager.prepareOffsets($.ui.ddmanager.current, null);
                    $('#pm-iframe').contents().find('.pm-block, .pm-placeholder').removeClass('pm-accept');
                },
                start: function (event, ui) {
                    $(this).addClass('pm-accept').closest('.pm-layout').addClass('pm-accept');
                    $('#pm-iframe').contents().find('.pm-preview').addClass('pm-dragging');
                },
                stop: function (event, ui) {
                    $(document).scrollTop(0); // Fix for dissappearing .navbar.
                    console.log('Stopped sorting:', ui.placeholder);
                    $('#pm-iframe').contents().find('.pm-preview').removeClass('pm-dragging');
                    $('#pm-iframe').contents().find('.pm-block, .pm-placeholder').removeClass('pm-accept');
                    paintEmptyPlaceholders();

                    if (!$(ui.item).hasClass('pm-block-item')) {
                        //Push order of blocks to backend service
                        var sortOrder = $(ui.item).closest('.pm-placeholder').sortable('toArray', {attribute: 'data-pm-block-id'});
                        var blockId = $(ui.item).attr('data-pm-block-id');
                        var parentId = $(ui.item).parent().closest('.pm-layout').attr('data-pm-block-id');
                        var placeholderKey = $(ui.item).closest('.pm-placeholder').attr('data-pm-placeholder-key');
                        console.log('Posting re-sort of items to backend service', sortOrder, blockId, parentId, placeholderKey);

                        $.post(Routing.generate('opifer_content_api_contenteditor_move_block'), {sort: sortOrder, id: blockId, rootVersion: version, parent: parentId, placeholder: placeholderKey}).done(function (data, textStatus, request) {
                            //console.log("Block moved", data);
                            updateVersionPicker();
                        }).error(function(data){
                            showAPIError(data);
                        });
                    }
                }
            });

            return this;
        };


        // Find placeholders for this layout specifically.
        var highlightPlaceholders = function(layoutId) {
            $('#pm-iframe').contents().find('.pm-placeholder').removeClass('highlight');
            $(this).find('.pm-placeholder').each(function(index) {
                if ($(this).closest('.pm-layout').attr('data-pm-block-id') == layoutId) {
                    $(this).addClass('highlight');
                }
            });
        };

        var destroySortables = function () {
            sortables().sortable('destroy');

            return this;
        };

        var postBlockForm = function ($form, callback) {
            $form.css('opacity',.5);
            var values =  $form.serialize();

            $.ajax({
                type: $form.attr('method'),
                url: $form.attr('action'),
                data: values,
                success: function (data) {
                    callback(data);
                }
            }).error(function(data){
                showAPIError(data);
            });
        };

        var publish = function() {
            bootbox.dialog({
                title: 'Confirm publication of content',
                message: '<p>Are you sure you want to put this version live?</p> <div class="well">'+ $('#pm-version-picker a[data-pm-version='+version+']').html()+'</div>',
                buttons: {
                    cancel: {
                        label: "Cancel",
                        className: "btn-default"
                    },
                    ok: {
                        label: 'Publish',
                        className: 'btn-primary',
                        callback: function() {
                            version_published = version;
                            $.post(Routing.generate('opifer_content_api_contenteditor_publish'), {id: ownerId, version: version}).done(function (data, textStatus, request) {
                                updateVersionPicker();
                                bootbox.alert("Published.", function() {});
                            });
                        }
                    }
                }
            });
        };

        var updateVersionPicker = function() {
            $.get(Routing.generate('opifer_content_contenteditor_version_picker', {id: ownerId, current: version, published: version_published})).done(function(data){
                $('#pm-version-picker').replaceWith(data);
            });
        }

        var loadVersion = function(versionToLoad) {
            isLoading();
            version = versionToLoad;
            $('#pm-iframe').attr('src', Routing.generate('opifer_content_contenteditor_view', {id: ownerId, version: versionToLoad}));
            updateVersionPicker();
        };

        return {
            onReady: onReady,
            isLoading: isLoading,
            isNotLoading: isNotLoading,
            refreshBlock: refreshBlock,
            selectBlock: selectBlock,
            unselectBlock: unselectBlock,
            getBlockElement: getBlockElement,
            paintEmptyPlaceholders: paintEmptyPlaceholders,
            createBlock: createBlock,
            editBlock: editBlock,
            deleteBlock: deleteBlock,
            closeEditBlock: closeEditBlock,
            destroySortables: destroySortables,
            postBlockForm: postBlockForm,
            onClientReady: onClientReady
        };
    })();

    pagemanager.onReady();
});