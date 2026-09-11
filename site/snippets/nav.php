<?php
  $categories = site()->find('categories')->children()->listed();
  $current = $categories->find((string) $page->id()) ?? $categories->first();
?>

<nav data-section-dock-init aria-label="on this page" class="section-dock">
  <div data-section-dock-pill class="section-dock__pill">
    <button data-section-dock-toggle aria-expanded="false" aria-controls="section-dock-list" class="section-dock__toggle">
      <span data-section-dock-label-wrap class="section-dock__label-wrap">
        <span class="section-dock__label">
          <span class="section-dock__link-num"><?= str_pad($current->num(), 2, '0', STR_PAD_LEFT) ?></span>
          <span><?= $current->title()->html() ?></span>
        </span>
      </span>
    </button>
    <div data-section-dock-list id="section-dock-list" class="section-dock__list">
      <div data-section-dock-indicator class="section-dock__indicator"></div>
      <ul class="section-dock__items">
        <?php foreach ($categories as $category): ?>
        <li>
          <a <?= $category->is($current) ? 'data-active ' : '' ?>data-section-dock-link data-barba-update href="<?= $category->url() ?>" class="section-dock__link">
            <span class="section-dock__link-num"><?= str_pad($category->num(), 2, '0', STR_PAD_LEFT) ?></span>
            <span><?= $category->title()->html() ?></span>
          </a>
        </li>
        <?php endforeach ?>
      </ul>
    </div>
  </div>
</nav>