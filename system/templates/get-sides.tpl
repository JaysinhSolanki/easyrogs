<h1 style="text-align: center">-------------------- TEST ---------------------</h1>
<h4>Sides</h4>
{foreach key=sideNumber item=side from=$sides}
<div class="panel panel-default">
  <div class="panel-heading">
    <div class="row">
      <div class="col-md-6 side-role">SIDE {$side.id}</div>
      <div class="col-md-6">
        <strong class="attorney-label">Lead Counsel:</strong>
        <span class="attorney-name">{$side.attorney.full_name}</span>
      </div>
    </div>
  </div>
  <div class="panel-body">

    <h5>Clients</h5>
    <br/>
    <table class="table table-striped table-hover">
      <tr>
        <th>Name</th>
        <th>Role</th>
        <th>Email</th>
        <!--<th></th>-->
      </tr>
      {foreach item=client from=$side.clients}
      <tr>
        <td><strong>{$client.client_name}</strong></td>
        <td>{$client.client_role}</td>
        <td><a href="mailto:{$client.client_email}">{$client.client_email}</a></td>
        <!--<td style="text-align: right">
          <a class="edit-client btn btn-sm btn-small" data-side-id="{$side.id}" data-client-id="{$client.id}" title="Edit"><i class="fa fa-edit fa-2x"></i></a>
          {if count($side.clients)}
            <a class="side-delete-client btn btn-sm btn-small" data-side-id="{$side.id}" data-client-id="{$client.id}" title="Delete"><i class="fa fa-trash fa-2x" style="color: red"></i></a>
          {/if}
        </td>-->
      </tr>
      {/foreach}
    </table>
    <hr/>

    <h5>Staff</h5>
    <br/>
    <table class="table table-striped  table-hover">
      <tr>
        <th>User ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Side Active</th>
        <th>Lead Counsel?</th>
        <!--<th></th> -->
      </tr>
      {foreach item=user from=$side.users}
      <tr>
        <td>
          <b>{$user.pkaddressbookid}</b>
          {if !User::isActive($user)}
            <i>(inactive)</i>
          {/if}
        </td>
        <td><strong>{$user.firstname} {$user.middlename} {$user.lastname}</strong></td>
        <td><strong>{$user.email}</strong></td>
        <td>{if $user.group_name}{$user.group_name}{else}Unknown{/if}</td>
        <td>
          {if $user.is_primary || $user.side_active}
            Yes
          {else}
            No <!-- &nbsp; <a class="btn btn-xs btn-warning side-change-primary" data-side-id="{$side.id}" data-user-id="{$user.pkaddressbookid}">Make Primary</a>-->
          {/if}
        </td>
        <td>
          {if $user.is_primary}
            Yes
          {else}
            No <!-- &nbsp; <a class="btn btn-xs btn-warning side-change-primary" data-side-id="{$side.id}" data-user-id="{$user.pkaddressbookid}">Make Primary</a>-->
          {/if}
        </td>
        <!--<td style="text-align: right">
          {if !$user.is_primary}
            <a class="side-delete-client btn btn-small" data-side-id="{$side.id}" data-user-id="{$user.pkaddressbookid}" title="Delete"><i class="fa fa-trash fa-2x"></i></a>
          {/if}
        </td>-->
      </tr>
      {/foreach}
    </table>
    <hr/>

    <h5>Service List</h5>
    <br/>
    <table class="table table-striped  table-hover">
      <tr>
        <th>User ID</th>
        <th>User Name</th>
        <th>SL Name</th>
        <th>SL Email</th>
        <th>Parties</th>
      </tr>
      {foreach item=user from=$side.serviceList}
      <tr>
        <td>
          <strong>{$user.pkaddressbookid}</strong>
          {if !User::isActive($user)}
            <i>(inactive)</i>
          {/if}
        </td>
        <td><strong>{$usersModel->getFullName($user)}</strong></td>
        <td><strong>{$user.attorney_name}</strong></td>
        <td>{$user.attorney_email}</td>
        <td>
          {foreach item=client from=$user.clients}
            {$client.client_name}
            <br/>
          {/foreach}
        </td>
      </tr>
      {/foreach}
    </table>

  </div>
</div>
{/foreach}