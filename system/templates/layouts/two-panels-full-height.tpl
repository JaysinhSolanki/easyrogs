<!doctype html>
<html lang="en">
  <head>
    <title>{$title} | AI4Discovery</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="{$ASSETS_URL}/layouts/two-panels-full-height.css" />
    {include file="assets/global-styles.tpl"}
    {block name=css_dependencies}{/block}
  </head>
  <body class="er-full-height-two-panels">
    <div class="container-fluid h-100">
      <div class="row h-100">
        <div class="col-md-6 er-panel-left d-none d-md-block">
          {block name=panel_left}{/block}
        </div>
        <div class="col-md-6 er-panel-right">
          <div class="d-block d-md-none text-center">
            <img src="{$ASSETS_URL}images/logo.png" alt="AI4Discovery Logo" width="200"/>
            <br/>
            <br/>
          </div>
          {block name=panel_right}{/block}
        </div>
      </div>
    </div>
    
    {include file="assets/global-scripts.tpl"}
    {block name=js_dependencies}{/block}
  </body>
</html>
