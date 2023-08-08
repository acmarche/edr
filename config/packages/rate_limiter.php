<?php

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework) {

    $framework->rateLimiter()
        ->limiter('anonymous_api')
        ->policy('fixed_window')
        ->limit(30)
        ->interval('60 minutes');
};
