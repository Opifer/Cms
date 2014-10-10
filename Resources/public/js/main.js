/**
 * Taggable
 */
var jsonurl = $('#mediatype_tags').attr('data-autocomplete');
$('#mediatype_tags').select2({
    tags:true,
    tokenSeparators: [",", " "],
    minimumInputLength: 1,
    ajax: {
        url: jsonurl,
        dataType: 'json',
        data:    function(term) { return { q: term }; },
        results: function(data) {
            var array = [];
            $.each(data, function(key, value){
                array.push({id: value, text: value});
            });

            return { results: array };
        }
    },
    createSearchChoice: function(term, data) {
        if ($(data).filter(function() {return this.text.localeCompare(term)===0; }).length===0) {
            return { id: term, text: term };
        }
    },
    formatResult:    function(item, page){ return item.text; },
    formatSelection: function(item, page){ return item.text; }
});
