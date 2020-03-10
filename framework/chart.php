<?php
require_once("adminsecurity.php");
$id	=	$_GET['id']; 
if($id != '-1')
{
	$charts	=	$AdminDAO->getrows('system_chart',"*","pkchartid	=	'$id'");
	$chart	=	$charts[0];
}
$charttypes	=	$AdminDAO->getrows('system_charttype',"*","status	=	'1'");
/****************************************************************************/
?>
<script src="js/jquery.form.js"></script>
<script type="text/javascript">
$(document).ready(function(){
$('html, body').animate({ scrollTop: 0 }, 0);
})
</script>
<div id="screenfrmdiv" style="display: block;">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-heading">
                <?php 
			if($id=='-1')
			{echo "Add Chart";}
			else
			{echo "Edit Chart >>&nbsp;".$chart['chartname'];}
			?>
            </div>
            <div class="panel-body">
                <form  name="chartform" id="chartform" class="form form-horizontal" method="post">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Name<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                        <div class="col-sm-8">
                            <input type="text"  name="chartname" id="chartname" placeholder="Name" value="<?php echo  $chart['chartname'];?>" class="form-control m-b"  >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Title <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                        <div class="col-sm-8">
                            <input type="text"  name="charttitle" id="charttitle" placeholder="Title" value="<?php echo  $chart['charttitle'];?>" class="form-control m-b" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Sub Title <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                        <div class="col-sm-8">
                            <input type="text"  name="chartsubtitle" id="chartsubtitle" placeholder="Sub Title" value="<?php echo  $chart['chartsubtitle'];?>" class="form-control m-b" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Chart Type<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                        <div class="col-sm-8">
                            <select  name="fkcharttypeid" id="fkcharttypeid"  value="<?php echo  $chart['chartname'];?>" class="form-control m-b"  >
                            <option value="0">Select</option>
                            <?php
							foreach($charttypes as $charttype)
							{
							?>
                                <option <?php echo $chart['fkcharttypeid']==$charttype['pkcharttypeid']?'selected':'';?>  value="<?php echo $charttype['pkcharttypeid']?>"><?php echo $charttype['charttypename']?></option>
							<?php
							}
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Description<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                        <div class="col-sm-8">
                            <textarea  name="description" id="description" placeholder="Description" class="form-control m-b"  ><?php echo  $chart['description'];?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">X-axis Category Name<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                        <div class="col-sm-8">
                            <select  name="X-axisselect" id="X-axisselect"  value="Statix / Query" class="form-control m-b"  >
                            <option value="0" selected="selected">Query / Static</option>
                            <option value="1">Static</option>
                            <option value="2">Query</option>
                            </select>
                            <?php
							if($id=='-1')
							{
							?>
                            <textarea  name="xaxis_staticname" id="xaxisnamestatic" placeholder="Please Enter X-axis Static Names" class="form-control m-b" ></textarea>
                            <textarea  name="xaxis_queryname" id="xaxisnameQuery" placeholder="Please Enter X-axis Query For Names" class="form-control m-b" ></textarea>
                            <?php
							}
							else
							{
								if($chart['xaxis_staticname'] != "" || $chart['xaxis_staticname'] == "")
								{
							?>
                            	<textarea  name="xaxis_staticname" id="xaxisnamestatic" placeholder="Please Enter X-axis Static Names" class="form-control m-b" ><?php echo  $chart['xaxis_staticname'];?></textarea>
                            <?php
								}
								
								if($chart['xaxis_queryname'] != "" || $chart['xaxis_queryname'] == "")
								{
							?>
                            	<textarea  name="xaxis_queryname" id="xaxisnameQuery" placeholder="Please Enter X-axis Query For Names" class="form-control m-b" ><?php echo  $chart['xaxis_queryname'];?></textarea>
                            <?php
								}
							}
							?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Y-axis Category Name<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                        <div class="col-sm-8">
                            <select  name="yaxisnameselect" id="yaxisnameselect"  value="Statix / Query" class="form-control m-b"  >
                            <option value="0" selected="selected">Query / Static</option>
                            <option value="1">Static</option>
                            <option value="2">Query</option>
                            
                            </select>
                            
                            <?php
							if($id=='-1')
							{
							?>
                            <textarea  name="yaxis_staticname" id="yaxisnamestatic" placeholder="Please Enter Y-axis Static Names" class="form-control m-b" ></textarea>
                            <textarea  name="yaxis_queryname" id="yaxisnamequery" placeholder="Please Enter Y-axis Query For Names" class="form-control m-b" ></textarea>
                            
                            <?php
							}
							else
							{
								if($chart['yaxis_staticname'] != "" || $chart['yaxis_staticname'] == "")
								{
							?>
                           			<textarea  name="yaxis_staticname" id="yaxisnamestatic" placeholder="Please Enter Y-axis Static Names" class="form-control m-b" ><?php echo  $chart['yaxis_staticname'];?></textarea>
                            <?php
							   }
							   if($chart['yaxis_queryname'] != "" || $chart['yaxis_queryname'] == "")
								{
							?>
                           		    <textarea  name="yaxis_queryname" id="yaxisnamequery" placeholder="Please Enter Y-axis Query For Names" class="form-control m-b" ><?php echo  $chart['yaxis_queryname'];?></textarea>
                            <?php
								}
							}
							?>
                           
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Y-axis Category Data<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                        <div class="col-sm-8">
                            <select  name="yaxisdataselect" id="yaxisdataselect"  value="Statix / Query" class="form-control m-b"  >
                            <option value="0" selected="selected">Query / Static</option>
                            <option value="1">Static</option>
                            <option value="2">Query</option>
                            
                            </select>
                            <?php
							if($id=='-1')
							{
							?>
                            <textarea  name="yaxis_staticdata" id="yaxisdatastatic" placeholder="Please Enter Y-axis Static Data" class="form-control m-b" ></textarea>
                            <textarea  name="yaxis_querydata" id="yaxisdataquery" placeholder="Please Enter Y-axis Query For Data" class="form-control m-b" ></textarea>
                            <?php
							}
							else
							{
								if($chart['yaxis_staticdata'] != "" || $chart['yaxis_staticdata'] == "")
								{
							?>
                         		<textarea  name="yaxis_staticdata" id="yaxisdatastatic" placeholder="Please Enter Y-axis Static Data" class="form-control m-b" ><?php echo  $chart['yaxis_staticdata'];?></textarea>
                            <?php
							   }
							   if($chart['yaxis_querydata'] != "" || $chart['yaxis_querydata'] == "")
								{
							?>
                            		<textarea  name="yaxis_querydata" id="yaxisdataquery" placeholder="Please Enter Y-axis Query For Data" class="form-control m-b" ><?php echo  $chart['yaxis_querydata'];?></textarea>
                            <?php
								}
							}
							?>
                           
                        </div>
                    </div>
                    <input type="hidden" name="id" value ="<?php echo $id;?>" />
                    <?php
					buttons('chartaction.php','chartform','maindiv','main.php?pkscreenid=19',0)
					?>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
var staticnames = $('#xaxisnamestatic');
var query		= $('#xaxisnameQuery');
var select   = $('#X-axisselect').val();
if (select == '0'){
	$("p").remove();
    staticnames.hide();
    query.hide();
}
$('#X-axisselect').change(function(){

var staticnames = $('#xaxisnamestatic');
var query		= $('#xaxisnameQuery');
var select   = $('#X-axisselect').val();
if (select == '0'){
    staticnames.hide();
	$("p").remove();
    query.hide();
}
	
if (select == '1'){
  staticnames.show();
  $("p").remove();
  query.hide();
}
if (select == '2'){
  staticnames.hide();
  $("<p><strong>Hint:</strong> Project Column as Categoryname</p>").insertAfter( "#xaxisnameQuery" );
  query.show();
}
});

var staticnames = $('#yaxisnamestatic');
var query		= $('#yaxisnamequery');
var select   = $('#yaxisnameselect').val();
    staticnames.hide();
    query.hide();
	$("p").remove();

if (select == '0'){
  staticnames.hide();
  $("p").remove();
  query.hide();
}	
$('#yaxisnameselect').change(function(){

var staticnames = $('#yaxisnamestatic');
var query		= $('#yaxisnamequery');
var select   = $('#yaxisnameselect').val();
	
if (select == '0'){
    staticnames.hide();
    query.hide();
	$("p").remove();
}
if (select == '1'){
  $("p").remove();
  staticnames.show();
  query.hide();
}
if (select == '2'){
  staticnames.hide();
  $("<p><strong>Hint:</strong> Project Column as Y-axis Name</p> ").insertAfter( "#yaxisnamequery" );
  query.show();
}
});
  
var staticnames = $('#yaxisdatastatic');
var query		= $('#yaxisdataquery');
var select   = $('#yaxisdataselect').val();
if (select == '0'){
	$("p").remove();
  	staticnames.hide();
    query.hide();
	
}
  
$('#yaxisdataselect').change(function(){

var staticnames = $('#yaxisdatastatic');
var query		= $('#yaxisdataquery');
var select   = $('#yaxisdataselect').val();
if (select == '0'){
  $("p").remove();
  staticnames.hide();
    query.hide();
	
}
	
if (select == '1'){
  $("p").remove();
  staticnames.show();
  query.hide();
}
if (select == '2'){
  staticnames.hide();
  $("<p><strong>Hint:</strong> Project Column as Y-axis Data</p>").insertAfter( "#yaxisdataquery" );
  query.show();
}
    });
</script>