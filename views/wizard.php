<?php
$title = 'Find My Codepoint';
$hDescription = 'Find a certain codepoint by answering a set of questions.';
include "header.php";
include "nav.php";
?>
<div class="payload wizard">
  <h1><?php e($title)?></h1>
  <?php if (isset($message) && $message):?>
    <p class="error"><?php e($message)?></p>
  <?php endif?>
  <p>You search for a specific character? Answer the following questions and
     we try to figure out candidates.</p>
  <div id="wizard_container" class="wizard">
    <noscript>
      <p>We’re sorry, but for the wizard Javascript is needed.</p>
      <p>We’d like to apologize for the inconvenience.</p>
    </noscript>
  </div>
</div>
<?php
$footer_scripts = array(
    "/static/js/wizard.js"
);
include "footer.php"?>
