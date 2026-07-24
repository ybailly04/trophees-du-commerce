<?php snippet('header') ?>

<main data-barba="container" data-barba-namespace="error">

    <div>
        <h2><?php echo $page->errortitle()->html() ?></h2>
        <div class="big-error-number">404</div>
        <a href="<?php echo $site->url() ?>"><button><?php echo $site->homePage()->title() ?></button></a>

    </div>

<?php snippet('footer') ?>