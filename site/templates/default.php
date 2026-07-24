<?php snippet('header') ?>

  <main data-barba="container" data-barba-namespace="default">
    <h1><?= $page->title() ?></h1>
    <?= $page->text()->kirbytext() ?>

<?php snippet('footer') ?>
