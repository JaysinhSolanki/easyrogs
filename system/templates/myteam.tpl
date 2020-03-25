<div class="row">
    <h4 for="fkstateid" class="col-md-10">My Team</h4>
    
	  <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped" id="table_attornys">
            <tr>
                <th width="45%">Name</th>
                <th width="40%">Email</th>
                <th width="15%">Action</th>
            </tr>
            <tr>
              <td colspan="3">
                <select placeholder="Invite Member" class="form-control m-b attr_names" name="member_id" id="member_id" ></select>
                
                <div id="invite-member-form" class="row">
                  <div class="form-group col-md-6" >
                    <input type="text" placeholder="Name" class="form-control attr_names" name="member_name" id="member_name" >
                  </div>
                  <div class="form-group col-md-6">
                    <input type="text" placeholder="Email" class="form-control attr_emails"  name="member_email" id="member_email">
                  </div>  
                </div>
              </td>
            </tr>
            <tr>
              <td colspan="3" valign="middle"> <a class="btn btn-primary" href="#" id="add-team-member-btn">Add Member</a> <br />
                  <i style="color:red; font-size:12px; font-weight:400" id="msgAttr"></i> 
              </td>
            </tr>
        </table>
    </div>
    <div class="col-sm-12" id="loadattoneys"> </div>
</div>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> 
<script src="custom.js"></script>
<script src="/system/assets/sections/myteam.js"></script>