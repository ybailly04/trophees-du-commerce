<?php

Kirby::plugin('trophees/candidates-in-category', [
    'pageMethods' => [
        // Les candidats ne sont pas des enfants de la page catégorie : ils vivent
        // sous "candidates" et référencent leurs catégories via le champ `categories`
        // (type `pages`). Utilisé par le blueprint de catégorie pour lister les
        // candidats qui la référencent.
        'candidatesInCategory' => function () {
            return site()->find('candidates')?->children()->filter(
                fn ($candidate) => $candidate->categories()->toPages()->has($this)
            ) ?? new Pages([]);
        },
    ],
]);
