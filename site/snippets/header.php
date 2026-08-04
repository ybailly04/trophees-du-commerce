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
        <div class="header-search" data-search>
          <form class="header-search-form" role="search" autocomplete="off" data-search-form>
            <input
              type="text"
              name="q"
              class="header-search-input"
              placeholder="TAPEZ VOTRE RECHERCHE"
              data-search-input
              aria-label="Rechercher un candidat"
              aria-autocomplete="list"
              aria-controls="header-search-results"
            >
          </form>
          <button
            type="button"
            class="header-search-toggle"
            data-search-toggle
            aria-expanded="false"
            aria-controls="header-search-panel"
            aria-label="Rechercher un candidat"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="27" viewBox="0 0 28 27" fill="none">
              <path d="M12.8333 21.375C17.988 21.375 22.1667 17.3456 22.1667 12.375C22.1667 7.40444 17.988 3.375 12.8333 3.375C7.67868 3.375 3.5 7.40444 3.5 12.375C3.5 17.3456 7.67868 21.375 12.8333 21.375Z" stroke="#F24F43" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M24.4998 23.6252L19.4248 18.7314" stroke="#F24F43" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <div class="header-search-panel" id="header-search-panel" data-search-panel hidden>
            <ul class="header-search-results" id="header-search-results" data-search-results></ul>
          </div>
        </div>
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