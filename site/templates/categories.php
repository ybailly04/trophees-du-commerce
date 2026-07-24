<?php snippet('header') ?>

<main data-barba="container" data-barba-namespace="categories">

  <div class="categories">
  
    <?php snippet('category', ['category' => $page->children()->first()]) ?>
      
  </div>

<?php snippet('footer') ?>