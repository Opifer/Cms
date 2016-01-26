$(document).ready(function() {
    $('.tree > ul').attr('role', 'tree').find('ul').attr('role', 'group');
    $('.tree').find('li:has(ul)').addClass('parent_li').attr('role', 'treeitem').find(' > span.expand').on('click', function (e) {
        var children = $(this).parent('li.parent_li').find(' > ul > li');
        if (children.is(':visible')) {
            children.hide('fast');
            $(this).removeClass('expanded');
        }
        else {
            children.show('fast');
            $(this).addClass('expanded');
        }
        e.stopPropagation();
    });

    $('.tree').find('span.checkmark').on('click', function(e) {
        var inputId = $(this).closest('.tree').data('input-id');

        $('#'+inputId).val($(this).parent('li').data('id'));
        $(this).closest('.tree').find('li.selected').removeClass('selected');
        $(this).parent('li').addClass('selected');
    });
});
