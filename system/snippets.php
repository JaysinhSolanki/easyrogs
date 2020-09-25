<?php

define( 'SNIPPET_ANALYTICS', @$_ENV['ANALYTICS_DISABLED'] ? '' : '
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id='. APP_GOOGLE_ANALYTICS_ID .'"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag("js", new Date());

      gtag("config", "'. APP_GOOGLE_ANALYTICS_ID .'", { "transport_type": "beacon"});
    </script>
  ' );

define( 'SNIPPET_SMARTSUPP', @$_ENV['SMARTSUPP_DISABLED'] ? '' : <<<SNIPPET
<!-- Smartsupp Live Chat script -->
<script type="text/javascript">
    var _smartsupp = _smartsupp || {};
    _smartsupp.key = 'ae242385584ca4d3fd78d74a04dbd806ef3957e0';
    window.smartsupp||(function(d) {
    var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
    s=d.getElementsByTagName('script')[0];c=d.createElement('script');
    c.type='text/javascript';c.charset='utf-8';c.async=true;
    c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
    })(document);
</script>
SNIPPET );

define( 'SNIPPET_STRIPE', @$_ENV['PAY_DISABLED'] ? '<script>function Stripe() {}</script>' : <<<SNIPPET
<script src="https://js.stripe.com/v3/"></script>
SNIPPET );
