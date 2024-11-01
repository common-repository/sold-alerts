=== Sold Alerts ===
Contributors: (8blocks), edward_plainview
Donate link: http://wordpress.org
Tags: sold home alerts, sold email, home sales, real estate
Requires at least: 4.6
Stable tag: trunk
Tested up to: 4.8
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Sold Alerts provides monthly emails to subscribers with a list of sold homes in their area.

== Description ==

Sold Alerts is a subscription service that delivers sold home alert emails to a subscribers inbox every single month. A user simply subscribes on your website using the Sold Alerts plugin by providing their address, name and email address and that's it. From there they receive an immediate email of homes recently sold in their area as well as an email every 30 days with new homes that have been sold in their area. It's a valuable service for real estate professionals and users alike.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Network administrators can use the Network Settings > 8b Sold Alerts screen to configure the plugin. Single installations have the settings available under the "8b Sold Alerts" menu item.
4. From the main settings screen, check the "Generate or retrieve key" checkbox & then click save settings. This will generate your Sold Alerts license key applicable to your website only.
5. Navigate to the "Lead Email" & "Sold Alerts Email" tabs and enter in your desired email address to receive email notifications upon users subscribing to receive sold home alerts.
6. Finally place the <strong>[8b_sold_alerts]</strong> shortcode on a page where you'd like the subscriber signup form to display and you're ready to start getting leads!

== Frequently Asked Questions ==

= How many subscribers do I get for free? =

You get 10 free subscribers per license key. Everytime we provide sold alert data to your users it costs us money so unfortunately we can only provide 10 at no charge.

= What if I want to allow more subscribers to sign up? =

1. We offer premium upgrades that allow you to purchase additional subscriptions in bulk at rates much cheaper than the industry average starting at just $10/month.
2. From your 8b Sold Alerts settings panel simply look for the <strong>Increase Subscriptions</strong> button and click it to view the current options available.
3. Upon checking out you will just need to refresh your API status to view your additional subscriber allotment just purchased (under the Increase Subscriptions button).
4. Renewal and enterprise discounts are also available at <a href="https://soldalertsplugin.com" target="_blank">SoldAlertsPlugin.com</a>.

= Does this plugin work outside of the United States? =

It currently only works in the United States.

== Screenshots ==

1. Sign Up Form
2. Sold Alert Email
3. Sold Alerts Settings Screen

== Upgrade Notice ==

= 1.7 20171013 =

* New: Add webhooks option. The new lead data is sent to any specified webhook URLs.

= 1.5 20170902 =

* New: Allow hiding of some fields in the form.
* New: Allow changing of placeholder texts in the form.

= 1.4 20170822 =

* Admin UI reworked slightly to match new common code base.
* Fix: Google API Key Issue

= 1.3 20170703 =

* Fix: Only show "unable to send" message when there are more than 0 sales to be sent. E-mails are not sent if there are no sales.

= 1.2 20170620 =

* New: Added optional to temporarily delete leads before they expire. This should only be used for debugging purposes and will not delete the subscription from the server.

= 1.1 20170619 =

* New: Add optional reply-to e-mail address on sent sales e-mail.
* Fix: Dupe subscriber check.
* Fix: Extra check for non-existent addresses.
* Fix: Download export file automatically upon button press, instead of clicking download link afterwards.
* Fix: Check for orhaned postmeta from leads before trying to resend sales for the next month.
* Fix: Check for inactive leads before asking for more sales.

= 1.0 20170408 =

First Version! Enjoy Kiddos

== Changelog ==

* New: Allow shortcodes in the e-mail sender e-mail and name.

= 1.0 20170408 =

* First Version of Sold Alerts Plugin
