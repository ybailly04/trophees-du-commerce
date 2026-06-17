<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $page->title() ?> — <?= $site->title() ?></title>
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
      --green: #2e9e5b;
      --radius: 12px;
    }
    body { font-family: system-ui, -apple-system, sans-serif; background: var(--bg); color: var(--text); }

    header {
      background: var(--dark);
      color: #fff;
      padding: 2.5rem 1.25rem;
      border-bottom: 4px solid var(--gold);
    }
    .header-inner { max-width: 960px; margin: 0 auto; }
    .back { display: inline-block; color: var(--gold); text-decoration: none; font-size: .9rem; margin-bottom: 1rem; }
    .back:hover { text-decoration: underline; }
    header h1 { font-size: 2rem; display: flex; align-items: center; gap: .6rem; }
    header .desc { margin-top: .6rem; color: #b0b8c8; }

    main { max-width: 960px; margin: 2.5rem auto; padding: 0 1.25rem; }

    .results-note { text-align: right; font-size: .85rem; color: var(--muted); margin-bottom: 1rem; }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
      gap: 1.5rem;
    }

    .card {
      background: var(--card-bg);
      border-radius: var(--radius);
      padding: 1.5rem;
      box-shadow: 0 2px 10px rgba(0,0,0,.07);
      border-left: 4px solid var(--gold);
      display: flex;
      flex-direction: column;
      gap: .5rem;
    }
    .card h2 { font-size: 1.15rem; }
    .card .address { color: var(--muted); font-size: .88rem; }
    .card .desc { color: var(--muted); font-size: .9rem; flex: 1; }

    .vote-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: .75rem;
      padding-top: .75rem;
      border-top: 1px solid #eee;
    }
    .vote-count { font-weight: 700; color: var(--gold); font-size: 1.05rem; }
    .vote-count span { font-weight: 400; font-size: .85rem; color: var(--muted); }

    .btn-vote {
      background: var(--gold);
      color: #fff;
      border: none;
      padding: .55rem 1.3rem;
      border-radius: 7px;
      cursor: pointer;
      font-size: .95rem;
      font-weight: 600;
      transition: background .15s, transform .1s;
    }
    .btn-vote:hover:not(:disabled) { background: var(--gold-dark); transform: scale(1.04); }
    .btn-vote:disabled { opacity: .5; cursor: default; transform: none; }

    .btn-voted {
      background: var(--green);
      color: #fff;
      border: none;
      padding: .55rem 1.3rem;
      border-radius: 7px;
      font-size: .95rem;
      font-weight: 600;
      cursor: default;
    }

    .toast {
      position: fixed;
      bottom: 1.5rem;
      left: 50%;
      transform: translateX(-50%) translateY(80px);
      background: #222;
      color: #fff;
      padding: .75rem 1.5rem;
      border-radius: 8px;
      font-size: .95rem;
      opacity: 0;
      transition: opacity .3s, transform .3s;
      pointer-events: none;
      z-index: 100;
    }
    .toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

    footer { text-align: center; padding: 2.5rem 1rem; color: var(--muted); font-size: .85rem; }
  </style>
</head>
<body>

<header>
  <div class="header-inner">
    <a href="<?= $page->parent()->url() ?>" class="back">← Toutes les catégories</a>
    <h1>
      <?php if ($page->icon()->isNotEmpty()): ?>
        <span><?= $page->icon() ?></span>
      <?php endif ?>
      <?= $page->title() ?>
    </h1>
    <?php if ($page->description()->isNotEmpty()): ?>
      <p class="desc"><?= $page->description() ?></p>
    <?php endif ?>
  </div>
</header>

<main>
  <?php $establishments = $page->children()->listed(); ?>
  <p class="results-note"><?= $establishments->count() ?> établissement<?= $establishments->count() > 1 ? 's' : '' ?> en compétition</p>

  <div class="grid">
    <?php foreach ($establishments as $e):
      $cookieName = 'voted_' . str_replace('/', '_', $e->id());
      $hasVoted = isset($_COOKIE[$cookieName]);
      $votes = $e->votes()->toInt();
    ?>
    <div class="card" id="card-<?= $e->slug() ?>">
      <h2><?= $e->title() ?></h2>
      <?php if ($e->address()->isNotEmpty()): ?>
        <p class="address">📍 <?= $e->address() ?></p>
      <?php endif ?>
      <?php if ($e->description()->isNotEmpty()): ?>
        <p class="desc"><?= $e->description() ?></p>
      <?php endif ?>
      <div class="vote-row">
        <div class="vote-count" id="count-<?= $e->slug() ?>">
          <?= $votes ?> <span>vote<?= $votes > 1 ? 's' : '' ?></span>
        </div>
        <?php if ($hasVoted): ?>
          <button class="btn-voted" disabled>✓ Voté</button>
        <?php else: ?>
          <button
            class="btn-vote"
            data-id="<?= $e->id() ?>"
            data-slug="<?= $e->slug() ?>"
          >Voter</button>
        <?php endif ?>
      </div>
    </div>
    <?php endforeach ?>
  </div>
</main>

<div class="toast" id="toast"></div>

<footer>
  <p><?= $site->title() ?></p>
</footer>

<script>
  const toast = document.getElementById('toast');
  function showToast(msg, duration = 2800) {
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), duration);
  }

  document.querySelectorAll('.btn-vote').forEach(btn => {
    btn.addEventListener('click', async function () {
      const id = this.dataset.id;
      const slug = this.dataset.slug;

      this.disabled = true;
      this.textContent = '…';

      try {
        const res = await fetch('/api/vote', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id }),
        });
        const data = await res.json();

        if (data.success) {
          this.className = 'btn-voted';
          this.textContent = '✓ Voté';
          const countEl = document.getElementById('count-' + slug);
          const v = data.votes;
          countEl.innerHTML = v + ' <span>vote' + (v > 1 ? 's' : '') + '</span>';
          showToast('🏆 Merci pour votre vote !');
        } else if (data.error === 'already_voted') {
          this.className = 'btn-voted';
          this.textContent = '✓ Voté';
          showToast('Vous avez déjà voté pour cet établissement.');
        } else {
          this.disabled = false;
          this.textContent = 'Voter';
          showToast('Une erreur est survenue. Réessayez.');
        }
      } catch {
        this.disabled = false;
        this.textContent = 'Voter';
        showToast('Erreur de connexion. Réessayez.');
      }
    });
  });
</script>

</body>
</html>
