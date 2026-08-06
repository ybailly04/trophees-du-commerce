<?php
  $candidates = site()->find('candidates')->children()->filter(function ($candidate) use ($category) {
      return $candidate->categories()->toPages()->has($category);
  });
  $votedIds = json_decode(Cookie::get('votes', '[]'), true);
  $totalCategories = $category->parent()->children()->count();

?>

<article class="category">
  <div class="category-index">
    Catégorie <?= $category->num() ?>/<?= $totalCategories ?>
  </div>
  <div class="category-top">
    <?php if ($prev = $category->prev()): ?>
        <a class="category-prev-svg" href="<?= $prev->url() ?>" data-cursor-text="<?= $prev->title() ?>">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none">
            <circle cx="32" cy="32" r="32" transform="matrix(-1 0 0 1 64 0)" fill="#F24F43"/>
            <path d="M34 24L25.5147 32.4853L34 40.9706" stroke="#F7F1DA" stroke-width="2"/>
          </svg>
        </a>
      <?php else: ?>
        <div class="category-prev-svg disabled">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none">
            <circle cx="32" cy="32" r="32" transform="matrix(-1 0 0 1 64 0)" fill="#F24F43"/>
            <path d="M34 24L25.5147 32.4853L34 40.9706" stroke="#F7F1DA" stroke-width="2"/>
          </svg>
      </div>
    <?php endif ?>
    <h2 class="category-title"><?= $category->title()->html() ?></h2>
    <?php if ($next = $category->next()): ?>
      <a class="category-next" href="<?= $next->url() ?>" data-cursor-text="<?= $next->title() ?>">
        <svg class="category-next-svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none">
          <circle cx="32" cy="32" r="32" fill="#F24F43"/>
          <path d="M30 24L38.4853 32.4853L30 40.9706" stroke="#F7F1DA" stroke-width="2"/>
        </svg>
      </a>
    <?php else: ?>
      <div class="category-next disabled">
        <svg class="category-next-svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none">
          <circle cx="32" cy="32" r="32" fill="#F24F43"/>
          <path d="M30 24L38.4853 32.4853L30 40.9706" stroke="#F7F1DA" stroke-width="2"/>
        </svg>
    </div>
    <?php endif ?>
  </div>
  <ul class="category-listing">
    <?php foreach ($candidates->shuffle() as $candidate): ?>
      <?php $hasVoted = is_array($votedIds) && in_array($candidate->id(), $votedIds, true); ?>
      <li class="category-list">
        <a href="<?= $candidate->url() ?>">
          <?php if ($logo = $candidate->logo()->toFile()): ?>
            <img class="category-logo" src="<?= $logo->url() ?>" alt="<?= $candidate->title() ?>" data-cursor-text="<?= $candidate->title() ?>">
            <?php else: ?>
            <img class="category-logo" src="<?= $site->image('logo.svg')->url(); ?>" alt="<?= $candidate->title() ?>" data-cursor-text="<?= $candidate->title() ?>">
          <?php endif ?>
        </a>
        <a class="category-link button" href="<?= $candidate->url() ?>">
          <span class="button-inner">
            <span class="button-text">En savoir plus</span>
          </span></a>
        <button
          class="category-vote button"
          type="button"
          data-vote-button
          data-vote-url="/vote/<?= $candidate->id() ?>"
          data-csrf="<?= csrf() ?>"
          <?= $hasVoted ? 'disabled' : '' ?>
        >
        <div class="button-inner">
          <div class="button-text"><?= $hasVoted ? 'Votre pris en compte' : 'Voter' ?></div>
          <span class="button-spinner"></span>
        </div>
        </button>
      </li>
    <?php endforeach ?>
  </ul>
</article>