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
					<div class="panel-title" style="padding-left: 0"><h2><?=($action=='create')?'Create':'Edit'?> User Profile</h2></div>
				</div>
  				<div class="panel-body">
  					<!-- <div class="row"> -->
	  					<form action="<?=$router->generate('save_user', array('userid' => ((isset($parameters['userid'])) ? $parameters['userid'] : 'toreplace')))?>" method="post">
	  						
	  						<?php if(isset($response['error'])) { ?>
	  							<div class="alert alert-danger"><?=$response['error']?></div>
	  						<?php }elseif(isset($response['success'])) { ?>
	  							<div class="alert alert-success"><?=$response['success']?></div>
	  						<?php } ?>

	  						<h4>Data login:</h4>
	  						<div class="form-group">
	  							<div class="col-sm-4">
	  								<label class="form-label">User</label>
	  								<input type="text" id="userid" class="form-control" name="user" value="<?=$user['user']?>" required />
	  							</div>
	  							<div class="col-sm-4">
	  								<label class="form-label">Email</label>
	  								<input type="email" class="form-control" name="email" value="<?=$user['email']?>" required />
	  							</div>
	  							<div class="col-sm-4">
	  								<label class="form-label">Password</label>
	  								<input type="password" class="form-control" name="password" value="<?=$user['password']?>" required />
	  							</div>
	  						</div>
	  						
							<div class="clearfix"></div>

							<h4>Data notification (channels):</h4>

							<div class="telegram-container">
		  						<div class="form-group">
		  							<div class="col-sm-1">
		  								<h4>Telegram</h4>
		  								<!-- <span class="tooltips button-service addTelegram">Add more chat</span> -->
		  							</div>
		  							<div class="col-sm-5">
		  								<label class="form-label">Botid</label>
		  								<input type="text" class="form-control" name="telegram[botid]" value="<?=$user['telegram']['botid']?>" />
		  							</div>
		  							<div class="col-sm-5">
		  								<label class="form-label">Chatids</label>
	  									
		  								<?php // foreach ((array)$user['telegram']['chatids'] as $chat) { ?>
	  									<input type="text" value="<?=implode(',',(array)$user['telegram']['chatids'])?>" class="form-control tags" name="telegram[chatids]" />
		  								<?php // } ?>
		  									
		  							</div>
		  						</div>
							</div>

							<div class="clearfix"></div>

							<div class="email-container">
		  						<div class="form-group">
		  							<div class="col-sm-1">
		  								<h4>Emails</h4>
		  								<!-- <label class="form-label"><br/></label>
		  								<input type="text" disabled class="form-control" value="Email" /> -->
		  							</div>
		  							<!-- <div class="col-sm-3">
		  								<label class="form-label">Recipients</label>
		  								<input type="text" class="form-control" name="emails[recipients][0][email]" value="<?=$user['emails']['recipients'][0]['email']?>" />
		  							</div> -->
		  							<div class="col-sm-10">
		  								<label class="form-label">Recipients</label>
	  									
		  								<?php //foreach ((array)$user['emails']['recipients'] as $i => $email) { ?>
	  									<input type="text" value="<?php foreach ((array)$user['emails']['recipients'] as $email){ echo $email['email'].','; }?>" class="form-control tags" name="emails[recipients]" />
		  								<?php //} ?>
		  									
		  							</div>
		  						</div>
							</div>
							
							<div class="clearfix"></div>

							<div class="sms-container">
		  						<div class="form-group">
		  							<div class="col-sm-1">
		  								<h4>SMS</h4>
		  								<!-- <label class="form-label"><br/></label>
		  								<input type="text" disabled class="form-control" value="SMS" /> -->
		  							</div>
		  							<div class="col-sm-10">
		  								<label class="form-label">Numbers</label>
	  									
		  								<?php //foreach ((array)$user['sms']['numbers'] as $number) { ?>
	  									<input type="text" class="form-control tags" name="sms[numbers]" value="<?=implode(',',(array)$user['sms']['numbers'])?>" />
		  								<?php //} ?>
		  									
		  							</div>
		  						</div>
							</div>
							
							<div class="clearfix"></div>
							<br/>

							<div class="form-group">
								<div class="col-md-3 col-md-push-4">
									<input type="submit" onclick="$('form').attr('action', $('form').attr('action').replace('toreplace', $('#userid').val()));" class="btn btn-block btn-info" />
								</div>
							</div>

	  					</form>
  					<!-- </div> -->
  				</div>
  			</div>


		  </div>
		</div>
    </div>
<?php include(DIR_VIEWS . '/inc/footer.php') ?>