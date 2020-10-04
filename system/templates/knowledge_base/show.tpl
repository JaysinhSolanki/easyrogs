{* TODO: DRY vvv *}
{function render}{$text|utf8_encode|nl2br|unescape|replace:'“':'&quot;'|replace:'”':'&quot;'|replace:'"':'&quot;'|replace:"'":"&apos;"|replace: "`":"&#96;"}{/function}

{extends file="layouts/embedded.tpl"}

{assign var="title" value="EasyRogs Knowledge Base"}

{block name=panel_body}
  <ul class="nav nav-tabs" id="kb-index-tabs" role="tablist">
    {foreach from=$kb item=area}
      <li role="presentation" {if $area.id eq 2}class="active"{/if}>
        <a href="#knowledge-base-{$area.id}" aria-controls="home" role="tab" data-toggle="tab">{$area.name}</a></li>
      </li>
    {/foreach}
  </ul>
  <div class="tab-content" id="kb-tabs">
    {foreach from=$kb item=area}
      <div class="tab-pane fade {if $area.id eq 2}active in{/if}" id="knowledge-base-{$area.id}" role="tabpanel" aria-labelledby="area-{$area.id}-tab">
        {foreach from=$area.items item=item}
          <br/>
          <div class="kn-item panel panel-default">
            <div class="panel-heading">
              <h4>{$item.issue}</h4>
            </div>
            <div class="panel-body">
              <h5>{$item.solution|nl2br}</h5>
            </div>
            {if $loggedIn && $item.solution neq $item.explanation}
              <div class="panel-footer">
                {render text=$item.explanation}
              </div>
            {/if}
          </div>
        {/foreach}
      </div>
    {/foreach}
  </div>
{/block}

{if !$loggedIn}
  {block name=main_bottom}
    <div class="row">
      <div class="er-action-bar">
        <div class="container">
          <div class="text-center">
            <strong>Join now and get 10 complimentary Services</strong> &nbsp;
            <a href="signup.php?coupon={$coupon}" class="btn btn-success er-kb-signup-button"><i class="fa fa-sign-in"></i> &nbsp;Join</a>
          </div>
        </div>
      </div>
    </div>
  {/block}
{/if}

{block name=css_dependencies}
  <link rel="stylesheet" href="{$ASSETS_URL}styles.css" />
  <link rel="stylesheet" href="{$ASSETS_URL}sections/knowledge_base.css" />
{/block}

{block name=js_dependencies}
  <script src="{$ASSETS_URL}sections/knowledge_base.js"></script>
{/block}