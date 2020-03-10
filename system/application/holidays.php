<?php
@session_start();
require_once("adminsecurity.php");
$holidays		=	$AdminDAO->getrows('forms',"*");
?>

<div id="screenfrmdiv" style="display: block;">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-heading text-center">
			Holidays
            </div>
            <div class="panel-body">
                <form  name="holidaysform" id="holidaysform" class="form form-horizontal" method="post">
                    
                    <div class="form-group">
                        <label class=" col-sm-2 control-label">Form<span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
                        <div class="col-sm-8">
                        	<select  name="year" id="year" class="form-control m-b" onchange="loadholidays(this.value)">
                            	<option value="">Select Year</option>
                            	<?php
								for($i=2019; $i<=2050; $i++)
								{
									?>
                                 	<option value="<?php echo $i ?>"><?php echo $i ?></option>   
                                    <?php
								}
								?>
                            </select>
                        </div>
                    </div>
                    <div id="DivHollidays">
                    </div>
                    <div class="form-group" id="start_questionid" style="display:none">
                        <label class=" col-sm-2 control-label">Date: <span class="redstar" style="color:#F00" title="This field is compulsory"></span></label>
                        <div class="col-sm-8">
                        	<input type="text" name="holidays[]"  placeholder="Select Dates"  min="1" class="form-control m-b">
                        </div>
                    </div>
                    
                    
                    
                    <div class="form-group" style="margin-top:20px">
                        <div class="col-sm-offset-2 col-sm-8">
                        	<div id="loading" class="loading" style="display:none; position:absolute; color:#F00;"></div>
                            <button type="button" class="btn btn-success buttonid" data-style="zoom-in" onclick="buttonsave();">
                            <i class="icon-ok bigger-110"></i>
                            <span class="ladda-label">Save</span><span class="ladda-spinner"></span></button>
							<?php
                            //buttonsave('discoveryaction.php','discoveriesform','wrapper','discoveries.php?pkscreenid=45&pid='.$case_id,0);
                            buttoncancel(45,'discoveries.php?pid='.$case_id.'&iscancel=1');
							?>
						</div>
                    </div>   
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
$(document).ready(function()
{

});
function loadholidays(year)
{
	$("#DivHollidays").load("loadholidays.php?year="+year);
}

function buttonsave()
{
	$("#instruction_html").val($("#instruction_data").html());
	var isagree	=	true;
	setTimeout(function()
	{
		$( ".questionscls" ).each(function(index) 
		{
		  //alert($(this).val()+"  <======>  "+$(".question_titlecls:eq("+index+")").val());
		  if($(this).val() > 35 && $(".question_titlecls:eq("+index+")").val() != '')
		  {
			 isagree	=	false; 
		  }
		});
		//alert(isagree);
		if(isagree == false)
		{
			swal({
					title: "Are you Sure?",
					text: "Do you really want to save?",
					icon: "warning",
					dangerMode: true,
					buttons: {
					cancel: "No",
					catch: {
					  text: "Yes"
					}
				  },
				})
				.then((willDelete) => {
				if (willDelete) {
				addform('discoveryaction.php','discoveriesform','wrapper','discoveries.php?pkscreenid=45&pid=<?php echo $case_id?>');
				} 
			});
		}
		else
		{
			addform('discoveryaction.php','discoveriesform','wrapper','discoveries.php?pkscreenid=45&pid=<?php echo $case_id?>');
		}
	},200);
	
}
$(function () 
{
 $('.datepicker').datepicker({format: 'yyyy-mm-dd',autoclose:true});
});
</script>