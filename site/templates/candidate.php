<?php
  $votedIds = json_decode(Cookie::get('votes', '[]'), true);
  $hasVoted = is_array($votedIds) && in_array($page->id(), $votedIds, true);
  $category = $page->categories()->toPages()->first();
?>
<?php snippet('header') ?>

  <main data-barba="container" data-barba-namespace="candidate">
    <div class="candidate">
      <div class="candidate-left">
        <img class="candidate-left-svg" src="<?= $site->image('pattern-streets.svg')->url() ?>" alt="Trophées du commerce">
        <a href="<?= $category->url() ?>" class="candidate-left-back button-secondary">
            <?= asset('/src/assets/icons/arrow-left.svg')->read() ?>
            <span class="button-inner">
                <span class="button-text">Retour aux catégories</span>
            </span>
        </a>
        <div class="candidate-top">
          <?php if ($logo = $page->logo()->toFile()): ?>
            <img class="candidate-logo" src="<?= $logo->url() ?>" alt="<?= $page->title() ?>">
          <?php endif ?>
          <div class="candidate-top-wrapper">
            <?php if ($category): ?>
              <a class="candidate-top-category" href="<?= $category->url() ?>"><?= $category->title() ?></a>
            <?php endif ?>
            <h1 class="candidate-top-title"><?= $page->title() ?></h1>
          </div>
        </div>
        <h2 class="candidate-qr-title">Votez pour nous sur le site !</h2>
        <div class="candidate-votes">
          <button
            type="button"
            class="candidate-vote button"
            data-vote-button
            data-vote-url="/vote/<?= $page->id() ?>"
            data-csrf="<?= csrf() ?>"
            <?= $hasVoted ? 'disabled' : '' ?>
          >
            <div class="button-inner">
              <div class="button-text">
                <?= $hasVoted ? 'Déjà voté' : 'Voter' ?>
              </div>
          </div>
            <?= asset('/src/assets/icons/arrow-right.svg')->read() ?>
          </button>
          <div class="candidate-votes-text"><span data-vote-count><?= $page->count()->toInt() ?></span> votes</div>
          <div class="candidate-qr" data-social-qr>
            <div data-social-qr-canvas class="candidate-qr-canvas"></div>
          </div>
        </div>
        <div class="candidate-tabs">
            <div class="candidate-tabs-list">
                <input type="radio" name="tabs" id="desc" checked>
                <label class="candidate-tabs-tab" for="desc">Description</label>
                <input type="radio" name="tabs" id="contact">
                <label class="candidate-tabs-tab" for="contact">Contact</label>
            </div>
            <div class="candidate-tabs-content">
              <div class="candidate-tabs-desc" id="content-desc"><?= $page->description() ?></div>
              <div class="candidate-tabs-contact" id="content-contact">
                <?= $page->adress() ?>
                <div class="candidate-links">
                  <?php if($page->phone()): ?>
                    <a href="tel:<?= $page->phone() ?>" class="candidate-links-link" target="_blank"><?= $page->phone() ?></a>
                  <?php endif; ?>
                  <?php if($page->website()): ?>
                    <a href="<?= $page->website() ?>" class="candidate-links-link" target="_blank">Website</a>
                  <?php endif; ?>
                  <?php if($page->instagram()): ?>
                    <a href="<?= $page->instagram() ?>" class="candidate-links-link" target="_blank">Instagram</a>
                  <?php endif; ?>
                  <?php if($page->facebook()): ?>
                    <a href="<?= $page->facebook() ?>" class="candidate-links-link" target="_blank">Facebook</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
        </div>
        <div data-social-share-title="Trophées du Commerce - Vote pour <?= $page->title() ?>" data-social-share="" data-social-share-link="<?= $page->url() ?>" class="candidate-socials social-share">
          <button type="button" data-social-share-toggle="" aria-expanded="false" aria-label="Partager" class="social-share__button social-share__button--toggle"><i class="social-share__icon"><svg xmlns="http://www.w3.org/2000/svg" width="100%" viewbox="0 0 24 24" fill="none"><path d="M18 8C19.6569 8 21 6.65685 21 5C21 3.34315 19.6569 2 18 2C16.3431 2 15 3.34315 15 5C15 6.65685 16.3431 8 18 8Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M6 15C7.65685 15 9 13.6569 9 12C9 10.3431 7.65685 9 6 9C4.34315 9 3 10.3431 3 12C3 13.6569 4.34315 15 6 15Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M18 22C19.6569 22 21 20.6569 21 19C21 17.3431 19.6569 16 18 16C16.3431 16 15 17.3431 15 19C15 20.6569 16.3431 22 18 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M8.59 13.51L15.42 17.49" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M15.41 6.51L8.59 10.49" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></i></button>
          <button type="button" data-social-share-type="print" aria-label="Imprimer" class="social-share__button"><i class="social-share__icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 48 48"><desc>Printer Streamline Icon: https://streamlinehq.com</desc><g stroke="#782D3E" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" fill="none"><path d="M12.016 27.757c2.93.14 6.836.244 11.984.244s9.052-.104 11.982-.244c-.044 5.962-.167 10.022-.277 12.579-.092 2.135-1.605 3.892-3.724 4.167a62.5 62.5 0 0 1-7.982.497c-3.278 0-6.007-.24-7.982-.497-2.118-.275-3.63-2.03-3.722-4.164-.111-2.557-.235-6.617-.28-12.582"/><path d="M35.854 35.87c1.828-.045 3.337-.097 4.554-.147 2.37-.098 4.252-1.916 4.387-4.286.11-1.92.205-4.45.205-7.437 0-3.262-.228-5.981-.471-7.955-.263-2.132-2.017-3.665-4.163-3.76C37.298 12.15 32.074 12 24 12c-7.858 0-13.166.142-16.404.275-2.373.097-4.256 1.916-4.391 4.287C3.095 18.482 3 21.014 3 24c0 2.987.096 5.517.205 7.437.135 2.37 2.016 4.188 4.387 4.286 1.217.05 2.725.102 4.552.147M12.491 7.5l.053-.469c.214-1.832 1.573-3.247 3.4-3.505C17.817 3.261 20.543 3 24 3c3.458 0 6.183.261 8.056.526 1.827.258 3.186 1.673 3.4 3.505q.026.226.053.469M36 20h-1M18 34h12M18 39h12"/></g></svg></i></button>          
          <button type="button" data-social-share-type="clipboard" aria-label="Copier le lien" class="social-share__button"><i class="social-share__icon"><svg xmlns="http://www.w3.org/2000/svg" width="100%" viewbox="0 0 24 24" fill="none"><path d="M14.1256 17.262L12.6686 18.719C12.1697 19.2183 11.5773 19.6143 10.9253 19.8846C10.2733 20.1548 9.57437 20.2939 8.86857 20.2939C8.16277 20.2939 7.46389 20.1548 6.81187 19.8846C6.15984 19.6143 5.56746 19.2183 5.06857 18.719C4.56943 18.2201 4.17347 17.6277 3.90333 16.9756C3.63318 16.3236 3.49414 15.6248 3.49414 14.919C3.49414 14.2133 3.63318 13.5144 3.90333 12.8624C4.17347 12.2104 4.56943 11.618 5.06857 11.119L6.52957 9.66602" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M8.89453 14.897L15.1045 8.68701" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M9.87402 6.32198L11.331 4.86498C11.8298 4.36548 12.4221 3.96921 13.0742 3.69884C13.7262 3.42847 14.4251 3.28931 15.131 3.28931C15.8369 3.28931 16.5358 3.42847 17.1879 3.69884C17.8399 3.96921 18.4323 4.36548 18.931 4.86498C19.4303 5.36387 19.8263 5.95625 20.0966 6.60828C20.3668 7.2603 20.5059 7.95918 20.5059 8.66498C20.5059 9.37078 20.3668 10.0697 20.0966 10.7217C19.8263 11.3737 19.4303 11.9661 18.931 12.465L17.47 13.918" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </svg></i><i class="social-share__icon is--success"><svg xmlns="http://www.w3.org/2000/svg" width="100%" viewbox="0 0 24 24" fill="none" class="btn-social__icon-svg is--bigger"> <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5"></path> <path d="M8 12L11 15L16 10" stroke="currentColor" stroke-miterlimit="10" stroke-width="1.5"></path> </svg></i></button>
          <button type="button" data-social-share-type="mail" aria-label="Partager par mail" class="social-share__button"><i class="social-share__icon"><svg xmlns="http://www.w3.org/2000/svg" width="100%" viewbox="0 0 24 24" fill="none"> <path d="M7.5 9.75L10.4255 11.5536C10.8988 11.8455 11.4439 12 12 12C12.5561 12 13.1012 11.8455 13.5745 11.5536L16.5 9.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M18 5H6C4.34315 5 3 6.34315 3 8V16C3 17.6569 4.34315 19 6 19H18C19.6569 19 21 17.6569 21 16V8C21 6.34315 19.6569 5 18 5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </svg></i></button>
          <button type="button" data-social-share-type="facebook" aria-label="Partager sur Facebook" class="social-share__button"><i class="social-share__icon"><svg xmlns="http://www.w3.org/2000/svg" width="100%" viewbox="0 0 24 24" fill="none"> <path d="M22.06 11.987C22.0597 10.0644 21.5085 8.18206 20.4717 6.56294C19.4348 4.94383 17.9558 3.6557 16.2096 2.85105C14.4635 2.04641 12.5233 1.75894 10.6188 2.02269C8.71437 2.28643 6.92535 3.09035 5.46358 4.33926C4.00181 5.58817 2.92849 7.22977 2.37071 9.06972C1.81292 10.9097 1.79401 12.8709 2.31623 14.7213C2.83845 16.5717 3.87992 18.2336 5.31735 19.5105C6.75478 20.7873 8.52797 21.6256 10.427 21.926V14.9H7.872V11.987H10.427V9.771C10.3721 9.25334 10.4318 8.72994 10.6017 8.23788C10.7716 7.74581 11.0476 7.29713 11.4101 6.92358C11.7727 6.55004 12.213 6.26079 12.6998 6.0763C13.1865 5.89181 13.7079 5.8166 14.227 5.856C14.9815 5.8675 15.7342 5.93435 16.479 6.056V8.529H15.211C14.9948 8.50024 14.7749 8.52047 14.5676 8.58821C14.3603 8.65594 14.1709 8.76945 14.0134 8.92031C13.8559 9.07118 13.7344 9.25555 13.6578 9.45975C13.5812 9.66396 13.5515 9.88278 13.571 10.1V11.987H16.362L15.915 14.9H13.571V21.93C15.9381 21.5549 18.0937 20.3474 19.6499 18.5247C21.2061 16.7019 22.0607 14.3837 22.06 11.987Z" fill="currentColor"></path> </svg></i></button>
          <button type="button" data-social-share-type="whatsapp" aria-label="Partager sur WhatsApp" class="social-share__button"><i class="social-share__icon"><svg xmlns="http://www.w3.org/2000/svg" width="100%" viewbox="0 0 24 24" fill="none"> <path fill-rule="evenodd" clip-rule="evenodd" d="M19.049 4.90701C17.3493 3.20721 15.0898 2.18312 12.6912 2.02547C10.2926 1.86782 7.91847 2.58735 6.01096 4.05007C4.10345 5.51278 2.7926 7.61896 2.32257 9.97633C1.85254 12.3337 2.25535 14.7816 3.456 16.864L2.049 22L7.3 20.621C8.75245 21.4124 10.3799 21.8277 12.034 21.829C13.9949 21.8301 15.912 21.2495 17.5429 20.1607C19.1737 19.072 20.445 17.5239 21.1958 15.7125C21.9467 13.9011 22.1434 11.9077 21.7611 9.98441C21.3788 8.06116 20.4346 6.29453 19.048 4.90801L19.049 4.90701ZM12.041 20.157C10.5648 20.1573 9.11572 19.76 7.846 19.007L7.546 18.827L4.428 19.644L5.261 16.605L5.066 16.293C4.38658 15.2118 3.96778 13.9875 3.84265 12.7167C3.71752 11.4459 3.88948 10.1634 4.34497 8.97049C4.80045 7.77755 5.52699 6.70681 6.46722 5.84279C7.40746 4.97877 8.53566 4.34513 9.76277 3.99188C10.9899 3.63863 12.2823 3.57544 13.538 3.8073C14.7937 4.03916 15.9783 4.55973 16.9984 5.3279C18.0184 6.09608 18.8459 7.09084 19.4156 8.23366C19.9853 9.37647 20.2816 10.6361 20.281 11.913C20.2778 14.0977 19.4088 16.192 17.8642 17.7371C16.3197 19.2822 14.2257 20.153 12.041 20.157ZM16.557 13.985C16.311 13.86 15.092 13.263 14.865 13.185C14.638 13.107 14.474 13.06 14.306 13.31C14.138 13.56 13.666 14.11 13.521 14.283C13.376 14.456 13.233 14.47 12.986 14.345C12.2559 14.0533 11.5819 13.6371 10.994 13.115C10.4534 12.6137 9.98909 12.0359 9.616 11.4C9.471 11.154 9.6 11.018 9.726 10.9C9.852 10.782 9.972 10.611 10.097 10.466C10.1992 10.3409 10.2824 10.2014 10.344 10.052C10.3769 9.98367 10.3922 9.90823 10.3887 9.83248C10.3852 9.75674 10.363 9.68304 10.324 9.61801C10.261 9.49301 9.765 8.27401 9.562 7.77801C9.359 7.28201 9.156 7.35201 9 7.34401C8.844 7.33601 8.69 7.33601 8.527 7.33601C8.40171 7.33967 8.2785 7.36898 8.16498 7.42213C8.05147 7.47527 7.95005 7.55113 7.867 7.64501C7.58693 7.9105 7.36521 8.23139 7.21594 8.58725C7.06667 8.94312 6.99313 9.32616 7 9.71201C7.08057 10.6462 7.43193 11.5365 8.011 12.274C9.07331 13.8657 10.5309 15.1541 12.241 16.013C12.831 16.267 13.292 16.419 13.651 16.537C14.156 16.6901 14.6896 16.7244 15.21 16.637C15.5548 16.567 15.8813 16.4264 16.1691 16.224C16.4569 16.0216 16.6996 15.7618 16.882 15.461C17.0444 15.0917 17.0948 14.6827 17.027 14.285C16.969 14.174 16.805 14.114 16.555 13.985H16.557Z" fill="currentColor"></path> </svg></i></button>
          <button type="button" data-social-share-type="linkedin" aria-label="Partager sur LinkedIn" class="social-share__button"><i class="social-share__icon"><svg xmlns="http://www.w3.org/2000/svg" width="100%" viewbox="0 0 24 24" fill="none"> <path d="M19.039 19.043H16.078V14.4C16.078 13.294 16.055 11.87 14.534 11.87C12.99 11.87 12.754 13.07 12.754 14.319V19.041H9.792V9.50001H12.637V10.8H12.676C12.9609 10.3131 13.3725 9.91263 13.867 9.6411C14.3614 9.36957 14.9203 9.23717 15.484 9.25801C18.484 9.25801 19.04 11.233 19.04 13.804V19.042L19.039 19.043ZM6.447 8.19401C6.1069 8.19381 5.7745 8.09279 5.4918 7.90372C5.2091 7.71465 4.98879 7.44601 4.85874 7.13176C4.72868 6.81751 4.6947 6.47176 4.7611 6.13821C4.8275 5.80465 4.99129 5.49827 5.23178 5.25778C5.47226 5.0173 5.77865 4.8535 6.1122 4.7871C6.44575 4.72071 6.79151 4.75468 7.10575 4.88474C7.42 5.0148 7.68864 5.2351 7.87771 5.5178C8.06678 5.8005 8.1678 6.13291 8.168 6.47301C8.16826 6.69909 8.12393 6.923 8.03753 7.13192C7.95114 7.34084 7.82438 7.53066 7.66452 7.69052C7.50466 7.85039 7.31483 7.97714 7.10591 8.06354C6.89699 8.14993 6.67308 8.19427 6.447 8.19401ZM7.932 19.043H4.963V9.50001H7.932V19.043ZM20.521 2.00001H3.476C3.28445 1.99763 3.0943 2.03302 2.91643 2.10417C2.73856 2.17531 2.57646 2.28081 2.43938 2.41464C2.30231 2.54846 2.19295 2.70799 2.11756 2.8841C2.04217 3.06021 2.00222 3.24945 2 3.44101V20.559C2.00222 20.7506 2.04217 20.9398 2.11756 21.1159C2.19295 21.292 2.30231 21.4515 2.43938 21.5854C2.57646 21.7192 2.73856 21.8247 2.91643 21.8958C3.0943 21.967 3.28445 22.0024 3.476 22H20.518C20.9055 22.0051 21.2792 21.8562 21.5571 21.586C21.8349 21.3159 21.9942 20.9465 22 20.559V3.44101C21.9939 3.05359 21.8346 2.68438 21.5568 2.41427C21.279 2.14416 20.9054 1.99519 20.518 2.00001H20.521Z" fill="currentColor"></path> </svg></i></button>
        </div>
      </div>

      <div class="candidate-right">
        <div class="candidate-right-thumbs">
          <?php
            $images =  $page->galery()->toFiles();
            foreach($images as $image): 
                $thumb_options = [
                  'image' => $image,
                  'srcset' => 'thumb',
                  'ratio' => '5/7',
                  'loading' => 'eager',
                  'attributes' => [
                    'picture' => [
                      'class' => ['candidate-right-thumb'],
                      'data-index' =>  $image->id(),
                      'data-gallery-thumb'
                    ]
                  ]
                ];
            ?>
              <?php snippet('imagex-picture', $thumb_options) ?>
          <?php endforeach ?> 
        </div>
        <a href="<?= page("/categories")->url() ?>" class="candidate-right-back button-secondary">
            <?= asset('/src/assets/icons/arrow-left.svg')->read() ?>
            <span class="button-inner">
                <span class="button-text">Retour aux catégories</span>
            </span>
        </a>
        <div class="candidate-right-images">
          <?php $options = [
              'image' => $page->image() ? $page->image() : $site->image('placeholder.png'),
              'srcset' => 'half',
              'ratio' => '24/31',
              'loading' => 'eager',
              'attributes' => [
                'picture' => [
                  'class' => ['candidate-right-image'],
                ]
              ]
            ]; ?>

          <?php snippet('imagex-picture', $options) ?>
        <?php
            $images =  $page->galery()->toFiles();
            foreach($images as $image): 
                $options = [
                  'image' => $image,
                  'srcset' => 'half',
                  'ratio' => '24/31',
                  'loading' => 'eager',
                  'attributes' => [
                    'picture' => [
                      'class' => ['candidate-right-image'],
                      'data-gallery-index' => $image->id(),
                    ]
                  ]
                ];
            ?>
              <?php snippet('imagex-picture', $options) ?>
          <?php endforeach ?> 
        </div>
        <?php if (!empty($images)): ?>
          <div class="candidate-right-slider swiper" data-swiper>
              <div class="swiper-wrapper">
                <!-- Slides -->
                <div class="swiper-slide">
                  <?php $options = [
                      'image' => $page->image() ? $page->image() : $site->image('placeholder.png'),
                      'srcset' => 'default',
                      'ratio' => '5/3',
                      'loading' => 'eager',
                      'attributes' => [
                        'picture' => [
                          'class' => ['candidate-right-image'],
                        ]
                      ]
                    ]; ?>
                  <?php snippet('imagex-picture', $options) ?>
                </div>
                <?php foreach($images as $image): 
                      $options = [
                      'image' => $image,
                      'srcset' => 'default',
                      'ratio' => '5/3',
                      'loading' => 'eager',
                      'attributes' => [
                        'picture' => [
                          'class' => ['candidate-right-image'],
                        ]
                      ]
                    ];
                ?>
                  <div class="swiper-slide">
                    <?php snippet('imagex-picture', $options) ?>
                  </div>
                <?php endforeach; ?>
              </div>
              <!-- If we need pagination -->
              <div class="candidate-right-pagination swiper-pagination" data-swiper-pagination></div>
          </div>
        <?php endif; ?>
      </div>
    </div>

  <?php snippet('footer') ?>
