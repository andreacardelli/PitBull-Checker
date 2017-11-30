<?php include(DIR_VIEWS.'/inc/head.php'); ?>
  <?php include(DIR_VIEWS.'/inc/header.php'); ?>

    <div class="page-content">
    	<div class="row">
		  <div class="col-md-2">
		  	<?php include(DIR_VIEWS . '/inc/sidebar.php') ?>
		  </div>
		  <div class="col-md-10">

  			<div class="content-box-large">
  				<div class="panel-heading">
					<div class="panel-title"><strong>Status of your checks</strong></div>
					
					<div class="panel-options">
						<!-- <a href="#" data-rel="collapse"><i class="glyphicon glyphicon-refresh"></i></a>
						<a href="#" data-rel="reload"><i class="glyphicon glyphicon-cog"></i></a> -->
					</div>
				</div>
  				<div class="panel-body">
  					<div class="row">
  						<div class="col-md-4 text-center">
  							<h4>Success</h4>
  						</div>
  						<div class="col-md-4 text-center">
  							<h4>Errors</h4>
  						</div>
  						<div class="col-md-4 text-center">
  							<h4>Disabled</h4>
  						</div>
  					</div>
  					<div class="row">
  						<div class="col-md-4 text-center">
  							<input type="text" data-min="0" data-max="<?=count($checks)?>" value="<?=$stats['success']?>" class="knob second" data-readonly="true" data-thickness=".3" data-inputColor="#333" data-fgColor="#8ac368" data-bgColor="#c4e9aa" data-width="200">
  						</div>
  						<div class="col-md-4 text-center">
  							<input type="text" data-min="0" data-max="<?=count($checks)?>" value="<?=$stats['error']?>" class="knob second" data-readonly="true" data-thickness=".3" data-inputColor="#333" data-fgColor="#b85e80" data-bgColor="#a94442" data-width="200">
  						</div>
  						<div class="col-md-4 text-center">
  							<input type="text" data-min="0" data-max="<?=count($checks)?>" value="<?=$stats['disabled']?>" class="knob second" data-readonly="true" data-thickness=".3" data-inputColor="#333" data-fgColor="#30a1ec" data-bgColor="#d4ecfd" data-width="200">
  						</div>
  					</div>
  				</div>
  			</div>

  			<div class="row">
  				<div class="col-md-12">
  					<div class="content-box-large">
		  				<div class="panel-heading">
							<div class="panel-title"><strong>Total Alert Sent</strong></div>
							
							<div class="panel-options">
								<!-- <a href="#" data-rel="collapse"><i class="glyphicon glyphicon-refresh"></i></a>
								<a href="#" data-rel="reload"><i class="glyphicon glyphicon-cog"></i></a> -->
							</div>
						</div>
		  				<div class="panel-body">
		  					<div class="row">
		  						<div class="col-md-4 text-center">
		  							<h4>Telegram</h4>
		  						</div>
		  						<div class="col-md-4 text-center">
		  							<h4>Email</h4>
		  						</div>
		  						<div class="col-md-4 text-center">
		  							<h4>Sms</h4>
		  						</div>
		  					</div>

		  					<div class="col-md-4 text-center">
	  							<input type="text" data-min="0" data-max="<?=$user['telegram']['sent']?>" value="<?=$user['telegram']['sent']?>" class="knob second" data-readonly="true" data-thickness=".3" data-inputColor="#333" data-fgColor="#30a1ec" data-bgColor="#d4ecfd" data-width="200">
	  						</div>
	  						<div class="col-md-4 text-center">
	  							<input type="text" data-min="0" data-max="<?=$user['emails']['sent']?>" value="<?=$user['emails']['sent']?>" class="knob second" data-readonly="true" data-thickness=".3" data-inputColor="#333" data-fgColor="#30a1ec" data-bgColor="#d4ecfd" data-width="200">
	  						</div>
	  						<div class="col-md-4 text-center">
	  							<input type="text" data-min="0" data-max="<?=$user['sms']['sent']?>" value="<?=$user['sms']['sent']?>" class="knob second" data-readonly="true" data-thickness=".3" data-inputColor="#333" data-fgColor="#30a1ec" data-bgColor="#d4ecfd" data-width="200">
	  						</div>
		  				</div>
		  			</div>
  				</div>
  			</div>
			
			
  			<div class="row">
  				<div class="col-md-12">
  					<div class="content-box-large">
		  				<div class="panel-heading">
							<div class="panel-title latestevents" ><strong>Latest events</strong></div>
							
							<div class="panel-options">
								<input type="text" placeholder="Search in table" class="form-control" id="searchInTable" data-table="#latestevents" />
								<!-- <a href="#" data-rel="reload"><i class="glyphicon glyphicon-cog"></i></a> -->
							</div>
						</div>
		  				<div class="panel-body">
							<!-- questa tabella deve diventare una view -->
		  					<table class="table table-responsive table-striped" id="latestevents">
		  						<thead>
									<th class="text-center"></th>
									<th >Name</th>
									<th >Date</th>
									<th >Status</th>
									<th >Status Code</th>
									<th >Response Time</th>
									<th >Message</th>
		  						</thead>
		  						<tbody>
		  						</tbody>
		  					</table>
		  				</div>
		  			</div>
  				</div>
  			</div>
			

		  </div>
		</div>
    </div>
	
	<?php include(DIR_VIEWS . '/inc/graph-modal.php') ?>
<?php include(DIR_VIEWS.'/inc/footer.php'); ?>