import app from 'flarum/app';
import addSubscriptionSettings from './addSubscriptionSettings';

app.initializers.add('subscriptions', function() {
  addSubscriptionSettings();
});
