<?php

$smtp = include __DIR__ . '/config.smtp.php';

return [
    'debug'  => false,
    'cache' => [
        'pages' => [
            'active' => true,
            'ignore' => ['formulaire-de-candidature']
        ]
    ],
    'session' => [
        // 2h d'inactivité au lieu des 30 min par défaut : le formulaire de
        // candidature est long (photos, description...) et une session
        // expirée invalidait silencieusement le jeton CSRF à la soumission.
        'timeout' => 7200,
    ],
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
                        'from'     => option('email.from'),
                        'fromName' => option('email.fromName'),
                        'to'       => $newPage->email()->value(),
                        'subject'  => site()->objMail()->value(),
                        'body'     => [
                            'html' => str_replace('{link}', $newPage->url(), site()->mailValid()->value()),
                        ],
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
                $rawCategories = get('categories', []);
                if (!is_array($rawCategories)) {
                    $rawCategories = [$rawCategories];
                }

                if (empty($_POST) && empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
                    kirby()->session()->set('form_errors', [
                        'Les fichiers envoyés sont trop volumineux. Merci de réduire leur taille (5 Mo maximum par fichier) et réessayer.',
                    ]);
                    go('formulaire-de-candidature');
                }

                // Protection CSRF
                if (!csrf(get('_csrf'))) {
                    kirby()->session()->set('form_errors', [
                        'Votre session a expiré. Merci de valider à nouveau le formulaire ci-dessous (vos champs ont été conservés, il faudra seulement rejoindre vos fichiers).',
                    ]);
                    kirby()->session()->set('form_data', array_merge(get(), ['categories' => $rawCategories]));
                    go('formulaire-de-candidature');
                }

                $errors = [];
                $maxFileSize = 5 * 1024 * 1024; // 5 Mo
                $maxGalleryFiles = 5;

                $fileTooLarge = static function (array $error) {
                    return in_array($error['error'] ?? null, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true);
                };

                foreach (['logo' => 'Logo', 'image' => 'Image principale'] as $field => $label) {
                    $error = $_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE;

                    if ($error === UPLOAD_ERR_NO_FILE) {
                        $errors[] = "Le champ \"$label\" est requis.";
                    } elseif ($fileTooLarge(['error' => $error]) || ($_FILES[$field]['size'] ?? 0) > $maxFileSize) {
                        $errors[] = "Le fichier \"$label\" dépasse la taille maximale autorisée (5 Mo).";
                    } elseif ($error !== UPLOAD_ERR_OK) {
                        $errors[] = "Une erreur est survenue lors de l'envoi du fichier \"$label\".";
                    }
                }

                if (isset($_FILES['gallery'])) {
                    $galleryErrors = $_FILES['gallery']['error'] ?? [];
                    $gallerySizes  = $_FILES['gallery']['size'] ?? [];

                    if (count($galleryErrors) > $maxGalleryFiles) {
                        $errors[] = "Vous ne pouvez pas envoyer plus de $maxGalleryFiles photos dans la galerie.";
                    }

                    foreach ($galleryErrors as $i => $error) {
                        if ($error === UPLOAD_ERR_NO_FILE) {
                            continue;
                        }

                        if ($fileTooLarge(['error' => $error]) || ($gallerySizes[$i] ?? 0) > $maxFileSize) {
                            $errors[] = 'Une des images de la galerie dépasse la taille maximale autorisée (5 Mo).';
                        } elseif ($error !== UPLOAD_ERR_OK) {
                            $errors[] = "Une erreur est survenue lors de l'envoi d'une image de la galerie.";
                        }
                    }
                }

                $dateInscriptions = site()->dateInscriptions()->toDate('U');
                if ($dateInscriptions && time() > $dateInscriptions) {
                    $errors[] = 'La période d\'inscription est terminée.';
                }

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

                if (count($rawCategories) > 5) {
                    $errors[] = 'Vous ne pouvez pas sélectionner plus de 5 catégories.';
                }

                if (!empty($errors)) {
                    kirby()->session()->set('form_errors', $errors);
                    kirby()->session()->set('form_data', array_merge(get(), ['categories' => $rawCategories]));
                    go('formulaire-de-candidature');
                }

                $parent = site()->find('candidates');
                if (!$parent) {
                    kirby()->session()->set('form_errors', [
                        "Une erreur de configuration empêche l'envoi du formulaire. Merci de réessayer plus tard ou de nous contacter.",
                    ]);
                    kirby()->session()->set('form_data', array_merge(get(), ['categories' => $rawCategories]));
                    go('formulaire-de-candidature');
                }

                // Le champ "categories" du blueprint est de type `pages` : il attend des
                // références `page://<uuid>`, pas les uri brutes envoyées par le <select>.
                // On ne garde que celles qui correspondent à une vraie catégorie existante.
                $categoryPages = [];
                foreach (site()->find('categories')?->children() ?? [] as $categoryPage) {
                    if (in_array($categoryPage->uri(), $rawCategories, true)) {
                        $categoryPages[] = $categoryPage->uuid()->toString();
                    }
                }

                $content = [
                    'title'       => get('title'),
                    'adress'      => get('adress'),
                    'email'       => $email,
                    'phone'       => get('phone'),
                    'description' => $description,
                    'categories'  => $categoryPages,
                    'website'     => get('website'),
                    'instagram'   => get('instagram'),
                    'facebook'    => get('facebook'),
                ];

                $slug = Str::slug(get('title')) . '-' . bin2hex(random_bytes(4));

                try {
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
                } catch (\Throwable $e) {
                    error_log('Erreur lors de la création de la candidature : ' . $e->getMessage());

                    if (isset($page) && $page instanceof \Kirby\Cms\Page) {
                        kirby()->impersonate('kirby', function () use ($page) {
                            $page->delete(true);
                        });
                    }

                    kirby()->session()->set('form_errors', [
                        "Une erreur est survenue lors de l'envoi de votre candidature. Merci de réessayer.",
                    ]);
                    kirby()->session()->set('form_data', array_merge(get(), ['categories' => $rawCategories]));
                    go('formulaire-de-candidature');
                }

                go('formulaire-de-candidature?success=1');
            }
        ],
        [
            'pattern' => 'vote/(:all)',
            'method'  => 'POST',
            'action'  => function (string $id) {
                if (!csrf(get('_csrf'))) {
                    return Response::json(['error' => 'Jeton de sécurité invalide.'], 403);
                }

                $dateVotes = site()->dateVotes()->toDate('U');
                if ($dateVotes && time() > $dateVotes) {
                    return Response::json(['error' => 'La période de vote est terminée.'], 403);
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
            'pattern' => 'candidates/search',
            'method'  => 'GET',
            'action'  => function () {
                $query = trim(get('q', ''));

                if (mb_strlen($query) < 2) {
                    return Response::json(['results' => []]);
                }

                $candidates = site()->find('candidates')->children()->listed()
                    ->filter(function ($candidate) use ($query) {
                        return mb_stripos($candidate->title()->value(), $query) !== false;
                    })
                    ->sortBy('title', 'asc')
                    ->limit(8);

                $results = [];
                foreach ($candidates as $candidate) {
                    $logo = $candidate->logo()->toFile();

                    $results[] = [
                        'title' => $candidate->title()->value(),
                        'url'   => $candidate->url(),
                        'logo'  => $logo ? $logo->url() : null,
                    ];
                }

                return Response::json(['results' => $results]);
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
                '200w' => ['width' => 200, 'crop' => true, 'quality' => 80],
            ],
            'gallery-webp' => [
                '200w'  => ['width' =>  200, 'crop' => true, 'quality' => 75, 'format' => 'webp', 'sharpen' => 10],
            ],
            'logo' => [
                '200w' => ['width' => 250, 'crop' => true, 'quality' => 80],
            ],
            'logo-webp' => [
                '250w'  => ['width' =>  250, 'crop' => false, 'quality' => 75, 'format' => 'webp', 'sharpen' => 10],
            ],
            'thumb' => [
                '65w' => ['width' => 65, 'crop' => true, 'quality' => 80],
            ],
            'thumb-webp' => [
                '65w'  => ['width' => 65, 'crop' => true, 'quality' => 75, 'format' => 'webp', 'sharpen' => 10],
            ]
        ]
    ],
    'sylvainjule.matomo.url'        => 'https://stats.tropheesducommerce.fr',
    'sylvainjule.matomo.id'         => '1',
    'sylvainjule.matomo.token'      => 'b1123ce2da29c533ccac8eb75a18270f',
    'sylvainjule.matomo.active'     => true
];
