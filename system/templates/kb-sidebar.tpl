{function render}{$text|utf8_encode|nl2br|unescape|replace:'“':'&quot;'|replace:'”':'&quot;'|replace:'"':'&quot;'|replace:"'":"&apos;"}{/function}

{if $items}
    {counter start=0 assign=idx}
    <div class="kb-section-list">
      {foreach item=item from=$items}
        <div class="kb-item">
          <h4>{render text=$item.issue}</h4>
          <div class="kb-item-content"
               id="kb-issue-{$idx}-sidebar">
            <a data-toggle="collapse" data-parent=".kb-item-content#kb-issue-{$idx}-sidebar" href="#kb-issue-{$idx}-user-notes"
                    aria-expanded="true" class="btn btn-round btn-outline pull-right">
                <i class='fa fa-chevron-down'/>
            </a>
            <p id="kb-issue-{$idx}-user-notes" class="collapse">{render text=$item.explanation}</p>
            {if $item.solution}
              <button class='btn-add-objection {if $side == 'left' } pull-right {else} pull-left {/if}'
                      onclick='javascript:{$fn}(`{render text=$item.solution}`);'>
                  {if $side == 'left' }
                      Insert &nbsp; <i class='fa fa-arrow-right' />
                  {else}
                      <i class='fa fa-arrow-left' /> &nbsp; Insert 
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
