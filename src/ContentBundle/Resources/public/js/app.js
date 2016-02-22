$(document).ready(function() {
    loadListTree();

    loadDatePickers();

    loadNavigationBlockScripts();
});

$(document).ajaxComplete(function() {
    loadListTree();

    loadDatePickers();

    loadNavigationBlockScripts();
});

function loadListTree() {
    $('.tree > ul').attr('role', 'tree').find('ul').attr('role', 'group');
    $('.tree').find('li:has(ul)').addClass('parent_li').attr('role', 'treeitem').find(' > span.expand').on('click', function (e) {
        var children = $(this).parent('li.parent_li').find(' > ul > li');
        if (children.is(':visible')) {
            $(this).parent('li.parent_li').removeClass('expanded');
        }
        else {
            $(this).parent('li.parent_li').addClass('expanded');
        }
        e.stopPropagation();
    });

    $('.tree').find('span.checkmark').on('click', function (e) {
        var inputId = $(this).closest('.tree').data('input-id');

        $('#' + inputId).val($(this).parent('li').data('id'));
        $(this).closest('.tree').find('li.selected').removeClass('selected');
        $(this).parent('li').addClass('selected');
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

function loadNavigationBlockScripts() {
    $('.toggle-content-picker').change(function(e) {
        var selected = $(this).val();
        if (selected == 'custom') {
            $('.content-picker').closest('.form-group').show();
        } else {
            $('.content-picker').closest('.form-group').hide();
        }
    });
}
