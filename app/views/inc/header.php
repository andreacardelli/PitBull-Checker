  	<div class="header">
	     <!-- <div class="container"> -->
	        <div class="row">
	           <div class="col-md-3">
	              <!-- Logo -->
	              <div class="logo">
	                 <img src="<?php echo PATH_ASSETS?>/img/h50-nogrey.png" clas="logoimg"><h1><a href="<?=$router->generate('list_checker')?>">PitBull Checker</a></h1>
	              </div>
	           </div>
	           <!-- <div class="col-md-5">
	              <div class="row">
	                <div class="col-lg-12">
	                  <div class="input-group form">
	                       <input type="text" class="form-control" placeholder="Search...">
	                       <span class="input-group-btn">
	                         <button class="btn btn-primary" type="button">Search</button>
	                       </span>
	                  </div>
	                </div>
	              </div>
	           </div> -->
	           <div class="pull-right col-md-2">
	              <div class="navbar navbar-inverse" role="banner">
	                  <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
	                    <ul class="nav navbar-nav">
	                      <li class="dropdown">
	                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">My Account (<?=$_SESSION['user']?>) <b class="caret"></b></a>
	                        <ul class="dropdown-menu animated fadeInUp">
	                          <li><a href="<?=$router->generate('user_settings', array('userid' => $_SESSION['user']))?>">Profile</a></li>
	                          <li><a href="<?=$router->generate('do_logout')?>">Logout</a></li>
	                        </ul>
	                      </li>
	                    </ul>
	                  </nav>
	              </div>
	           </div>
	        </div>
	     <!-- </div> -->
	</div>