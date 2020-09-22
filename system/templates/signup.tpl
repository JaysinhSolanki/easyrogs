{extends file="layouts/two-panels-full-height.tpl"}

{assign var="title" value="Create your account"}

{block name=panel_left}
  <div id="signup-highlights">
    <img src="{$ASSETS_URL}images/logo-white.png" alt="EasyRogs Logo" width="200"/>
    <ul class="signup-fancy-bulletpoints text-left">
      <li>
        <strong>Makes Discovery Easy</strong> <br/>
        {* <small>
          Lorem ipsum dolor sit amet
          Lorem ipsum dolor sit amet
        </small> *}
      </li>
      <li>
        <strong>Saves Time & Money</strong> <br/>
        {* <small>
          Lorem ipsum dolor sit amet
          Lorem ipsum dolor sit amet
        </small> *}
      </li>
      <li>
        <strong>Protects our Health</strong> <br/>
        {* <small>
          Lorem ipsum dolor sit amet
          Lorem ipsum dolor sit amet
        </small> *}
      </li>
      <br/>
    </ul>
  </div>  
{/block}

{block name=panel_right}
  <h1 class="step1">{$title}</h1>
  <br/>
  {include file="partials/notifications.tpl"}

  <form method="post" id="signup-form" enctype="multipart/form-data" class="needs-validation" novalidate>
    <div class="step2">
      <h1>
        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-envelope" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383l-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z"/>
        </svg>    
        You've got email!
      </h1>
      <br/>
      <p>
        We just sent an email to the address below. 
        <br/>
        <br/>
        <strong>Please check your email to complete the signup process and login to your account. </strong>
      </p>
      <br/>
    </div>

    <input type="hidden" name="invitation_uid" value="{$invitation_uid}" />
    <div class="step step-form">
      <div class="form-group text-left step1">
        <div class="custom-control-lg custom-switch custom-switch-adaptive">
          <input type="checkbox" class="custom-control-input" id="attorney-switch" name="is_attorney" value="1" data-target="#barnumber-form-group" data-toggle="collapse">
          <label class="custom-control-label font-weight-bold" for="attorney-switch">I am an Attorney</label>
        </div>
      </div>

      <div class="form-group step1 collapse" id="barnumber-form-group">
        <label for="barnumber">Bar No.</label>
        <input type="text" id="barnumber" class="form-control " name="barnumber"  maxlength="15"/>
        <div class="invalid-feedback text-left">Please enter your Bar Number.</div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-6 step1">
          <label for="firstname">First Name</label>
          <input autofocus type="text" id="firstname" value="{$firstname}" class="form-control" name="firstname" required minlength="3"/>
          <div class="invalid-feedback text-left">Please enter your first name.</div>
        </div>
        <div class="form-group col-md-6 step1">
        <label for="lastname">Last Name</label>
          <input type="text" id="lastname" class="form-control" name="lastname" value="{$lastname}" required minlength="3"/>
          <div class="invalid-feedback text-left">Please enter your last name.</div>
        </div>
      </div>

      <div class="form-group always-visible step1 step2">
        <label for="email">Email</label>
        <input type="email" value="{$email}" id="email" class="form-control" name="email" required/>
        <div class="invalid-feedback text-left">Please enter your email.</div>
      </div>

      <div class="form-group step1">
        <label for="password">Password</label>
        <input type="password" value="" id="password" class="form-control" name="password" required/>
        <div class="invalid-feedback text-left">Please enter a password.</div>
      </div>
      
      <div class="form-check form-group text-left step1">
        <input class="form-check-input" name="terms" id="terms" type="checkbox" value="1" required />
        <label for="terms" class="form-check-label">
          Agree to our <a href="termsofservice.php" target="_blank">Terms of Service</a>
        </label>
        <div class="invalid-feedback text-left">You must agree to our terms of service.</div>      
      </div>

      <div class="form-group step1">
        <button class="btn btn-info get-started" data-toggle="modal" data-target="#verification-modal">Get Started</button>
      </div>
    </div>

    <div class="text-center step1">Already have an account? <a href="userlogin.php">Login</a></div>
    <div class="step2 text-center">
      Didn't get the email? <button class="btn btn-warning btn-sm get-started resend-email" data-toggle="modal" data-target="#verification-modal">Re-send</button>
    </div>

  </form>
{/block}

{block name=css_dependencies}
  <link rel="stylesheet" href="{$ASSETS_URL}sections/signup.css" />
{/block}

{block name=js_dependencies}
  <script src="{$ASSETS_URL}sections/signup.js"></script>
{/block}