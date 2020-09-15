<div id="notifications">
  {if $notifications} 
    {foreach from=$notifications item=notification}
      <div class="alert alert-{$notification.colorClass|default: 'info'} alert-dismissible fade show" role="alert">
        {$notification.message}
        {if $notification.action}
          <a class="btn btn-sm btn-primary" href="{$notification.action.url}">{$notification.action.text}</a>
        {/if}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>        
      </div>
    {/foreach}
  {/if}
</div>