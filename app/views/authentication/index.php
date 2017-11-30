<?php include(DIR_VIEWS.'/inc/head.php'); ?>
  <div class="header">
       <div class="container">
          <div class="row">
             <div class="col-md-12">
                <!-- Logo -->
                <div class="logo">
                   <h1><a href="#"> </a></h1>
                </div>
             </div>
          </div>
       </div>
  </div>

  <div class="page-content container">
    <div class="row">
      <div class="col-md-4 col-md-offset-4">
        <div class="login-wrapper">
              <div class="box">
                  <div class="content-wrap">
                      
                      <center><img src="<?php echo PATH_ASSETS?>/img/h180.png" width="216" height="180"></center>
                      <h2 style="font-family:'Source Sans Pro', sans-serif;margin-bottom:20px">PitBull Checker Log In</h2>
                      <form action="" method="post">
                        <input class="form-control" name="username" type="text" placeholder="Username">
                        <input class="form-control" name="password" type="password" placeholder="Password">
                        <?php if(isset($response) && !empty($response['error'])) { ?>
                        <div class="alert alert-danger"><?=$response['error']?></div>
                        <?php } ?>
                        <div class="action">
                            <input type="submit" class="btn btn-primary signup" value="Login" />
                        </div>        
                      </form>        
                  </div>
              </div>
          </div>
      </div>
    </div>
  </div>
  <?php include(DIR_VIEWS.'/inc/js.php'); ?>
  </body>
</html>