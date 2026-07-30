<?php
/** @var \Kirby\Cms\Block $block */
$caption = $block->caption();
$crop    = $block->crop()->isTrue();
$ratio   = $block->ratio()->or('auto');
?>
<figure<?= Html::attr(['data-ratio' => $ratio, 'data-crop' => $crop], null, ' ') ?>>
  <ul>
    <?php foreach ($block->images()->toFiles() as $image): ?>
    <?php
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
            'srcset' => 'gallery',
            'loading' => 'lazy',
        ];
    ?>
    <li>
        <?php if ($image->extension() === 'svg' || $image->extension() === 'gif'): ?>
            <img class="block-image-svg" src="<?= $image->url() ?>" alt="<?= $image->title() ?>">
        <?php else: ?>
            <?php snippet('imagex-picture', $options) ?>
        <?php endif; ?>
    </li>
    <?php endforeach ?>
  </ul>
  <?php if ($caption->isNotEmpty()): ?>
  <figcaption>
    <?= $caption ?>
  </figcaption>
  <?php endif ?>
</figure>