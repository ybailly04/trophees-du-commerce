<?php
Kirby::plugin('bim/bim-blocks', [
  'blueprints' => [
    'blocks/faq' => __DIR__ . '/blueprints/blocks/faq.yml',
    'blocks/image' => __DIR__ . '/blueprints/blocks/image.yml',
    'blocks/largeText' => __DIR__ . '/blueprints/blocks/largeText.yml',
    // more blueprints
  ],
  'snippets' => [
    'blocks/faq' => __DIR__ . '/snippets/blocks/faq.php',
    'blocks/image' => __DIR__ . '/snippets/blocks/image.php',
    'blocks/gallery' => __DIR__ . '/snippets/blocks/gallery.php',
    'blocks/largeText' => __DIR__ . '/snippets/blocks/largeText.php',
    // more snippets
  ],
  'translations' => [
    // more languages
  ]
]);