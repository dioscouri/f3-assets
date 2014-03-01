<div class="well">

<form id="detail-form" class="form" method="post">

    <div class="row">
        <div class="col-md-12">
        
            <div class="clearfix">

                <div class="pull-right">
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

                    &nbsp;
                    <a class="btn btn-default" href="./admin/assets">Cancel</a>
                </div>

            </div>
            <!-- /.form-actions -->
            
            <hr />
        
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab-basics" data-toggle="tab"> Basics </a>
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
                        
                            <h3>Details</h3>
                                    
                        </div>
                        <!-- /.col-md-2 -->
                                    
                        <div class="col-md-10">
                        
                            <div class="form-group">
                                <p class="help-block">
                                    Full-size Link: 
                                    <a target="_blank" href="./asset/<?php echo $item->{'metadata.slug'}; ?>">
                                    /<?php echo $item->{'metadata.slug'}; ?>
                                    </a>
                                </p>
                            </div>
                            <!-- /.form-group -->
                                                        
                            <div class="form-group">
                                <p class="help-block">
                                    Thumb Link: 
                                    <a target="_blank" href="./asset/thumb/<?php echo $item->{'metadata.slug'}; ?>">
                                    /thumb/<?php echo $item->{'metadata.slug'}; ?>
                                    </a>
                                </p>
                            </div>
                            <!-- /.form-group -->                            
                            
                            
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="metadata[title]" placeholder="Title" value="<?php echo $flash->old('metadata.title'); ?>" class="form-control" />
                            </div>
                            <!-- /.form-group -->
                            
                            <div class="form-group">
                                <label>Slug</label>
                                <input type="text" name="metadata[slug]" value="<?php echo $flash->old('metadata.slug'); ?>" class="form-control" />
                            </div>
                            <!-- /.form-group -->
                            
                            <div class="form-group">
                                <label>Tags: Enter multiple tags, separated by a comma</label>
                                <input name="metadata[tags]" data-tags='<?php echo json_encode( $all_tags ); ?>' value="<?php echo implode(",", (array) $flash->old('metadata.tags') ); ?>" type="text" name="tags" class="form-control ui-select2-tags" />
                            </div>
                            <!-- /.form-group -->
                            
                            
                        </div>
                        <!-- /.col-md-10 -->
                    </div>
                    <!-- /.row -->
                    
                    <div class="row">
                        <div class="col-md-2">
                            <h3>Preview</h3>                                    
                        </div>
                        <!-- /.col-md-2 -->
                                    
                        <div class="col-md-10">
                    
                            <?php if ($item->isImage()) { ?>
                            <div class="form-group">
                                <img src="./asset/<?php echo $item->{'metadata.slug'}; ?>" class="img-responsive" />
                            </div>
                            <!-- /.form-group -->
                            <?php } ?>
                        
                        </div>
                        <!-- /.col-md-10 -->
                    </div>
                    <!-- /.row -->                    
                
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