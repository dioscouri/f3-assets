<?php // echo \Dsc\Debug::dump( $flash->get('old'), false ); ?>

<form id="detail-form" action="./admin/category" class="form" method="post">
    <div class="row">
        <div class="col-md-12">
            
            <div class="form-group clearfix">
                <fieldset>
                <legend>Local</legend>
                <?php echo $this->renderLayout('Assets/Create_Local.php'); ?>
                </fieldset>
            </div>
            <!-- /.form-group -->
            
            <div class="form-group clearfix">
                <fieldset>
                <legend>Amazon S3</legend>            
                <?php echo $this->renderLayout('Assets/Create_S3.php'); ?>
                </fieldset>
            </div>
            <!-- /.form-group -->
    
        </div>
        
    </div>
</form>