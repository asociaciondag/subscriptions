<?php

/*
 * This file is part of Flarum.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flarum\Subscriptions\Listener;

use Flarum\Notification\NotificationSyncer;
use Flarum\Post\Event\Posted;
use Flarum\User\User;
use Flarum\Subscriptions\Notification\NewPostBlueprint;
use Flarum\Group\Permission;
use Flarum\Group\Group;

class FollowNewDiscussions
{
    /**
     * @var NotificationSyncer
     */
    protected $notifications;

    /**
     * @param NotificationSyncer $notifications
     */
    public function __construct(NotificationSyncer $notifications)
    {
        $this->notifications = $notifications;
    }

    public function handle(Posted $event)
    {
        $actor = $event->actor;
        $post = $event->post;
        $discussion = $post->discussion;
        $users = [];

        foreach ($discussion->tags as $tag) {
            $permission = "tag{$tag->id}.viewDiscussions";

            $groupPermissions = Permission::where('permission', $permission)->get();

            foreach ($groupPermissions as $groupPermission) {
                self::subscribeGroup($groupPermission->group, $discussion, $users);
            }
        }

        $adminGroup = Group::where('id', 1)->first();

        self::subscribeGroup($adminGroup, $discussion, $users);

        $this->notifications->sync(
            new NewPostBlueprint($post),
            $users
        );
    }

    private static function subscribeGroup(Group $group, $discussion, &$users) {
        foreach ($group->users as $user) {
            if (!$user->getPreference('followNewDiscussions')) {
                continue;
            }

            $state = $discussion->stateFor($user);

            $state->subscription = 'follow';
            $state->save();
            $users[] = $user;
        }
    }
}
