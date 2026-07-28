<?php snippet('header') ?>

  <main data-barba="container" data-barba-namespace="default">

    <div class="default">
      <?php if($page->toggleHeader() == "true"): ?>
        <div class="default-header">
          <?php $options = [
                'image' => $page->imageHeader() ? $page->imageHeader()->toFile() : $site->image('placeholder.png'),
                'srcset' => 'default',
                'ratio' => '16/9',
                'attributes' => [
                  'picture' => [
                    'class' => ['default-header-picture'],
                  ],
                  'img' => [
                    'sizes' => '100vw',
                  ]
                ]
          ]; ?>
          <?php snippet('imagex-picture', $options) ?>
          <div class="default-header-texts">
            <h1 class="default-header-title"><?= $page->title() ?></h1>
            <div class="default-header-subtitle"><?= $page->intro() ?></div>
          </div>
        </div>
      <?php endif; ?>
      <div class="default-container">
        <?php foreach ($page->layout()->toLayouts() as $layout): ?>
          <section class="layout-grid" id="<?= $layout->id() ?>">
            <?php foreach ($layout->columns() as $column): ?>
            <div class="column" style="--span:<?= $column->span(12) ?>">
              <div class="blocks">
                <?php foreach ($column->blocks() as $block): ?>
                <div class="block block-type-<?= $block->type() ?>">
                  <?= $block ?>
                </div>
                <?php endforeach ?>
              </div>
            </div>
            <?php endforeach ?>
          </section>
        <?php endforeach ?>
      </div>
    </div>
<?php snippet('footer') ?>
