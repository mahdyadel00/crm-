<!--text field-->
<div class="form-group row p-t-25">
    <div class="col-12">
        <div class="dropzone dz-clickable" id="webform_files">
            <div class="dz-default dz-message {{ $payload['class'] }}">
                <span></br>{{ $payload['placeholder'] }}</span>
            </div>
        </div>
    </div>
</div>


<!--dynamic sccript - for iframe webforms-->
<script>
    $(document).ready(function () {
        $("#webform_files").dropzone({
            url: "/webform/fileupload",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            init: function () {
                this.on("error", function (file, message, xhr) {

                    //is there a message from backend [abort() response]
                    if (typeof xhr != 'undefined' && typeof xhr.response != 'undefined') {
                        var error = $.parseJSON(xhr.response);
                        var message = error.notification.value;
                    }

                    //any other message
                    var message = (typeof message == 'undefined' || message == '' ||
                        typeof message == 'object') ? NXLANG.generic_error : message;

                    //error message
                    NX.notification({
                        type: 'error',
                        message: message
                    });
                    //remove the file
                    this.removeFile(file);
                });
            },
            success: function (file, response) {
                //get the priview box dom elemen
                var $preview = $(file.previewElement);
                //create a hidden form field for this file
                $preview.append('<input type="hidden" name="attachments[' + response.uniqueid +
                    ']"  value="' + response.filename + '">');
            }
        });
    });
</script>