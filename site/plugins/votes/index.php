<?php

Kirby::plugin('trophees/votes', [
    'routes' => [
        [
            'pattern' => 'api/vote',
            'method'  => 'POST',
            'action'  => function () {
                $kirby = kirby();
                $input = json_decode(file_get_contents('php://input'), true);
                $pageId = trim($input['id'] ?? '');

                if (!$pageId) {
                    return \Kirby\Http\Response::json(['error' => 'missing_id'], 400);
                }

                $page = page($pageId);

                if (!$page || $page->intendedTemplate()->name() !== 'establishment') {
                    return \Kirby\Http\Response::json(['error' => 'not_found'], 404);
                }

                $cookieName = 'voted_' . str_replace('/', '_', $pageId);

                if (isset($_COOKIE[$cookieName])) {
                    return \Kirby\Http\Response::json([
                        'error' => 'already_voted',
                        'votes' => $page->votes()->toInt(),
                    ]);
                }

                $kirby->impersonate('kirby');
                $newVotes = $page->votes()->toInt() + 1;
                $page->update(['votes' => $newVotes]);

                setcookie($cookieName, '1', time() + 60 * 60 * 24 * 30, '/');

                return \Kirby\Http\Response::json(['success' => true, 'votes' => $newVotes]);
            },
        ],
    ],
]);
