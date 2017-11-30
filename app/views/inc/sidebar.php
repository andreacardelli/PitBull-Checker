<div class="sidebar content-box" style="display: block;">
    <ul class="nav">
        <!-- Main menu -->
        <li><a href="<?=$router->generate('dashboard')?>"><i class="glyphicon glyphicon-home"></i> Dashboard</a></li>
        <!-- <li><a href="<?=$router->generate('stats')?>"><i class="glyphicon glyphicon-stats"></i> Statistics (Charts)</a></li> -->
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'superadmin') { ?>
        <li><a href="<?=$router->generate('list_user')?>"><i class="glyphicon glyphicon-user"></i> Users</a></li>
        <?php } ?>
        <li><a href="<?=$router->generate('list_checker')?>"><i class="glyphicon glyphicon-stats"></i> Checks</a></li>
        <!-- <li class="submenu open">
             <a href="#">
                <i class="glyphicon glyphicon-list"></i> Checks
                <span class="caret pull-right"></span>
             </a>
            
             <ul>
                <li><a href="<?=$router->generate('list_checker')?>">List</a></li>
                <li><a href="<?=$router->generate('create_check')?>">Create</a></li>
            </ul>
        </li> -->
    </ul>

</div>
<center>
<img src="<?php echo PATH_ASSETS?>/img/logo-mono.png" height="180">
</center>
<br><br>