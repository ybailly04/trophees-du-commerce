<?php

return function ($page, $site) {
    $limit = $page->limit()->or(3)->toInt();

    // Pour chaque catégorie, les candidats ayant reçu au moins un vote,
    // classés par nombre de votes (puis par nom en cas d'égalité).
    $results = [];
    foreach ($site->find('categories')->children()->listed() as $category) {
        $results[] = [
            'category'   => $category,
            'candidates' => $category->candidatesInCategory()
                ->filter(fn ($candidate) => $candidate->count()->toInt() > 0)
                ->sortBy(
                    fn ($candidate) => $candidate->count()->toInt(), 'desc',
                    'title', 'asc'
                )
                ->limit($limit),
        ];
    }

    return compact('results');
};
