<?php $settings = \Assets\Models\Settings::fetch(); ?>

<div class="well">
<form id="detail-form" class="form" method="post">

    <div class="row">
        <div class="col-md-12">
        
            <div class="clearfix">
                 <ul class="list-filters list-unstyled list-inline pull-right">
                    <?php if ($flash->old('storage' ) != 's3' && $settings->isS3Enabled()) {?>                 
                    <li>
 		          		<a class="btn btn-success" href="./admin/asset/moveToS3/<?php echo (string)$flash->old('_id'); ?>">Move to S3</a>
                    </li>
                    <?php } ?>
                    <li>
 		          		<a class="btn btn-info" href="./admin/asset/rethumb/<?php echo (string)$flash->old('slug'); ?>">Rebuild Thumb</a>
 		          		<?php \Dsc\System::instance()->get( 'session' )->set( 'asset.rethumb.redirect', '/admin/asset/edit/' . $flash->old('slug') ); ?>
                    </li>                    
                    <li>
                     	<div class="btn-group">
	                        <button type="submit" class="btn btn-primary">Save</button>
	                        <input id="primarySubmit" type="hidden" value="save_edit" name="submitType" />
	                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
	                            <span class="caret"></span>
	                        </button>
	                        <ul class="dropdown-menu" role="menu">
	                            <li>
	                                <a onclick="document.getElementById('primarySubmit').value='save_close'; document.getElementById('detail-form').submit();" href="javascript:void(0);">Save & Close</a>
	                            </li>
	                        </ul>
	                    </div>
                    </li>
                    <li>
                        <a class="btn btn-default" href="./admin/assets">Cancel</a>
                    </li>
                </ul>    

            </div>
            <!-- /.form-actions -->
            
            <hr />
        
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab-basics" data-toggle="tab"> Basics </a>
                </li>
                <li>
                    <a href="#tab-details" data-toggle="tab"> Details </a>
                </li>
                <li>
                    <a href="#tab-replace" data-toggle="tab"> Replace </a>
                </li>                                
                <?php if (!empty($this->event)) { foreach ((array) $this->event->getArgument('tabs') as $key => $title ) { ?>
                <li>
                    <a href="#tab-<?php echo $key; ?>" data-toggle="tab"> <?php echo $title; ?> </a>
                </li>
                <?php } } ?>
            </ul>
            
            <div class="tab-content">

                <div class="tab-pane active" id="tab-basics">
                
                    <div class="row">
                        <div class="col-md-2">
                        
                            <h3>Links</h3>
                                    
                        </div>
                        <!-- /.col-md-2 -->
                                    
                        <div class="col-md-10">
                            <div class="form-group">
                                <div>Full-size:</div> 
                                <a target="_blank" href="./asset/<?php echo $item->{'slug'}; ?>">
                                /<?php echo $item->{'slug'}; ?>
                                </a>
                            </div>
                            <!-- /.form-group -->
                                                        
                            <div class="form-group">
                                <div>Thumb:</div> 
                                <a target="_blank" href="./asset/thumb/<?php echo $item->{'slug'}; ?>">
                                /thumb/<?php echo $item->{'slug'}; ?>
                                </a>
                            </div>
                            <!-- /.form-group -->                            
                        </div>
                    </div>
                                
                    <hr/>
                
                    <div class="row">
                        <div class="col-md-2">
                        
                            <h3>Basics</h3>
                                    
                        </div>
                        <!-- /.col-md-2 -->
                                    
                        <div class="col-md-10">
                            
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" placeholder="Title" value="<?php echo $flash->old('title'); ?>" class="form-control" />
                            </div>
                            <!-- /.form-group -->
                            
                            <div class="form-group">
                                <label>Slug</label>
                                <input type="text" name="slug" value="<?php echo $flash->old('slug'); ?>" class="form-control" />
                            </div>
                            <!-- /.form-group -->
                            
                            <div class="form-group">
                                <label>Tags: Enter multiple tags, separated by a comma</label>
                                <input name="tags" data-tags='<?php echo json_encode( \Dsc\Mongo\Collections\Assets::distinctTags() ); ?>' value="<?php echo implode(",", (array) $flash->old('tags') ); ?>" type="text" name="tags" class="form-control ui-select2-tags" />
                            </div>
                            <!-- /.form-group -->
                            
                            <div class="form-group">
                                <label>Type</label>
                                <input name="type" class="form-control ui-select2" data-tags='<?php echo json_encode( \Dsc\Mongo\Collections\Assets::distinctTypes() ); ?>' data-maximum="1" value="<?php echo $flash->old('type'); ?>" />
                            </div>
                            <!-- /.form-group -->
                            
                        </div>
                        <!-- /.col-md-10 -->
                    </div>
                    <!-- /.row -->
                    
                    <hr/>
                    
                    <div class="row">
                        <div class="col-md-2">
                            <h3>Preview</h3>                                    
                        </div>
                        <!-- /.col-md-2 -->
                                    
                        <div class="col-md-10">
                    
                            <?php if ($item->isImage()) { ?>
                            <div class="form-group">
                                <img src="./asset/<?php echo $item->slug; ?>" class="img-responsive" />
                            </div>
                            <div id="imageTools"><a href="/admin/asset/rotate/<?php echo $item->slug; ?>/90"><i class="fa fa-rotate-left"></i></a> <a href="/admin/asset/rotate/<?php echo $item->slug; ?>/270"><i class="fa fa-rotate-right"></i></a></div>
                            <!-- /.form-group -->
                            <?php } ?>
                        
                        </div>
                        <!-- /.col-md-10 -->
                    </div>
                    <!-- /.row -->                    
                
                </div>
                <!-- /.tab-pane -->
                
                <div class="tab-pane" id="tab-details">
                
                    <div class="row">
                        <div class="col-md-2">
                        
                            <h3>Storage</h3>
                                    
                        </div>
                        <!-- /.col-md-2 -->
                                    
                        <div class="col-md-10">
                            <div class="form-group">
                                <?php echo $item->{'storage'}; ?>
                            </div>
                            <!-- /.form-group -->                            
                        </div>
                    </div>
                                
                    <hr/>
                
                    <div class="row">
                        <div class="col-md-2">
                        
                            <h3>Source</h3>
                                    
                        </div>
                        <!-- /.col-md-2 -->
                                    
                        <div class="col-md-10">
                            <?php if ($item->{'source_url'}) { ?>
                            <div class="form-group">
                                <div>URL:</div>
                                <a target="_blank" href="<?php echo $item->{'source_url'}; ?>">
                                <?php echo $item->{'source_url'}; ?>
                                </a>
                            </div>
                            <!-- /.form-group -->
                            <?php } ?>
                            
                            <?php if ($item->{'filename'}) { ?>                            
                            <div class="form-group">
                                <div>Filename:</div> 
                                <?php echo $item->{'filename'}; ?>
                            </div>
                            <!-- /.form-group -->
                            <?php } ?>
                                                        
                        </div>
                    </div>
                                
                    <hr/>
                    
                    <?php if ($flash->old('storage' ) == 's3') { ?>
                    <div class="row">
                        <div class="col-md-2">
                        
                            <h3>S3 Data</h3>
                                    
                        </div>
                        <!-- /.col-md-2 -->
                                    
                        <div class="col-md-10">
                            <div class="form-group">
                                <div>URL:</div>
                                <a target="_blank" href="<?php echo $item->{'url'}; ?>">
                                    <?php echo $item->{'url'}; ?>
                                </a>
                            </div>
                            <!-- /.form-group -->
                                                        
                            <div class="form-group">
                                <div>Bucket:</div> 
                                <?php echo $item->{'s3.bucket'}; ?>
                            </div>
                            <!-- /.form-group -->
                            
                            <div class="form-group">
                                <div>UUID:</div> 
                                <?php echo $item->{'s3.uuid'}; ?>
                            </div>
                            <!-- /.form-group -->
                            
                            <div class="form-group">
                                <div>ETag:</div> 
                                <?php echo $item->{'s3.ETag'}; ?>
                            </div>
                            <!-- /.form-group -->
                        </div>
                    </div>
                                
                    <hr/>                    
                    <?php } ?>                
                
                </div>
                <!-- /.tab-pane -->
                
                <div class="tab-pane" id="tab-replace">
                    <?php echo $this->renderLayout('Assets/Admin/Views::assets/replace.php'); ?>    
                </div>                
                <!-- /.tab-pane -->
                
                <?php if (!empty($this->event)) { foreach ((array) $this->event->getArgument('content') as $key => $content ) { ?>
                <div class="tab-pane" id="tab-<?php echo $key; ?>">
                    <?php echo $content; ?>
                </div>
                <?php } } ?>
                
            </div>
            <!-- /.tab-content -->
            
        </div>
    </div>
    
</form>

</div>