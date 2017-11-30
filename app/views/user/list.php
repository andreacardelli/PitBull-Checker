<?php include(DIR_VIEWS . '/inc/head.php') ?>
	<?php include(DIR_VIEWS . '/inc/header.php') ?>

    <div class="page-content">
    	<div class="row">
		  <div class="col-md-2">
			<?php include(DIR_VIEWS . '/inc/sidebar.php') ?>
		  </div>
		  <div class="col-md-10">

  			<div class="content-box-large">
  				<div class="panel-heading">
					<div class="panel-title">User list</div>
					<div class="pull-right">
						<a href="<?=$router->generate('create_user')?>" class="btn btn-info">+ Add new user</a>
					</div>
				</div>
  				<div class="panel-body">
  					<div class="table-responsive table-condensed">
  						<table class="table table-striped">
			              <thead>
			                <tr>
			                  <th class="text-center">Name</th>
			                  <th class="text-center">Email</th>
			                  <th class="text-center">Role</th>
			                  <th class="text-center"></th>
			                </tr>
			              </thead>
			              <tbody>
			              	<?php foreach ($users as $user) { 
		              		?>
			                <tr>
			                  <td align="center"><?=$user["user"]?></td>
			                  <td align="center"><?=$user["email"]?></td>
			                  <td align="center"><?=$user["role"]?></td>
			                  <td align="center">
			                  	<a href="<?=$router->generate('user_settings', array('userid' => $user['user']))?>" title="Edit" class="edit-btn green-icon glyphicon glyphicon-edit tooltips"></a>
			                  </td>
			                </tr>
			              	<?php } ?>
			              </tbody>
			            </table>
  					</div>
  				</div>
  			</div>


		  </div>
		</div>
    </div>

    <!-- <div id="graphModal" class="modal fade" role="dialog">
	  <div class="modal-dialog" style="width: 60%">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Check Graph</h4>
	      </div>
	      <div class="modal-body">
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>

	  </div>
	</div> -->

	<?php include(DIR_VIEWS . '/inc/graph-modal.php') ?>
<?php include(DIR_VIEWS . '/inc/footer.php') ?>