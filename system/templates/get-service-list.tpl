{if $clients}
  <div class="row">
    <div class="col-md-11" style="text-align:right"> 
      <a href="javascript:;"  class="pull-right btn btn-success btn-small" onclick="loadServiceListModal({$caseId})" style="margin-bottom:10px !important"><i class="fa fa-plus"></i> Add New</a>
    </div>
    <div class="col-md-1"></div>
  </div>
{/if}
  
<div class="row">  
  <div class="col-md-1"></div>
  <div class="col-md-2">
    <label>Service List</label>
  </div>
  <div class="col-md-8">
    {if !$clients}
      <i>(Parties must be added before Service List is created.)</i>
    {else}  
      <table class="table table-bordered table-hover table-striped">
        <tbody>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Parties</th>
            <th class="text-center">Action</th>
          </tr>
          {if $serviceList}
            {foreach item=user from=$serviceList}
            <tr>
              <td><strong>{$user.attorney_name}</strong></td>
              <td>{$user.attorney_email}</td>
              <td>
                {foreach item=client from=$user.clients}
                  {$client.client_name}<br/>
                {/foreach}
              </td>
              <td class="text-center">
                <a title="Edit" class="edit-service-list-user-btn" emailverify="{$user.emailverified}" data-user-id="{$user.id}" data-case-id="{$caseId}" data-sl-attorney-id="{$user.attorney_id}">
                  <i class="fa fa-edit fa-2x"></i>
                </a>
                <a title="Delete" class="delete-service-list-user-btn" data-user-id="{$user.id}" data-case-id="{$caseId}" data-sl-attorney-id="{$user.attorney_id}">
                  <i class="fa fa-trash fa-2x" style="color:red"></i>
                </a>
              </td>
            </tr>
            {/foreach}
          {else}
            <tr>
              <td colspan="5" style="text-align:center">There are no members</td>
            </tr>
          {/if}
        </tbody>
      </table>
    {/if}
  </div>
  <div class="col-md-1"></div>
</div>
