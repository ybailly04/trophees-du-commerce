<footer>
    
</footer>

</main>

<div data-transition-wrap class="transition">
  <div class="transition__panels">
    <div data-transition-column class="transition__panel"></div>
    <div data-transition-column class="transition__panel"></div>
  </div>
</div>

<?php snippet('popup') ?>
<?php snippet('nav') ?>

</body>
<?= vite()->js("assets/js/app.js") ?>
<?php echo snippet('matomo') ?>
</html>