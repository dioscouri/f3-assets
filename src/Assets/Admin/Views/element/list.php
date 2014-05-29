<div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="javascript:void(0);" onclick="$('#createNew').collapse('toggle');">
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
                        <option value="">All Types</option>
                        <?php foreach (\Dsc\Mongo\Collections\Assets::distinctTypes() as $type) { ?>
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
			<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
				<?php if (!empty($paginated->total_pages) && $paginated->total_pages > 1) { ?>
					<?php echo $paginated->serve(); ?>
				<?php } ?>
			</div>
			<?php if (!empty($paginated->items)) { ?>
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
					<span class="pagination">
						<?php echo $paginated->getLimitBox( $state->get('list.limit') ); ?>
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
    			<th data-sortable="title">Title</th>
    			<th class="col-md-1">Location</th>
    		</tr>
    	</thead>
    	<tbody>    
    
        <?php if (!empty($paginated->items)) { ?>
    
            <?php foreach($paginated->items as $item) 
            	{ ?>
            <tr>
                
                <td class="">
                    <?php if ($item->thumb) { ?>
                        <div class="thumbnail text-center">
                        <img src="./asset/thumb/<?php echo $item->slug; ?>" alt="<?php echo $item->{'title'}; ?>" />
                        </div>
                    <?php } ?>
                </td>
                
                <td class="">
                    <h5>
                    <a onclick="window.parent.<?php echo $select_function_name; ?>('<?php echo $item->$elementItemKey; ?>', '<?php echo str_replace( array("'", "\""), array("\\'", ""), $item->{'title'} ); ?>', '<?php echo $PARAMS['id']; ?>' );" href="javascript:void(0);">
                    <?php echo $item->{'title'}; ?>
                    </a>
                    </h5>
    
                    <a class="help-block" target="_blank" href="./asset/<?php echo $item->{'slug'}; ?>">
                    /<?php echo $item->{'slug'}; ?>
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
    
	 <div class="dt-row dt-bottom-row">
		<div class="row">
			<div class="col-sm-10">
		    	<?php if (!empty($paginated->total_pages) && $paginated->total_pages > 1) { ?>
		        	<?php echo $paginated->serve(); ?>
		        <?php } ?>
	      	</div>
	     	<div class="col-sm-2">
	       		<div class="datatable-results-count pull-right">
	           		<span class="pagination">
	                	<?php echo (!empty($paginated->total_pages)) ? $paginated->getResultsCounter() : null; ?>
	            	</span>
	        	</div>
	    	</div>        
		</div>
	</div>
    
</form>