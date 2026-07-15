<?php

return [
    'debug'  => true,
    'routes' => [
        [
            'pattern' => 'candidature/submit',
            'method'  => 'POST',
            'action'  => function () {
                // Protection CSRF
                if (!csrf(get('_csrf'))) {
                    go('candidature?error=csrf');
                }

                $errors = [];

                if (empty(trim(get('title', '')))) {
                    $errors[] = 'Le nom de l\'établissement est requis.';
                }

                $email = get('email', '');
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'L\'adresse email est invalide.';
                }

                $description = get('description', '');
                if (strlen($description) > 600) {
                    $errors[] = 'La description ne peut pas dépasser 600 caractères.';
                }

                $rawCategories = get('categories', []);
                if (!is_array($rawCategories)) {
                    $rawCategories = [$rawCategories];
                }
                if (count($rawCategories) > 5) {
                    $errors[] = 'Vous ne pouvez pas sélectionner plus de 5 catégories.';
                }

                if (!empty($errors)) {
                    kirby()->session()->set('form_errors', $errors);
                    kirby()->session()->set('form_data', array_merge(get(), ['categories' => $rawCategories]));
                    go('candidature');
                }

                $parent = site()->find('candidates');
                if (!$parent) {
                    go('candidature?error=configuration');
                }

                $content = [
                    'title'       => get('title'),
                    'adress'      => get('adress'),
                    'email'       => $email,
                    'phone'       => get('phone'),
                    'description' => $description,
                    'categories'  => implode("\n", $rawCategories),
                    'website'     => get('website'),
                    'instagram'   => get('instagram'),
                ];

                $slug = Str::slug(get('title') . '-' . time());

                $page = kirby()->impersonate('kirby', function () use ($parent, $slug, $content) {
                    return $parent->createChild([
                        'slug'     => $slug,
                        'template' => 'candidate',
                        'content'  => $content,
                    ]);
                });

                // Upload de l'image si fournie
                if (
                    isset($_FILES['image']) &&
                    $_FILES['image']['error'] === UPLOAD_ERR_OK &&
                    $page instanceof \Kirby\Cms\Page
                ) {
                    kirby()->impersonate('kirby', function () use ($page) {
                        $page->createFile([
                            'source'   => $_FILES['image']['tmp_name'],
                            'filename' => $_FILES['image']['name'],
                        ]);
                    });
                }

                go('candidature?success=1');
            }
        ],
        [
            'pattern' => 'vote/(:all)',
            'method'  => 'POST',
            'action'  => function (string $id) {
                if (!csrf(get('_csrf'))) {
                    return Response::json(['error' => 'Jeton de sécurité invalide.'], 403);
                }

                $page = site()->find($id);

                if (!$page || $page->intendedTemplate()->name() !== 'candidate') {
                    return Response::json(['error' => 'Candidat introuvable.'], 404);
                }

                $votedIds = json_decode(Cookie::get('votes', '[]'), true);
                if (!is_array($votedIds)) {
                    $votedIds = [];
                }

                if (in_array($page->id(), $votedIds, true)) {
                    return Response::json(['error' => 'Vous avez déjà voté pour ce candidat.'], 409);
                }

                $count = $page->count()->toInt() + 1;

                kirby()->impersonate('kirby', function () use ($page, $count) {
                    $page->update(['count' => $count]);
                });

                $votedIds[] = $page->id();
                Cookie::set('votes', json_encode($votedIds), ['lifetime' => 60 * 24 * 365]);

                return Response::json(['count' => $count]);
            }
        ],
        [
        'pattern' => 'logout',
        'action'  => function() {

            if ($user = kirby()->user()) {
            $user->logout();
            }

            go('login');

        }
        ]
    ]
];
