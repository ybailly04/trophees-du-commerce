<?php
if (!$kirby->user()) {
  kirby()->session()->set('loginRedirect', kirby()->request()->url()->toString());
  go('/login');
}
$errors  = kirby()->session()->get('form_errors', []);
$data    = kirby()->session()->get('form_data', []);
$success = get('success') === '1';

kirby()->session()->remove('form_errors');
kirby()->session()->remove('form_data');

$categories = site()->find('categories')?->children() ?? [];
?>

<?php if ($success): ?>
  <p class="form-success">Votre candidature a bien été envoyée !</p>
<?php else: ?>

  <?php if (!empty($errors)): ?>
    <ul class="form-errors">
      <?php foreach ($errors as $error): ?>
        <li><?= html($error) ?></li>
      <?php endforeach ?>
    </ul>
  <?php endif ?>

  <form method="post" action="/candidature/submit" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="_csrf" value="<?= csrf() ?>">

    <!-- Nom / Titre -->
    <div class="field">
      <label for="title">Nom de l'établissement <span aria-hidden="true">*</span></label>
      <input
        type="text"
        id="title"
        name="title"
        value="<?= html($data['title'] ?? '') ?>"
        required
      >
    </div>

    <!-- Adresse -->
    <div class="field">
      <label for="adress">Adresse</label>
      <textarea id="adress" name="adress" rows="3"><?= html($data['adress'] ?? '') ?></textarea>
    </div>

    <!-- Email -->
    <div class="field">
      <label for="email">Email <span aria-hidden="true">*</span></label>
      <input
        type="email"
        id="email"
        name="email"
        value="<?= html($data['email'] ?? '') ?>"
        required
      >
    </div>

    <!-- Téléphone -->
    <div class="field">
      <label for="phone">Téléphone</label>
      <input
        type="tel"
        id="phone"
        name="phone"
        value="<?= html($data['phone'] ?? '') ?>"
        placeholder="06 XX XX XX XX"
        pattern="^(?:0|\+33|0033)[1-9](?:[ .-]?\d{2}){4}$"
      >
    </div>

    <!-- Description -->
    <div class="field">
      <label for="description">Description <small>(600 caractères max.)</small></label>
      <textarea
        id="description"
        name="description"
        rows="5"
        maxlength="600"
      ><?= html($data['description'] ?? '') ?></textarea>
    </div>

    <!-- Image -->
    <div class="field">
      <label for="image">Image d'illustration</label>
      <input type="file" id="image" name="image" accept="image/*">
    </div>

    <!-- Catégories -->
    <?php if ($categories->count() > 0): ?>
    <fieldset class="field">
      <legend>Catégories <small>(5 max.)</small></legend>
      <?php foreach ($categories as $cat): ?>
        <label class="checkbox-label">
          <input
            type="checkbox"
            name="categories[]"
            value="<?= $cat->uri() ?>"
            <?= in_array($cat->uri(), $data['categories'] ?? []) ? 'checked' : '' ?>
          >
          <?= html($cat->title()) ?>
        </label>
      <?php endforeach ?>
    </fieldset>
    <?php endif ?>

    <!-- Site web -->
    <div class="field">
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
    <div class="field">
      <label for="instagram">Instagram</label>
      <input
        type="url"
        id="instagram"
        name="instagram"
        value="<?= html($data['instagram'] ?? '') ?>"
        placeholder="https://instagram.com/..."
      >
    </div>

    <button type="submit">Envoyer ma candidature</button>
  </form>

<?php endif ?>
