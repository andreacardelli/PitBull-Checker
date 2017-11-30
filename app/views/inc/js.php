
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?=PATH_ASSETS?>/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=PATH_ASSETS?>/vendors/tags/js/bootstrap-tags.min.js"></script>

    <link href="<?=PATH_ASSETS?>/vendors/tags-input/tagsinput.min.css" rel="stylesheet">
    <script src="<?=PATH_ASSETS?>/vendors/tags-input/tagsinput.min.js"></script>

    <?php if($controller == 'check' || $controller == 'dashboard') { ?>
    <link href="<?=PATH_ASSETS?>/vendors/datatables/dataTables.bootstrap.css" rel="stylesheet" media="screen">
    <!-- <script src="<?=PATH_ASSETS?>/vendors/datatables/dataTables.bootstrap.js"></script> -->
    <!-- <script src="<?=PATH_ASSETS?>/js/tables.js"></script> -->
    <!-- // <script src="https://code.highcharts.com/highcharts.src.js"></script> -->
    <script src="http://code.highcharts.com/stock/highstock.js"></script>
    <!-- // <script src="http://code.highcharts.com/stock/5.0.9/modules/boost.src.js"></script> -->
    <script src="https://code.highcharts.com/modules/boost.js"></script>
    <script src="https://code.highcharts.com/stock/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <!-- // <script src="https://code.highcharts.com/modules/exporting.js"></script> -->
    <script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
    <script src="<?=PATH_ASSETS?>/vendors/raphael-min.js"></script>
    <link rel="stylesheet" href="<?=PATH_ASSETS?>/vendors/morris/morris.css">
    <script src="<?=PATH_ASSETS?>/vendors/morris/morris.min.js"></script>
    <?php } ?>


    <script src="<?=PATH_ASSETS?>/js/custom.js?v=201711031840"></script>
    <script src="<?=PATH_ASSETS?>/vendors/papaparser/papaparse.min.js"></script>
    
    <?php if($controller == 'stats' || $controller == 'dashboard') { ?>
    <script src="<?=PATH_ASSETS?>/vendors/jquery.knob.js"></script>
    <script src="<?=PATH_ASSETS?>/vendors/flot/jquery.flot.js"></script>
    <script src="<?=PATH_ASSETS?>/vendors/flot/jquery.flot.categories.js"></script>
    <script src="<?=PATH_ASSETS?>/vendors/flot/jquery.flot.pie.js"></script>
    <script src="<?=PATH_ASSETS?>/vendors/flot/jquery.flot.time.js"></script>
    <script src="<?=PATH_ASSETS?>/vendors/flot/jquery.flot.stack.js"></script>
    <script src="<?=PATH_ASSETS?>/vendors/flot/jquery.flot.resize.js"></script>
    <script src="<?=PATH_ASSETS?>/js/stats.js"></script>
    <?php } ?>
