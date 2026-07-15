<html>
<head>
  <meta charset="UTF-8">
  <meta name="description" content="<?= $site->description() ?>">
  <title>
    <?= $page->title() ?> | <?= $site->title() ?>
  </title>
  <?= vite()->css("assets/css/style.scss") ?>
</head>
<body>

  <header class="header">
    <div class="header-wrapper">
      <a class="header-button" href="/categories">
        <span>Catégories</span>
      </a>
      <a class="header-logo" href="<?= $site->url() ?>" title="Trophées du commerce">
        <?= $site->image('logo.svg') ?>
      </a>
      <button class="header-nav" data-menu>
        <span>Menu</span>
      </button>
    </div>
  </header>