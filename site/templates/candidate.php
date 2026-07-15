<?php
  $votedIds = json_decode(Cookie::get('votes', '[]'), true);
  $hasVoted = is_array($votedIds) && in_array($page->id(), $votedIds, true);
?>
<?php snippet('header') ?>

  <main>
    <h1><?= $page->title() ?></h1>
    <?= $page->text()->kirbytext() ?>

    <button
      type="button"
      data-vote-button
      data-vote-url="/vote/<?= $page->id() ?>"
      data-csrf="<?= csrf() ?>"
      <?= $hasVoted ? 'disabled' : '' ?>
    >
      <?= $hasVoted ? 'Déjà voté' : 'Voter' ?> (<span data-vote-count><?= $page->count()->toInt() ?></span>)
    </button>
  </main>

<?php snippet('footer') ?>