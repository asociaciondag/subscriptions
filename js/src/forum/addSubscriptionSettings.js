import { extend } from 'flarum/extend';
import SettingsPage from 'flarum/components/SettingsPage';
import Switch from 'flarum/components/Switch';

export default function() {
  extend(SettingsPage.prototype, 'notificationsItems', function(items) {
    items.add('followNewDiscussions',
      Switch.component({
        children: app.translator.trans('flarum-subscriptions.forum.settings.forum_follow_new_discussions_label'),
        state: this.user.preferences().followNewDiscussions,
        onchange: this.preferenceSaver('followNewDiscussions')
      })
    );
  });
}
