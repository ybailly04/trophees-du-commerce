<?php snippet('header', ['isHome' => true]) ?>
    
<main data-barba="container" data-barba-namespace="home">
    <section class="homepage">
        <span class="homepage-grid"></span>
        <?php if ($video = $page->video()->toFile()): ?>
            <video class="homepage-video" autoplay muted loop playsinline
                poster="<?= $site->image('frame-home.png')->url() ?>">
                <source src="<?= $video->url() ?>" type="video/mp4">
            </video>
        <?php endif; ?>
        <div class="homepage-container">
            <div class="homepage-top">
                <img class="homepage-top-svg" src="<?= $site->image('title_top.svg')->url() ?>" alt="Trophées du commerce">
                <div class="homepage-tagline"><?= $page->tagline() ?></div>
                <button class="homepage-menu" data-menu-button="burger">
                    <div class="homepage-menu-title">MENU</div>
                    <div class="homepage-menu-burger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
            </div>
    
            <div class="homepage-center">
                <h1 class="homepage-center-title"><?= $page->h1() ?></h1>
                <div class="homepage-nav">
                    <?php snippet('button', ['link' => $page->link_vote(), 'class' => 'button-primary']) ?>
                    <?php snippet('button', ['link' => $page->link_form(), 'class' => 'button-secondary']) ?>
                </div>
            </div>
    
            <div class="homepage-bottom">
                <div class="homepage-dates">
                    <div class="homepage-dates-title"><?= $page->labelVote() ?></div>
                    <div class="homepage-dates-text">
                        <span><?= $page->dateDebut() ?></span>
                        <span>— <?= $page->dateFin() ?></span>
                    </div>
                </div>
                <div class="homepage-logos">
                    <img class="homepage-logos-bim" src="<?= $site->image('logo_bim.svg')->url() ?>" alt="Bim Agency">
                    <img class="homepage-logos-vitrines" src="<?= $site->image('logo_vitrines.png')->url() ?>" alt="Les Vitrines d'Annecy">
                </div>
    
                <img class="homepage-bottom-svg" src="<?= $site->image('title_bottom.svg')->url() ?>" alt="Trophées du commerce">
            </div>
        </div>
    </section>

<?php snippet('footer') ?>
