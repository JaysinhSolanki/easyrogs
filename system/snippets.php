<?php

define('SNIPPET_ANALYTICS', @$_ENV['ANALYTICS_DISABLED'] ? '' : '
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id='. APP_GOOGLE_ANALYTICS_ID .'"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag("js", new Date());

    gtag("config", "'. APP_GOOGLE_ANALYTICS_ID .'", { "transport_type": "beacon"});
  </script>
');

define( 'SNIPPET_SMARTSUPP', @$_ENV['SMARTSUPP_DISABLED'] ? '' : "
<!-- Smartsupp Live Chat script -->
<script type='text/javascript'>
    var _smartsupp = _smartsupp || {};
    _smartsupp.key = 'ae242385584ca4d3fd78d74a04dbd806ef3957e0';
    window.smartsupp||(function(d) {
    var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
    s=d.getElementsByTagName('script')[0];c=d.createElement('script');
    c.type='text/javascript';c.charset='utf-8';c.async=true;
    c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
    })(document);
</script>
");

define( 'SNIPPET_STRIPE', @$_ENV['PAY_DISABLED'] ? '<script>function Stripe() {}</script>' : '<script src="https://js.stripe.com/v3/"></script>' );

define( 'SNIPPET_ER_LAW_TOOLTIP_2031_280_a', '
  <i class="fa fa-university" aria-hidden="true"></i>
  Code Civ.Proc., § 2031.280.
  <a href="#">
    <i style="font-size:16px;"
      data-placement="right"
      data-toggle="tooltip"
      title=""
      class="fa fa-info-circle tooltipshow"
      aria-hidden="true"
      data-original-title="CODE OF CIVIL PROCEDURE SECTION 2031.280 (a)
      Any documents or category of documents produced in response to a demand for inspection, copying, testing, or sampling shall be identified with the specific request number to which the documents respond."
    ></i>
  </a>
');