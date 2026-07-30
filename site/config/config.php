<?php

$smtp = include __DIR__ . '/config.smtp.php';

return [
    'debug'  => true,
    'email'  => [
        'transport' => [
            'type'     => 'smtp',
            'host'     => $smtp['host'],
            'port'     => $smtp['port'],
            'security' => $smtp['security'],
            'auth'     => true,
            'username' => $smtp['username'],
            'password' => $smtp['password'],
        ],
        'from'     => $smtp['from'],
        'fromName' => $smtp['fromName'],
    ],
    'hooks' => [
        'page.changeStatus:after' => function ($newPage, $oldPage) {
            if ($newPage->intendedTemplate()->name() !== 'candidate') {
                return;
            }

            $wasDraft       = $oldPage->status() === 'draft';
            $isNowPublished = $newPage->status() === 'listed';

            if ($wasDraft && $isNowPublished) {
                try {
                    kirby()->email([
                        'to'      => 'yann@bimagency.fr',
                        'subject' => 'Votre inscription ' . $newPage->title() . " a bien été validée !",
                        'body'    => "Votre inscription pour \"{$newPage->title()}\" a bien été validée et est disponible sur le site à l'adresse " . $newPage->url() .". \n Bonne chance !",
                    ]);
                } catch (\Throwable $e) {
                    error_log('Erreur envoi email publication candidat : ' . $e->getMessage());
                }
            }
        },
    ],
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

                // Upload des fichiers (logo, image, galerie) et mise à jour du contenu en une seule fois
                if ($page instanceof \Kirby\Cms\Page) {
                    kirby()->impersonate('kirby', function () use ($page) {
                        $update = [];

                        foreach (['logo', 'image'] as $field) {
                            if (($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                                continue;
                            }

                            $update[$field] = $page->createFile([
                                'source'   => $_FILES[$field]['tmp_name'],
                                'filename' => $_FILES[$field]['name'],
                            ])->uuid()->toString();
                        }

                        if (isset($_FILES['gallery'])) {
                            $galleryUuids = [];
                            $names = $_FILES['gallery']['name'] ?? [];

                            foreach ($names as $i => $name) {
                                if ($_FILES['gallery']['error'][$i] !== UPLOAD_ERR_OK) {
                                    continue;
                                }

                                $galleryUuids[] = $page->createFile([
                                    'source'   => $_FILES['gallery']['tmp_name'][$i],
                                    'filename' => $name,
                                ])->uuid()->toString();
                            }

                            if (!empty($galleryUuids)) {
                                $update['galery'] = $galleryUuids;
                            }
                        }

                        if (!empty($update)) {
                            $page->update($update);
                        }
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
            'pattern' => 'sitemap.xml',
            'action'  => function() {
                $pages = site()->pages()->index();

                // fetch the pages to ignore from the config settings,
                // if nothing is set, we ignore the error page
                $ignore = kirby()->option('sitemap.ignore', ['error']);

                $content = snippet('sitemap', compact('pages', 'ignore'), true);

                // return response with correct header type
                return new Kirby\Cms\Response($content, 'application/xml');
            }
        ],
        [
            'pattern' => 'sitemap',
            'action'  => function() {
                return go('sitemap.xml', 301);
            }
        ]
    ],
    'timnarr.imagex' => [
        'cache' => true,
        'compareFormatsWeights' => 'mobile',
        'customLazyloading' => false,
        'formats' => ['webp'],
        'addOriginalFormatAsSource' => false,
        'noSrcsetInImg' => false,
        'relativeUrls' => false,
    ],
    'thumbs' => [
        'srcsets' => [
            'default' => [
                '800w' => ['width' => 800, 'quality' => 80],
                '1024w' => ['width' => 1024, 'quality' => 80],
                '1440w' => ['width' => 1440, 'quality' => 80],
                '2048w' => ['width' => 2048, 'quality' => 80]
            ],
            'default-webp' => [ // preset for webp
                '800w'  => ['width' =>  800, 'crop' => true, 'quality' => 75, 'format' => 'webp', 'sharpen' => 10],
                '1200w' => ['width' => 1200, 'crop' => true, 'quality' => 75, 'format' => 'webp', 'sharpen' => 10],
                '1400w' => ['width' => 1400, 'crop' => true, 'quality' => 75, 'format' => 'webp', 'sharpen' => 10],
                '2048w' => ['width' => 2048, 'crop' => true, 'quality' => 75, 'format' => 'webp', 'sharpen' => 10],
            ],
            'half' => [
                '400w' => ['width' => 400, 'quality' => 80],
                '800w' => ['width' => 800, 'quality' => 80],
                '1024w' => ['width' => 1024, 'quality' => 80],
                '1440w' => ['width' => 1440, 'quality' => 80],
            ],
            'half-webp' => [ // preset for webp
                '400w'  => ['width' =>  400, 'crop' => true, 'quality' => 75, 'format' => 'webp', 'sharpen' => 10],
                '800w'  => ['width' =>  800, 'crop' => true, 'quality' => 75, 'format' => 'webp', 'sharpen' => 10],
                '1200w' => ['width' => 1200, 'crop' => true, 'quality' => 75, 'format' => 'webp', 'sharpen' => 10],
                '1400w' => ['width' => 1400, 'crop' => true, 'quality' => 75, 'format' => 'webp', 'sharpen' => 10],
            ],
            'gallery' => [
                '200w' => ['width' => 200, 'quality' => 80],
            ],
            'gallery-webp' => [
                '200w'  => ['width' =>  200, 'crop' => true, 'quality' => 75, 'format' => 'webp', 'sharpen' => 10],
            ]
        ]
    ]
];
