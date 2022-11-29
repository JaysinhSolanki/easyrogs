
<table class="table table-bordered table-hover table-striped">
  <tbody>
    <tr>
        <th>Name</th>
        <th>Role</th>
        <th class="text-center">Action</th>        
    </tr>
    {if $users}
      {foreach item=user from=$users}
      <tr>
        <td><strong>{$user.firstname} {$user.middlename} {$user.lastname}</strong></td>
        <td>
          {if $user.is_primary}
            Lead Counsel
          {else}  
            {$user.group_name}
          {/if}
        </td>
        <td  class="text-center">
          {if $user && !$user.is_primary && !$user.side_active}
            <b>Awaiting Approval</b>
            <br/>
            <a href="#" class="btn btn-xs approve-join-request btn-warning" data-user-id="{$user.id}" data-case-id="{$caseId}">Approve</a>
            <a href="#" class="btn btn-xs deny-join-request btn-danger" data-user-id="{$user.id}" data-case-id="{$caseId}">Deny</a>
          {/if}
          {if !$user.is_current_user && !$user.is_primary && $user.side_active}
            <a class="side-delete-user btn btn-small delete-user-btn" data-user_id="{$user.id}" data-case_id="{$caseId}" title="Delete"><i class="fa fa-trash fa-2x" style="color: red"></i></a>
          {else}
            {if $user.is_primary}
              <a ><i style="" data-placement="top" data-toggle="tooltip" title=
                  "Lead Counsel cannot be deleted from the Team. To delete this member, you must first replace them as Lead Counsel." 
                  class="tooltipshow fa fa-2x fa-info-circle" aria-hidden="true"></i></a>
            {/if}
          {/if}
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