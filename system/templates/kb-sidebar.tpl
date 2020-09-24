{function render}{$text|utf8_encode|nl2br|unescape|replace:'“':'&quot;'|replace:'”':'&quot;'|replace:'"':'&quot;'|replace:"'":"&apos;"|replace: "`":"&#96;"}{/function}

{if $items}
    {counter start=0 assign=idx}
    <div class="kb-section-list">
      {foreach item=item from=$items}
        <div class="kb-item">
          <h4>{render text=$item.issue}</h4>
          <div class="kb-item-content" id="kb-issue-{$idx}-sidebar">
            <a class="btn btn-round btn-outline pull-right" data-toggle="collapse" data-parent=".kb-item-content#kb-issue-{$idx}-sidebar" href="#kb-issue-{$idx}-user-notes" aria-expanded="true">
              <span class='fa fa-chevron-down'></span>
            </a>
            <div id="kb-issue-{$idx}-user-notes" class="collapse kb-sidebar-issue-user-notes">
              <p>
                {render text=$item.explanation}
              </p>
            </div>
            {if $item.solution}
              <button class='btn-add-{$itemType} {if $side == 'left' } pull-right {else} pull-left {/if}'
                onclick='javascript:{$fn}(`{{render text=$item.solution}|strip_tags}`);'>
                  {if $side == 'left' }
                      Insert &nbsp; <span class='fa fa-arrow-right'></span>
                  {else}
                      <span class='fa fa-arrow-left'></span> &nbsp; Insert
                  {/if}
              </button>
              <br/>
              <br/>
            {/if}
          </div>
        </div>
      {counter}
      {/foreach}
      <div class="kb-padding" style="clear:both;padding-bottom: 250px"></div>
    </div>
{/if}
