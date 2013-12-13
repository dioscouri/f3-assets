<?php /*?><link href="fineuploader-{VERSION}.css" rel="stylesheet">*/ ?>

    <script type="text/template" id="simple-previews-template">
        <div class="qq-uploader-selector qq-uploader">
            <div class="qq-upload-drop-area-selector qq-upload-drop-area">
                <h4 class="help-block">Drop files here</h4>
                <div class="help-block">or</div>
                <div class="qq-upload-button-selector qq-upload-button btn btn-primary">
                    <div>Upload a file</div>
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
                    <span class="qq-edit-filename-icon-selector qq-edit-filename-icon"></span>
                    <span class="qq-upload-file-selector qq-upload-file"></span>
                    <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                    <span class="qq-upload-size-selector qq-upload-size"></span>
                    <a class="qq-upload-cancel-selector btn-xs btn-warning" href="#">Cancel</a>
                    <a class="qq-upload-retry-selector btn-xs btn-info" href="#">Retry</a>
                    <a class="qq-upload-delete-selector btn-xs btn-warning" href="#">Delete</a>
                    <a class="qq-upload-pause-selector btn-xs btn-info" href="#">Pause</a>
                    <a class="qq-upload-continue-selector btn-xs btn-info" href="#">Continue</a>
                    <span class="qq-upload-status-text-selector qq-upload-status-text"></span>
                    <a class="view-btn btn-xs btn-info hide" target="_blank">View</a>
                    <a class="hide btn btn-xs btn-link edit-btn">Edit</a>
                </li>
            </ul>
        </div>
    </script>


<div id="fineuploader-s3"></div>

<?php /*?><script src="s3.jquery.fineuploader-{VERSION}.js"></script>*/ ?>
<script>
jQuery(document).ready(function () {
    jQuery('#fineuploader-s3').fineUploaderS3({
            request: {
                // REQUIRED: We are using a custom domain
                // for our S3 bucket, in this case.  You can
                // use any valid URL that points to your bucket.
                endpoint: "<?php echo \Base::instance()->get('aws.endpoint'); ?>",

                // REQUIRED: The AWS public key for the client-side user
                // we provisioned.
                accessKey: "<?php echo \Base::instance()->get('aws.clientPublicKey'); ?>"
            },

            template: "simple-previews-template",

            // REQUIRED: Path to our local server where requests
            // can be signed.
            signature: {
                endpoint: "./admin/asset/handleS3"
            },

            // OPTIONAL: An endopint for Fine Uploader to POST to
            // after the file has been successfully uploaded.
            // Server-side, we can declare this upload a failure
            // if something is wrong with the file.
            uploadSuccess: {
                endpoint: "./admin/asset/handleS3?success=1"
            },

            // USUALLY REQUIRED: Blank file on the same domain
            // as this page, for IE9 and older support.
            iframeSupport: {
                localBlankPagePath: "./blank.html"
            },

            // optional feature
            chunking: {
                enabled: true
            },

            // optional feature
            resume: {
                enabled: true
            },

            // optional feature
            deleteFile: {
                enabled: true,
                method: "POST",
                endpoint: "./admin/asset/handleS3"
            },

            // optional feature
            validation: {
                itemLimit: 5,
                sizeLimit: <?php echo \Base::instance()->get('aws.maxsize'); ?>
            },

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
            var $fileEl = jQuery(this).fineUploaderS3("getItemByFileId", id),
                $editBtn = $fileEl.find(".edit-btn");

            if (response.asset_id) {
                $editBtn.attr("href", './admin/asset/'+response.asset_id+'/edit');
                $editBtn.removeClass('hide').show();
            }
        });
    });
</script>