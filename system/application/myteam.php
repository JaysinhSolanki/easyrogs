<?php
require_once("adminsecurity.php");
$id		=	$_SESSION['addressbookid'];
?> 
<div class="row">
    <h4 for="fkstateid" class="col-md-10">My Team <?php instruction(1); ?></h4>
    
	<div class="col-md-2" align="right">
    	<a href="javascript:;" onclick="loadModalCaseTeamFunction()" class="btn btn-primary"  style="margin-bottom:10px !important"><i class="fa fa-plus"></i> Add New</a>
        <br />
    </div>
    <?php /*?><div class="col-md-12">
        <table class="table table-bordered table-hover table-striped" id="table_attornys">
            <tr>
                <th width="45%">Name</th>
                <th width="40%">Email</th>
                <th width="15%">Action</th>
            </tr>
            <tr>
                <td><input type="text" placeholder="Attorney Name" class="form-control m-b attr_names" name="attorney_name" id="attorney_name" ></td>
                <td><input type="text" placeholder="Attorney Email" class="form-control m-b attr_emails"  name="attorney_email" id="attorney_email"></td>
                <th valign="middle"> <a class="btn btn-primary" href="javascript:;" onclick="addAttorney(0,1)">Add Attorney</a> <br />
                    <i style="color:red; font-size:12px; font-weight:400" id="msgAttr"></i> </th>
            </tr>
        </table>
    </div><?php */?>
    <div class="col-sm-12" id="loadattoneys"> </div>
</div>
<script>
/*function addmyteammember()
{
	$('#addmyteammember').modal('show');
}*/
</script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> 
<script src="custom.js"></script> 
<script>

$( document ).ready(function() 
{	
	loadAttoneysFunction(0,1,"loadattoneys");
	$('[data-toggle="tooltip"]').tooltip();
});

</script>