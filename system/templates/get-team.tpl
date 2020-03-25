<table class="table table-bordered table-hover table-striped" id="table_attornys" >
  <tr>
    <th>Name</th>
    <th>Email</th>
    <th  width="15%">Action</th> 
  </tr>
  {foreach from=$members item=member}
  <tr id="attr_{$member.pkaddressbookid}">
    <td>{$member.firstname} {$member.lastname}</td>
    <td>{$member.email}</td>
    <td  width="15%" align="center">
      <a href="#" title="Delete" class="delete-team-member" data-id="{$member.pkaddressbookid}"><i class="fa fa-trash fa-2x" style="color:red"></i></a>
    </td> 
  </tr>
  {/foreach}
</table>