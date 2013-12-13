<?php //echo \Dsc\Debug::dump( $state, false ); ?>
<?php //echo \Dsc\Debug::dump( $PARAMS ); ?>

<form id="assets" class="searchForm" action="./admin/assets/element/<?php echo $PARAMS['id']; ?>" method="post">

    <?php echo $this->renderLayout('element/list_datatable.php'); ?>

</form>