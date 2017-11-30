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
					<div class="panel-title"><h3><?=ucfirst($action)?> Check <?=isset($check['name'])?('"'.$check['name'].'"'):''?></h3></div>
				</div>
  				<div class="panel-body">
  					<form class="col-md-push-6" action="<?=$router->generate('save_check', array('checkid' => $check['id']));?>" method="post">
  						
						<?php if(isset($response['error'])) { ?>
  							<div class="alert alert-danger"><?=$response['error']?></div>
  						<?php }elseif(isset($response['success'])) { ?>
  							<div class="alert alert-success"><?=$response['success']?></div>
  						<?php } ?>

  						<input type="hidden" name="id" value="<?=$check['id']?>" />
  						<input type="hidden" name="user" value="<?=$check['user']?>" />
						
						<div class="clearfix"></div>

						<h4 class="col-md-3 col-md-push-3">General Info</h4>
						
						<div class="clearfix"></div><br/>

						<div class="form-group">
							<div class="col-md-4 col-md-push-3">
								<!-- <label class="form-label">Status</label> -->
								<!-- <label class="custom-control custom-checkbox">
                                    <input style="display: none" type="checkbox" class="custom-control-input">
                                    <span class="custom-control-indicator"></span>
                                </label> -->
                                <label class="switch">
								  <input type="checkbox" name="status"<?=($check['status']=='active') ? 'checked' : ''?> value="active">
								  <span class="slider round"></span>
								</label>
								<strong style="display: inline-block;margin-top: 10px;vertical-align: top;">Check active</strong>
							</div>
							<div class="clearfix"></div>
						</div>

						<div class="form-group">
							<div class="col-md-2 col-md-push-3">
								<label class="form-label">Name *</label>
								<input type="text" required name="name" class="form-control" value="<?=$check['name']?>" />
							</div>
						</div>

						<div class="clearfix"></div>
						<br/>
							
						<div class="form-group">
							<div class="col-md-3 col-md-push-3">
								<label class="form-label">Max Consecutives Errors *</label>
								<input type="number" required class="form-control" name="max_consecutives_errors" value="<?=$check['max_consecutives_errors']?>" />
							</div>
							
							<div class="col-md-3 col-md-push-3">
								<label class="form-label">Frequency</label>
								<div class="input-group">
									<span class="input-group-addon">every</span>
									<input type="text" class="form-control" name="frequency" value="<?=$check['frequency']?>" />
									<span class="input-group-addon">minutes</span>
								</div>
							</div>
						</div>

						<div class="clearfix"></div>
						<br/>

						<div class="form-group">
							<div class="col-md-2 col-md-push-3">
								<label class="form-label">HTTP Request Type</label>
								<!-- <select required class="form-control" name="request_type"> -->
								<select required class="form-control" id="request-method" name="check[method]">
									<option value="GET" <?=$check['check']['method']=='GET' ? 'selected' : ''?>>GET</option>
									<option value="POST" <?=$check['check']['method']=='POST' ? 'selected' : ''?>>POST</option>
									<option value="HEAD" <?=$check['check']['method']=='HEAD' ? 'selected' : ''?>>HEAD</option>
								</select>
							</div>
	
							<div class="col-md-4 col-md-push-3">
								<label class="form-label">Url *</label>
								<input required type="text" class="form-control" value="<?=$check['check']['url']?>" name="check[url]" />
							</div>

						</div>
						<div class="clearfix"></div>
						<br/>
						
						<div id="params-container" <?php if($check['check']['method'] != 'POST') { ?> style="display: none" <?php } ?> >
							<h4 class="col-md-5 col-md-push-3">Form Params<div data-parent-id="params-container" title="Add a params" class="tooltips button-service addParams"><i class="glyphicon glyphicon-plus"></i> Add params</div></h4>
							<div class="clearfix"></div>

							<?php foreach ($check['check']['form_params'] as $key => $value) { ?>

							<div class="form-group rowParam">
								<div class="col-md-2 col-md-push-3">
									<label class="form-label">Key</label>
									<input type="text" class="form-control paramsKey" value="<?=$key?>" />
								</div>
								<div class="col-md-4 col-md-push-3">
									<label class="form-label">Value</label>
									<input type="text" class="form-control paramsValue" value="<?=$value?>" name="check[form_params][<?=$key?>]" />
								</div>

								<div class="col-md-1 col-md-push-3 text-left" style="top: 20px">
									<i class="glyphicon glyphicon-trash removeRow tooltips" title="remove param"></i>
								</div>

								<div class="clearfix"></div>
							</div>

							<?php } ?>

						</div>

						<div class="clearfix"></div>
						<br/>

						<h4 class="col-md-5 col-md-push-3">HTTP Basic Auth <input <?=(isset($check['check']['auth']['username']))?'checked':''?> type="checkbox" onclick="$('#container-auth').toggle();" /></h4>
						<div class="clearfix"></div>

						<div class="form-group" id="container-auth" <?php if(!isset($check['check']['auth']['username'])) { ?> style="display: none" <?php } ?> >
							<div class="col-md-3 col-md-push-3">
								<label class="form-label">Auth username</label>
								<input type="text" class="form-control" value="<?=$check['check']['auth']['username']?>" name="check[auth][username]" />
							</div>
							<div class="col-md-3 col-md-push-3">
								<label class="form-label">Auth password</label>
								<input type="text" class="form-control" value="<?=$check['check']['auth']['password']?>" name="check[auth][password]" />
							</div>
						</div>

						<div class="clearfix"></div>
						<br/>
						
						<div id="alert-container">
							<h4 class="col-md-5 col-md-push-3">Notify (alert) <div title="Add a notify address" data-user="<?=htmlentities(json_encode($userChannels))?>" class="tooltips button-service addNotify" data-parent-id="alert-container"><i class="glyphicon glyphicon-plus"></i> Add Notify Address</div></h4>
							<div class="clearfix"></div>

							<div class="col-md-3 col-md-push-3"><strong>Channel</strong></div>
							<div class="col-md-3 col-md-push-3"><strong>Address</strong></div>
							<div class="clearfix"></div>

							<?php foreach ($check['alert'] as $channel => $array) { ?>
							<?php foreach ($array as $value) { ?>
							<div class="form-group">
								<div class="col-md-3 col-md-push-3">
									<select class="form-control channelList">
										<option value="">-- select a channel --</option>
										<?php foreach (array_keys($userChannels) as $userChannel) { ?>
										<option <?=($userChannel == $channel)?'selected':''?> value="<?=$userChannel?>"><?=$userChannel?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-md-3 col-md-push-3">
									<!-- <input type="text" class="form-control channelValue" name="alert[<?=$channel?>][]" value="<?=implode(',', $array);?>" /> -->
									<select class="form-control channelValue" name="alert[<?=$channel?>][]">
										<?php foreach ($userChannels[$channel] as $address) { ?>
											<option <?=($address == $value) ? 'selected' : ''?> value="<?=$address?>"><?=$address?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-md-1 col-md-push-3 text-left">
									<i class="glyphicon glyphicon-trash removeRow tooltips" title="remove channel"></i>
								</div>
								<div class="clearfix"></div>
							</div>
							<?php } ?>
							<?php } ?>
						</div>

						<br/>
						<div class="clearfix"></div>
						<br/>
						
						<div id="criteria-container">
							<h4 class="col-md-5 col-md-push-3">Success Criteria <div data-parent-id="criteria-container" title="Add a criteria" class="tooltips button-service addCriteria"><i class="glyphicon glyphicon-plus"></i> Add success criteria</div></h4>
							<div class="clearfix"></div>

							<div class="col-md-3 col-md-push-3"><strong>Type</strong></div>
							<div class="col-md-3 col-md-push-3"><strong>Value</strong></div>
							<div class="clearfix"></div>

						<?php foreach ($check['check']['success_criteria'] as $key => $success_criteria) { ?>
							<div class="form-group">
								<div class="col-md-3 col-md-push-3">
									<!-- <label class="form-label">Action</label> -->
									<select name="check[success_criteria][<?=$key?>][action]" class="form-control success_criteria_type">
										<option data-operation="=" <?=(key($success_criteria) == 'http_response' ? 'selected' : '')?> value="http_response">http_response</option>
										<option data-operation="<" <?=(key($success_criteria) == 'http_response_time' ? 'selected' : '')?> value="http_response_time">http_response_time</option>
										<option data-operation="contains" <?=(key($success_criteria) == 'body_contains' ? 'selected' : '')?> value="body_contains">body_contains</option>
										<option data-operation="not contains" <?=(key($success_criteria) == 'body_not_contains' ? 'selected' : '')?> value="body_not_contains">body_not_contains</option>
									</select>
								</div>
								<div class="col-md-3 col-md-push-3">
									<!-- <label class="form-label"></label> -->
									<div class="input-group">
										<span class="input-group-addon">=</span>
										<input type="text" class="form-control" name="check[success_criteria][<?=$key?>][value]" value="<?=$success_criteria[key($success_criteria)]?>" />
									</div>
								</div>
								<div class="col-md-1 col-md-push-3 text-left">
									<i class="glyphicon glyphicon-trash removeRow" title="remove channel"></i>
								</div>
								<div class="col-md-5"></div>
								<div class="clearfix"></div>
							</div>
						<?php } ?>
						</div>
						
						<div class="form-group">
							<div class="col-sm-2 col-md-push-5">
								<br/>
	  							<input type="submit" class="btn btn-block btn-info" value="Save" />
  							</div>
  						</div>
  					</form>
  				</div>
  			</div>


		  </div>
		</div>
    </div>
    
	<div id="dialog-notify" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Add Notify Alert</h4>
	      </div>
	      <div class="modal-body">
	        <p>Select a channel and an address</p>
	        <div class="form-group">
	        	<div class="col-md-5">
		        	<select id="channelList" class="form-control">
		        		<option value="">-- Select a channel --</option>
		        	</select>
	        	</div>
	        	<div class="col-md-7">
	        		<select class="form-control" id="channelValue">
	        			<option value="">-- Select an address --</option>
	        		</select>
	        	</div>
	        </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-primary saveNotify" data-dismiss="modal">Save</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>

	  </div>
	</div>

<?php include(DIR_VIEWS . '/inc/footer.php') ?>