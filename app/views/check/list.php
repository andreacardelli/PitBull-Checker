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
					<div class="panel-title">Checks List</div>
					<div class="pull-right">
						<a href="<?=$router->generate('create_check')?>" class="btn btn-info">+ Add new check</a>
					</div>
				</div>
  				<div class="panel-body">
  					<div class="table-responsive table-condensed">
  						<table class="table table-striped checkTable">
			              <thead>
			                <tr>
		                	  <th><input onclick="var checked = $(this).prop('checked'); $('.row-select').prop('checked', checked);" class="tooltips checkbox-inline" title="Select All" type="checkbox" /></th>
			                  <th class="text-center">Check Active?</th>
			                  <th>Name</th>
			                  <th class="text-center">Frequency (minutes)</th>
			                  <th class="text-center">Last Check Status</th>
			                  <th>Last Check</th>
			                  <th class="text-right" style="min-width: 150px">Avgerage Total<br>Response Time</th>
			                  <th class="text-center">Last Total<br>Response Time</th>
			                  <th class="text-center">Last Time<br>To First Byte</th>
			                  <th class="text-center">Redirect</th>
			                  <th></th>
			                  <th class="text-center"></th>
			                  <th class="text-center"></th>
			                  <th class="text-center"></th>
			                </tr>
			              </thead>
			              <tbody>
			              	<?php foreach ($checks as $key => $check) { 
			              		// Need to be a timestamp in ms (they are in seconds)
			              		$check['last_check']['stats']['namelookup_time'] = (int)($check['last_check']['stats']['namelookup_time']*1000);
								$check['last_check']['stats']['connect_time'] = (int)($check['last_check']['stats']['connect_time']*1000);
								$check['last_check']['stats']['pretransfer_time'] = (int)($check['last_check']['stats']['pretransfer_time']*1000);
								$check['last_check']['stats']['starttransfer_time'] = (int)($check['last_check']['stats']['starttransfer_time']*1000);
				              	
				              	// Build html for popover of graph type
				              	$methodGraph = "<div style=\"width:200px\">";
				              	$methodGraph .= "<select class='form-control timeSelect'>";
				              	$methodGraph .= "<option value=''>-- Select source of graph --</option>";
				              	$methodGraph .= '<option value="">Total Response Time</option>';
				              	$methodGraph .= '<option value="namelookup_time">NameLookup Time</option>';
				              	$methodGraph .= '<option value="connect_time">Connect Time</option>';
				              	$methodGraph .= '<option value="pretransfer_time">Pretransfer Time</option>';
				              	$methodGraph .= '<option value="starttransfer_time">Time to First Byte</option>';
				              	$methodGraph .= "</select>";
				              	$methodGraph .= '<div class="clearfix"></div><br/>';
				              	$methodGraph .= '<center><a class="viewGraph edit-btn btn btn-info btn-sm" data-title="'.$check['name'].'" data-avg="'.(int)$check['all_checks']['avg_time'].'" data-user="'.$_SESSION['user'].'" data-id="'.$check['id'].'">Open</a></center>';
				              	$methodGraph .= '</div>';

				              	// Take the partial for each type of time
								$connectTime = $check['last_check']['stats']['connect_time'] - $check['last_check']['stats']['namelookup_time'];
								$preTransferTime = $check['last_check']['stats']['pretransfer_time'] - $check['last_check']['stats']['connect_time'];
								$startTransferTime = $check['last_check']['stats']['starttransfer_time'] - $check['last_check']['stats']['pretransfer_time'];
								$transferTime = $check['last_check']['time'] - $check['last_check']['stats']['starttransfer_time'];

								// Assign perc in array
								$width = array(
										'namelookup_time' => @calcPercGraph($check['last_check']['stats']['namelookup_time'], $check['last_check']['time']),
										'connect_time' => @calcPercGraph($connectTime, $check['last_check']['time']),
										'pretransfer_time' => @calcPercGraph($preTransferTime, $check['last_check']['time']),
										'starttransfer_time' => @calcPercGraph($startTransferTime, $check['last_check']['time']),
										'transfer_time' => @calcPercGraph($transferTime, $check['last_check']['time']),
									);

								$totalWidth = array_sum($width);

								$i = 0;
								// Fix progress bar width (we need to view all the bars)
								while($totalWidth < 100 && $i < 20) {
									foreach ($width as $key => $value) {
										if($value < 10) {
											$width[$key]++;
											$totalWidth++;
											break;
										}
									}
									$i++;
								}

			              		$status = (isset($check['last_check']['status']))?$check['last_check']['status']:'UNKNOWN';
			              		// Create popover html with progressbar
			              		$progressBar = '<div class="progress" style="width: 200px">';
								$progressBar .= '<div class="progress-bar progress-bar-warning tooltips" title="Name Lookup" role="progressbar" style="width: '.$width['namelookup_time'].'%">';
								$progressBar .= $check['last_check']['stats']['namelookup_time'];
								$progressBar .= '</div>';
								$progressBar .= '<div class="progress-bar progress-bar-danger tooltips" title="Connect" role="progressbar" style="width: '.$width['connect_time'].'%">';
								$progressBar .= $connectTime;
								$progressBar .= '</div>';
								$progressBar .= '<div class="progress-bar progress-bar-success tooltips" title="PreTransfer" role="progressbar" style="width: '.$width['pretransfer_time'].'%">';
								$progressBar .= $preTransferTime;
								$progressBar .= '</div>';
								$progressBar .= '<div class="progress-bar progress-bar-info tooltips" title="StartTransfer (TTFB)" role="progressbar" style="width: '.$width['starttransfer_time'].'%">';
								$progressBar .= $startTransferTime;
								$progressBar .= '</div>';
								$progressBar .= '<div class="progress-bar progress-bar-success tooltips" title="TransferTime" role="progressbar" style="width: '.$width['transfer_time'].'%">';
								$progressBar .= $transferTime;
								$progressBar .= '</div>';
								$progressBar .= '</div>';
								$progressBar .= '<span>'.((isset($check['last_check']['message']) && strtolower($check['last_check']['message']) != 'all rules were met!') ? $check['last_check']['message'] : '').'</span>';

		              		?>
			                <tr>
			                	<td><input data-user="<?=$_SESSION['user']?>" data-checkid="<?=$check['id']?>" type="checkbox" class="row-select" /></td>
			                  <td align="center">
			                  	<!-- <a data-user="<?=$_SESSION['user']?>" title="<?=($check['status']=='disabled')?'Click to active':'Click to disable'?>" data-checkid="<?=$check['id']?>" class="tooltips glyphicon glyphicon-<?=($check['status'] == 'disabled')?'play':'stop'?>"></a> -->
			                  	<i title="<?=$statusIcon[$check['status']]['label']?>" class="status-check tooltips glyphicon glyphicon-<?=$statusIcon[$check['status']]['icon']?>"></i>
			                  </td>
			                  <td class="mw240">
			                  	<big style="cursor: pointer" onclick="$(this).parent().parent().find('.openPopover').trigger('click'); return false;" class="tooltips" title="<?=$check['check']['url']?>">
			                  		<strong><?=$check['name']?></strong>
			                  	</big>
			                  	<!-- <button data-user="<?=$_SESSION['user']?>" data-checkid="<?=$check['id']?>" class="btn btn-sm showCurrentGraph">Show Live Graph</button> -->
			                  	<!-- <div style="height: 350px; width: 650px; display: none" id="hero-graph-<?=$check['id']?>"></div> -->
			                  </td>
			                  <td align="center" class="frequency"><?=$check['frequency']?></td>
			                  <td align="center">
			                  	<div title="<?=$status?>" class="statuses tooltips status-<?=$status?>">
			                  		<?=$statusSimbol[$status]?>
			                  	</div>
			                  </td>
			                  <td style="min-width: 140px">
			                  	<i class="glyphicon glyphicon-info-sign tooltips alert-warning" title="<?=$check['last_check']['message']?>"></i>
			                  	<span><?=(isset($check['last_check']['timestamp']))?date('Y-m-d H:i:s', (int)$check['last_check']['timestamp']):''?></span>
			                  </td>
			                  <td align="right">
			                  	<span><?=(int)$check['all_checks']['avg_time']?> ms</span>
			                  </td>
			                  <td align="right" class="responseTime">
			                  	<span data-toggle="popover" style="cursor: pointer" data-content="<?=htmlentities($progressBar)?>" data-placement="top" title="Total time: <?=$check['last_check']['time']?> ms"><?=(int)$check['last_check']['time']?> ms</span>
			                  </td>
			                  <td align="right" class="responseTime">
			                  	<span><?=$startTransferTime?> ms</span>
			                  </td>
			                  <td align="center">
			                  	<?php if(isset($check['last_check']['redirect']) && $check['last_check']['redirect'] == 1) { ?>
			                  	<div>
			                  	<span title="The url has a redirect" class="tooltips label label-success">Yes</span>
			                  	</div>
			                  	<?php } ?>
			                  </td>
			                  <td align="center">
			                  	<?php if(isset($check['check']['errors']) && $check['check']['errors'] >= 3) { ?>
			                  	<div>
			                  	<span title="The notification has been sent" class="tooltips label label-danger">Alert Sent</span>
			                  	</div>
			                  	<?php } ?>
			                  </td>
			                  <td align="center">
			                  	<a data-toggle="popover" data-placement="top" data-content="<?=htmlentities($methodGraph)?>" href="#" title="View graph" class="edit-btn openPopover tooltips glyphicon glyphicon-stats"></a>
			                  </td>
			                  <td align="center">
			                  	<a href="#" data-user="<?=$_SESSION['user']?>" data-id="<?=$check['id']?>" title="View Error Report" class="edit-btn openTable tooltips glyphicon glyphicon-th-list"></a>
			                  </td>
			                  <td align="center">
			                  	<a data-id="<?=$check['id']?>" href="<?=$router->generate('edit_check', array('checkid' => $check['id']))?>" title="Edit" class="edit-btn green-icon glyphicon glyphicon-edit tooltips"></a>
			                  </td>
			                </tr>
			              	<?php } ?>
			              </tbody>
			            </table>
  					</div>
  					<div class="col-md-2">
  						<select id="selectAction" class="form-control">
  							<option value="">-- Select an action --</option>
  							<option value="enable">Enable</option>
  							<option value="disable">Disable</option>
  							<option value="changefrequency">Change Frequency</option>
  							<option value="changemaxerrors">Change Max Errors</option>
  							<option value="reseterrors">Reset Errors</option>
  						</select>
  					</div>
  					<div class="col-md-3">
  						<button class="btn btn-info updateBtn">Update</button>
  					</div>
  				</div>
  			</div>


		  </div>
		</div>
    </div>

    <div id="errorModal" class="modal fade" role="dialog">
	  <div class="modal-dialog" style="width: 60%">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Errors</h4>
	      </div>
	      <div class="modal-body" style="max-height: 450px; overflow-y: scroll;">
	      	<table class="table table-striped table-responsive text-center">
	      		<thead>
	      			<th></th>
	      			<th class="text-center">Date</th>
	      			<th class="text-center">Status</th>
	      			<th class="text-center">Code</th>
	      			<th class="text-center">Response Time</th>
	      			<th class="text-center">Message</th>
	      		</thead>
	      		<tbody>
	      		</tbody>
	      	</table>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>

	  </div>
	</div>

	<?php include(DIR_VIEWS . '/inc/graph-modal.php') ?>
<?php include(DIR_VIEWS . '/inc/footer.php') ?>