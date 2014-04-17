<div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#createNew">
          Create a New Asset
        </a>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <small><a href="javascript:void(0);" onclick="location.reload();">Refresh after uploading</a> in order to select one of the new assets.</small>
        <div class="pull-right"><a href="javascript:void(0);" onclick="$('#createNew').collapse('toggle');"><span class="fa fa-unsorted"></span> Toggle </a></div>
      </h4>
    </div>
    <div id="createNew" class="panel-collapse collapse">
      <div class="panel-body">
        <?php echo $this->renderLayout('Assets/Admin/Views::assets/create.php'); ?>
      </div>
    </div>
  </div>
</div>

<h3>Select from an Existing Asset</h3>
<form id="assets" class="searchForm" action="./admin/assets/element/<?php echo $PARAMS['id']; ?>" method="post">

    <div class="row">
        <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">
        
            <ul class="list-filters list-unstyled list-inline">
                <li>
                    <select name="filter[type]" class="form-control" onchange="this.form.submit();">
                        <option value="-1">All Types</option>
                        <?php foreach (\Dsc\Models\Assets::distinctTypes() as $type) { ?>
                        	<option value="<?php echo $type; ?>" <?php if ($state->get('filter.type') == $type) { echo "selected='selected'"; } ?>><?php echo $type; ?></option>
                        <?php } ?>                            
                    </select>
                </li>
            </ul>    
            
        </div>
        <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
            <div class="form-group">
                <div class="input-group">
                    <input class="form-control" type="text" name="filter[keyword]" placeholder="Search..." maxlength="200" value="<?php echo $state->get('filter.keyword'); ?>"> <span class="input-group-btn"> <input class="btn btn-primary" type="submit"
                        onclick="this.form.submit();" value="Search"
                    />
                        <button class="btn btn-danger" type="button" onclick="Dsc.resetFormFilters(this.form);">Reset</button>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-5 col-md-3 col-lg-3">
            <span class="pagination">
                <div class="input-group">
                    
                </div>
            </span>
        </div>
        <div class="col-xs-12 col-sm-7 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
            <div class="row text-align-right">
                <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
                    <?php if (!empty($list['count']) && $list['count'] > 1) { ?>
                        <?php echo $pagination->serve(); ?>
                    <?php } ?>
                </div>
                <?php if (!empty($list['subset'])) { ?>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                    <span class="pagination">
                    <?php echo $pagination->getLimitBox( $state->get('list.limit') ); ?>
                    </span>
                </div>
                <?php } ?>
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