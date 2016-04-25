$(document).ready(function() {

    /**
     * Submit datagrid filters on change
     */
    $('.js-submitonchange').change(function() {
        $.ajax({
            type: "POST",
            url : $(this).closest('form').attr('action'),
            data: $(this).closest('form').serialize(),
            success: function(result) {
                $('table').replaceWith(result);
            }
        });
    });

    $(function () {
        $('[data-form-widget=collection]').each(function () {
            new window.infinite.Collection(this, $(this).parent().siblings('.btn-group').find('[data-prototype]'));
        });
    });

    // Disable submitting forms on enter key
    $('.prevent-enter-submit').bind("keyup keypress", function(e) {
        var code = e.keyCode || e.which;
        if (code  == 13) {
            e.preventDefault();
            return false;
        }
    });

    /**
     * Handle menu group collapsing
     *
     * @param element parent
     * @param int initial_level
     * @returns null
     */
    function menuSiblingCollapse(parent, initial_level) {

        //handle only its children, not those of same level
        if (parent.next().data("level") !== initial_level) {

            //skip deeper level
            if((parent.next().data("level")-initial_level)<2){
                if(parent.next().hasClass("collapse")){
                         parent.next().removeClass("collapse");

                         //only make active clicked group
                         if(parent.data("level")===initial_level){
                            parent.find("td:nth-child(2) > span").addClass("active");
                         }

                }else{
                    //collapse only if level is greater than level of clicked group
                    if(parent.next().data("level") > initial_level){
                        parent.next().addClass("collapse");
                        parent.find("td:nth-child(2) > span").removeClass("active");
                    }

                }
            }else{
                //deeper level should be collapsed if parent is collapsed
                if(parent.hasClass("collapse")){
                  parent.next().addClass("collapse");
                  parent.find("td:nth-child(2) > span").removeClass("active");
                }
            };

            //do recursion if next element is there
            if(parent.next().length){
                menuSiblingCollapse(parent.next(), initial_level);
            }

        }
    }


    //manage collapse state of menu items by clicking on menugroup
    $(".panel-section tr td:nth-child(2) > span").click(function () {
        var initial_level = $(this).closest("tr").data("level");
        menuSiblingCollapse($(this).closest("tr"), initial_level)
    });

    //handle click event for delete buttons
    $(".panel-section a.delete.danger").on('click', function (e) {
        e.preventDefault();

        $(".confirmation-modal").find("span.data-type").html($(this).data("type"));
        $(".confirmation-modal").find("span.data-name").html($(this).data("name"));
        $(".confirmation-modal").find(".modal-footer > a").attr("href", $(this).data("href"));
        $(".confirmation-modal").modal("show")
    });

    //Creates and appends slug from input field
    $(document.body).on('input', '.slugify', function () {
        var options = {};
        var separator = $(this).attr('data-slugify-separator');
        if(separator) {
            options = {
                'separator': separator
            };
        }
        var item = $(this).attr('data-slugify-target');
        $(item).slugify(this, options);
    });


    function adjustModalMaxHeightAndPosition(){
        $('.modal').each(function(){
            if($(this).hasClass('in') === false){
                $(this).show();
            }
            var contentHeight = $(window).height() - 60;
            var headerHeight = $(this).find('.modal-header').outerHeight() || 2;
            var footerHeight = $(this).find('.modal-footer').outerHeight() || 2;

            $(this).find('.modal-content').css({
                'max-height': function () {
                    return contentHeight;
                }
            });

            $(this).find('.modal-body').css({
                'max-height': function () {
                    return contentHeight - (headerHeight + footerHeight);
                }
            });

            $(this).find('.modal-dialog').addClass('modal-dialog-center').css({
                'margin-top': function () {
                    return -($(this).outerHeight() / 2);
                },
                'margin-left': function () {
                    return -($(this).outerWidth() / 2);
                }
            });
            if($(this).hasClass('in') === false){
                $(this).hide();
            }
        });
    }

    if ($(window).height() >= 320){
        $(window).resize(adjustModalMaxHeightAndPosition).trigger("resize");
    }

    adjustCkeditorConfig();

    $('body').on('click', 'div[data-ckeditor]', function(e){
        history.pushState("", document.title, window.location.pathname);
        e.preventDefault();
        e.stopPropagation();
        browsePath = Routing.generate('opifer_ckeditor_content', {'type': 'link'});
        browsePathImages = Routing.generate('opifer_ckeditor_media', {'type': 'image'});

        $('#'+$(this).attr('data-ckeditor')).removeClass('hidden');
        CKEDITOR.replace($(this).attr('data-ckeditor'), {
            extraPlugins: 'iframe,justify,showblocks,tabletools,tableresize,selectall,find',
            filebrowserBrowseUrl: browsePath,
            filebrowserImageBrowseUrl: browsePathImages,
            allowedContent: true
        });
        CKEDITOR.dtd.$removeEmpty['i'] = false;
        CKEDITOR.dtd.$removeEmpty['span'] = false;

        $(this).hide();

        return false;
    });

    $('.btn-group-columns .dropdown-menu').click(function(event) {
        event.stopPropagation();
    });

    $('.btn-group-columns input').on('change', function(e) {
        $(this).closest('form').submit();
    })
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

function adjustCkeditorConfig() {
    CKEDITOR.dtd.$removeEmpty['i'] = 0;
    CKEDITOR.dtd.$removeEmpty['span'] = 0;
    CKEDITOR.config.allowedContent = true;
    CKEDITOR.config.stylesSet = 'cms_styles:'+Routing.generate('opifer_ckeditor_styles');
    CKEDITOR.config.customConfig = Routing.generate('opifer_ckeditor_config');
}
