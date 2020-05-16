<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

use Flarum\Event\ConfigureUserPreferences;
use Flarum\Extend;
use Flarum\Post\Event\Posted;
use Flarum\Subscriptions\Listener;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\View\Factory;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js'),

    function (Dispatcher $events, Factory $views) {
        $events->listen(ConfigureUserPreferences::class, function (ConfigureUserPreferences $event) {
            $event->add('followNewDiscussions', 'boolval', false);
        });
        $events->listen(Posted::class, Listener\FollowNewDiscussions::class);
    }
];
