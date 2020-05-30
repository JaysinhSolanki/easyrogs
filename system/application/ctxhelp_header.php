<?php
require_once(SYSTEMPATH.'bootstrap.php');
header('Content-Type: text/html; charset=UTF-8');
require_once(FRAMEWORK_PATH."head.php");
?>
<style>
.navbar-nav > li > a:hover,
.navbar-nav > li > a:focus,
.navbar-nav .open > a,
.navbar-nav .open > a:hover,
.navbar-nav .open > a:focus {
  background: #FFFFFF !important;
}
navbar.navbar-static-top a, .nav.navbar-nav li a {
     color: #34495e !important;
}
.fixed-navbar #wrapper {
    top: 0px;
}
</style>
<?php
require_once(FRAMEWORK_PATH.'faq_modal.php');
require_once(SYSTEMPATH.'application/ctxhelp_modal.php');
?>

<div id="header" class="" style="background-color:#f7f9fa">
    <div class="color-line" />
    <div id="logo" class="light-version"> 
        <h4 style="color:#34495e; font-size:17px; font-weight:600; ">
            <a class="mylogo f32" href="index.php">
                <?= $systemmaintitle ?> 
                <?php if ($_ENV['APP_ENV'] !== 'prod'): ?>
                  <div style="font-family: Arial; color: white; font-size: 12px; display: inline-block; background-color: red; padding: 3px; text-transform: uppercase"><?= exec('git rev-parse --abbrev-ref HEAD') ?></div>
                <?php endif; ?>
            </a>
        </h4> 
    </div>
        
    <!-- Right sidebar -->
    <nav role="navigation">
        <div class="navbar-right">
            <ul class="nav navbar-nav no-borders">
<?php
    if( $_SESSION['name'] ) {
?>
                <li class="dropdown"  id="nav-welcome">
                    <h4 style="color:#34495e !important; font-size:12px !important; font-weight:500 !important; padding-top:10px; margin-right:20px;">  
                        Welcome <?= $_SESSION['name'] ?>
                    </h4>
                </li>
<?php
    }
?>
                <li id="nav-ctxhelp">
                    <h4 style="color:#34495e !important; font-size:12px !important; font-weight:500 !important; padding-top:10px; margin-right:20px;">  
                        <a onclick="javascript:showCtxHelp();" href="javascript:;"><b>Help</b></a>
                    </h4>
                </li>
                <li class="dropdown"  id="nav-faq">
                    <h4 style="color:#34495e !important; font-size:12px !important; font-weight:500 !important; padding-top:10px; margin-right:20px;">  
                        <a onclick="javascript:showFAQ();" href="javascript:;"><b>FAQ</b></a>
                    </h4>
                </li>
            </ul>
        </div><!-- navbar-right -->
    </nav>
</div><!-- header -->
