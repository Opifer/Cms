var pagemanager;
var CKEDITOR_BASEPATH = '/bundles/opifercms/components/ckeditor/';


//
// Override refreshPositions
//
$.widget( "ui.sortable", $.ui.sortable, {
    refreshPositions: function (fast) {

        this._super();

        // Determine whether items are being displayed horizontally
        this.floating = this.items.length ?
        this.options.axis === "x" || this._isFloating(this.items[0].item) :
            false;

        //This has to be redone because due to the item being moved out/into the offsetParent, the offsetParent's position will change
        if (this.offsetParent && this.helper) {
            this.offset.parent = this._getParentOffset();
        }

        var i, item, t, p;

        for (i = this.items.length - 1; i >= 0; i--) {
            item = this.items[i];

            //We ignore calculating positions of all connected containers when we're not over them
            if (item.instance !== this.currentContainer && this.currentContainer && item.item[0] !== this.currentItem[0]) {
                continue;
            }

            t = this.options.toleranceElement ? $(this.options.toleranceElement, item.item) : item.item;

            if (!fast) {
                item.width = t.outerWidth();
                item.height = t.outerHeight();
            }

            p = t.offset();
            // CHANGED
            if (window.iFrameSortable) {
                p.top -= $('#pm-iframe').contents().scrollTop();
            }
            // CHANGED (END)
            item.left = p.left;
            item.top = p.top; // Remove iFrame scroll position (only change)
        }

        if (this.options.custom && this.options.custom.refreshContainers) {
            this.options.custom.refreshContainers.call(this);
        } else {
            for (i = this.containers.length - 1; i >= 0; i--) {
                p = this.containers[i].element.offset();

                // CHANGED
                if (window.iFrameSortable) {
                    p.top -= $('#pm-iframe').contents().scrollTop();
                }
                //console.log(p.top, $('#pm-iframe').contents().scrollTop());
                // CHANGED (END)
                this.containers[i].containerCache.left = p.left;
                this.containers[i].containerCache.top = p.top;
                this.containers[i].containerCache.width = this.containers[i].element.outerWidth();
                this.containers[i].containerCache.height = this.containers[i].element.outerHeight();
            }
        }

        return this;
    },

    _contactContainers: function (event) {
        var i, j, dist, itemWithLeastDistance, posProperty, sizeProperty, cur, nearBottom, floating, axis,
            innermostContainer = null,
        // CHANGED
            innermostZIndex = null,
        // CHANGED (END)
            innermostIndex = null;

        // get innermost container that intersects with item
        for (i = this.containers.length - 1; i >= 0; i--) {

            // never consider a container that's located within the item itself
            if ($.contains(this.currentItem[0], this.containers[i].element[0])) {
                continue;
            }

            var zIndex = 0;
            try {
                zIndex = $(this.containers[i].element[0]).zIndex();
            } catch (err) {
                zIndex = 0;
            }
            //console.log(zIndex, this.containers[i].element[0]);

            if (this._intersectsWith(this.containers[i].containerCache)) {
                // if we've already found a container and it's more "inner" than this, then continue
                // or if we've already found a container that has a z-index larger than this, then also continue
                if (innermostContainer && ($.contains(this.containers[i].element[0], innermostContainer.element[0]) || zIndex < innermostZIndex)) {
                    continue;
                }

                innermostContainer = this.containers[i];
                // CHANGED
                innermostZIndex = zIndex;
                // CHANGED (END)
                innermostIndex = i;

            } else {
                // container doesn't intersect. trigger "out" event if necessary
                if (this.containers[i].containerCache.over) {
                    this.containers[i]._trigger("out", event, this._uiHash(this));
                    this.containers[i].containerCache.over = 0;
                }
            }

        }

        // if no intersecting containers found, return
        if (!innermostContainer) {
            return;
        }

        // move the item into the container if it's not there already
        if (this.containers.length === 1) {
            if (!this.containers[innermostIndex].containerCache.over) {
                this.containers[innermostIndex]._trigger("over", event, this._uiHash(this));
                this.containers[innermostIndex].containerCache.over = 1;
            }
        } else {

            //When entering a new container, we will find the item with the least distance and append our item near it
            dist = 10000;
            itemWithLeastDistance = null;
            floating = innermostContainer.floating || this._isFloating(this.currentItem);
            posProperty = floating ? "left" : "top";
            sizeProperty = floating ? "width" : "height";
            axis = floating ? "clientX" : "clientY";

            for (j = this.items.length - 1; j >= 0; j--) {
                if (!$.contains(this.containers[innermostIndex].element[0], this.items[j].item[0])) {
                    continue;
                }
                if (this.items[j].item[0] === this.currentItem[0]) {
                    continue;
                }

                cur = this.items[j].item.offset()[posProperty];
                nearBottom = false;
                if (event[axis] - cur > this.items[j][sizeProperty] / 2) {
                    nearBottom = true;
                }

                if (Math.abs(event[axis] - cur) < dist) {
                    dist = Math.abs(event[axis] - cur);
                    itemWithLeastDistance = this.items[j];
                    this.direction = nearBottom ? "up" : "down";
                }
            }

            //Check if dropOnEmpty is enabled
            if (!itemWithLeastDistance && !this.options.dropOnEmpty) {
                return;
            }

            if (this.currentContainer === this.containers[innermostIndex]) {
                if (!this.currentContainer.containerCache.over) {
                    this.containers[innermostIndex]._trigger("over", event, this._uiHash());
                    this.currentContainer.containerCache.over = 1;
                }
                return;
            }

            itemWithLeastDistance ? this._rearrange(event, itemWithLeastDistance, null, true) : this._rearrange(event, null, this.containers[innermostIndex].element, true);
            this._trigger("change", event, this._uiHash());
            this.containers[innermostIndex]._trigger("change", event, this._uiHash(this));
            this.currentContainer = this.containers[innermostIndex];

            //Update the placeholder
            this.options.placeholder.update(this.currentContainer, this.placeholder);

            this.containers[innermostIndex]._trigger("over", event, this._uiHash(this));
            this.containers[innermostIndex].containerCache.over = 1;
        }
    }
});

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

            //$(document).on('click', '.pm-block .pm-btn-edit', function (e) {
            //    e.preventDefault();
            //    editBlock($(this).closest('.pm-block').attr('data-pm-block-id'));
            //});

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

            $(document).on('click', '.pm-btn-localize', function(e) {
                e.preventDefault();
                scrollToBlock($(this).attr('data-pm-block-id'));
            });

            $(document).on('click', '.pm-btn-edit', function(e) {
                e.preventDefault();
                editBlock($(this).attr('data-pm-block-id'), 'general');
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

        var scrollToBlock = function (id) {
            selectBlock(id);
            iFrame.contents().find('html, body').animate({
                scrollTop: getBlockElement(id).offset().top
            }, 500);
        };

        var refreshBlock = function (id) {
            $.get(Routing.generate('opifer_content_api_contenteditor_view_block', {type: ownerType, typeId: typeId, id: id})).done(function (data) {
                getBlockElement(id).replaceWith(data.view);
                showToolbars();
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
            var element = getBlockElement(id).addClass('selected');
            showToolbar(element);
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
        var editBlock = function (id, tab) {
            if ($('#pm-block-edit').attr('data-pm-block-id') != id) {
                $('#pm-block-edit').attr('data-pm-block-id', id);

                var refId = getBlockReferenceId(id);
                var editId = (refId) ? refId : id;

                isLoadingEdit();
                selectBlock(id);

                $('.pm-toolset-body').animate({
                    scrollTop: 0
                }, 200);
                $.get(Routing.generate('opifer_content_contenteditor_edit_block', {id: editId})).success(function (data) {
                    $('#pm-block-edit').html(data);

                    // Bootstrap AngularJS app (media library etc) after altering DOM
                    angular.bootstrap($('#pm-block-edit form'), ["MainApp"]);

                    if (tab) {
                        $('#pm-block-edit .nav-tabs a[href="#block-'+tab+'"]').tab('show');
                    }
                }).fail(function(data){
                    showAPIError(data);
                });
            } else if (tab) {
                $('#pm-block-edit .nav-tabs a[href="#block-'+tab+'"]').tab('show');
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
                                    updateVersionPicker();
                                    showToolbars();
                                    sortables();
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
                    hideToolbar();
                    getBlockElement(id).remove();
                    pagemanager.closeEditBlock(id);
                    updateVersionPicker();
                    loadToC(sortables);
                }
            }).error(function(data){
                showAPIError(data);
            });
        };

        //
        // Call API to create a new block
        //
        var createBlock = function (block) {
            var parentId = block.parent;
            var idx = (block.sort.indexOf("") != -1) ? block.sort.indexOf("") : block.sort.indexOf("0");
            var placeholder = block.placeholder;
            $.post(Routing.generate('opifer_content_api_contenteditor_create_block', {type: ownerType, typeId: typeId, ownerId: ownerId}), block).done(function (data, textStatus, request) {
                var viewUrl = request.getResponseHeader('Location');
                var id = data.id;

                $.get(viewUrl).done(function (data) {
                    $('body').remove('.pm-block-insert');
                    var reference = iFrame.contents().find('.pm-block-insert');

                    if (reference.length) {
                        $(reference).replaceWith(data.view);
                    } else {
                        var container = iFrame.contents().find('body *[data-pm-placeholder-id="' + parentId + '"][data-pm-placeholder-key="'+placeholder+'"]');
                        if (container.length) {
                            if (idx == 0) {
                                container.prepend(data.view);
                            } else {
                                var siblings = container.children();
                                (idx >= siblings.length) ? siblings.eq(idx - 1).after(data.view) : siblings.eq(idx).before(data.view);
                            }
                        }
                    }

                    editBlock(id);
                    updateVersionPicker();
                    showToolbars();
                    loadToC(sortables);
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
                //showToolbars();
            };

            iFrame.contents().find('body').append(link);

            iFrame.contents().find('*[data-pm-block-manage="true"]').each(function() {
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
                editBlock(id, 'general');
            });

            iFrame.contents().find('body').on('click', '.pm-btn-properties', function(e) {
                e.preventDefault();
                var id = $(this).closest('.pm-toolbar').attr('data-pm-control-id');
                editBlock(id, 'properties');
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
                '<a href="#" class="pm-btn pm-btn-icon pm-btn-drag" title="Drag this block to a new position"><i class="material-icons">drag_handle</i></a>' +
                '<a href="#" class="pm-btn pm-btn-icon pm-btn-edit" title="Edit contents of this block"><i class="material-icons">create</i></a>' +
                '<a href="#" class="pm-btn pm-btn-icon pm-btn-properties" title="Make changes to properties of this block"><i class="material-icons">settings</i></a>' +
                '<a href="#" class="pm-btn pm-btn-icon pm-btn-delete" title="Delete this block"><i class="material-icons">delete</i></a>' +
                '</div>' +
                '</div>');
            toolbar = iFrame.contents().find('#pm-toolbar');

            // Position Toolbar on Block when hovered
            iFrame.contents().find('body').on('mouseover', '*[data-pm-block-manage]', function (e) {
                e.stopPropagation();

                if (isDragging == false) {
                    selectBlock($(this).attr('data-pm-block-id'));
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

            loadToC(sortables);

            setViewMode(VIEWMODE_CONTENT);

            if (version <= versionPublished) {
                lockEditing();
            }


            isNotLoading();
        };


        var showToolbar = function (element) {
            toolbar.addClass('hidden');
            iFrame.contents().find('*[data-pm-block-manage]').removeClass('pm-hovered');
            if ($(element).hasClass('pm-inherited')) {
                return;
            }
            $(element).addClass('pm-hovered');

            var offset = $(element).offset();
            var width = $(element).width();
            var pos = (iFrame.contents().find('body').scrollTop() > offset.top) ? 'fixed' : 'absolute';
            var dir = ($(element).attr('data-pm-block-type') == 'layout') ? 'right' : 'left';

            var side = (dir == 'right') ? (iFrame.contents().width() - (offset.left+width-1)) : offset.left+1;
            var top = (pos == 'fixed') ? 1 : offset.top + 1;
            toolbar.attr('data-pm-control-id', $(element).attr('data-pm-block-id')).css('position', pos).css('left', 'auto').css('right', 'auto').css('top', top).css(dir, side).removeClass('hidden');
            iFrame.contents().find('body').find('.remove').remove();
            $(element).find('.pm-handle').remove();
            $('<div class="pm-handle" />').appendTo(element);

            var toolData = $.parseJSON($(element).attr('data-pm-tool'));
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
            destroySortables();

            var sortStop = function (event, ui) {
                isDragging = false;

                if (!$(ui.item).hasClass('pm-block-item')) {
                    //Push order of blocks to backend service
                    var sortOrder = $(ui.item).closest('.pm-placeholder').sortable('toArray', {attribute: 'data-pm-block-id'});
                    var blockId = $(ui.item).attr('data-pm-block-id');
                    var parentId = $(ui.item).parent().closest('*[data-pm-block-id]').attr('data-pm-block-id');
                    var placeholderKey = $(ui.item).closest('.pm-placeholder').attr('data-pm-placeholder-key');
                    var isIframe = ($(ui.item).closest('body').hasClass('pm-body')) ? false : true;

                    $.post(Routing.generate('opifer_content_api_contenteditor_move_block'), {sort: sortOrder, id: blockId, parent: parentId, placeholder: placeholderKey}).done(function (data, textStatus, request) {
                        //console.log("Block moved", data);
                        updateVersionPicker();
                        if (! isIframe) {
                            refreshAll();
                        } else {
                            loadToC(sortables);
                        }
                    }).error(function(data){
                        showAPIError(data);
                    });
                }
            };

            var sortReceive = function (event, ui) {
                // Create new block
                if ($(ui.item).hasClass('pm-block-item')) {
                    $(ui.item).addClass('pm-block-insert');
                    $(this).find('.pm-block-item').addClass('pm-block-insert');
                    var className = $(ui.item).attr('data-pm-block-type');
                    var parent = $(this).closest('*[data-pm-placeholder-key]').closest('*[data-pm-block-id]').attr('data-pm-block-id');
                    var placeholderKey = $(this).closest('*[data-pm-placeholder-key]').attr('data-pm-placeholder-key');
                    var data = $(ui.item).attr('data-pm-block-data');

                    $(ui.item).attr('data-pm-block-id', '0'); // Set default so toArray won't trip and fall below
                    var sortOrder = $(this).closest('.pm-placeholder').sortable('toArray', {attribute: 'data-pm-block-id'});

                    createBlock({className: className, parent: parent, placeholder: placeholderKey, sort: sortOrder, data: data});
                }
            };

            var frame = iFrame.contents().find('.pm-placeholder').sortable({
                handle: '.pm-handle',
                revert: true,
                iframeFix: true,
                scroll: false,
                connectWith: iFrame.contents().find('.pm-placeholder'),
                tolerance: "pointer",
                cursorAt: { top: 0, left: 0 },
                placeholder: 'pm-drag-placeholder',
                receive: sortReceive,
                start: function (event, ui) {
                    isDragging = true;
                    hideToolbar();
                },
                stop: sortStop
            });

            var toc = $('.pm-placeholder').sortable({
                handle: '.pm-btn-drag',
                revert: true,
                connectWith: '.pm-placeholder',
                tolerance: "pointer",
                cursorAt: { top: 0, left: 0 },
                placeholder: 'pm-drag-placeholder',
                receive: sortReceive,
                start: function (event, ui) {
                    isDragging = true;
                    hideToolbar();
                },
                stop: sortStop
            });

            var sortinstances = $.merge(frame, toc);

            $('.pm-block-item').draggable({
                appendTo: '#pm-list-group-container',
                helper: 'clone',
                iframeFix: true,
                scroll: false,
                connectToSortable: sortinstances,
                cursorAt: { top: 5, left: -5 },
                start: function (event, ui) {
                    isDragging = true;
                    hideToolbar();

                    ui.helper.animate({
                        width: 350,
                        height: 60
                    });
                },
                stop: function () {
                    isDragging = false;
                },
                drag: function (event, ui) {
                    var top = $('#pm-iframe').offset().top;
                    var left = $('#pm-iframe').offset().left;
                    var width = $('#pm-iframe').width();
                    var height = $('#pm-iframe').height();

                    if (event.pageY > top && event.pageY < (top+height) &&
                        event.pageX > left && event.pageX < (left+width)) {
                        window.iFrameSortable = true;
                    } else {
                        window.iFrameSortable = false;
                    }
                }
            });

            return this;
        };

        var destroySortables = function () {
            //if ($('.pm-placeholder').hasClass('ui-sortable') && $('.pm-placeholder').sortable("instance")) {
            //    $('.pm-placeholder').sortable('destroy');
            //}
            //
            //if (iFrame.contents().find('.pm-placeholder').hasClass('ui-sortable') && iFrame.contents().find('.pm-placeholder').sortable("instance")) {
            //    iFrame.contents().find('.pm-placeholder').sortable('destroy');
            //}
            //
            //if ($('.pm-block-item').hasClass('ui-draggable')) {
            //    $('.pm-block-item').draggable('destroy');
            //}

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

        var loadToC = function (callback) {
            $.ajax({
                url: Routing.generate('opifer_content_contenteditor_toc', {type: ownerType, id: typeId, version: version}),
                cache: false
            }).done(function (data) {
                $('#pm-toc').html(data);
                if (callback) callback();
            });
        };

        var refreshAll = function () {
          loadVersion(version);
        };

        return {
            onReady: onReady,
            isLoading: isLoading,
            isNotLoading: isNotLoading,
            refreshBlock: refreshBlock,
            selectBlock: selectBlock,
            unselectBlock: unselectBlock,
            getBlockElement: getBlockElement,
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


