{assign var='actionUrl' value=$actionUrl|default:'https://easyrogs.com'}
{assign var='actionText' value=$actionText|default:'Go to EasyRogs.com'}
<html>
  <head>
    <title>EasyRogs</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  </head>
  <body>
    {literal}
    <!--[if mso]>
      <style type="text/css">
        body, table, td {font-family: OpenSans, Helvetica, Arial, sans-serif !important;color: #4a4a4a; }
      </style>
    <![endif]-->
    {/literal}
    
    <div class="easyrogs-mailer" style="background-color: #eeeeee; font-family: OpenSans, Helvetica, Arial, sans-serif; font-size: 14px; color: #4a4a4a; line-height: 1.25;" align="left">
      <table class="easyrogs-mailer" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" style="font-family: OpenSans, Helvetica, Arial, sans-serif; font-size: 14px; text-align: left; color: #4a4a4a; line-height: 1.25;" bgcolor="#eeeeee">
        <tbody>
          <tr>
            <td align="center" valign="top">
              <table border="0" cellpadding="20" cellspacing="0" width="600">
                <tbody>
                  <tr>
                    <td align="center" valign="top">
                      <div>
                        <table class="main" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-radius: 3px; border: 1px solid #e9e9e9;" bgcolor="#ffffff">
                          <tbody>
                            <tr>
                              <td align="center" valign="top">
                                <table border="0" cellpadding="20" cellspacing="0" height="50" width="100%" style="" bgcolor="#ffffff">
                                  <tbody>
                                    <tr>
                                      <td align="center" valign="middle" height="30" style="padding-top: 30px;">
                                        <a href="https://easyrogs.com" style="font-family: Authenia-Solid !important; font-weight: bold; font-size: 32px !important">
                                          <img src="{$ASSETS_URL}images/logo.png" alt="EasyRogs Logo" />
                                        </a>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                            <tr>
                              <td align="center" valign="top" style="height: 1px;">
                                <table class="line-holder" border="0" cellpadding="0" cellspacing="0" height="1" width="100%" style="" bgcolor="#eeeeee">
                                  <tbody>
                                    <tr>
                                      <td class="line-side" align="center" valign="middle" style="height: 1px;" width="150" bgcolor="#eeeeee"></td>
                                      <td class="line-center" valign="middle" style="height: 1px;" width="200" bgcolor="#eeeeee"></td>
                                      <td class="line-side" valign="middle" style="height: 1px;" width="150" bgcolor="#eeeeee"></td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                            <tr>
                              <td align="center" valign="top">
                                <table border="0" cellpadding="0" cellspacing="0" height="0" width="100%">
                                  <tbody>
                                    <tr>
                                      <td class="content" align="left" valign="middle" style="padding: 40px 30px 20px;">
                                        <div>
                                          {block name=body}{/block}
                                          {if !$overwriteSalutation}
                                          <br/>
                                          <br/>
                                          <br/>
                                          Regards,
                                          <br/>
                                          <i>The EasyRogs Team</i>
                                          {/if}
                                        </div>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                            <tr>
                              <td align="center" valign="top">
                                <table border="0" cellpadding="30" cellspacing="0" height="0" width="100%" style="border-top-width: 1px; border-top-style: solid; border-top-color: #eee;">
                                  <tbody>
                                    <tr>
                                      <td align="center" valign="middle" width="100%">
                                        <div>
                                          {if $overwriteAction}
                                            {block name=action}{/block}
                                          {else}  
                                            <a href="{$actionUrl}" style="background-color: #187204; color: #ffffff; display: inline-block; font-family: sans-serif; font-size: 16px; line-height: 40px; margin-bottom: 10px; text-align: center; text-decoration: none; width: 200px; mso-hide: all; font-weight: bold;" target="_blank">
                                              {$actionText}
                                            </a>
                                          {/if}
                                        </div>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" valign="top">
                      <p style="color: #7f7f7f; font-size: 12px; padding: 20px 0;">All rights reserved &copy; {$smarty.now|date_format:"%Y"} EasyRogs. U.S. Patent Pending.</p>
                    </td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </body>
</html>