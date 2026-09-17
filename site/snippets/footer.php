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

<div id="turnstile-widget" style="display:none;"></div>
<script>
  window.TURNSTILE_SITE_KEY = "<?= esc(option('turnstile.siteKey')) ?>";
  window.onloadTurnstileCallback = function () {
    window.__turnstileWidgetId = turnstile.render('#turnstile-widget', {
      sitekey: window.TURNSTILE_SITE_KEY,
      appearance: 'execute',
      execution: 'execute',
      callback: function (token) {
        document.dispatchEvent(new CustomEvent('turnstile:token', { detail: token }));
      },
      'error-callback': function () {
        document.dispatchEvent(new CustomEvent('turnstile:error'));
      },
    });
  };
</script>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onloadTurnstileCallback" async defer></script>
</body>
<?= vite()->js("assets/js/app.js") ?>
<?php echo snippet('matomo') ?>
</html>