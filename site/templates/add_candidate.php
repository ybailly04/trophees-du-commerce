<?php
$errors  = kirby()->session()->get('form_errors', []);
$data    = kirby()->session()->get('form_data', []);
$success = get('success') === '1';

kirby()->session()->remove('form_errors');
kirby()->session()->remove('form_data');

$categories = site()->find('categories')?->children() ?? [];
?>
<?php snippet('header') ?>

<main data-barba="container" data-barba-namespace="form">
  <div class="form">
    <h1 class="form-title">Inscrire mon adresse</h1>
    <h2 class="form-subtitle">Vous possédez un établissement et souhaitez participer? </h2>

    <?php if ($success): ?>
      <p class="form-success">Votre candidature a bien été envoyée ! Vous pouvez inscrire un autre établissement ci-dessous.</p>
    <?php endif ?>

      <?php if (!empty($errors)): ?>
        <ul class="form-errors">
          <?php foreach ($errors as $error): ?>
            <li><?= html($error) ?></li>
          <?php endforeach ?>
        </ul>
      <?php endif ?>
  
      <form class="form-form" method="post" action="/candidature/submit" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="_csrf" value="<?= csrf() ?>">
        <!-- Email -->
        <div class="form-field">
          <label for="email">Email <span aria-hidden="true">*</span></label>
          <input
            type="email"
            id="email"
            name="email"
            value="<?= html($data['email'] ?? '') ?>"
            required
          >
        </div>
        <!-- Nom / Titre -->
        <div class="form-field">
          <label for="title">Nom de l'entreprise <span aria-hidden="true" class="form-asterisk">*</span></label>
          <input
            type="text"
            id="title"
            name="title"
            value="<?= html($data['title'] ?? '') ?>"
            required
          >
        </div>
  
        <!-- Adresse -->
        <div class="form-field">
          <label for="adress">Adresse</label>
          <input type="text" id="adress" name="adress">
        </div>
  
        <!-- Téléphone -->
        <div class="form-field">
          <label for="phone">Téléphone</label>
          <input
            type="tel"
            id="phone"
            name="phone"
            value="<?= html($data['phone'] ?? '') ?>"
            pattern="^(?:0|\+33|0033)[1-9](?:[ .-]?\d{2}){4}$"
          >
        </div>

        <!-- Site web -->
        <div class="form-field full">
          <label for="website">Site web</label>
          <input
            type="url"
            id="website"
            name="website"
            value="<?= html($data['website'] ?? '') ?>"
            placeholder="https://"
          >
        </div>
  
        <!-- Instagram -->
        <div class="form-field">
          <label for="instagram">Instagram</label>
          <input
            type="url"
            id="instagram"
            name="instagram"
            value="<?= html($data['instagram'] ?? '') ?>"
            placeholder="https://instagram.com/..."
          >
        </div>

        <!-- Facebook -->
        <div class="form-field">
          <label for="facebook">Facebook</label>
          <input
            type="url"
            id="facebook"
            name="facebook"
            value="<?= html($data['facebook'] ?? '') ?>"
            placeholder="https://facebook.com/..."
          >
        </div>
  
        <!-- Description -->
        <div class="form-field full">
          <label for="description">Description <small>(600 caractères max.)</small></label>
          <textarea
            id="description"
            name="description"
            rows="5"
            maxlength="600"
          ><?= html($data['description'] ?? '') ?></textarea>
        </div>

        <!-- Catégories -->
        <?php if ($categories->count() > 0): ?>
        <div class="form-field full">
          <label for="categories">Catégorie <span aria-hidden="true" class="form-asterisk">*</span></label>
          <select id="categories" name="categories" required>
            <option value="">-- Sélectionner --</option>
            <?php foreach ($categories as $cat): ?>
              <option
                value="<?= $cat->uri() ?>"
                <?= in_array($cat->uri(), $data['categories'] ?? []) ? 'selected' : '' ?>
              >
                <?= html($cat->title()) ?>
              </option>
            <?php endforeach ?>
          </select>
        </div>
        <?php endif ?>
  
        <!-- Logo -->
        <div class="form-field full">
          <label for="logo">Logo <span aria-hidden="true" class="form-asterisk">*</span></label>
          <div class="form-field-info">✓ PNG, SVG • 5 Mo maximum par fichier • Logo noir ou couleur</div>
          <input type="file" class="bfi" id="logo" name="logo" accept="image/*" required>
        </div>

        <!-- Image -->
        <div class="form-field full">
          <label for="image">Image principale <span aria-hidden="true" class="form-asterisk">*</span></label>
          <div class="form-field-info">✓ PNG, JPG • 5 Mo maximum par fichier • Format portrait préféré</div>
          <input type="file" class="bfi" id="image" name="image" accept="image/*" required>
        </div>

        <!-- Galerie -->
        <div class="form-field full">
          <label for="gallery">Galerie d'images</label>
          <div class="form-field-info">✓ PNG, JPG • Max 5 photos • Format Portrait conseillé</div>
          <input type="file" class="bfi" id="gallery" name="gallery[]" accept="image/*" multiple>
        </div>
  
        <button class="form-submit button-primary" type="submit">
          <div class="button-inner">
            <div class="button-text">Inscrire mon adresse</div>
          </div>
        </button>
      </form>
  </div>

<?php snippet('footer') ?>