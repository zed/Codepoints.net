<?php $title = 'Codepoints';
include "header.php";
include "nav.php";
?>
<div class="payload front">
  <h1><?php e($title)?></h1>
  <ol>
    <?php foreach ($planes as $plane):?>
      <li><a href="<?php e($router->getUrl($plane))?>"><?php e($plane->name)?></a></li>
    <?php endforeach?>
  </ol>
</div>
<?php include "footer.php"?>
