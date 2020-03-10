<?php
//echo __LINE__;
//require_once("{$_SESSION['includes_path']}/classes/adminsecurity.php");
$addressbookid			=	$_SESSION['addressbookid'];
$_SESSION['section']	=	@$_GET['sectionid'];
$section				=	$_SESSION['section'];
//$AdminDAO->displayquery=1;
//Warning: PDOStatement::execute() expects parameter 1 to be array, string given in D:\wamp\www\gumption\kamna\system\includes\classes\AdminDAO.php on line 116
$sectionres	=	$AdminDAO->getrows("system_section","*","status=:status order by  sortorder", array(":status"=>1));
//dump($sectionres);
//exit;

$fkgroupid	=	$_SESSION['groupid'];
$users			=	$AdminDAO->getrows("system_addressbook","*","pkaddressbookid	=	:addressbookid", array(":addressbookid"=>$addressbookid));
//dump($users);
//exit;
$user			=	$users[0];
$userimage		=	$user['userimage'];	
if($userimage == "")
{
	$userimage	=	"../images/gumptech-logo.png";	
}
?>
<aside id="menu">
    <div id="navigation">
        <div class="profile-picture">
        	<a href="index.php">
            	<img  src="../uploads/profile/logo.png" class="m-b" width="100%" style="background-attachment:fixed; background-repeat:no-repeat; background-size:100% 100%; margin-left:-15px; " alt="logo image">
            </a>
            <?php echo $systemmaindescription; ?>
        </div>
        <ul class="nav" id="side-menu">
		<?php 
        //dump($sectionres,1);
		
        for($sec=0; $sec<sizeof($sectionres); $sec++)
        {
			$sectionid		=	$sectionres[$sec]['pksectionid'];
			$sectionname	=	$sectionres[$sec]['sectionname'];
			$sectionicon	=	$sectionres[$sec]['sectionicon'];
			$screens		=	$_SESSION['screenids'];
			if($screens==0)//no screen assigned to this group
			{
				continue;
			}
		
			$screens		= 	@implode(",", $screens);
			//echo "<h1>-----------------</h1>";
			//$AdminDAO->displayquery = 1;
			$tabres		=	$AdminDAO->getrows("system_screen s","*",
													"pkscreenid IN ($screens) AND 
													showontop =1  AND 
													fkmoduleid = (select pkmoduleid from system_module where modulename='System Access Permissions') AND 
													fksectionid='$sectionid' AND
													showtoadmin <> 0 
													",array(),
													"displayorder",
													"ASC");
			
			
			//dump($tabres);
			//$AdminDAO->displayquery = 0;
			if(sizeof($tabres) > 0)
			{
				if(sizeof($tabres) > 1)
				{
					$icon	=	"<span class='fa arrow'></span>";
	        	?>
                    <li id="<?php echo $sectionid;?>">
    	               
                            <a href="javascript:;" >
                            <?php
							if($sectionid=='26')
							{
								//$icons	=	"<i class='pe-7s-config fa-4x'></i>";
							}
							?>
                            	 <span class="nav-label"><?php echo ucwords($sectionname)." ".@$icons;?> </span>
                                 <span class="fa arrow"></span>
                            </a>
                            
                       
                    	<ul class="nav nav-second-level">
        		<?php
				}
				else
				{
					$icon	=	"";
				}
				for($i=0;$i<sizeof($tabres);$i++)
				{
					if($_SESSION['language']=='hebrew')
					{
						$screenname		=	$tabres[$i]['screennamehebrew'];
					}
					else
					{
						$screenname		=	$tabres[$i]['screenname'];
					}
					$firstscreen	=	$tabres[0]['pkscreenid'];
					$firsturl		=	$tabres[0]['url'];
					$pkscreenid		=	$tabres[$i]['pkscreenid'];
					$screenurl		=	$tabres[$i]['url'];
					$visibility		=	$tabres[$i]['visibility'];
					if($visibility==1 || $visibility==2)
					{
						$extradata = '';
						//echo "<pre>";
						//print_r($_SESSION);
						if($pkscreenid == 112)
						{
							$fkagentid				=	$_SESSION['addressbookid'];
							if($_SESSION['groupid'] == 10)
							{
								$inprogresspassports	=	$AdminDAO->getrows("tblorderpassport,tblpassport","*","fkpassportid = pkpassportid AND fkagentid = :fkagentid AND orderpassportstatus = :orderpassportstatus",array(":orderpassportstatus"=> 0, ":fkagentid"=> $fkagentid));
							}
							else
							{
								$inprogresspassports	=	$AdminDAO->getrows("tblorderpassport,tblpassport","*","fkpassportid = pkpassportid AND orderpassportstatus = :orderpassportstatus",array("orderpassportstatus"=> 0));
							}
							$inprogresspassport		=	sizeof($inprogresspassports);
							$extradata = '(<span id="inprogress_count">'.$inprogresspassport.'</span>)';
						}
						elseif($pkscreenid == 95)
						{
							$newpassports		=	$AdminDAO->getrows("tblorderpassport,tblpassport","*","fkpassportid = pkpassportid AND orderpassportstatus = :orderpassportstatus",array("orderpassportstatus"=> 1));
							$newpassport		=	sizeof($newpassports);
							$extradata = '(<span id="new_count">'.$newpassport.'</span>)';
						}
						elseif($pkscreenid == 113)
						{
							if($_SESSION['groupid'] == 10)
							{
								$senttocompanypassports		=	$AdminDAO->getrows("tblorderpassport,tblpassport","*","fkpassportid = pkpassportid AND fkagentid = :fkagentid AND orderpassportstatus = :orderpassportstatus",array(":orderpassportstatus"=> 1, ":fkagentid"=> $fkagentid));
							}
							else
							{
								$senttocompanypassports		=	$AdminDAO->getrows("tblorderpassport,tblpassport","*","fkpassportid = pkpassportid AND orderpassportstatus = :orderpassportstatus",array("orderpassportstatus"=> 1));
							}
							$senttocompanypassport		=	sizeof($senttocompanypassports);
							$extradata = '(<span id="senttocompany_count">'.$senttocompanypassport.'</span>)';
						}
						elseif($pkscreenid == 96)
						{
							if($_SESSION['groupid'] == 10)
							{
								$sentforapprovalpassports		=	$AdminDAO->getrows("tblorderpassport,tblpassport","*","fkpassportid = pkpassportid AND fkagentid = :fkagentid AND orderpassportstatus = :orderpassportstatus",array(":orderpassportstatus"=> 2, ":fkagentid"=> $fkagentid));
							}
							else
							{
								$sentforapprovalpassports		=	$AdminDAO->getrows("tblorderpassport,tblpassport","*","fkpassportid = pkpassportid AND orderpassportstatus = :orderpassportstatus",array("orderpassportstatus"=> 2));
							}
							
							$sentforapprovalpassport		=	sizeof($sentforapprovalpassports);
							$extradata = '(<span id="sentforapproval_count">'.$sentforapprovalpassport.'</span>)';
						}
						elseif($pkscreenid == 97)
						{
							if($_SESSION['groupid'] == 10)
							{
								$mofareceivedpassports		=	$AdminDAO->getrows("tblorderpassport,tblpassport","*","fkpassportid = pkpassportid AND fkagentid = :fkagentid AND orderpassportstatus = :orderpassportstatus",array(":orderpassportstatus"=> 3, ":fkagentid"=> $fkagentid));
							}
							else
							{
								$mofareceivedpassports		=	$AdminDAO->getrows("tblorderpassport,tblpassport","*","fkpassportid = pkpassportid AND orderpassportstatus = :orderpassportstatus",array("orderpassportstatus"=> 3));
							}
							
							$mofareceivedpassport		=	sizeof($mofareceivedpassports);
							$extradata = '(<span id="mofareceived_count">'.$mofareceivedpassport.'</span>)';
						}
						elseif($pkscreenid == 98)
						{
							if($_SESSION['groupid'] == 10)
							{
								$sentforvisapassports		=	$AdminDAO->getrows("tblorderpassport,tblpassport","*","fkpassportid = pkpassportid AND fkagentid = :fkagentid AND orderpassportstatus = :orderpassportstatus",array(":orderpassportstatus"=> 4, ":fkagentid"=> $fkagentid));
							}
							else
							{
								$sentforvisapassports		=	$AdminDAO->getrows("tblorderpassport,tblpassport","*","fkpassportid = pkpassportid AND orderpassportstatus = :orderpassportstatus",array("orderpassportstatus"=> 4));
							}
							
							$sentforvisapassport		=	sizeof($sentforvisapassports);
							$extradata = '(<span id="sentforvisa_count">'.$sentforvisapassport.'</span>)';
						}
						elseif($pkscreenid == 99)
						{
							if($_SESSION['groupid'] == 10)
							{
								$visareceivedpassports		=	$AdminDAO->getrows("tblorderpassport,tblpassport","*","fkpassportid = pkpassportid AND fkagentid = :fkagentid AND orderpassportstatus = :orderpassportstatus",array(":orderpassportstatus"=> 5, ":fkagentid"=> $fkagentid));
							}
							else
							{
								$visareceivedpassports		=	$AdminDAO->getrows("tblorderpassport,tblpassport","*","fkpassportid = pkpassportid AND orderpassportstatus = :orderpassportstatus",array("orderpassportstatus"=> 5));
							}
							
							$visareceivedpassport		=	sizeof($visareceivedpassports);
							$extradata = '(<span id="visareceived_count">'.$visareceivedpassport.'</span>)';
						}
						elseif($pkscreenid == 108)
						{
							if($_SESSION['groupid'] == 10)
							{
								$hotelvotcherpassports		=	$AdminDAO->getrows("tblorderpassport,tblpassport","*","fkpassportid = pkpassportid AND fkagentid = :fkagentid AND orderpassportstatus = :orderpassportstatus",array(":orderpassportstatus"=> 6, ":fkagentid"=> $fkagentid));
							}
							else
							{
								$hotelvotcherpassports		=	$AdminDAO->getrows("tblorderpassport,tblpassport","*","fkpassportid = pkpassportid AND orderpassportstatus = :orderpassportstatus",array("orderpassportstatus"=> 6));
							}
							
							$hotelvotcherpassport		=	sizeof($hotelvotcherpassports);
							$extradata = '(<span id="hotelvoucher_count">'.$hotelvotcherpassport.'</span>)';
						}
					?>
                                        <!-- Having children -->
                        <li  id="<?php echo $pkscreenid.'_tab';?>">
                        	<a href="javascript:;" onclick="javascript: selecttab('<?php echo $pkscreenid.'_tab';?>','<?php echo FRAMEWORK_URL.$screenurl;?>','<?php echo $pkscreenid;?>');"><?php echo ucwords($screenname).' '.$extradata;?> </a>
                        </li>
                  <?php
                  }//if
				}//for
				if(sizeof($tabres) > 1)
				{
					?>
					</ul>
					</li>
					<?php 
				}
			}
		}
		
        ?>
        <!-- <li><a href="charts.php" > <i class="icon-bar-chart"></i> Charts </a> </li>-->
        <li><a href="<?php echo FRAMEWORK_URL; ?>signout.php"> Exit System </a> </li>
  </ul>
    </div>
</aside>