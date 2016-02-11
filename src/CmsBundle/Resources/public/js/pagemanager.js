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
        var btnMakeShared = $('.pm-make-shared');
        var btnPublishShared = $('.pm-make-shared');
        var btnRun = $('#pm-btn-run');
        var btnViewContent = $('#pm-btn-viewmode-content');
        var btnViewPreview = $('#pm-btn-viewmode-preview');
        var btnViewLayout = $('#pm-btn-viewmode-layout');
        var toolbar = null;
        var VIEWMODE_CONTENT = 'CONTENT';
        var VIEWMODE_LAYOUT = 'LAYOUT';
        var VIEWMODE_PREVIEW = 'PREVIEW';
        var iFrame = $('#pm-iframe');
        var permalink = null;
        var splitPane = $('.split-pane');
        var settings = {rightColumnWidth: 380};
        var isDragging = false;

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
            version = parseInt($('#pm-document').attr('data-pm-version'));
            versionPublished = parseInt($('#pm-document').attr('data-pm-version-published'));
            permalink = $('#pm-document').attr('data-pm-permalink');

            // Split page library
            splitPane.splitPane();
            splitPane.on('dividerdragend', function (event, data) {
                settings.rightColumnWidth = data.lastComponentSize;
                saveSettings();
            });

            $(document).ajaxStart(function (e) {
                isLoading();
            });

            $(function () {
                $('[data-toggle="tooltip"]').tooltip({trigger:'hover'})
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
            //
            //$('a[href="#tab-history"]').on('shown.bs.tab', function (e) {
            //    e.target // newly activated tab
            //    e.relatedTarget // previous active tab
            //
            //
            //})

            $(document).on('submit', '#pm-block-edit form', function (e) {
                e.preventDefault();
                var id = $('#pm-block-edit').attr('data-pm-block-id');
                postBlockForm($(this), function (data) {
                    $('#pm-block-edit').html(data);
                    // Bootstrap AngularJS app (media library etc) after altering DOM
                    angular.bootstrap($('#pm-block-edit form'), ["MainApp"]);
                    if (id) {
                        refreshBlock(id);
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

            $(document).on('click', '.pm-btn-make-shared', function (e) {
                e.preventDefault();
                makeShared($(this).closest('.pm-toolset-card').attr('data-pm-block-id'));
            });

            $(document).on('click', '.pm-btn-publish-shared', function (e) {
                e.preventDefault();
                publishShared($(this).closest('form').attr('data-pm-block-id'));
            });

            var cookieSettings = Cookies.getJSON('pmSettings');
            jQuery.extend(settings, cookieSettings);

            applySettings();

            window.onbeforeunload = function() {
                if (hasUnsavedChanges) {
                    return "Attention: you will possible lose changes made.";
                }
            };
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

        var applySettings = function () {
            splitPane.splitPane('lastComponentSize', settings.rightColumnWidth);
        };

        var saveSettings = function () {
            Cookies.set('pmSettings', settings);
        };

        var refreshBlock = function (id) {
            $.get(Routing.generate('opifer_content_api_contenteditor_view_block', {type: ownerType, typeId: typeId, id: id})).done(function (data) {
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
            return $('#pm-iframe').contents().find('*[data-pm-block-id="' + id + '"]');
        };

        var selectBlock = function (id) {
            $('#pm-iframe').contents().find('.pm-block').removeClass('selected');
            getBlockElement(id).addClass('selected');
        };

        var getBlockReferenceId = function (id) {
            var blockElement = getBlockElement(id);
            var attr = blockElement.attr('data-pm-block-reference-id');
            if (typeof attr !== typeof undefined && attr !== false) {
                return attr;
            }

            return false;
        };

        var unselectBlock = function (id) {
            $('#pm-iframe').contents().find('.pm-block').removeClass('selected');
        };

        var lockEditing = function() {
            btnPublish.prop("disabled", true);
            btnDiscard.prop("disabled", true);
            btnViewContent.addClass('disabled');
            //btnViewLayout.addClass('disabled');
            setViewMode(VIEWMODE_PREVIEW);
        };

        var unlockEditing = function() {
            btnPublish.prop("disabled", false);
            btnDiscard.prop("disabled", false);
            btnViewContent.removeClass('disabled');
            //btnViewLayout.removeClass('disabled');
        };

        var setViewMode = function(mode) {
            if (mode == VIEWMODE_CONTENT) {
                $('.pm-tools-blockset').addClass('hidden');
                $('#pm-tools-blocks').removeClass('hidden');
                $('#pm-tools-layouts').removeClass('hidden');
            //} else if (mode == VIEWMODE_LAYOUT) {
            //    $('.pm-tools-blockset').addClass('hidden');
            //    $('#pm-tools-layouts').removeClass('hidden');
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

                var refId = getBlockReferenceId(id);
                var editId = (refId) ? refId : id;

                isLoadingEdit();
                selectBlock(id);

                $.get(Routing.generate('opifer_content_contenteditor_edit_block', {id: editId})).success(function (data) {
                    $('#pm-block-edit').html(data);

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
                angular.bootstrap($('#pm-block-edit form'), ["MainApp"]);
            });

            $('#pm-block-edit').removeClass('hidden');

            return this;
        };

        var isLoadingEdit = function() {
            $('#pm-block-edit').html('<div class="loading panel-body"><span>Loading…</span></div>');
        };

        var closeEditBlock = function (id) {
            unselectBlock(id);
            $('#pm-block-edit').attr('data-pm-block-id', '').addClass('hidden');
        };

        var clearEditBlock = function () {
            $('#pm-block-edit').attr('data-pm-block-id', 0);
            $('#pm-block-edit').addClass('hidden');
        };

        var makeShared = function (id) {
            bootbox.dialog({
                title: 'Sharing block',
                message: '<p>Are you sure you want to convert this block to make it shared?</p>',
                buttons: {
                    cancel: {
                        label: "Cancel",
                        className: "btn-default"
                    },
                    ok: {
                        label: 'Convert',
                        className: 'btn-primary',
                        callback: function() {

                            $.post(Routing.generate('opifer_content_api_contenteditor_make_shared', {type: ownerType, typeId: typeId, ownerId: ownerId}), {id: id}).done(function (data, textStatus, request) {
                                var viewUrl = request.getResponseHeader('Location');
                                var newId = data.id;
                                var reference = getBlockElement(id);

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
                            });
                        }
                    }
                }
            });
        };

        // Delete block
        var deleteBlock = function (id) {
            $.ajax({
                url: Routing.generate('opifer_content_api_contenteditor_remove_block', {id: id}),
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
            $.post(Routing.generate('opifer_content_api_contenteditor_create_block', {type: ownerType, typeId: typeId, ownerId: ownerId}), block).done(function (data, textStatus, request) {
                var viewUrl = request.getResponseHeader('Location');
                var id = data.id;

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
            });
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

            link = document.createElement('link');
            link.type = "text/css";
            link.rel = "stylesheet";
            link.href = '/bundles/opifercms/css/pagemanager-client.css';
            link.onload = function() {
                showToolbars();
            };

            iFrame.contents().find('body').append(link);

            iFrame.contents().find('.pm-block').each(function() {
                console.log($(this).attr('data-pm-block-owner-id'), ownerId);
                if ($(this).attr('data-pm-block-owner-id') != ownerId) {
                    $(this).addClass('pm-inherited');
                }
            });

            $('.pm-placeholder', iFrame.contents()).on('mousemove mouseup mousedown', function (event) {
                $(parent.document).trigger(event);
            });

            iFrame.contents().find('body').on('click', '.pm-btn-edit', function(e) {
                e.preventDefault();
                var id = $(this).closest('.pm-toolbar').attr('data-pm-control-id');
                editBlock(id);
            });

            // Delete block (click)
            iFrame.contents().find('body').on('click', '.pm-btn-delete', function(e) {
                e.preventDefault();
                var id = $(this).closest('.pm-toolbar').attr('data-pm-control-id');
                deleteBlock(id);
            });


            iFrame.contents().find('body').append('' +
                '<div id="pm-toolbar" class="pm pm-toolbar hidden">' +
                    //'   <div class="pm-toolbar-text"><code>{{ block.id }}</code> {{ block_service.name(block) }}</div>' +
                '<div class="pm-btn-group">' +
                '<span class="pm-btn pm-btn-icon pm-btn-label"><i class="material-icons"></i></span>' +
                '<a href="#" class="pm-btn pm-btn-icon pm-btn-drag"><i class="material-icons">drag_handle</i></a>' +
                '<a href="#" class="pm-btn pm-btn-icon pm-btn-delete"><i class="material-icons">delete</i></a>' +
                '<a href="#" class="pm-btn pm-btn-icon pm-btn-edit"><i class="material-icons">create</i></a>' +
                '</div>' +
                '</div>');
            toolbar = iFrame.contents().find('#pm-toolbar');


            //iFrame.contents().find('*[data-pm-block-manage]').append('<div class="pm-handle" style="background: red; width: 50px; height: 50px;"></div>');
            //destroySortables();
            //sortables();

            // Position Toolbar on Block when hovered
            iFrame.contents().find('body').on('mouseover', '*[data-pm-block-manage]', function (e) {
                e.stopPropagation();

                if (isDragging == false) {
                    showToolbar(this);
                }
            });


            // Signal drag to Block from Toolbar
            iFrame.contents().find('body').on('mousedown', '.pm-btn-drag', function (e) {
                e.preventDefault();
                var id = $(this).closest('.pm-toolbar').attr('data-pm-control-id');
                var element = iFrame.contents().find('*[data-pm-block-id=\''+id+'\'] > .pm-handle');

                e.type = "mousedown";
                e.target = element[0];

                element.trigger(e);
                return false;
            });

            sortables();

            $('.pm-block-item').draggable({
                appendTo: '#pm-list-group-container',
                helper: 'clone',
                iframeFix: true,
                connectToSortable: sortables(),
                start: function (event, ui) {
                    isDragging = true;
                    hideToolbar();

                    ui.helper.animate({
                        width: 330,
                        height: 80
                    });

                    //$('.pm-preview').addClass('pm-dragging');
                },
                stop: function () {
                    isDragging = false;
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


        var showToolbar = function (element) {
            iFrame.contents().find('*[data-pm-block-manage]').removeClass('pm-hovered');
            $(element).addClass('pm-hovered');

            var offset = $(element).offset();
            var width = $(element).width();
            var pos = (iFrame.contents().find('body').scrollTop() > offset.top) ? 'fixed' : 'absolute';
            var dir = ($(element).attr('data-pm-block-type') == 'layout') ? 'right' : 'left';
            console.log($(element).attr('data-pm-block-type'), dir);
            var side = (dir == 'right') ? (iFrame.contents().width() - (offset.left+width-1)) : offset.left+1;
            var top = (pos == 'fixed') ? 1 : offset.top + 1;
            toolbar.attr('data-pm-control-id', $(element).attr('data-pm-block-id')).css('position', pos).css('left', 'auto').css('right', 'auto').css('top', top).css(dir, side).removeClass('hidden');
            iFrame.contents().find('body').find('.remove').remove();
            $(element).find('.pm-handle').remove();
            $('<div class="pm-handle" />').appendTo(element);
            var toolData = $.parseJSON($(element).attr('data-pm-tool'));
            console.log(toolData);
            toolbar.find('.pm-btn-label .material-icons').text(toolData.icon);
        };

        var hideToolbar = function () {
            iFrame.contents().find('*[data-pm-block-manage]').removeClass('pm-hovered');
            toolbar.addClass('hidden');
        };

        //
        // Create a block by dropping in a placeholder
        //
        var sortables = function () {
            return iFrame.contents().find('.pm-placeholder').sortable({
                handle: '.pm-handle',
                revert: false,
                connectWith: iFrame.contents().find('.pm-placeholder'),
                //greedy: true,
                iframeFix: false,
                placeholder: 'pm-placeholder-droparea',
                forcePlaceholderSize: true,
                tolerance: "pointer",
                cursorAt: { top: 5, left: 5 },
                receive: function (event, ui) {
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
                    //console.log(event, ui);
                    isDragging = true;
                    hideToolbar();
                },
                stop: function (event, ui) {
                    $(document).scrollTop(0); // Fix for dissappearing .navbar.

                    iFrame.contents().find('.pm-preview').removeClass('pm-dragging');
                    iFrame.contents().find('.pm-block, .pm-placeholder').removeClass('pm-accept');
                    isDragging = false;
                    paintEmptyPlaceholders();

                    if (!$(ui.item).hasClass('pm-block-item')) {
                        //Push order of blocks to backend service
                        var sortOrder = $(ui.item).closest('.pm-placeholder').sortable('toArray', {attribute: 'data-pm-block-id'});
                        var blockId = $(ui.item).attr('data-pm-block-id');
                        var parentId = $(ui.item).parent().closest('*[data-pm-block-id]').attr('data-pm-block-id');
                        var placeholderKey = $(ui.item).closest('.pm-placeholder').attr('data-pm-placeholder-key');

                        $.post(Routing.generate('opifer_content_api_contenteditor_move_block'), {sort: sortOrder, id: blockId, parent: parentId, placeholder: placeholderKey}).done(function (data, textStatus, request) {
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
                    updateVersionPicker();
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
                            $.post(Routing.generate('opifer_content_api_contenteditor_discard'), {id: ownerId}).done(function (data, textStatus, request) {
                                loadVersion(version);
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
                            $.post(Routing.generate('opifer_content_api_contenteditor_publish'), {id: ownerId, type: ownerType, typeId: typeId}).done(function (data, textStatus, request) {
                                version++;
                                updateVersionPicker();
                                loadVersion(version);
                                bootbox.alert("Published.", function() {

                                });
                            }).error(function(data){
                                showAPIError(data);
                            });
                        }
                    }
                }
            });
        };

        var publishShared = function(id) {
            $.post(Routing.generate('opifer_content_api_contenteditor_publish_shared'), {id: id}).done(function() {
                bootbox.alert("Published.", function() {});
            }).error(function(data){
                showAPIError(data);
            });
        }

        var updateVersionPicker = function() {
            $.get(Routing.generate('opifer_content_contenteditor_version_picker', {id: ownerId, current: version, published: versionPublished})).done(function(data){
                $('#pm-version-picker').replaceWith(data);
            });
        }

        var loadVersion = function(versionToLoad) {
            isLoading();
            version = versionToLoad;
            iFrame.attr('src', Routing.generate('opifer_content_contenteditor_view', {type: ownerType, id: typeId, version: versionToLoad}));
            updateVersionPicker();
            clearEditBlock();
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