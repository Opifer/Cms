/**
 * Dropzone
 */
Dropzone.autoDiscover = false;

if ($('.dropzone').length) {
    var dropzoneForm = new Dropzone(".dropzone", {
        url: $('.dropzone').attr('data-path'),
        uploadMultiple: true,
        parallelUploads: 1,
        addRemoveLinks: true,
        dictDefaultMessage: 'Sleep uw afbeeldingen naar dit vak, of klik om ze handmatig toe te voegen.',

        maxFilesize: 11, // MB
        maxFiles: 10,
        acceptedFiles: 'image/jpeg,image/png,image/gif,application/pdf',

        // Listen to all dropzone events
        init: function() {
            // Called when a file has successfully been uploaded
            this.on("successmultiple", function(files, response) {
                var prototype = $('.dropzone').attr('data-prototype');
                prototype = prototype.replace(/__id__/g, response.id);
                prototype = prototype.replace(/__value__/g, response.name);
                prototype = prototype.replace(/__thumb__/g, $('.dz-preview img[alt="' + files[0]["name"] +'"]').last().attr('src'));

                // If the dropzone has the data-mapped attribute set to true,
                // Place the media ID inside the hidden field
                if ($('.dropzone').attr('data-mapped')) {
                    var mapping = $('.dropzone').attr('data-mapped');

                    if ($('#' + mapping).val()) {
                        $('#' + mapping).val($('#' + mapping).val() + ',' + response.id);
                    } else {
                        $('#' + mapping).val(response.id);
                    }
                }

                if (!$('#update-form').length) {
                    var form = $('.dropzone').attr('data-prototype-form');
                    $('.dropzone').closest('form').after(form);
                }

                $('#update-form button').before(prototype);
            });

            // Called when a file has been removed from the dropzone
            this.on("removedfile", function(file) {
                // If the dropzone has the data-mapped attribute set to true,
                // remove the media ID from the hidden field
                if ($('.dropzone').attr('data-mapped')) {
                    //var response = JSON.parse(file.xhr.response);
                    //response = response[0]['id'];

                    var mapping = $('.dropzone').attr('data-mapped');
                    var str = $('#' + mapping).val();
                    var arr = str.split(',');

                    $('#' + mapping).val(arr.join(','));
                }
            });

            this.on("errormultiple", function(files, response) {
                console.log('error', response);
            });

            this.on("complete", function() {
                if (this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0) {
                    // Activate save button
                }
            });
        }
    });

    //if ($('.dropzone').attr('data-mapped')) {
    //    var mapping = $('.dropzone').attr('data-mapped');
    //    var mapping = $('#' + mapping).val();
    //
    //    var mediaRequest = Routing.generate('opifer_api_media', {'ids' : mapping});
    //}
};
