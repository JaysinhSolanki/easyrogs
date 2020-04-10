<table class="table table-bordered table-hover table-striped">
  <tbody>
    <tr>
      <th>Name</th>
      <th>Role</th>
      <th>Representation</th>
      <th>Email</th>
      <th>Action</th> 
    </tr>
    {foreach item=client from=$clients}            
    <tr>
      <td><strong>{$client.client_name}</strong></td>
      <td>{$client.client_role}</td>
      <td>{$client.client_type}</td>
      <td><a href="mailto:{$client.client_email}">{$client.client_email}</a></td>
      <td style="text-align: right">
        <a href="javascript:;" title="Edit" onclick="editCaseClient({$client.id}, {$caseId})"><i class="fa fa-edit fa-2x"></i></a>
        <a href="javascript:;" onclick="deleteCaseClient({$client.id}, {$caseId})"><i class="fa fa-trash fa-2x" style="color:red"></i></a>
      </td>
    </tr>
    {/foreach}
  </tbody>
</table>