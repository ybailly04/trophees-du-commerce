<?php snippet('header') ?>

<main data-barba="container" data-barba-namespace="results">

  <div class="results">

    <div class="results-header">
      <h1 class="results-title"><?= $page->title()->html() ?></h1>
      <?php if ($page->intro()->isNotEmpty()): ?>
        <div class="results-intro"><?= $page->intro() ?></div>
      <?php endif ?>
    </div>

    <div class="results-categories">
      <?php foreach ($results as $result): ?>
        <section class="results-category">
          <h2 class="results-category-title">
            <a href="<?= $result['category']->url() ?>"><?= $result['category']->title()->html() ?></a>
          </h2>

          <?php if ($result['candidates']->isNotEmpty()): ?>
            <ol class="results-listing">
              <?php $rank = 0; foreach ($result['candidates'] as $candidate): $rank++ ?>
                <li class="results-item results-item--<?= $rank ?>">
                  <span class="results-rank"><?= $rank ?></span>
                  <a class="results-logo" href="<?= $candidate->url() ?>">
                    <?php if ($logo = $candidate->logo()->toFile()): ?>
                      <?php snippet('imagex-picture', [
                        'image' => $logo,
                        'srcset' => 'logo',
                        'attributes' => [
                          'img' => [
                            'alt' => $candidate->title(),
                          ]
                        ]
                      ]) ?>
                    <?php else: ?>
                      <img src="<?= $site->image('logo.svg')->url() ?>" alt="<?= $candidate->title() ?>">
                    <?php endif ?>
                  </a>
                  <a class="results-name" href="<?= $candidate->url() ?>"><?= $candidate->title()->html() ?></a>
                  <span class="results-votes">
                    <?php $count = $candidate->count()->toInt() ?>
                    <?= $count ?> <?= $count > 1 ? 'votes' : 'vote' ?>
                  </span>
                </li>
              <?php endforeach ?>
            </ol>
          <?php else: ?>
            <p class="results-empty">Aucun vote pour le moment dans cette catégorie.</p>
          <?php endif ?>
        </section>
      <?php endforeach ?>
    </div>

  </div>

<?php snippet('footer') ?>
