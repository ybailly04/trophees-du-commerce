<?php 
    $mainMenu = $site->menus()->toStructure()->filterBy('menuHeadline', 'Menu principal')->first();
    $subMenu = $site->menus()->toStructure()->filterBy('menuHeadline', 'Menu secondaire')->first(); 
?>

<div class="popup">
    <div class="popup-overlay"></div>
	<div class="popup-content">
		<button class="popup-close" data-menu-button="close" data-lenis-resume>
			<span>Fermer</span>
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
				<path d="M15 5L5 15" stroke-miterlimit="10"/>
				<path d="M5 5L15 15" stroke-miterlimit="10"/>
			</svg>
		</button>
		<div class="popup-menu">
			<?php if ($mainMenu): ?>
			<ul>
				<?php foreach ($mainMenu->menu()->toStructure() as $item): ?>
				<li>
					<a href="<?= $item->link()->toUrl() ?>"<?= $item->target()->toBool() ? ' target="_blank"' : '' ?>>
						<?= $item->linkText()->or($item->link()->toPage()?->title()) ?>
					</a>
				</li>
				<?php endforeach ?>
			</ul>
			<?php endif ?>
		</div>
        <div class="popup-submenu">
            <?php if ($subMenu): ?>
			<ul>
				<?php foreach ($subMenu->menu()->toStructure() as $item): ?>
				<li>
					<a href="<?= $item->link()->toUrl() ?>"<?= $item->target()->toBool() ? ' target="_blank"' : '' ?>>
						<?= $item->linkText()->or($item->link()->toPage()?->title()) ?>
					</a>
				</li>
				<?php endforeach ?>
			</ul>
			<?php endif ?>
			<div class="popup-submenu-text">
				<?= $site->menuText() ?>
			</div>
        </div>
		<div class="popup-bottom">
			<a class="popup-vote" href="/categories">
				<div class="popup-vote-text">Votez maintenant</div>
				<svg class="popup-vote-arrow" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
					<path d="M14 19L21 12L14 5" stroke="#F24F43" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"/>
					<path d="M21 12H2" stroke="#F24F43" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"/>
				</svg>
				<img class="popup-vote-svg" src="<?= $site->image('pattern-streets-cta.svg')->url() ?>" role="presentation" alt="Trophées du commerce">
			</a>
			<a class="popup-bim" href="https://bimagency.fr" target="_blank">
				<div class="popup-bim-text">
					<span>Site créé par</span>
					<span>BIM AGENCY</span>
				</div>
				<svg class="popup-vote-arrow" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
					<path d="M14 19L21 12L14 5" stroke="#F9D3C8" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"/>
					<path d="M21 12H2" stroke="#F9D3C8" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"/>
				</svg>
				<img class="popup-bim-image" src="<?= $site->image('bim.png')->url() ?>" role="presentation" alt="BIM Agency">
			</a>
		</div>
	</div>
</div>