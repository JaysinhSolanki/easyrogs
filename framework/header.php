<?php
//echo "HELLO>>>>>>>>>>>>>>>>>";
//echo __LINE__;
@session_start();
//echo $_SESSION['gumption_path'];
require_once($_SESSION['gumption_path']."adminsecurity.php");
//echo __LINE__;

$addressbookid			=	$_SESSION['addressbookid'];
$_SESSION['section']	=	@$_GET['sectionid'];
$section				=	$_SESSION['section'];
$tabres					=	$AdminDAO->getrows("system_screen s,system_groupscreen gs,system_groups g, system_addressbook a","s.*"," 1 AND s.pkscreenid = gs.fkscreenid AND gs.fkgroupid = g.pkgroupid AND a.fkgroupid = g.pkgroupid AND pkaddressbookid = '$addressbookid' ");
$cases	=	$AdminDAO->getrows(
								"cases c,attorneys_cases ac"
								,
								"								
								c.id as id,
								c.uid,
								c.plaintiff,
								c.defendant,
								case_title,
								case_number,
								jurisdiction,
								county_name,
								judge_name,
								date_filed 
								",
								"ac.attorney_id  = :attorney_id AND
								c.id = ac.case_id
								",
								array(
										":attorney_id"=>$_SESSION['addressbookid']
									 )
							  );
//dump($cases);
?>
<style>

.datepicker{z-index:1151 !important;}
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
.hpanel
{
	margin-top:60px;
}
.fixed-navbar #wrapper {
    top: 0px;
}
</style>
<div id="header" class="" style="background-color:#f7f9fa">
    <div class="color-line"> </div>
    <div id="logo" class="light-version"> <h4 style="color:#34495e; font-size:17px; font-weight:600; "><a class="mylogo f32" href="index.php"><?php echo $systemmaintitle;?></a></h4> </div>
    <nav role="navigation">
        <div class="navbar-right">
            <ul class="nav navbar-nav no-borders">
                <li class="dropdown"  id="profile-right">
                	<h4 style="color:#34495e !important; font-size:12px !important; font-weight:500 !important; padding-top:10px; margin-right:20px;">  
                        Welcome <?php echo $_SESSION['name']; ?>
                    </h4>
                </li>
            	<li class="dropdown"  id="profile-right">
                	<h4 style="color:#34495e !important; font-size:12px !important; font-weight:500 !important; padding-top:10px; margin-right:20px;">  
                        <a href="javascript:void(0);" onclick="selecttab('44_tab','cases.php','44');"><b>Cases</b></a> 
                    </h4>
                </li>
            	<li class="dropdown"  id="profile-right">
                	<h4 style="color:#34495e !important; font-size:12px !important; font-weight:500 !important; padding-top:10px; margin-right:20px;">  
                        <a onclick="javascript:PopupfaqModal();" href="javascript:;"><b>FAQ</b></a>
                    </h4>
                </li>
                <li class="dropdown"  id="profile-right">
                	<h4 style="color:#34495e !important; font-size:12px !important; font-weight:500 !important; padding-top:10px; margin-right:20px;">  
                        <a href="javascript:;" onclick="javascript: selecttab('8_tab','<?php echo FRAMEWORK_URL; ?>profile.php','8');">
 	                       <b>My Profile</b>
                        </a>
                    </h4>
                </li>
                <li class="dropdown"  id="profile-right" style="padding-right: 16px;">
                	<h4 style="color:#34495e !important; font-size:12px !important; font-weight:500 !important; padding-top:10px; margin-right:20px;">  
                        <a href="<?php echo FRAMEWORK_URL; ?>signout.php">
 	                       <b>Log Out</b>
                        </a>
                    </h4>
                </li>
                
            </ul>
            
        </div>
    </nav>
</div>
 <!-- Right sidebar -->
</div>