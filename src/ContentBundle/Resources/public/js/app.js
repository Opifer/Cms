$(document).ready(function() {
    loadListTree();

    loadDatePickers();
});

$(document).ajaxComplete(function() {
    loadListTree();

    loadDatePickers();
});

function loadListTree() {
    $('.tree > ul').attr('role', 'tree').find('ul').attr('role', 'group');
    $('.tree').find('li:has(ul)').addClass('parent_li').attr('role', 'treeitem');

    $('body').on('click', '.tree span.expand', function (e) {
        var children = $(this).parent('li.parent_li').find(' > ul > li');
        if (children.is(':visible')) {
            $(this).parent('li.parent_li').removeClass('expanded');
        }
        else {
            $(this).parent('li.parent_li').addClass('expanded');
        }
        e.stopPropagation();
    });

    $('body').on('click', '.tree span.checkmark', function (e) {
        var inputId = $(this).closest('.tree').data('input-id');
        var val = $(this).parent('li').data('id');
        $(this).closest('.tree').find('li.selected').removeClass('selected');

        if ($('#' + inputId).val() != val) {
            $('#' + inputId).val(val);
            $(this).parent('li').addClass('selected');
        } else {
            $('#' + inputId).val('');
        }
    });
}

function loadDatePickers() {
    $('.datetimepicker').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
    });

    $('.datepicker').datetimepicker({
        format: 'YYYY-MM-DD'
    })
}
