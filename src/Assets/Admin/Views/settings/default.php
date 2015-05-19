<div class="well">

<form id="settings-form" role="form" method="post" class="form-horizontal clearfix">

    <div class="clearfix">
        <button type="submit" class="btn btn-primary pull-right">Save Changes</button>
    </div>

    <hr />

    <div class="row">
        <div class="col-lg-2 col-md-3 col-sm-4">
            <ul class="nav nav-pills nav-stacked">
                <li class="active">
                    <a href="#tab-general" data-toggle="tab"> General Settings </a>
                </li>
                <li>
                    <a href="#tab-aws" data-toggle="tab"> AWS Settings </a>
                </li>                
                              
            </ul>
        </div>

        <div class="col-lg-10 col-md-9 col-sm-8">

            <div class="tab-content stacked-content">

                <div class="tab-pane fade in active" id="tab-general">
                    <h4>General Settings</h4>
                    
                    <hr />
                    
                    <div class="form-group">
                        <div class="row">
                         <div class="form-group">
                            <div class="col-md-5">
                                <label>Default Thumbnail</label>
                                <input name="images[default_thumb]" placeholder="Link to a default thumbnail" value="<?php echo $flash->old('images.default_thumb'); ?>" class="form-control" type="text" />
                            </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.form-group -->                   
                    
                </div>
                
                <div class="tab-pane fade" id="tab-aws">
                    <h4>AWS Settings</h4>
           
                    <hr />
                    
                    <div class="form-group">
                        <div class="row">
                         <div class="form-group">
                            <div class="col-md-5">
                                <label>Max Size</label>
                                <input name="aws[maxsize]" placeholder="15000000" value="<?php echo $flash->old('aws.maxsize'); ?>" class="form-control" type="text" />
                            </div>
                            </div>
                             <div class="form-group">
                            <div class="col-md-5">
                                <label>Bucketname</label>
                                <input name="aws[bucketname]" placeholder="" value="<?php echo $flash->old('aws.bucketname'); ?>" class="form-control" type="text" />
                            </div>
                            </div>
                            <div class="form-group">
                            <div class="col-md-5">
                                <label>Endpoint</label>
                                <input name="aws[endpoint]" placeholder="" value="<?php echo $flash->old('aws.endpoint'); ?>" class="form-control" type="text" />
                            </div>
                            </div>
                            <div class="form-group">
                            <div class="col-md-5">
                                <label>Client Public Key</label>
                                <input name="aws[clientPublicKey]" placeholder="" value="<?php echo $flash->old('aws.clientPublicKey'); ?>" class="form-control" type="text" />
                            </div>
                            </div>
                            <div class="form-group">
                            <div class="col-md-5">
                                <label>Client Private Key</label>
                                <input name="aws[clientPrivateKey]" placeholder="" value="<?php echo $flash->old('aws.clientPrivateKey'); ?>" class="form-control" type="text" />
                            </div>
                            </div>
                            <div class="form-group">
                             <div class="col-md-5">
                                <label>Server Public Key</label>
                                <input name="aws[serverPublicKey]" placeholder="" value="<?php echo $flash->old('aws.serverPublicKey'); ?>" class="form-control" type="text" />
                            </div></div>
                            <div class="form-group">
                             <div class="col-md-5">
                                <label>Server Private Key</label>
                                <input name="aws[serverPrivateKey]" placeholder="" value="<?php echo $flash->old('aws.serverPrivateKey'); ?>" class="form-control" type="text" />
                            </div>
                            </div>
                        </div>
                    </div>
                                 
                </div>
                
                
                    <!-- /.form-group -->                    
                    
                </div>                
                
            </div>

        </div>
    </div>

</form>

</div>