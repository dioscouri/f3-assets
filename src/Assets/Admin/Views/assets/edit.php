
<form id="detail-form" action="./admin/asset/<?php echo $item->get( $model->getItemKey() ); ?>" class="form" method="post">

    <div class="row">
        <div class="col-md-12">
        
            <div class="form-actions clearfix">

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
                <?php foreach ((array) $this->event->getArgument('tabs') as $key => $title ) { ?>
                <li>
                    <a href="#tab-<?php echo $key; ?>" data-toggle="tab"> <?php echo $title; ?> </a>
                </li>
                <?php } ?>
            </ul>
            
            <div class="tab-content">

                <div class="tab-pane active" id="tab-basics">
                
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="metadata[title]" placeholder="Title" value="<?php echo $flash->old('metadata.title'); ?>" class="form-control" />
                                <?php if ($flash->old('metadata.slug')) { ?>
                                    <p class="help-block">
                                    <label>Slug</label>
                                    <input type="text" name="metadata[slug]" value="<?php echo $flash->old('metadata.slug'); ?>" class="form-control" />
                                    </p>
                                <?php } ?>
                                
                                <p class="help-block">
                                Current Link: 
                                <a target="_blank" href="./asset/<?php echo $item->{'metadata.slug'}; ?>">
                                /<?php echo $item->{'metadata.slug'}; ?>
                                </a>
                                </p>
                                
                            </div>
                            <!-- /.form-group -->
                            
                            <?php if ($item->isImage()) { ?>
                            <div class="form-group">
                                <img src="./asset/<?php echo $item->{'metadata.slug'}; ?>" />
                            </div>
                            <!-- /.form-group -->
                            <?php } ?>
                    
                        </div>
                        <div class="col-md-3">
                                    
                            <div class="portlet">
                
                                <div class="portlet-header">
                
                                    <h3>Tags</h3>
                
                                </div>
                                <!-- /.portlet-header -->
                
                                <div class="portlet-content">
                                
                                    <div class="input-group">
                                        <input name="metadata[tags]" data-tags='<?php echo json_encode( $all_tags ); ?>' value="<?php echo implode(",", (array) $flash->old('metadata.tags') ); ?>" type="text" name="tags" class="form-control ui-select2-tags" /> 
                                    </div>
                                    <!-- /.form-group -->
                
                                </div>
                                <!-- /.portlet-content -->
                
                            </div>
                            <!-- /.portlet -->        
                        </div>
                        
                    </div>
                
                </div>
                <!-- /.tab-pane -->
                
                <?php foreach ((array) $this->event->getArgument('content') as $key => $content ) { ?>
                <div class="tab-pane" id="tab-<?php echo $key; ?>">
                    <?php echo $content; ?>
                </div>
                <?php } ?>
                
            </div>
            <!-- /.tab-content -->
            
        </div>
    </div>
    
</form>