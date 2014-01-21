<h3>Create a New Asset
<small><a href="javascript:void(0);" onclick="location.reload();">Refresh after uploading</a> in order to select one of the new assets.</small>
</h3>

<div class="well well-sm">
    <?php echo $this->renderLayout('Assets/Admin/Views::assets/create.php'); ?>
</div>


<h3>Select from an Existing Asset</h3>
<form id="assets" class="searchForm" action="./admin/assets/element/<?php echo $PARAMS['id']; ?>" method="post">

    <div class="row datatable-header">
        <div class="col-sm-6">
            <div class="row row-marginless">
                <?php if (!empty($list['subset'])) { ?>
                <div class="col-sm-2">
                    <?php echo $pagination->getLimitBox( $state->get('list.limit') ); ?>
                </div>
                <?php } ?>
                <?php if (!empty($list['count']) && $list['count'] > 1) { ?>
                <div class="col-sm-10">
                    <?php echo (!empty($list['count']) && $list['count'] > 1) ? $pagination->serve() : null; ?>
                </div>
                <?php } ?>
            </div>
        </div>    
        <div class="col-sm-6">
            <div class="input-group">
                <input class="form-control" type="text" name="filter[keyword]" placeholder="Keyword" maxlength="200" value="<?php echo $state->get('filter.keyword'); ?>"> 
                <span class="input-group-btn">
                    <input class="btn btn-primary" type="submit" onclick="this.form.submit();" value="Search" />
                    <button class="btn btn-danger" type="button" onclick="Dsc.resetFormFilters(this.form);">Reset</button>
                </span>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="list[order]" value="<?php echo $state->get('list.order'); ?>" />
    <input type="hidden" name="list[direction]" value="<?php echo $state->get('list.direction'); ?>" />
    
    <div class="table-responsive datatable">
    
    <table class="table table-striped table-bordered table-hover table-highlight media-table">
    	<thead>
    		<tr>
    		    <th class="col-md-1"></th>
    			<th data-sortable="metadata.title">Title</th>
    			<th class="col-md-1" data-sortable="storage">Location</th>
    		</tr>
    	</thead>
    	<tbody>    
    
        <?php if (!empty($list['subset'])) { ?>
    
        <?php foreach ($list['subset'] as $item) { ?>
            <tr>
                
                <td class="">
                    <?php if ($item->thumb) { ?>
                        <div class="thumbnail text-center">
                        <img src="<?php echo \Dsc\Image::dataUri( $item->thumb->bin ); ?>" alt="<?php echo $item->{'metadata.title'}; ?>" />
                        </div>
                    <?php } ?>
                </td>
                
                <td class="">
                    <h5>
                    <a onclick="window.parent.<?php echo $select_function_name; ?>('<?php echo $item->$elementItemKey; ?>', '<?php echo str_replace( array("'", "\""), array("\\'", ""), $item->{'metadata.title'} ); ?>', '<?php echo $PARAMS['id']; ?>' );" href="javascript:void(0);">
                    <?php echo $item->{'metadata.title'}; ?>
                    </a>
                    </h5>
    
                    <a class="help-block" target="_blank" href="./asset/<?php echo $item->{'metadata.slug'}; ?>">
                    /<?php echo $item->{'metadata.slug'}; ?>
                    </a>
    
                </td>
                
                <td class="">
                    <?php echo $item->{'storage'}; ?>
                </td>
    
            </tr>
        <?php } ?>
        
        <?php } else { ?>
            <tr>
            <td colspan="100">
                <div class="">No items found.</div>
            </td>
            </tr>
        <?php } ?>
    
        </tbody>
    </table>
    
    </div>
    
    <div class="row datatable-footer">
        <?php if (!empty($list['count']) && $list['count'] > 1) { ?>
        <div class="col-sm-10">
            <?php echo (!empty($list['count']) && $list['count'] > 1) ? $pagination->serve() : null; ?>
        </div>
        <?php } ?>
        <div class="col-sm-2 pull-right">
            <div class="datatable-results-count pull-right">
            <?php echo $pagination ? $pagination->getResultsCounter() : null; ?>
            </div>
        </div>        
    </div>    

</form>