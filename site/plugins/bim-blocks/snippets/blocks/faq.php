<?php $faqItems = $block->faq()->toStructure(); ?>
<?php if ($faqItems->isNotEmpty()): ?>
    <div data-accordion-close-siblings="true" data-accordion-css-init="" class="accordion-css">
        <ul class="accordion-css__list">
            <?php foreach($faqItems as $item): ?>
                <li data-accordion-status="not-active" class="accordion-css__item">
                    <div data-hover="" data-accordion-toggle="" class="accordion-css__item-top">
                        <h3 class="accordion-css__item-h3"><?= $item->question() ?></h3>
                        <div class="accordion-css__item-icon">
                            <svg class="accordion-css__item-icon-svg" xmlns="http://www.w3.org/2000/svg" width="100%" viewbox="0 0 36 36" fill="none"><path d="M28.5 22.5L18 12L7.5 22.5" stroke="currentColor" stroke-width="3" stroke-miterlimit="10"></path></svg>
                        </div>
                    </div>
                    <div class="accordion-css__item-bottom">
                        <div class="accordion-css__item-bottom-wrap">
                            <div class="accordion-css__item-bottom-content">
                                <p class="accordion-css__item-p"><?= $item->answer() ?></p>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>