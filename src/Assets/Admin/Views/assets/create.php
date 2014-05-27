<?php $settings = \Assets\Models\Settings::fetch(); ?>

<div class="row">
    <div class="col-md-12">

        <form id="create_url_form" method="post" action="./admin/asset/handleUrl">
            <div class="form-group clearfix">
                <legend>Upload to the Server via URL</legend>
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
    
        <form id="create_local_form" class="form" method="post">
            <div class="form-group clearfix">
                <legend>Upload to the Server</legend>
                <?php echo $this->renderLayout('Assets/Admin/Views::assets/create_local.php'); ?>
            </div>
            <!-- /.form-group -->
        </form>
        
        <?php if ($settings->isS3Enabled()) { ?>
        <form id="create_s3_form" class="form" method="post">
            <div class="form-group clearfix">
                <legend>Upload directly to Amazon S3</legend>            
                <?php echo $this->renderLayout('Assets/Admin/Views::assets/create_s3.php'); ?>
            </div>
            <!-- /.form-group -->
        </form>
        <?php } ?>
    </div>
    
</div>