$(document).ready(function() {
    $('.tree > ul').attr('role', 'tree').find('ul').attr('role', 'group');
    $('.tree').find('li:has(ul)').addClass('parent_li').attr('role', 'treeitem').find(' > span').attr('title', 'Collapse this branch').on('click', function (e) {
        var children = $(this).parent('li.parent_li').find(' > ul > li');
        if (children.is(':visible')) {
            children.hide('fast');
            $(this).attr('title', 'Expand this branch').find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
        }
        else {
            children.show('fast');
            $(this).attr('title', 'Collapse this branch').find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
        }
        e.stopPropagation();
    });

    $('.tree').find('span').on('click', function(e) {
        var inputId = $(this).closest('.tree').data('input-id');

        $('#'+inputId).val($(this).parent('li').data('id'));
        $(this).closest('.tree').find('li.selected').removeClass('selected');
        $(this).parent('li').addClass('selected');
    });
});
