<?php $settings = \Assets\Models\Settings::fetch(); ?>

<h2>Replace this asset using one of the following methods:</h2>

<div class="row">
    <div class="col-md-12">
    <?php /* ?>
        <?php if ($settings->isS3Enabled()) { ?>
        <form id="replace_url_form_s3" method="post" action="./admin/asset/handleUrlS3">
            <div class="form-group clearfix">
                <legend>Upload directly to S3 via URL</legend>
                    <div class="input-group">
                        <input type="text" class="form-control" name="upload_url" />
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit">Upload</button>
                            <?php $path = \Base::instance()->hive()['PATH']; ?>
                            <?php \Dsc\System::instance()->get( 'session' )->set( 'assets.handleUrl.redirect', $path ); ?>
                        </span>                
                    </div>
            </div>
            <!-- /.form-group -->
        </form>
                
        <form id="replace_s3_form" class="form" method="post">
            <div class="form-group clearfix">
                <legend>Upload directly to Amazon S3</legend>            
                <?php echo $this->renderLayout('Assets/Admin/Views::assets/replace_s3.php'); ?>
            </div>
            <!-- /.form-group -->
        </form>
        
        <hr />
        <?php } ?>
        */ ?>
        
        <div id="replace_url_form">
            <div class="form-group clearfix">
                <legend>Upload to the Server via URL</legend>
                    <div class="input-group">
                        <input type="text" class="form-control" name="upload_url" id="upload_url" />
                        <span class="input-group-btn">
                            <a id="replace_url_submit" class="btn btn-primary" href="javascript:void(0);">Upload</a>
                            <?php $path = \Base::instance()->hive()['PATH']; ?>
                            <?php \Dsc\System::instance()->get( 'session' )->set( 'assets.handleUrl.redirect', $path ); ?>
                        </span>                
                    </div>
            </div>
            <!-- /.form-group -->
            
            <script>
            jQuery(document).ready(function () {
                jQuery('#replace_url_submit').on('click', function(){
                    jQuery.ajax({
                        type: "POST",
                        url: './admin/asset/<?php echo $item->slug; ?>/replace/url',
                        data: {
                            upload_url: jQuery('#upload_url').val()
                        },
                        success: function(){
                            jQuery('#detail-form').submit();
                        }
                    });
                });
            });
            
            </script>            
        </form>        

        
        <div class="form-group clearfix">
            <legend>Upload to the Server</legend>
            <?php echo $this->renderLayout('Assets/Admin/Views::assets/replace_local.php'); ?>
        </div>
        <!-- /.form-group -->
        

    </div>
    
</div>