<?php //echo \Dsc\Debug::dump( $state, false ); ?>
<?php //echo \Dsc\Debug::dump( $list ); ?>

<form id="assets" class="searchForm" action="./admin/assets" method="post">

    <?php echo $this->renderLayout('Assets/List_Datatable.php'); ?>

</form>