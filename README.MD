JP bbPress Slack Integration
============================
Get notifications in your Slack when there is a new topic or reply in your bbPress.

### Setup A New Slack WebHook
* Go To `https://<your-team-name>.slack.com/services/new/incoming-webhook`
* Create a new webhook
* Set a channel to receive the notifications
* Copy the URL for the webhook

### Setting Up The Plugin
I need to add a settings page for this. For now, just save the option "jp_bbpress_slack_webhook" with your webhook's URL manually, with `update_option( 'jp_bbpress_slack_webhook', $url );` where `$url` equals your webhook URL.

### License, Copyright etc.
Copyright 2014 [Josh Pollock](http://JoshPress.net)

Licensed under the terms of the [GNU General Public License version 2](http://www.gnu.org/licenses/gpl-2.0.html) or later. Please share with your neighbor.
