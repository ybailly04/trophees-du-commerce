<?php snippet('header') ?>

<main>

    <h1><?= $page->title() ?></h1>
    <?php foreach ($candidates as $candidate): ?>
        <a href="<?= $candidate->permalink() ?>"><?= $candidate->title() ?></a>
    <?php endforeach ?>

</main>

<?php snippet('footer') ?>