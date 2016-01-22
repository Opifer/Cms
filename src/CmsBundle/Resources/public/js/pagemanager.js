var pagemanager;
var CKEDITOR_BASEPATH = '/bundles/opifercms/components/ckeditor/';


$(document).ready(function() {
    //
    // PageManager: general encapsulation, you know, to organise and stuff
    //
    pagemanager = (function () {
        var client = null;
        var ownerType = 'template';
        var typeId = 0;
        var ownerId = 0;
        var mprogress = null;
        var hasUnsavedChanges = false;
        var version = 0;
        var versionPublished = 0;
        var btnPublish = $('#pm-btn-publish');
        var btnDiscard = $('#pm-btn-discard');
        var btnRun = $('#pm-btn-run');
        var btnViewContent = $('#pm-btn-viewmode-content');
        var btnViewPreview = $('#pm-btn-viewmode-preview');
        var btnViewLayout = $('#pm-btn-viewmode-layout');
        var VIEWMODE_CONTENT = 'CONTENT';
        var VIEWMODE_LAYOUT = 'LAYOUT';
        var VIEWMODE_PREVIEW = 'PREVIEW';
        var iFrame = $('#pm-iframe');
        var permalink = null;

        var onReady = function () {
            mprogress = new Mprogress({
                template: 3
            });
            isLoading();

            // Toggle view between Content, Preview and Layout
            $('input[name="viewmode"]:radio').change(function () {
                pagemanager.unselectBlock();
                setViewMode($('input[name="viewmode"]:checked').val());
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


            ownerType = $('#pm-document').attr('data-pm-type');
            typeId = $('#pm-document').attr('data-pm-type-id');
            ownerId = $('#pm-document').attr('data-pm-id');
            version = $('#pm-document').attr('data-pm-version');
            versionPublished = $('#pm-document').attr('data-pm-version-published');
            permalink = $('#pm-document').attr('data-pm-permalink');

            // Split page library
            $('.split-pane').splitPane();

            $(document).ajaxStart(function (e) {
                isLoading();
            });

            // Resize iframe based on their contents (for block editing view)
            iFrame.attr('src', iFrame.attr('data-url'));
            iFrame.bind('load', function () {
                onClientReady();
            });

            loadRunButton();

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

            $(document).on('click', '.pm-block .pm-btn-edit', function (e) {
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

            $(document).on('click', '#pm-btn-discard', function(e) {
                e.preventDefault();
                discardChanges();
            });

            window.onbeforeunload = function() {
                if (hasUnsavedChanges) {
                    return "Attention: you will possible lose changes made.";
                }
            }
        };

        //var client = function () {
        //    return document.getElementById('pm-iframe').contentWindow['pagemanagerClient'];
        //};

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
            $.get(Routing.generate('opifer_content_api_contenteditor_view_block', {type: ownerType, typeId: typeId, id: id, rootVersion: version})).done(function (data) {
                getBlockElement(id).replaceWith(data.view);
                showToolbars();
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

        var loadRunButton = function () {
            if (permalink) {
                btnRun.attr('href', permalink);
                btnRun.parent().removeClass('hidden');
            }
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

        var lockEditing = function() {
            btnPublish.prop("disabled", true);
            btnDiscard.prop("disabled", true);
            btnViewContent.addClass('disabled');
            btnViewLayout.addClass('disabled');
            setViewMode(VIEWMODE_PREVIEW);
        };

        var unlockEditing = function() {
            btnPublish.prop("disabled", false);
            btnDiscard.prop("disabled", false);
            btnViewContent.removeClass('disabled');
            btnViewLayout.removeClass('disabled');
        };

        var setViewMode = function(mode) {
            if (mode == VIEWMODE_CONTENT) {
                $('.pm-tools-blockset').addClass('hidden');
                $('#pm-tools-blocks').removeClass('hidden');
            } else if (mode == VIEWMODE_LAYOUT) {
                $('.pm-tools-blockset').addClass('hidden');
                $('#pm-tools-layouts').removeClass('hidden');
            } else {
                $('.pm-tools-blockset').addClass('hidden');
            }

            iFrame.contents().find('body').removeClass (function (index, css) {
                return (css.match (/(^|\s)pm-viewmode-\S+/g) || []).join(' ');
            }).addClass('pm-viewmode-'+mode.toLowerCase());

            return this;
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
                }).fail(function(data){
                    showAPIError(data);
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

            $.post(Routing.generate('opifer_content_api_contenteditor_create_block', {type: ownerType, typeId: typeId, ownerId: ownerId, rootVersion: version}), block).done(function (data, textStatus, request) {
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
                    showToolbars();
                }).fail(function(data){
                    reference.remove();
                    showAPIError(data);
                });
            }).fail(function(data){
                reference.remove();
                showAPIError(data);
            });;
        };

        var showToolbars = function() {
            iFrame.contents().find('.pm-toolbar').removeClass('hidden');
        }

        var showAPIError = function(data) {
            var message = (typeof data.responseJSON === "undefined") ? data.responseText : data.responseJSON.error;
            bootbox.dialog({
                title: data.statusText,
                message: '<code>' + message + '</code>',
                buttons: {
                    ok: {
                        label: 'Ok',
                        className: 'btn-primary'
                    }
                }
            });
        };

        var onClientReady = function () {
            sortables();

            link = document.createElement('link');
            link.type = "text/css";
            link.rel = "stylesheet";
            link.href = '/bundles/opifercms/css/pagemanager-client.css';
            link.onload = function() {
                showToolbars();
            };

            iFrame.contents().find('body').append(link);

            $('.pm-placeholder', iFrame.contents()).on('mousemove mouseup', function (event) {
                $(parent.document).trigger(event);
            });

            iFrame.contents().find('body').on('click', '.pm-block .pm-btn-edit', function(e) {
                e.preventDefault();
                var id = $(this).closest('.pm-block').attr('data-pm-block-id');
                editBlock(id);
            });

            // Delete block (click)
            iFrame.contents().find('body').on('click', '.pm-block .pm-btn-delete', function(e) {
                e.preventDefault();
                var id = $(this).closest('.pm-block').attr('data-pm-block-id');
                deleteBlock(id);
            });


            iFrame.contents().find('.pm-block').mouseover(function() {
                iFrame.contents().find('.pm-block').removeClass('hovered');
                $(this).addClass('hovered');
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
                    $(document).scrollTop(0); // Fix for disappearing .navbar.
                    //$('.pm-preview').removeClass('pm-dragging');
                    //$('.pm-layout').removeClass('pm-layout-accept'); // cleaning up just to be sure
                },
                drag: function (event, ui) {
                    //ui.position.top += 200;
                    //console.log(ui.offset.top, ui.offset.left, ui);
                }
            });

            //$('.pm-block-item').on('dragstop',autoResizeFrame);
            console.log('Server: client reports ready.');
            setViewMode(VIEWMODE_CONTENT);

            if (version <= versionPublished) {
                lockEditing();
            }

            isNotLoading();
        };

        //
        // Create a block by dropping in a placeholder
        //
        var sortables = function () {
            return iFrame.contents().find('.pm-placeholder').sortable({
                handle: '.pm-toolbar',
                revert: false,
                distance: 10,
                connectWith: iFrame.contents().find('.pm-placeholder'),
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
                        var className = $(ui.item).attr('data-pm-block-type');
                        var parent = $(this).parent().closest('.pm-layout').attr('data-pm-block-id');
                        var placeholderKey = $(this).closest('.pm-placeholder').attr('data-pm-placeholder-key');
                        var data = $(ui.item).attr('data-pm-block-data');

                        $(ui.item).attr('data-pm-block-id', '0'); // Set default so toArray won't trip and fall below
                        var sortOrder = $(this).sortable('toArray', {attribute: 'data-pm-block-id'});

                        createBlock({className: className, parent: parent, placeholder: placeholderKey, sort: sortOrder, data: data}, reference);
                    }
                },
                over: function (event, ui) {
                    //if ($.ui.ddmanager.current)
                    //    $.ui.ddmanager.prepareOffsets($.ui.ddmanager.current, null);
                    iFrame.contents().find('.pm-block, .pm-placeholder').removeClass('pm-accept');
                    $(this).addClass('pm-accept').closest('.pm-layout').addClass('pm-accept');

                    var layoutId = $(this).addClass('pm-accept').closest('.pm-layout').attr('data-pm-block-id');
                    highlightPlaceholders(layoutId);
                },
                out: function (event, ui) {
                    //if ($.ui.ddmanager.current)
                    //    $.ui.ddmanager.prepareOffsets($.ui.ddmanager.current, null);
                    iFrame.contents().find('.pm-block, .pm-placeholder').removeClass('pm-accept');
                },
                start: function (event, ui) {
                    $(this).addClass('pm-accept').closest('.pm-layout').addClass('pm-accept');
                    iFrame.contents().find('.pm-preview').addClass('pm-dragging');
                },
                stop: function (event, ui) {
                    $(document).scrollTop(0); // Fix for dissappearing .navbar.
                    console.log('Stopped sorting:', ui.placeholder);
                    iFrame.contents().find('.pm-preview').removeClass('pm-dragging');
                    iFrame.contents().find('.pm-block, .pm-placeholder').removeClass('pm-accept');
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
            iFrame.contents().find('.pm-placeholder').removeClass('highlight');
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


        var discardChanges = function() {
            bootbox.dialog({
                title: 'Discard changes',
                message: '<p>Are you sure you want to discard all changes of version ' + version + '?</p>',
                buttons: {
                    cancel: {
                        label: "Cancel",
                        className: "btn-default"
                    },
                    ok: {
                        label: 'Discard',
                        className: 'btn-danger',
                        callback: function() {
                            $.post(Routing.generate('opifer_content_api_contenteditor_discard'), {id: ownerId, version: version}).done(function (data, textStatus, request) {
                                updateVersionPicker();
                                bootbox.alert("Discarded.", function() {});
                            }).error(function(data){
                                showAPIError(data);
                            });
                        }
                    }
                }
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
                            versionPublished = version;
                            $.post(Routing.generate('opifer_content_api_contenteditor_publish'), {id: ownerId, version: version}).done(function (data, textStatus, request) {
                                updateVersionPicker();
                                bootbox.alert("Published.", function() {});
                            }).error(function(data){
                                showAPIError(data);
                            });
                        }
                    }
                }
            });
        };

        var updateVersionPicker = function() {
            $.get(Routing.generate('opifer_content_contenteditor_version_picker', {id: ownerId, current: version, published: versionPublished})).done(function(data){
                $('#pm-version-picker').replaceWith(data);
            });
        }

        var loadVersion = function(versionToLoad) {
            isLoading();
            version = versionToLoad;
            iFrame.attr('src', Routing.generate('opifer_content_contenteditor_view', {id: ownerId, version: versionToLoad}));
            updateVersionPicker();
            version <= versionPublished ? lockEditing() : unlockEditing();
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