{block name=css_dependencies}{/block}

<div id="screenfrmdiv" class="main">
  <aside class="sidebar left">
    <div class="fixed"></div>
  </aside>

  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="hpanel">
          <div class="panel-heading text-center">
            <h3>
              {$title}
            </h3>
            {if subtitle}
              <h4>
                <strong>{$subtitle}</strong>
              </h4>
            {/if}
          </div>
          <div class="panel-body contents">
            {block name=panel_body}{/block}
          </div>
        </div>
      </div>
    </div>
    {block name=main_bottom}{/block}
  </div>
  <aside class="sidebar right">
    <div class="fixed"></div>
  </aside>
</div>

{block name=js_dependencies}{/block}