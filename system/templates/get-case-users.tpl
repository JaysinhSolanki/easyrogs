<table class="table table-bordered table-hover table-striped">
  <tbody>
    <tr>
        <th>Name</th>
        <th>Role</th>
        <th>Action</th>
    </tr>
    {foreach item=user from=$users}
    <tr>
      <td><strong>{$user.firstname} {$user.middlename} {$user.lastname}</strong></td>
      <td>{$user.group_name}</td>
      <td style="text-align: right">
        <a class="side-delete-user btn btn-small delete-user-btn" data-user_id="{$user.id}" data-case_id="{$caseId}" "title="Delete"><i class="fa fa-trash fa-2x" style="color: red"></i></a>
      </td>
    </tr>
    {/foreach}
  </tbody>
</table>