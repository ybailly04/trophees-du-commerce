<article>
  <h1><?= $category->title()->html() ?></h1>
  <time><?= $category->date()->toDate('d/m/Y') ?></time>
  <?= $category->intro()->kirbytext() ?>
  <a href="<?= $category->url() ?>">Voter</a>
</article>