<?php /*?><link href="fineuploader-{VERSION}.css" rel="stylesheet">*/ ?>

<script type="text/template" id="qq-template-bootstrap">
    <div class="qq-uploader-selector qq-uploader">
        <div class="qq-upload-drop-area-selector qq-upload-drop-area">
            <h4 class="help-block">Drop file here</h4>
            <div class="help-block">or</div>
            <div class="qq-upload-button-selector qq-upload-button btn btn btn-primary">
                <div><i class="icon-upload icon-white"></i>Upload a file</div>
            </div>
        </div>
    
        <span class="qq-drop-processing-selector qq-drop-processing">
            <span>Processing dropped files...</span>
            <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
        </span>
        <ul class="qq-upload-list-selector qq-upload-list">
            <li>
                <div class="qq-progress-bar-container-selector">
                    <div class="qq-progress-bar-selector qq-progress-bar"></div>
                </div>
                <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                <img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
                <span class="qq-upload-file-selector qq-upload-file"></span>
                <span class="qq-upload-size-selector qq-upload-size"></span>
                <a class="qq-upload-cancel-selector qq-upload-cancel" href="#">Cancel</a>
                <span class="qq-upload-status-text-selector qq-upload-status-text"></span>
                <a class="hide btn btn-xs btn-link edit-btn">Edit</a>
            </li>
        </ul>
    </div>
</script>

<div id="bootstrapped-fine-uploader"></div>

<script>
jQuery(document).ready(function () {
    jQuery('#bootstrapped-fine-uploader').fineUploader({
        multiple: false,
        request: {
            endpoint: './admin/asset/<?php echo $item->slug; ?>/replace/traditional'
        },
        template: 'qq-template-bootstrap',
        classes: {
            success: 'alert alert-success',
            fail: 'alert alert-danger'
        },

        thumbnails: {
            placeholders: {
                notAvailablePath: "./fineuploader/placeholders/not_available-generic.png",
                waitingPath: "./fineuploader/placeholders/waiting-generic.png"
            }
        }
    })
    .on('complete', function(event, id, name, response) {
        jQuery('#detail-form').submit();
    });    
});

</script>

