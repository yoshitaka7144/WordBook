<?php
require_once("config.php");

function h($str)
{
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$inputType = filter_input(INPUT_POST, "quiz-type");
$inputCount = filter_input(INPUT_POST, "quiz-count");
$inputRadio = filter_input(INPUT_POST, "radio");

?>
<?php include(dirname(__FILE__) . '/header.php'); ?>
<main>
  <div class="container">
    <div class="main-contents">
      <?=$inputType?>
      <?=$inputCount?>
      <?=$inputRadio?>
    </div>
  </div>
</main>
<?php include(dirname(__FILE__) . '/footer.php'); ?>