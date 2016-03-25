$(document).ready(function() {
    $('.add-cookiewall').click(function() {
        var blockId = $(this).attr('id');
        $.ajax({
            url: $(this).attr('data-route'),
            success: function(result) {
                console.log(result);
            }
        });
    });
});