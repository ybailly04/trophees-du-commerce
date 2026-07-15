<?php snippet('header') ?>

<main>

  <h1>Catégories</h1>

  <?php foreach($page->children() as $category): ?>
  <?php snippet('category', ['category' => $category]) ?>
  <?php endforeach ?>

</main>

<?php snippet('footer') ?>