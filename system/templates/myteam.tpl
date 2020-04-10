<div class="row">
  <div class="col-md-12">
    <a href="#add-member" class="pull-right btn btn-success btn-small" id="add-team-member-btn" style="margin-bottom:10px !important"><i class="fa fa-plus"></i> Add New</a>
    <h4>My Team</h4>
    <div id="memberModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <form id="submit-user-form">
            <div class="modal-header" style="padding: 15px;">
              <h5 class="modal-title" id="exampleModalLongTitle" style="font-size: 22px;">Add Member</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="member_name">Name</label>
                <input type="text" placeholder="Name" class="form-control m-b"  name="member_name" id="member_name" required />
              </div>
              <div class="form-group">
                  <label for="member_email">Email</label>
                  <input type="text" placeholder="Email" class="form-control m-b" name="member_email" id="member_email" required />
              </div>
            </div>

            <div class="modal-footer">
              <a href="#submit-member" class="btn btn-success" id="submit-team-member-btn"><i class="fa fa-save"></i> Save</a>
              <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
              <i id="msgClient" style="color:red"></i>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="row" style="margin-top: 20px">
      <div class="col-sm-12" id="loadattoneys"> </div>
    </div>
  </div>
</div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> 
<script src="/system/assets/sections/myteam.js"></script>