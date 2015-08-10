/*!

Split Pane v0.5.0

Copyright (c) 2014 Simon Hagström

Released under the MIT license
https://raw.github.com/shagstrom/split-pane/master/LICENSE

*/
(function($) {
	
	$.fn.splitPane = function() {
		var $splitPanes = this;
		$splitPanes.each(setMinHeightAndMinWidth);
		$splitPanes.append('<div class="split-pane-resize-shim">');
		var eventType = ('ontouchstart' in document) ? 'touchstart' : 'mousedown';
		$splitPanes.children('.split-pane-divider').html('<div class="split-pane-divider-inner"></div>');
		$splitPanes.children('.split-pane-divider').bind(eventType, mousedownHandler);
		setTimeout(function() {
			// Doing this later because of an issue with Chrome (v23.0.1271.64) returning split-pane width = 0
			// and triggering multiple resize events when page is being opened from an <a target="_blank"> .
			$splitPanes.each(function() {
				$(this).bind('_splitpaneparentresize', createParentresizeHandler($(this)));
			});
			$(window).trigger('resize');
		}, 100);
	};

	var SPLITPANERESIZE_HANDLER = '_splitpaneparentresizeHandler';

	/**
	 * A special event that will "capture" a resize event from the parent split-pane or window.
	 * The event will NOT propagate to grandchildren.
	 */
	jQuery.event.special._splitpaneparentresize = {
		setup: function(data, namespaces) {
			var element = this,
				parent = $(this).parent().closest('.split-pane')[0] || window;
			$(this).data(SPLITPANERESIZE_HANDLER, function(event) {
				var target = event.target === document ? window : event.target;
				if (target === parent) {
					event.type = "_splitpaneparentresize";
					jQuery.event.dispatch.apply(element, arguments);
				} else {
					event.stopPropagation();
				}
			});
			$(parent).bind('resize', $(this).data(SPLITPANERESIZE_HANDLER));
		},
		teardown: function(namespaces) {
			var parent = $(this).parent().closest('.split-pane')[0] || window;
			$(parent).unbind('resize', $(this).data(SPLITPANERESIZE_HANDLER));
			$(this).removeData(SPLITPANERESIZE_HANDLER);
		}
	};

	function setMinHeightAndMinWidth() {
		var $splitPane = $(this),
			$firstComponent = $splitPane.children('.split-pane-component:first'),
			$divider = $splitPane.children('.split-pane-divider'),
			$lastComponent = $splitPane.children('.split-pane-component:last');
		if ($splitPane.is('.fixed-top, .fixed-bottom, .horizontal-percent')) {
			$splitPane.css('min-height', (minHeight($firstComponent) + minHeight($lastComponent) + $divider.height()) + 'px');
		} else {
			$splitPane.css('min-width', (minWidth($firstComponent) + minWidth($lastComponent) + $divider.width()) + 'px');
		}
	}

	function mousedownHandler(event) {
		event.preventDefault();
		var isTouchEvent = event.type.match(/^touch/),
			moveEvent = isTouchEvent ? 'touchmove' : 'mousemove',
			endEvent = isTouchEvent? 'touchend' : 'mouseup',
			$divider = $(this),
			$splitPane = $divider.parent(),
			$resizeShim = $divider.siblings('.split-pane-resize-shim');
		$resizeShim.show();
		$divider.addClass('dragged');
		if (isTouchEvent) {
			$divider.addClass('touch');
		}
		var moveEventHandler = createMousemove($splitPane, pageXof(event), pageYof(event));
		$(document).on(moveEvent, moveEventHandler);
		$(document).one(endEvent, function(event) {
			$(document).unbind(moveEvent, moveEventHandler);
			$divider.removeClass('dragged touch');
			$resizeShim.hide();
		});
	}

	function createParentresizeHandler($splitPane) {
		var splitPane = $splitPane[0],
			firstComponent = $splitPane.children('.split-pane-component:first')[0],
			divider = $splitPane.children('.split-pane-divider')[0],
			lastComponent = $splitPane.children('.split-pane-component:last')[0];
		if ($splitPane.is('.fixed-top')) {
			var lastComponentMinHeight = minHeight(lastComponent);
			return function(event) {
				var maxfirstComponentHeight = splitPane.offsetHeight - lastComponentMinHeight - divider.offsetHeight;
				if (firstComponent.offsetHeight > maxfirstComponentHeight) {
					setTop(firstComponent, divider, lastComponent, maxfirstComponentHeight + 'px');
				}
				$splitPane.resize();
			};
		} else if ($splitPane.is('.fixed-bottom')) {
			var firstComponentMinHeight = minHeight(firstComponent);
			return function(event) {
				var maxLastComponentHeight = splitPane.offsetHeight - firstComponentMinHeight - divider.offsetHeight;
				if (lastComponent.offsetHeight > maxLastComponentHeight) {
					setBottom(firstComponent, divider, lastComponent, maxLastComponentHeight + 'px')
				}
				$splitPane.resize();
			};
		} else if ($splitPane.is('.horizontal-percent')) {
			var lastComponentMinHeight = minHeight(lastComponent),
				firstComponentMinHeight = minHeight(firstComponent);
			return function(event) {
				var maxLastComponentHeight = splitPane.offsetHeight - firstComponentMinHeight - divider.offsetHeight;
				if (lastComponent.offsetHeight > maxLastComponentHeight) {
					setBottom(firstComponent, divider, lastComponent, (maxLastComponentHeight / splitPane.offsetHeight * 100) + '%');
				} else {
					if (splitPane.offsetHeight - firstComponent.offsetHeight - divider.offsetHeight < lastComponentMinHeight) {
						setBottom(firstComponent, divider, lastComponent, (lastComponentMinHeight / splitPane.offsetHeight * 100) + '%');
					}
				}
				$splitPane.resize();
			};
		} else if ($splitPane.is('.fixed-left')) {
			var lastComponentMinWidth = minWidth(lastComponent);
			return function(event) {
				var maxFirstComponentWidth = splitPane.offsetWidth - lastComponentMinWidth - divider.offsetWidth;
				if (firstComponent.offsetWidth > maxFirstComponentWidth) {
					setLeft(firstComponent, divider, lastComponent, maxFirstComponentWidth + 'px');
				}
				$splitPane.resize();
			};
		} else if ($splitPane.is('.fixed-right')) {
			var firstComponentMinWidth = minWidth(firstComponent);
			return function(event) {
				var maxLastComponentWidth = splitPane.offsetWidth - firstComponentMinWidth - divider.offsetWidth;
				if (lastComponent.offsetWidth > maxLastComponentWidth) {
					setRight(firstComponent, divider, lastComponent, maxLastComponentWidth + 'px');
				}
				$splitPane.resize();
			};
		} else if ($splitPane.is('.vertical-percent')) {
			var lastComponentMinWidth = minWidth(lastComponent),
				firstComponentMinWidth = minWidth(firstComponent);
			return function(event) {
				var maxLastComponentWidth = splitPane.offsetWidth - firstComponentMinWidth - divider.offsetWidth;
				if (lastComponent.offsetWidth > maxLastComponentWidth) {
					setRight(firstComponent, divider, lastComponent, (maxLastComponentWidth / splitPane.offsetWidth * 100) + '%');
				} else {
					if (splitPane.offsetWidth - firstComponent.offsetWidth - divider.offsetWidth < lastComponentMinWidth) {
						setRight(firstComponent, divider, lastComponent, (lastComponentMinWidth / splitPane.offsetWidth * 100) + '%');
					}
				}
				$splitPane.resize();
			};
		}
	}

	function createMousemove($splitPane, pageX, pageY) {
		var splitPane = $splitPane[0],
			firstComponent = $splitPane.children('.split-pane-component:first')[0],
			divider = $splitPane.children('.split-pane-divider')[0],
			lastComponent = $splitPane.children('.split-pane-component:last')[0];
		if ($splitPane.is('.fixed-top')) {
			var firstComponentMinHeight =  minHeight(firstComponent),
				maxFirstComponentHeight = splitPane.offsetHeight - minHeight(lastComponent) - divider.offsetHeight,
				topOffset = divider.offsetTop - pageY;
			return function(event) {
				event.preventDefault();
				var top = Math.min(Math.max(firstComponentMinHeight, topOffset + pageYof(event)), maxFirstComponentHeight);
				setTop(firstComponent, divider, lastComponent, top + 'px');
				$splitPane.resize();
			};
		} else if ($splitPane.is('.fixed-bottom')) {
			var lastComponentMinHeight = minHeight(lastComponent),
				maxLastComponentHeight = splitPane.offsetHeight - minHeight(firstComponent) - divider.offsetHeight,
				bottomOffset = lastComponent.offsetHeight + pageY;
			return function(event) {
				event.preventDefault();
				var bottom = Math.min(Math.max(lastComponentMinHeight, bottomOffset - pageYof(event)), maxLastComponentHeight);
				setBottom(firstComponent, divider, lastComponent, bottom + 'px');
				$splitPane.resize();
			};
		} else if ($splitPane.is('.horizontal-percent')) {
			var splitPaneHeight = splitPane.offsetHeight,
				lastComponentMinHeight = minHeight(lastComponent),
				maxLastComponentHeight = splitPaneHeight - minHeight(firstComponent) - divider.offsetHeight,
				bottomOffset = lastComponent.offsetHeight + pageY;
			return function(event) {
				event.preventDefault();
				var bottom = Math.min(Math.max(lastComponentMinHeight, bottomOffset - pageYof(event)), maxLastComponentHeight);
				setBottom(firstComponent, divider, lastComponent, (bottom / splitPaneHeight * 100) + '%');
				$splitPane.resize();
			};
		} else if ($splitPane.is('.fixed-left')) {
			var firstComponentMinWidth = minWidth(firstComponent),
				maxFirstComponentWidth = splitPane.offsetWidth - minWidth(lastComponent) - divider.offsetWidth,
				leftOffset = divider.offsetLeft - pageX;
			return function(event) {
				event.preventDefault();
				var left = Math.min(Math.max(firstComponentMinWidth, leftOffset + pageXof(event)), maxFirstComponentWidth);
				setLeft(firstComponent, divider, lastComponent, left + 'px')
				$splitPane.resize();
			};
		} else if ($splitPane.is('.fixed-right')) {
			var lastComponentMinWidth = minWidth(lastComponent),
				maxLastComponentWidth = splitPane.offsetWidth - minWidth(firstComponent) - divider.offsetWidth,
				rightOffset = lastComponent.offsetWidth + pageX;
			return function(event) {
				event.preventDefault();
				var right = Math.min(Math.max(lastComponentMinWidth, rightOffset - pageXof(event)), maxLastComponentWidth);
				setRight(firstComponent, divider, lastComponent, right + 'px');
				$splitPane.resize();
			};
		} else if ($splitPane.is('.vertical-percent')) {
			var splitPaneWidth = splitPane.offsetWidth,
				lastComponentMinWidth = minWidth(lastComponent),
				maxLastComponentWidth = splitPaneWidth - minWidth(firstComponent) - divider.offsetWidth,
				rightOffset = lastComponent.offsetWidth + pageX;
			return function(event) {
				event.preventDefault();
				var right = Math.min(Math.max(lastComponentMinWidth, rightOffset - pageXof(event)), maxLastComponentWidth);
				setRight(firstComponent, divider, lastComponent, (right / splitPaneWidth * 100) + '%');
				$splitPane.resize();
			};
		}
	}

	function pageXof(event) {
		return event.pageX || event.originalEvent.pageX;
	}

	function pageYof(event) {
		return event.pageY || event.originalEvent.pageY;
	}

	function minHeight(element) {
		return parseInt($(element).css('min-height')) || 0;
	}

	function minWidth(element) {
		return parseInt($(element).css('min-width')) || 0;
	}

	function setTop(firstComponent, divider, lastComponent, top) {
		firstComponent.style.height = top;
		divider.style.top = top;
		lastComponent.style.top = top;
	}

	function setBottom(firstComponent, divider, lastComponent, bottom) {
		firstComponent.style.bottom = bottom;
		divider.style.bottom = bottom;
		lastComponent.style.height = bottom;
	}

	function setLeft(firstComponent, divider, lastComponent, left) {
		firstComponent.style.width = left;
		divider.style.left = left;
		lastComponent.style.left = left;
	}

	function setRight(firstComponent, divider, lastComponent, right) {
		firstComponent.style.right = right;
		divider.style.right = right;
		lastComponent.style.width = right;
	}

})(jQuery);

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

//var placeholder = (function() {
//
//
//})();
//
//var contentblock = (function() {
//
//
//})();
//

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