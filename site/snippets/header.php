<html>
<head>
  <meta charset="UTF-8">
  <meta name="title" content="<?= $page->seoTitle()->or($page->title()) ?>">
  <meta name="description" content="<?= $page->seoTitle()->or($site->description()) ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>
    <?= $page->title() ?> | <?= $site->title() ?>
  </title>
  <?= vite()->css("assets/css/style.scss") ?>
</head>

<body data-barba="wrapper">
  <header class="header <?= isset($isHome) && $isHome ? "home" : "" ?>">
    <div class="header-wrapper">
      <a class="header-logo" href="<?= $site->url() ?>" title="Trophées du commerce">
        <?= $site->image('logo.svg') ?>
      </a>
      <div class="header-right">
        <button class="header-search">
        </button>
        <button class="header-burger" data-menu-button="burger">
          <div class="header-burger-title">MENU</div>
          <div class="header-burger-burger">
              <span></span>
              <span></span>
              <span></span>
          </div>
        </button>
      </div>
    </div>
  </header>