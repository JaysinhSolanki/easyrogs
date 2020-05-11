<table class="table table-bordered table-hover table-striped">
  <tbody>
    <tr>
      <th>Name</th>
      <th>Role</th>
      <th>Representation</th>
      <th class="text-center">Action</th> 
    </tr>
    {if $clients}
      {foreach item=client from=$clients}            
      <tr>
        <td><strong>{$client.client_name}</strong></td>
        <td>{$client.client_role}</td>
        <td>{$client.client_type}</td>
        <td  class="text-center">
          <a href="javascript:;" title="Edit" onclick="editCaseClient({$client.id}, {$caseId})"><i class="fa fa-edit fa-2x"></i></a>
          <a href="javascript:;" onclick="deleteCaseClient({$client.id}, {$caseId})"><i class="fa fa-trash fa-2x" style="color:red"></i></a>
        </td>
      </tr>
      {/foreach}
    {else}
      <tr>
        <td colspan="5" style="text-align:center">There are no parties</td>
      </tr>
    {/if}
  </tbody>
</table>
