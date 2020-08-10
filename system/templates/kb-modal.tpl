{function render}{$text|utf8_encode|nl2br|unescape|replace:'“':'&quot;'|replace:'”':'&quot;'|replace:'"':'&quot;'|replace:"'":"&apos;"}{/function}

{if $items}
    {counter start=0 assign=idx}
    <div class="hpanel panel-group kb-section-list" id="accordion" role="tablist" aria-multiselectable="true" style="margin-top:10px !important;">
    {foreach item=item from=$items}
      <div class="kb-item">
        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#kb-issue-{$idx}" aria-expanded="false">
          <i class="fa fa-chevron-down pull-right text-muted" />
          {render text=$item.issue}
        </a>
        <div class="kb-item-content collapse"
             id="kb-issue-{$idx}" aria-expanded="false">
          <hr />
          <div>
              {render text=$item.explanation}
          </div>
          {if $item.solution}
              <button class='btn-add-objection pull-right'
                      onclick='javascript:{$fn}(\"{$target}\",\"{render text=$item.solution}\");'>
                <i class='fa fa-plus-circle' />
              </button>
          {/if}
        </div>
      </div>
    {counter}
    {/foreach}
{/if}