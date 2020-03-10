<?php
require_once("adminsecurity.php");
$groupid		=	$_REQUEST['id'];
$groupscreen	=	array();
$groupfield		=	array();
$groupaction	=	array();
$ownergroupid	=	$_SESSION['groupid'];
$groupowner		=	$_SESSION['groupowner'];
//dump($_SESSION);
if($groupid !='-1')
{
	$group_row	=	$AdminDAO->getrows("system_groups","*"," pkgroupid = :groupid", array(":groupid"=>$groupid));
	$groupname	=	$group_row[0]['groupname'];
	//adding new group
	/**************************************GROUP SCREEN***************************************/
	//$AdminDAO->displayquery =1;
	$groupscreen_row	=	$AdminDAO->getrows("system_groupscreen, system_screen s","*"," pkscreenid = fkscreenid AND fkgroupid = :groupid AND s.isdeleted = :isdeleted ", array(":groupid"=>$groupid, ":isdeleted"=>0));
	for($i=0;$i<sizeof($groupscreen_row);$i++)
	{
		$groupscreen[]		=	$groupscreen_row[$i]['fkscreenid'];
	}
	/**************************************GROUP FIELDS***************************************/
	$groupfield_row	=	$AdminDAO->getrows("system_groupfield","*"," fkgroupid = '$groupid' ");
	for($i=0;$i<sizeof($groupfield_row);$i++)
	{
		$groupfield[]		=	$groupfield_row[$i]['fkfieldid'];
	}
	/**************************************GROUP actions***************************************/
	$groupaction_row	=	$AdminDAO->getrows("system_groupaction","*"," fkgroupid = '$groupid' ");
	for($i=0;$i<sizeof($groupaction_row);$i++)
	{
		$groupaction[]		=	$groupaction_row[$i]['fkactionid'];
	}
	$groupscreen				=	array_unique($groupscreen);
	$_SESSION['groupfieldz']	=	$groupfield;
	$_SESSION['groupactionz']	=	$groupaction;
	$_SESSION['groupscreenz']	=	$groupscreen;
}
?>


<div class="col-lg-12" style="">
<div class="hpanel">
<div class="panel-heading">
<div class="panel-tools">
<?php /*?><a class="showhide"><i class="fa fa-chevron-up"></i></a>
<a class="closebox"><i class="fa fa-times"></i></a><?php */?>
</div>
<?php
if ($groupid > 1) {
echo "Edit User Group >> $groupname";
} else {

echo "Add User Group";
}
?>
</div>
<div class="panel-body">    
	<form class="form-horizontal" name="groupsform" id="groupsform">
<div class="form-group"><label class="col-sm-2 control-label">Group Name</label>
    <div class="col-sm-6">
        <input name="groupname" id="groupname" class="form-control col-md-4" type="text" value="<?php print"$groupname";?>" onkeydown="javascript:if(event.keyCode==13) {addform_group(); return false;}">
    </div>
</div>
<?php
$modules	=	$AdminDAO->getrows("system_section","*"," status != :status",array(":status"=>0), "sectionname", "ASC" );
foreach($modules as $module)
{
	$sectionname	=	$module['sectionname'];
	$pksectionid	=	$module['pksectionid'];
	
	$sectionjscall	.=	" collapsepanel({$pksectionid},'section'); ";
?>

<div class="col-lg-12" style="">
        <div class="hpanel" id="sectionheader<?php echo $pksectionid;?>">
            <div class="panel-heading" style="background-color:#F7F9FA !important;" onclick="collapsepanel(<?php echo $pksectionid;?>,'section');">
                <div class="panel-tools" >
                    <a class="showhide" ><i class="fa fa-chevron-up" id="sectionupdown<?php echo $pksectionid;?>"></i></a>
                   <?php /*?> <a class="closebox"><i class="fa fa-times"></i></a><?php */?>
                </div>
               <?php echo $sectionname;?>
            </div>
            <div class="panel-body" id="sectionbody<?php echo $pksectionid;?>">
                <p>
           
       
        <div class="content animate-panel" style="padding:5px !important;">
        	<div class="row">
			<?php
            /*if($groupowner==1)//if not system people
            {
	            $screens_row		=	$AdminDAO->getrows("system_screen,  system_groupscreen ","*"," pkscreenid = fkscreenid AND isdeleted = 0 AND fksectionid = '$pksectionid' AND fkgroupid = '$ownergroupid' ORDER BY displayorder ");	
            }
            else
            {*/
            	$screens_row		=	$AdminDAO->getrows("system_screen","*"," fksectionid = '$pksectionid' AND isdeleted = 0 ORDER BY displayorder ");
          //  }

            for($s=0;$s<sizeof($screens_row); $s++)
            {
				$sid		=	$screens_row[$s]['pkscreenid'];
				//$AdminDAO->displayquery =1;

				$fields_row	=	$AdminDAO->getrows("system_field",'*'," 1 AND fkscreenid = '$sid' ");
//				dump($fields_row);
	//			$AdminDAO->displayquery =0;
				$actions_row	=	$AdminDAO->getrows("system_action",'*'," 1 AND fkscreenid = '$sid' ");
				/*if(sizeof($fields_row) == 0 &&  sizeof($actions_row) == 0)
				{
					continue;
				}*/
	
			
				$screenjscall	.=	" collapsepanel({$sid},'screen'); ";	
				if($s%12==0)
				{
				?>
                	</div>
                    </div>
                	<div class="content animate-panel">
                    <div class="row">
                <?php
				}
            ?>
                
                        <div class="col-lg-4"   >
                            <div class="hpanel" >
                                <div class="panel-heading hbuilt" id="screebheader<?php echo $sid;?>" style="background-color:#F7F9FA !important; border-bottom:1px solid #e4e5e7;" >
                                    <div class="panel-tools">
                                        <a class="showhide"><i id="screenupdown<?php echo $sid;?>"  onclick="collapsepanel(<?php echo $sid;?>,'screen');" class="fa fa-chevron-up"></i></a>
                                       <?php /*?> <a class="closebox"><i class="fa fa-times"></i></a><?php */?>
                                    </div>
                                    <input onchange="toggleallitem('<?php echo $sid;?>')" id="screen_<?php echo $sid;?>" class="css-checkbox sme screendiv_<?php echo $sid;?>" type="checkbox" name="screens[]" value="<?php echo "$sid";?>" <?php if(@in_array($sid,$groupscreen)){echo "checked=\"checked\"";} ?>>
                                	<?php
										echo "<B>".$screens_row[$s]['screenname']."</B>";
										
									?>
                                    
                                </div>
                               
                                <div class="panel-body" style="overflow:auto !important; height:150px !important;" id="screenbody<?php echo $sid;?>">
                                    <div class="row">
                                       <?php
										if(sizeof($fields_row) > 0) 
										{
										
										?>
                                        <div class="col-lg-<?php if(sizeof($actions_row) > 0) {echo "6  border-right";}else{echo "12";} ?>" style="">
                                            <p>
                                            <?php
											
											for($f=0;$f < sizeof($fields_row); $f++)
											{
												$fid	=	$fields_row[$f]['pkfieldid'];
												?>
												<div>
												<input id="fields_<?php echo $sid.'_'.$fid;?>" class="i-checks screendiv_<?php print"$sid";?>" type="checkbox" name="fields_<?php echo $sid.'_'.$fid;?>" <?php if(in_array($fid,$groupfield)){print" CHECKED=CHECKED ";}?> value="1" <?php if($fields_row[$f]['fieldlabel']=='ID'){?> checked="checked" <?php }?>>
												<label class="css-label sme depressed" for="fields_<?php echo $sid.'_'.$fid;?>"></label>
												<?php
													echo $fields_row[$f]['fieldlabel'];
												?>
												</div>
											<?php
											}//for fields
                                           ?>
                                            
                                               
                                            </p>
                                        </div>
                                        <?php
										}
										if(sizeof($actions_row) > 0) 
										{
										
										?>
                                        <div class="col-lg-<?php if(sizeof($fields_row) > 0) {echo "6";}else{echo "12";} ?>">
                                            <p>
                                               <?php
											  //  $sid	=	$screens_row[$s]['pkscreenid'];
												
												for($a=0;$a < sizeof($actions_row); $a++)
												{
													$aid	=	$actions_row[$a]['pkactionid'];
												?>
                                                	<div>
                                                    <input id="actions_<?php echo $sid.'_'.$aid;?>" class="css-checkbox sme screendiv_<?php print"$sid";?>" type="checkbox" name="actions_<?php echo $sid.'_'.$aid;?>" value="1" <?php if(in_array($aid,$groupaction)){print" CHECKED=CHECKED ";}?>>
                                                    <label class="css-label sme depressed" for="actions_<?php echo $sid.'_'.$aid;?>"></label>
													<?php
                                                        echo $actions_row[$a]['actionlabel'];
                                                    ?>
                                                    </div>
											   <?php
												}//for actions
											   ?>
                                            </p>
                                        </div>
                                        <?php
										}
										?>
                                    </div>
                                </div>
                                <div class="panel-footer" id="screenfooter<?php echo $sid;?>">
                                	Change User Rights for <?php echo $screens_row[$s]['screenname'];?>
                                </div>
                         	</div>
                        </div>
                  
                   
            
            <?php    
            }//screen
            ?>
             </div>
                </div>
     

</p>

            </div>
            <div class="panel-footer" id="sectionfooter<?php echo $pksectionid;?>">
                This is footer for <?php echo $sectionname;?> Section
            </div>
        </div>
    </div>
<?php
}//section
?>
<div class="col-lg-12">
<?php
			buttons("insertgroup.php?groupid=$groupid",'groupsform','maindiv','main.php?pkscreenid=2',0)
			?> 
	<?php /*?><button type="button" class="btn btn-success" onclick="addform_group('<?php echo $groupid;?>');">
                <!--<img src="../images/tick.png" alt=""/> -->
                <?php
				if($groupid=='-1')
				{
					print"Save";
				}
				else
				{
					print"Update";
				}
				?>
            </button><?php */?>
</div>
</form>
</div>
</div>
<script language="javascript">
$( document ).ready(function() {
	<?php
		echo $sectionjscall;
		echo $screenjscall;
	?>
});
function addform_group(id)
{
	
	$.post( "insertgroup.php?groupid="+id,$( "#groupsform" ).serialize())
		  .done(function( data ) {
			if(data)//failure
			{
				adminnotice(text,0,5000);
			}
			else//success
			{
				if(id==<?php echo $ownergroupid;?>) //for admin group, reload the privileges
				{
					window.location.href	=	"index.php";  
				}
				else
				{
					adminnotice("Group data has been saved.",0,5000);
					jQuery('#maindiv').load('main.php?pkscreenid=2');
					jQuery('#sugrid').html('');
				}
				
			}
			
			    //alert( "Data Loaded: " + data );
  		});
}
</script>