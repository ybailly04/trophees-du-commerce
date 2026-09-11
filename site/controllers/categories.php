<?php

return function ($page, $site) {
    $candidates = $site->find('candidates')->children()->filter(function ($candidate) use ($page) {
        return $candidate->categories()->toPages()->has($page);
    });

    return compact('candidates');
};
