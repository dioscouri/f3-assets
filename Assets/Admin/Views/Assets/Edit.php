<?php // echo \Dsc\Debug::dump( $flash->get('old'), false ); ?>

<form id="detail-form" action="./admin/asset/<?php echo $item->get( $model->getItemKey() ); ?>" class="form" method="post">
    <div class="row">
        <div class="col-md-9">
            <div class="form-group">
                <input type="text" name="metadata[title]" placeholder="Title" value="<?php echo $flash->old('metadata.title'); ?>" class="form-control" />
                <?php if ($flash->old('metadata.slug')) { ?>
                    <p class="help-block">
                    <label>Slug:</label>
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

                    <h3>Save</h3>

                </div>
                <!-- /.portlet-header -->

                <div class="portlet-content">
                
                    <div class="form-actions">
    
                        <div>
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <input id="primarySubmit" type="hidden" value="save_edit" name="submitType" />
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a onclick="document.getElementById('primarySubmit').value='save_new'; document.getElementById('detail-form').submit();" href="javascript:void(0);">Save & Create Another</a>
                                    </li>
                                    <li>
                                        <a onclick="document.getElementById('primarySubmit').value='save_as'; document.getElementById('detail-form').submit();" href="javascript:void(0);">Save As</a>
                                    </li>
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

                </div>
                <!-- /.portlet-content -->

            </div>
            <!-- /.portlet -->        
                    
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
</form>