<?php $linkObject = $link->toObject(); ?>

<a href="<?= $linkObject->link()->toUrl() ?>" class="<?= $class ?>" target="<?= $linkObject->target()->toBool() === true ? 'target="_blank"' : '' ?>">
    <span class="button-inner">
        <span class="button-text"><?= $linkObject->linkText()->or($linkObject->link()) ?></span>
    </span>
</a>