<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Voter — <?= $site->title() ?></title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --gold: #c9a84c;
      --gold-dark: #a8882e;
      --dark: #16213e;
      --bg: #f7f6f2;
      --card-bg: #fff;
      --text: #222;
      --muted: #666;
      --radius: 12px;
    }
    body { font-family: system-ui, -apple-system, sans-serif; background: var(--bg); color: var(--text); }

    header {
      background: var(--dark);
      color: #fff;
      text-align: center;
      padding: 3.5rem 1rem 2.5rem;
      border-bottom: 4px solid var(--gold);
    }
    header .trophy { font-size: 3.5rem; display: block; margin-bottom: .75rem; }
    header h1 { font-size: 2.4rem; letter-spacing: -.5px; }
    header p { margin-top: .75rem; color: #b0b8c8; font-size: 1.05rem; }

    main { max-width: 960px; margin: 3rem auto; padding: 0 1.25rem; }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 1.5rem;
    }

    .cat-card {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: .75rem;
      background: var(--card-bg);
      border-radius: var(--radius);
      padding: 2.25rem 1.5rem;
      text-decoration: none;
      color: inherit;
      box-shadow: 0 2px 10px rgba(0,0,0,.07);
      border-top: 4px solid var(--gold);
      transition: transform .18s, box-shadow .18s;
    }
    .cat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 28px rgba(0,0,0,.13); }
    .cat-card .icon { font-size: 3.2rem; }
    .cat-card h2 { font-size: 1.4rem; text-align: center; }
    .cat-card .count { font-size: .9rem; color: var(--muted); }

    footer { text-align: center; padding: 2.5rem 1rem; color: var(--muted); font-size: .85rem; }
  </style>
</head>
<body>

<header>
  <span class="trophy">🏆</span>
  <h1><?= $site->title() ?></h1>
  <p>Votez pour vos adresses préférées dans chaque catégorie</p>
</header>

<main>
  <div class="grid">
    <?php foreach ($page->children()->listed() as $category):
      $count = $category->children()->count();
    ?>
    <a href="<?= $category->url() ?>" class="cat-card">
      <span class="icon"><?= $category->icon()->isNotEmpty() ? $category->icon() : '📂' ?></span>
      <h2><?= $category->title() ?></h2>
      <span class="count"><?= $count ?> adresse<?= $count > 1 ? 's' : '' ?></span>
    </a>
    <?php endforeach ?>
  </div>
</main>

<footer>
  <p><?= $site->title() ?> — Tous droits réservés</p>
</footer>

</body>
</html>
