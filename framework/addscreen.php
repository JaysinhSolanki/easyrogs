<?php
session_start();
include("adminsecurity.php");
$id		=	$_REQUEST['id'];
if($id!=-1)
{
	$screens			=	$AdminDAO->getrows("system_screen","screenname,screentype,screennamehebrew,issystemscreen,url,formurl,deletefilename,displayorder,fkmoduleid,fksectionid,visibility,showtoadmin,showontop,query,orderby,tablename,pkid","pkscreenid='$id'");
	$screenname			=	$screens[0]['screenname'];
	$screentype			=	$screens[0]['screentype'];
	//dump($screens);
	
 	$screennamehebrew 	=	$screens[0]['screennamehebrew'];//updated...1/16/2015 exit;
	$url				=	$screens[0]['url'];
	$formurl			=	$screens[0]['formurl'];
	$displayorder		=	$screens[0]['displayorder'];
	$fkmoduleid			=	$screens[0]['fkmoduleid'];
	$fksectionid		=	$screens[0]['fksectionid'];
	$visibility			=	$screens[0]['visibility'];
	$showtoadmin		=	$screens[0]['showtoadmin'];
	$issystemscreen		=	$screens[0]['issystemscreen'];
	
	$showontop			=	$screens[0]['showontop'];
	$query				=	$screens[0]['query'];
	$deletefilename		=	$screens[0]['deletefilename'];
	$orderby  			=	$screens[0]['orderby'];
	$tablename  		=	$screens[0]['tablename'];
	$pkid  				=	$screens[0]['pkid'];
	
	$extraparameter  	=	$screens[0]['extraparameter'];

	
	
}
if($url=="")
{
	$url=	"main.php";
}
$fields		=	array();
$filters	=	array();
$actions	=	array();
if($screentype==1)//if GRID
{
	/************************************fields**********************************/
	$fields		=	$AdminDAO->getrows("system_field","*","fkscreenid='$id' ORDER BY sortorder");
	/************************************fields**********************************/
	$filters		=	$AdminDAO->getrows("system_filter","*","fkscreenid='$id'");
	$fieldtypes	=	$AdminDAO->getrows("system_formfieldtype ","*","1 ORDER BY formfieldtype");
	/************************************************actions*************************/
	$actions			=	$AdminDAO->getrows("system_action","*","fkscreenid='$id' AND fkactiontypeid NOT IN(1,2,3)");
	
	
	$addactions			=	$AdminDAO->getrows("system_action","*","fkscreenid='$id' AND fkactiontypeid = 1 ");
	$editactions		=	$AdminDAO->getrows("system_action","*","fkscreenid='$id' AND fkactiontypeid = 2 ");
	$deleteactions		=	$AdminDAO->getrows("system_action","*","fkscreenid='$id' AND fkactiontypeid = 3 ");
	
	//dump($addactions);
	//dump($editactions);
	//dump($deleteactions);
	
	if(sizeof($addactions) > 0)
	{
		$addbutton  		=	1;
		$addactionid  		=	$addactions[0]['pkactionid'];
	}
	
	if(sizeof($editactions) > 0)
	{
		$editbutton		=	1;
		$editactionid  	=	$editactions[0]['pkactionid'];
	}
	
	if(sizeof($deleteactions) > 0)
	{
		$deletebutton		=	1;
		$deleteactionid  	=	$deleteactions[0]['pkactionid'];
	}
	
	
	
	// selecting modules 
}//grid
	$modulearray	= 	$AdminDAO->getrows("system_module","*","status=1");
	$modulesel		=	"<select name='fkmoduleid' id='module' class='form-control m-b' >";
	for($i=0;$i<sizeof($modulearray);$i++)
	{
		$modulename	=	$modulearray[$i]['modulename'];
		$moduleid	=	$modulearray[$i]['pkmoduleid'];
		$select		=	"";
		if($moduleid == $fkmoduleid)
		{
			$select = "selected='selected'";
		}
		$modulesel2	.=	"<option value='$moduleid' $select>$modulename</option>";
	}
	$modules		=	$modulesel.$modulesel2."</select>";
	// end modules
?>
<script language="javascript">
function loadscreenoptions(screentype)
{
	if(screentype==1)
	{
		$("#gridscreen").show();
		$("#customfilename").hide();
		
	}
	else
	{
		$("#gridscreen").hide();
		$("#customfilename").show();
	}
}
$(document).ready(function(){
	loadscreenoptions(<?php echo $screentype;?>);
	document.getElementById('screenname').focus();	
});
var newId = (function() {
    var id = 1;
    return function() {
        return id++;
    };
}());
function addanotherfield()
{
	$("#fromfiledclone").clone().appendTo("#tofieldclone").attr('id', newId()).addClass("fieldclass");
}
function addanotherfilter()
{
	var rand	=	Math.floor(Math.random() * 10); 
	$("#fromfilterclone").clone().appendTo("#tofilterclone").attr('id', newId()).addClass("filterclass");
}
function addanotheraction()
{
	$("#fromactionclone").clone().appendTo("#toactionclone").attr('id', newId()).addClass("actionclass");
}
function deletefield(e,fieldid)
{
	//alert(fieldid);
	if(confirm("Are you sure to delete?"))
	{ 
	 $(e).parents(".fieldclass").remove();
	 if(fieldid>0)
	 {
	 	$.post("deletefield.php?fieldid="+fieldid);
	 }
	return;
	}
}
function deletefilter(e,filterid)
{
	if(confirm("Are you sure to delete?"))
	{ 
	 $(e).parents(".filterclass").remove();
	 if(filterid>0)
	 {
	 	$.post("deletefilter.php?filterid="+filterid);
	 }
	return;
	}
}
function deleteaction(e,actionid)
{
	if(confirm("Are you sure to delete?"))
	{ 
	 $(e).parents(".actionclass").remove();
	 if(actionid>0)
	 {
	 	$.post("deleteaction.php?actionid="+actionid);
	 }
	return;
	}
}
</script>
<script src="../js/jquery.form.js"></script>
<style>
.icon-remove
{
	cursor:pointer;
}
</style>
<div class="col-lg-12">
    <div id="loaditemscript"> </div>
	<div id="screenfrmdiv" style="display: block;">
        <div class="hpanel">
            <div class="panel-heading">
                <?php 
				 if($id=='-1')
					{echo "Add Screen";}
					else
					{echo "Edit Screen >> $screenname";}
            	?>
            </div>
            <div class="panel-body">
            <form id="screenfrm" class="form form-horizontal" >
           <input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
           <input type="hidden" name="addactionid" id="addactionid" value="<?php echo $addactionid; ?>" />
           <input type="hidden" name="editactionid" id="editactionid" value="<?php echo $editactionid; ?>" />
           <input type="hidden" name="deleteactionid" id="deleteactionid" value="<?php echo $deleteactionid; ?>" />
           <div class="form-group">
                <div class="col-sm-10">
            	<?php
        		 buttons('insertscreen.php','screenfrm','maindiv','main.php?pkscreenid=4',0)
    			?>
                </div>
            </div>
    
            <div class="form-group">
                <label class="col-sm-2 control-label">Screen Name</label>
                <div class="col-sm-4">
                    <input type="text" placeholder="Screen Name" class="form-control m-b"  name="screenname" id="screenname" value="<?php echo $screenname; ?>" >
                </div>
                
                 <label class="col-sm-2 control-label">Select Section</label>
                <div class="col-sm-4">
                   <select name="fksectionid" id="fksectionid" class="form-control m-b">
                   	<?php 
					$sections		=	$AdminDAO->getrows("system_section","pksectionid,sectionname", "status=:status", array(":status"=>1), "sectionname", "ASC");
					foreach($sections as $section)
					{
					?> 
                       <option value="<?php echo $section['pksectionid'];?>" <?php if($section['pksectionid']==$fksectionid){?> selected="selected" <?php }?> ><?php echo $section['sectionname'];?></option>
                <?php 
                }
                ?>         
					</select>
                </div>
                
            </div>
            
            
           
             <div class="form-group">
                  <label class="col-sm-2  control-label">Show To Admin</label>
                  <div class="col-sm-1">
                       <label>
                       <input  <?php if($showtoadmin==1 || $showtoadmin=="" ){?> checked="checked" <?php }?>  name="showtoadmin" value="1" type="checkbox" class="" />
                            <span class="lbl"></span>
                       </label>
                   </div>
                    
                   <label class="col-sm-2  control-label" >Show On Menu</label>
                   <div class="col-sm-1">
                        <label>
                        <input <?php if($showontop==1 || $showontop==""){?>  checked="checked" <?php }?>  name="showontop" value="1" type="checkbox" class="" />
                        <span class="lbl"></span>
                        </label>
                   </div>
                   <label class="col-sm-2  control-label" >Is System Screen</label>
                   <div class="col-sm-1">
                        <label>
                        <input <?php if($issystemscreen==1){?>  checked="checked" <?php }?>  name="issystemscreen" value="1" type="checkbox" class="" />
                        <span class="lbl"></span>
                        </label>
                   </div>
        	</div>

            <div class="form-group">
             
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Screen Type</label>
            <div class="col-sm-4">
                <select name="screentype" id="screentype" onchange="loadscreenoptions(this.value)" class="form-control m-b">
                	<option value="0" >Select Screen Type</option>
                    <option value="1" <?php if($screentype==1){echo "SELECTED"; }?>>Grid</option>
                    <option value="2" <?php if($screentype==2){echo "SELECTED"; }?>>Custom PHP File</option>
                </select>
            </div>
             <label class="col-sm-2 control-label">Display Order </label>
                   <div class="col-sm-4">
                  <input type="text" placeholder="Display Order" class="form-control m-b"  name="displayorder" id="displayorder" value="<?php echo $displayorder; ?>" >
                  </div>
            </div>
                <div class="form-group" id="customfilename">
                    <label class="col-sm-2  control-label" >Custom File Name</label>
                    <div class="col-sm-10">
                    <input type="text"  class="form-control m-b" placeholder="Screen File Name" value="<?php echo $url;?>" name="customfilename" id="customfilename" onkeydown="javascript:if(event.keycode==13){addscreen(); return false;}"  />
                    </div>
                </div>
            <span id="gridscreen">
            <div class="form-group">
            <label class="col-sm-2 control-label">Action Buttons</label>
            	<div class="col-sm-10">
                <div class="col-sm-2">
                <div class="checkbox checkbox-inline">
                	<input type="checkbox" class=""  name="addbutton" id="addbutton" value="1" <?php if($addbutton==1) {echo "checked";}  ?> > <label for="addbutton">Add</label>
                    </div>
                    </div>
                <div class="col-sm-2">
                <div class="checkbox checkbox-inline">
                	<input type="checkbox" class=""  name="editbutton" id="editbutton" value="1" <?php if($editbutton==1) {echo "checked";}  ?>> <label for="editbutton">Edit</label>
                    </div>
                    </div>
                <div class="col-sm-2">
               	<div class="checkbox checkbox-inline">
                	<input type="checkbox" class=""  name="deletebutton" id="deletebutton" value="1" <?php if($deletebutton==1) {echo "checked";}  ?>> <label for="deletebutton">Delete</label>
                </div>
                </div> 
            </div>
            </div>
            <div class="form-group">
    	    <label class="col-sm-2 control-label">Add/Edit File Name</label>
            <div class="col-sm-4">
                <input type="text" class="form-control m-b" placeholder="Add/Edit File Name" value="<?php echo $formurl;?>" name="formurl" id="filename"   />
            </div>
            <label class="col-sm-2 control-label" >Operation File Name:</label>
           
            <div class="col-sm-4">
    <input type="text" class="form-control m-b"  placeholder="Delete File Name" value="<?php echo $deletefilename;?>" name="deletefilename" id="deletefilename"   />
    <small style="font-style:italic">operations like delete, mark as paid etc.</small>
        </div>
        	</div>
            
            <div class="form-group">
                <label class="col-sm-2 control-label">Order By </label>
                <div class="col-sm-10">
                    <input type="text" placeholder="Order By" class="form-control m-b"  name="orderby" id="orderby" value="<?php echo $orderby; ?>">
                </div>
            </div>
            
            <div class="form-group">
        	<label class="col-sm-2 control-label" >SQL Query</label>
            <div class="col-sm-10">
                <textarea name="query" class="form-control m-b" rows="10" placeholder="SQL Query" cols="200" ><?php echo $query;?></textarea>
            </div>
        	</div>
            
            <!--<div class="form-group">
         	<label class="col-sm-2 control-label" >Table Name</label>
            <div class="col-sm-8">
                <input type="text" class="form-control m-b" placeholder="Table Name" value="<?php //echo $tablename; ?>" name="tablename" id="tablename"   />
            </div>
        	</div>-->
        
       <!-- <div class="form-group">
         <label class="col-sm-2 control-label">Pk Field ID</label>
            <div class="col-sm-8">
                <input type="text"  class="form-control m-b" placeholder="Pk Field ID" value="<?php //echo $pkid; ?>" name="pkid" id="pkid"   />
            </div>
        </div>-->
        
         <h3>Fields</h3>
         
         <span id="tofieldclone">
        <?php
		foreach($fields as $field)
		{
		 	$pkfieldid			=	$field['pkfieldid'];
			$fieldlabel			=	$field['fieldlabel'];
			$fieldlabelherbew	=	$field['fieldlabelherbew'];
			$fieldname			=	$field['fieldname'];
			$sortorder			=	$field['sortorder'];
			$iseditable			=	$field['iseditable'];
			$dbfieldname		=	$field['dbfieldname'];
		?>
        	<span id="<?php echo $pkfieldid; ?>"  class="fieldclass" ><i class="fa fa-times" style="float:right;" onClick="deletefield(this,<?php echo $pkfieldid; ?>); return false" ></i>
                <input name="pkfieldid[]" class="text" value="<?php echo $pkfieldid; ?>" type="hidden" >
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">Field Label</label>
                    <div class="col-sm-2">
                        <input  name="fieldlabel[]"   class="form-control m-b" placeholder="Field Label" value="<?php echo $fieldlabel;?>"  type="text"  />
                    </div>
                    <label class="col-sm-2 control-label">Field Name</label>
                    <div class="col-sm-2">
                        <input name="fieldname[]"class="form-control m-b" placeholder="Field Name" value="<?php echo $fieldname; ?>" type="text" >
                    </div>
                    <label class="col-sm-2 control-label" >Sort Order</label>
                    <div class="col-sm-2">
                        <input name="sortorder[]" class="form-control m-b" placeholder="Sort Order" value="<?php echo $sortorder; ?>"  type="text" >
                    </div>
                </div>
                <?php /*?><div class="form-group">
                    <label class="col-sm-2 control-label" >Field Label (German)</label>
                    <div class="col-sm-8">
                        <input  name="fieldlabelherbew[]"  class="form-control m-b" placeholder="Field Label(German)" value="<?php echo $fieldlabelherbew;?>"  type="text"  />
                    </div>
                </div><?php */?>
                
                
                <?php /*?><div class="form-group">
                    <label class="col-sm-2 control-label" >Iseditable</label>
                    <div class="col-sm-8">
                    
                       <select name="iseditable[]" class="form-control m-b" >
                       <option value="1"  <?php if ($iseditable==1) {echo "selected  = 'selected'";}?> >YES</option>
                       <option value="0"  <?php if ($iseditable==0) {echo "selected = 'selected'";}?> >NO</option>
                    </select> 
                   </div>
                </div><?php */?>
                
                <?php /*?><div class="form-group">
                    <label class="col-sm-2 control-label">DB Field Name</label>
                    <div class="col-sm-8">
                        <input name="dbfieldname[]" class="form-control m-b" placeholder="DB Field Name" value="<?php echo $dbfieldname; ?>"  type="text" >
                    </div>
                </div><?php */?>
                <div class="hr-line-dashed"></div>
           </span>
        <?php
		}
		?>
        </span>
         
         <span id="fromfiledclone">
        	<i class="icon-remove bigger-110" style="float:right; margin-right:550px" onClick="deletefield(this,0); return false" ></i>
            
            
             <div class="form-group">
                    <label class="col-sm-2 control-label">Field Label</label>
                    <div class="col-sm-2">
                        <input  name="fieldlabel[]"   class="form-control m-b" placeholder="Field Label" value=""  type="text"  />
                    </div>
                    <label class="col-sm-2 control-label">Field Name</label>
                    <div class="col-sm-2">
                        <input name="fieldname[]"class="form-control m-b" placeholder="Field Name" value="" type="text" >
                    </div>
                    <label class="col-sm-2 control-label" >Sort Order</label>
                    <div class="col-sm-2">
                        <input name="sortorder[]" class="form-control m-b" placeholder="Sort Order" value=""  type="text" >
                    </div>
                </div>
                <?php /*?><div class="form-group">
                    <label class="col-sm-2 control-label" >Field Label (German)</label>
                    <div class="col-sm-8">
                        <input  name="fieldlabelherbew[]"  class="form-control m-b" placeholder="Field Label(German)" value=""  type="text"  />
                    </div>
                </div><?php */?>
                
                
                <?php /*?><div class="form-group">
                    <label class="col-sm-2 control-label" >Iseditable</label>
                    <div class="col-sm-8">
                    
                       <select name="iseditable[]" class="form-control m-b" >
                       <option value="1" >YES</option>
                       <option value="0" >NO</option>
                    </select> 
                   </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">DB Field Name</label>
                    <div class="col-sm-8">
                        <input name="dbfieldname[]" class="form-control m-b" placeholder="DB Field Name" value=""  type="text" >
                    </div>
                </div><?php */?>
			<div class="hr-line-dashed"></div>
         </span>        
         <a href="javascript:;" onclick="addanotherfield();"><i class="fa fa-plus"></i>Add Another Field</a>
         
       
         
        
         <h3>Actions</h3>
         
         <span id="toactionclone">
        <?php
		foreach($actions as $action)
		{
			$pkactionid				=	$action['pkactionid'];
			$actionlabel			=	$action['actionlabel'];
			$acttionlabelherbew		=	$action['acttionlabelherbew'];
			$fkscreenid				=	$action['fkscreenid'];
			$fkactiontypeid			=	$action['fkactiontypeid'];
			$actionsortorder		=	$action['sortorder'];
			
			$actioncodecustom		=	$action['actioncodecustom'];
			$selection				=	$action['selection'];
			$phpfile				=	$action['phpfile'];
			$title					=	$action['title'];
			$childdiv				=	$action['childdiv'];
			$buttonclass			=	$action['buttonclass'];
			$iconclass				=	$action['iconclass'];
			$actionparam			=	$action['actionparam'];
			$confirmation			=	$action['confirmation'];
			
		?>
        <span id="<?php echo $pkactionid; ?>" class="actionclass" ><i class="fa fa-times" style="float:right; margin-right:170px" onClick="deleteaction(this,<?php echo $pkactionid; ?>); return false" ></i>
        
         	<input name="pkactionid[]" class="text" value="<?php echo $pkactionid; ?>" type="hidden" >
             
             <div class="form-group">
                <label class="col-sm-2 control-label" >Action Label </label>
                    <div class="col-sm-4">
                        <input name="actionlabel[]"  class="form-control m-b" placeholder="Action Label" value="<?php echo $actionlabel; ?>" onKeyDown="javascript:if(event.keycode==13){addaction(); return false;}" type="text" >
                    </div>
                    <label class="col-sm-2 control-label" >Action Type</label>
                    <div class="col-sm-4">
                        <?php 
                        $actiontypes		=	$AdminDAO->getrows("system_actiontype","pkactiontypeid,actiontypelabel", " pkactiontypeid NOT IN (1,2,3) ");
						//dump($actiontypes);
						//echo __LINE__;
                        ?>
                            <select name="fkactiontypeid[]" class="form-control m-b"  >
                        <?php 
                        foreach($actiontypes as $actiontype)
                        {
                        ?>                        
                              <option value="<?php echo $actiontype['pkactiontypeid'];?>" <?php if($actiontype['pkactiontypeid']==$fkactiontypeid){?> selected="selected" <?php } ?>><?php echo $actiontype['actiontypelabel'];?></option>
                        <?php 
                        }
                        ?>
                            </select> 
                    </div>
             </div>
            <?php /*?> <div class="form-group">
                <label class="col-sm-2 control-label" >Action Label (German)</label>
                    <div class="col-sm-8">
                        <input name="acttionlabelherbew[]"  class="form-control m-b" placeholder="Action Label (German)" value="<?php echo $acttionlabelherbew; ?>" onKeyDown="javascript:if(event.keycode==13){addaction(); return false;}" type="text" >
                    </div>
             </div><?php */?>
             <div class="form-group">
                <label class="col-sm-2 control-label" >Sort Order</label>
                <div class="col-sm-4">
                    <input name="actionsortorder[]" class="form-control m-b" placeholder="Sort Order" value="<?php echo $actionsortorder; ?>"  type="text" >
                </div>
                
                <label class="col-sm-2 control-label">Selection</label>
                <div class="col-sm-4">
                    <select  name="selectioncustom[]" class="form-control m-b" >
                        <option <?php if($selection==1){?> selected="selected" <?php } ?> value="1">Yes</option>
                        <option <?php if($selection==0){?> selected="selected" <?php } ?> value="0">No</option>
                    </select>
  
                </div>
            </div>
            
            
            <?php /*?> <span id="customefields<?php echo $pkactionid; ?>" >
             <div class="form-group">
                <label class="col-sm-2 control-label" >Custom Code</label>
                <div class="col-sm-8">
                    <textarea name="actioncodecustom[]" class="form-control m-b" placeholder="Custom Code"><?php //echo $actioncodecustom; ?></textarea>
                </div>
            </div><?php */?>
             <div class="form-group">
                <label class="col-sm-2 control-label" > PHP File</label>
                <div class="col-sm-4">
                    <input name="phpfilecustom[]" value="<?php echo $phpfile; ?>" class="form-control m-b" placeholder="PHP File"   type="text" >
                </div>
                <label class="col-sm-2 control-label" for="form-field-1">Title</label>
                <div class="col-sm-4">
                    <input name="titlecustom[]"  value="<?php echo $title; ?>" class="form-control m-b" placeholder="Title"  type="text" >
                </div>
            </div>
            </span>
             
             <div class="form-group">
                <label class="col-sm-2 control-label" >Child Div</label>
                <div class="col-sm-8">
                    <input name="childdivcustom[]"  value="<?php echo $childdiv; ?>" class="form-control m-b" placeholder="Child Div"   type="text" >
                </div>
            </div>
             <div class="form-group">
                <label class="col-sm-2 control-label" >Button Class</label>
                <div class="col-sm-4">
                   <?php /*?> <input name="buttonclasscustom[]"  value="<?php echo $buttonclass; ?>" class="form-control m-b" placeholder="Button Class"   type="text" ><?php */?>
                    <select name="buttonclasscustom[]" class="form-control m-b" >
                        <option <?php if($buttonclass == 'btn btn-success'){ ?> selected="selected" <?php }?> value="btn btn-success">Success</option>
                        <option <?php if($buttonclass == 'btn btn-danger'){ ?> selected="selected" <?php }?> value="btn btn-danger">Danger</option>
                    	<option <?php if($buttonclass == 'btn btn-info'){ ?> selected="selected" <?php }?> value="fa fa-trash-o">Info</option>
                        <option <?php if($buttonclass == 'btn btn-primary'){ ?> selected="selected" <?php }?> value="fa fa-list-alt">Primary</option>
                    </select>
                    </div>
                    <label class="col-sm-2 control-label" >Icon Class</label>
                	<div class="col-sm-4">
                    <select name="iconclasscustom[]" class="form-control m-b" >
                       <?php /*?> <option <?php if($iconclass == 'fa fa-plus'){ ?> selected="selected" <?php }?> value="fa fa-plus">Plus</option>
                        <option <?php if($iconclass == 'fa fa-pencil-square-o'){ ?> selected="selected" <?php }?> value="fa fa-pencil-square-o">Pencil</option>
                    	<option <?php if($iconclass == 'fa fa-trash-o'){ ?> selected="selected" <?php }?> value="fa fa-trash-o">Trash</option><?php */?>
                        <option <?php if($iconclass == 'fa fa-list-alt'){ ?> selected="selected" <?php }?> value="fa fa-list-alt">List</option>
                        <option <?php if($iconclass == 'fa fa-envelope-o'){ ?> selected="selected" <?php }?>  value="fa fa-envelope-o">Envelope</option>
                    	<option <?php if($iconclass == 'fa fa-thumbs-o-up'){ ?> selected="selected" <?php }?>  value="fa fa-thumbs-o-up">Thumb Up</option>
                        <option <?php if($iconclass == 'fa fa-thumbs-o-down'){ ?> selected="selected" <?php }?>  value="fa fa-thumbs-o-down">Thumb Down</option>
                    </select>
                    
                    
                    <?php /*?><input name="iconclasscustom[]" class="form-control m-b" placeholder="Icon Class"  value="<?php echo $iconclass; ?>"  type="text" ><?php */?>
                </div>
            </div>
             
             <div class="form-group">
                <label class="col-sm-2 control-label" >Param</label>
                <div class="col-sm-4">
                    <input name="actionparamcustom[]" class="form-control m-b" placeholder="Param"  value="<?php echo $actionparam; ?>"  type="text" >
                </div>
                <label class="col-sm-2 control-label">Confirmation</label>
                <div class="col-sm-4">
                    <select  name="confirmation[]" class="form-control m-b" >
                        <option <?php if($confirmation==1){?> selected="selected" <?php } ?> value="1">Yes</option>
                        <option <?php if($confirmation==0){?> selected="selected" <?php } ?> value="0">No</option>
                    </select>
  
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            </span>
         <?php
         }
		 ?>
         </span>
         
        <span id="fromactionclone">
         <i class="fa fa-times" style="float:right;" onClick="deleteaction(this,0); return false" ></i>
         
         	 <div class="form-group">
                <label class="col-sm-2 control-label" >Action Label</label>
                    <div class="col-sm-4">
                        <input name="actionlabel[]"  class="form-control m-b" placeholder="Action Label" onKeyDown="javascript:if(event.keycode==13){addaction(); return false;}" type="text" >
                    </div>
                    <label class="col-sm-2 control-label">Action Type</label>
                    <div class="col-sm-4">
                        <?php 
                        $actiontypes		=	$AdminDAO->getrows("system_actiontype","pkactiontypeid,actiontypelabel ", " pkactiontypeid NOT IN (1,2,3) ");
						//dump($actiontypes);
                        ?>
                            <select name="fkactiontypeid[]" class="form-control m-b" >
                        <?php 
                        foreach($actiontypes as $actiontype)
                        {
                        ?>                        
                              <option value="<?php echo $actiontype['pkactiontypeid'];?>" ><?php echo $actiontype['actiontypelabel'];?></option>
                        <?php 
                        }
                        ?>
                            </select> 
                    </div>
             </div>             
             <?php /*?><div class="form-group">
                <label class="col-sm-2 control-label" >Action Label (German)</label>
                    <div class="col-sm-8">
                        <input name="acttionlabelherbew[]"  class="form-control m-b" placeholder="Action Label (German)" onKeyDown="javascript:if(event.keycode==13){addaction(); return false;}" type="text" >
                    </div>
             </div>  <?php */?>  
             <div class="form-group">
                <label class="col-sm-2 control-label" >Sort Order</label>
                <div class="col-sm-4">
                    <input name="actionsortorder[]" class="form-control m-b" placeholder="Sort Order"   type="text" >
                </div>
                <label class="col-sm-2 control-label" >Selection</label>
                <div class="col-sm-4">
                    <select  name="selectioncustom[]" class="form-control m-b" >
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
  
                </div>
            </div>
            
            <span class="customefieldsss" >
             <div class="form-group">
                <label class="col-sm-2 control-label" >Custom Code</label>
                <div class="col-sm-10">
                    <textarea name="actioncodecustom[]" class="form-control m-b" placeholder="Custom Code" ></textarea>
                </div>
            </div>
             
            
             <div class="form-group">
                <label class="col-sm-2 control-label" > PHP File</label>
                <div class="col-sm-4">
                    <input name="phpfilecustom[]" class="form-control m-b" placeholder="PHP File"   type="text" >
                </div>
                
                <label class="col-sm-2 control-label" >Title</label>
                <div class="col-sm-4">
                    <input name="titlecustom[]" class="form-control m-b" placeholder="Title"   type="text" >
                </div>
            </div>
            </span>
             
             <div class="form-group" style="display:none;">
                <label class="col-sm-2 control-label" for="form-field-1">Child Div</label>
                <div class="col-sm-10">
                    <input name="childdivcustom[]" value="sugrid" class="form-control m-b" placeholder="Child Div"   type="text" >
                </div>
            </div>
             <div class="form-group">
                <label class="col-sm-2 control-label" >Button Class</label>
                <div class="col-sm-4">
                    <!--<input name="buttonclasscustom[]" class="form-control m-b" placeholder="Button Class"  type="text" >-->
                    <select name="buttonclasscustom[]" class="form-control m-b" >
                        <option  value="btn btn-success">Success</option>
                        <option  value="btn btn-danger">Danger</option>
                    	<option  value="fa fa-trash-o">Info</option>
                        <option  value="fa fa-list-alt">Primary</option>
                        
                    </select>
                    
                </div>
                <label class="col-sm-2 control-label" >Icon Class</label>
                <div class="col-sm-4">
                    <select  name="iconclasscustom[]" class="form-control m-b" >
                        <option value="fa fa-plus";>Plus</option>
                        <option value="fa fa-pencil-square-o">Pencil</option>
                    	<option value="fa fa-trash-o">Trash</option>
                         <option value="fa fa-list-alt">List</option>
                        <option value="fa fa-envelope-o">Envelope</option>
                    	<option value="fa fa-thumbs-o-up">Thumb Up</option>
                        <option value="fa fa-thumbs-o-down">Thumb Down</option>
                    </select>
                   <!-- <input name="iconclasscustom[]"class="form-control m-b" placeholder="Icon Class" type="text" >-->
                </div>
            </div>
             
             <div class="form-group">
                <label class="col-sm-2 control-label" >Param</label>
                <div class="col-sm-4">
                    <input name="actionparamcustom[]" class="form-control m-b" placeholder="Param" type="text" >
                </div>
                <label class="col-sm-2 control-label" >Confirmation</label>
                <div class="col-sm-4">
                    <select  name="confirmation[]" class="form-control m-b" >
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
          <div class="hr-line-dashed"></div>
         </span> 
         
          <a href="javascript:;" onclick="addanotheraction();"><i class="fa fa-plus"></i> Add Another Action Button</a>
          <span style="display:none;">
           <h3>Default</h3>
           
            <div class="form-group">
        	<label class="col-sm-2 control-label" >Module</label>
            <div class="col-sm-8">
                <?php echo $modules; ?>
            </div>
        </div>
        
        <div class="form-group">
        	<label class="col-sm-2  control-label" >Screen File Name</label>
            <div class="col-sm-8">
                <input type="text"  class="form-control m-b" placeholder="Screen File Name" value="<?php echo $url;?>" name="filename" id="filename" onkeydown="javascript:if(event.keycode==13){addscreen(); return false;}"  />
            </div>
        </div>
        <div class="form-group">
        	<label class="col-sm-2  control-label" >Visibility</label>
            <div class="col-sm-8">
                <select name="visibility" id="visibility" class="form-control m-b">
                	<option value="">Select Visibility</option>
                    <option value="2" selected="selected"  <?php if($visibility==2) {?>  <?php }?>>Both</option>
                    <option value="1" <?php if($visibility==1) {?> selected="selected" <?php }?>>Main</option>
                    <option value="3" <?php if($visibility==3) {?> selected="selected" <?php }?>>Local</option>
                </select>
            </div>
        </div>
        
       
      </span>
         <h3>Filters</h3>
         <span id="tofilterclone">
        <?php
		foreach($filters as $filter)
		{
		?>
        	<span id="<?php echo $filter['pkfilterid']; ?>"  class="filterclass" ><i class="fa fa-times" style="float:right; margin-right:170px" onClick="deletefilter(this,<?php echo $filter['pkfilterid']; ?>); return false" ></i>
                <input name="pkfilterid[]" class="text" value="<?php echo $filter['pkfilterid']; ?>" type="hidden" >
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">Type</label>
                    <div class="col-sm-4">
                        <select name="fkformfieldtypeids[]"  onchange="filedtypefun(<?php echo $filter['pkfilterid']; ?>)"  class="form-control m-b"  >
                        <option value="0">Select Field Type</option>
                        <?php	
							foreach($fieldtypes as $fieldtype)
							{
								?>
                                <option <?php if($fieldtype['pkformfieldtypeid'] == $filter['fkformfieldtypeid']){ echo 'selected="selected"';}?>  value="<?php echo $fieldtype['pkformfieldtypeid'];?>"><?php echo $fieldtype['formfieldtype'];?></option> 
                                <?php
							}
                        ?>
                        </select>
                    </div>
                    
                    <label class="col-sm-2 control-label">Label </label>
                    <div class="col-sm-4">
                        <input name="filterlabelname[]"class="form-control m-b" placeholder="Label" value="<?php echo $filter['filterlabelname']; ?>" type="text" >
                    </div>
                </div>
                
                <div class="form-group isnotdropdown<?php echo $filter['pkfilterid']; ?>">
                    <label class="col-sm-2 control-label">Field Name </label>
                    <div class="col-sm-10">
                        <input name="filterfieldname[]"class="form-control m-b" placeholder="Field Name" value="<?php echo $filter['filterfieldname']; ?>" type="text" >
                    </div>
                </div>
                <div class="form-group isdropdown<?php echo $filter['pkfilterid']; ?>">
                    <label class="col-sm-2 control-label">Field Type Value </label>
                    <div class="col-sm-10">
                        <input name="filtervalue[]"class="form-control m-b" placeholder="Field Type Value" value="<?php echo $filter['filtervalue']; ?>" type="text" >
                    </div>
                </div>
                <div class="form-group isdropdown<?php echo $filter['pkfilterid']; ?>">
                    <label class="col-sm-2 control-label">Field Type Label</label>
                    <div class="col-sm-10">
                        <input name="filterlabel[]"class="form-control m-b" placeholder="Field Type Label" value="<?php echo $filter['filterlabel']; ?>" type="text" >
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Select</label>
                    <div class="col-sm-10">
                        <select name="filterselect[]"   class="form-control m-b"  >
                            <option <?php if($filter['filterselect']==1){ echo 'selected="selected"';}?> value="1"><?php //echo $filter['filterselect']; ?>Single</option>
                            <option <?php if($filter['filterselect']==2){ echo 'selected="selected"';}?> value="2"><?php //echo $filter['filterselect']; ?>Multiple</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Query</label>
                    <div class="col-sm-10">
                        <textarea name="filterquery[]"class="form-control m-b"  placeholder="Field Type Label" ><?php echo $filter['filterquery']; ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Status</label>
                    <div class="col-sm-4">
                        <select name="filterstatus[]"   class="form-control m-b"  >
                            <option <?php if($filter['filterstatus']==1){ echo 'selected="selected"';}?> value="1">Active</option>
                            <option <?php if($filter['filterstatus']==2){ echo 'selected="selected"';}?> value="2">Inactive</option>
                        </select>
                    </div>
                    
                    <label class="col-sm-2 control-label" >Sort Order</label>
                    <div class="col-sm-4">
                        <input name="filtersortorder[]" class="form-control m-b" placeholder="Sort Order" value="<?php echo $filter['filtersortorder']; ?>"  type="text" >
                    </div>
                </div>
                
                <div class="hr-line-dashed"></div>
           </span>
        <?php
		}
		?>
        </span>
         <span id="fromfilterclone">
        	<i class="icon-remove bigger-110" style="float:right; margin-right:550px" onClick="deletefilter(this,0); return false" ></i>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">Type</label>
                    <div class="col-sm-4">
                        <select name="fkformfieldtypeids[]"   class="form-control m-b"  >
                        <option value="0">Select Field Type</option>
                        <?php
                            foreach($fieldtypes as $fieldtype)
                            {
                                ?>
                                <option   value="<?php echo $fieldtype['pkformfieldtypeid'];?>"><?php echo $fieldtype['formfieldtype'];?></option> 
                                <?php
                            }
                        ?>
                        </select>
                    </div>
                    
                    <label class="col-sm-2 control-label">Label </label>
                    <div class="col-sm-4">
                        <input name="filterlabelname[]"class="form-control m-b" placeholder="Label" value="" type="text" >
                    </div>
                </div>
                
                <div class="form-group isdropdown">
                    <label class="col-sm-2 control-label">Field Type Value </label>
                    <div class="col-sm-10">
                        <input name="filtervalue[]"class="form-control m-b" placeholder="Field Type Value" value="" type="text" >
                    </div>
                </div>
                <div class="form-group isdropdown">
                    <label class="col-sm-2 control-label">Field Type Label</label>
                    <div class="col-sm-10">
                        <input name="filterlabel[]"class="form-control m-b" placeholder="Field Type Label" value="" type="text" >
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Select</label>
                    <div class="col-sm-10">
                        <select name="filterselect[]"   class="form-control m-b"  >
                            <option value="1">Single</option>
                            <option value="2">Multiple</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Query</label>
                    <div class="col-sm-10">
                        <textarea name="filterquery[]"class="form-control m-b"  placeholder="Field Type Label" ></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Status</label>
                    <div class="col-sm-4">
                        <select name="filterstatus[]"   class="form-control m-b"  >
                            <option  value="1">Active</option>
                            <option  value="2">Inactive</option>
                        </select>
                    </div>
                    <label class="col-sm-2 control-label" >Sort Order</label>
                    <div class="col-sm-4">
                        <input name="filtersortorder[]" class="form-control m-b" placeholder="Sort Order" value=""  type="text" >
                    </div>
                </div>
                
                <div class="hr-line-dashed"></div>
           </span>
			<div class="hr-line-dashed"></div>
         </span>        
         <a href="javascript:;" onclick="addanotherfilter();"><i class="fa fa-plus"></i>Add Another Filter</a>
          </span> 
          
            
            
            <div class="form-group">
                <div class="col-sm-10">
            	<?php
        		 buttons('insertscreen.php','screenfrm','maindiv','main.php?pkscreenid=4',1);
    			?>
                </div>
            </div>    
        </form>
    </div>
  </div>
	</div>
</div>