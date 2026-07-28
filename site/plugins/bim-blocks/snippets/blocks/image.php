<?php

/** @var \Kirby\Cms\Block $block */
$alt     = $block->alt();
$caption = $block->caption();
$crop    = $block->crop()->isTrue();
$link    = $block->link();
$ratio   = $block->ratio()->or('auto');
$src     = null;

if ($block->location() == 'web') {
    $src = $block->src()->esc();
} elseif ($image = $block->image()->toFile()) {
    $alt = $alt->or($image->alt());
    $src = $image->url();
    $options = [
    'image' => $image,
    'attributes' => [
        'img' => [
            'class' => ['block-image'],
        ],
        'picture' => [
            'class' => ['block-picture'],
        ]
    ],
    'ratio' => $ratio,
    'srcset' => 'default',
    'loading' => 'lazy',
    ];
}

?>
<?php if ($src): ?>
<figure<?= Html::attr(['data-ratio' => $ratio, 'data-crop' => $crop], null, ' ') ?> style="--ratio:<?= $ratio ?>">
  <?php if ($link->isNotEmpty()): ?>
  <a href="<?= Str::esc($link->toUrl()) ?>">
    <img src="<?= $src ?>" alt="<?= $alt->esc() ?>">
  </a>
  <?php else: ?>
    <?php snippet('imagex-picture', $options) ?>
  <?php endif ?>

  <?php if ($caption->isNotEmpty()): ?>
  <figcaption>
    <?= $caption ?>
  </figcaption>
  <?php endif ?>
</figure>
<?php endif ?>