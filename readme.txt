=== Expire Users ===
Contributors: husobj
Donate link: http://www.benhuson.co.uk/donate/
Tags: users, password, expire, login, roles
Requires at least: 3.4
Tested up to: 3.8.1
Stable tag: 0.7
License: GPLv2 or later

Set expiry dates for user logins.

== Description ==

> Important security update - if you are using version 0.2 or earlier please upgrade

**Currently a beta version.**

This plugin allows you to set expiry dates for user logins. You can set a user to:

* Never expire (default)
* Expire in X days, weeks, moths or years
* Expire on a specific date

When a user expires you can:

* Change the role of that user
* Replace the user's password with a randomly generated one
* Send an email notification to the user
* Send an email notification to the site administrator
* Perform you own actions using an [`expire_users_expired`](https://github.com/benhuson/expire-users/wiki/expire_users_expired) hook

You can automatically assign expiry details to users who sign up via the register form.

The email notification messages can be configured in the admin settings.

Please post in the [support forum](http://wordpress.org/support/plugin/expire-users) if you have any questions, or refer to the [documentation](https://github.com/benhuson/expire-users/wiki), [report bugs](https://github.com/benhuson/expire-users/issues) and [submit translations](https://github.com/benhuson/expire-users/issues) at the plugin's [GitHub page](https://github.com/benhuson/expire-users/).

== Installation ==

To install and configure this plugin...

1. Upload or install the plugin through your WordPress admin.
2. Activate the plugin via the 'Plugins' admin menu.
3. Edit a user to set password expiry options.

= Upgrading =

If you are upgrading manually via FTP rather that through the WordPress automatic upgrade link, please de-activate and re-activate the plugin to ensure the plugin upgrades correctly.

== Frequently Asked Questions ==

None at present.

== Screenshots ==

1. Expire Date column in users admin.
2. Edit a user's expiry date and expiry actions.
3. Edit email notification messages.
3. User login expired error.

== Changelog ==

= 0.7 =
* Fix expiry dates to work with site's timezone.
* Use date formats from WordPress settings.
* Use user's real name in notification emails if available.
* Ensure custom roles are listed in drop down menu.
* Add option to remove user expiry details and continue to allow user to login when they expire.
* Add admin help.

= 0.6 =
* Added support for translations. Submissions welcome.

= 0.5 =
* Added option to automatically set expiry details for users who register via the register form.

= 0.4 =
* Enables email notifications to users and administrators.
* Add a settings page where you can configure email notification messages.
* Perform you own actions on user expiry using an `expire_users_expired` hook.
* Only allow users with user editing capabilities (administrators) to edit expiration dates.

= 0.3 =

**Important Security Update!**

* Fix authenticate() and login issue.

= 0.2 =
* Prepare for translation.
* Fix 'Expire Date In…' settings.

= 0.1 =
* First Release. If you spot any bugs or issues please [log them here](https://github.com/benhuson/expire-users/issues).

== Upgrade Notice ==

= 0.7 =
* Fix expiry dates to work with site's timezone.

= 0.6 =
* Added support for translations.

= 0.5 =
* Assign expiry details to users when they register via the register form.

= 0.4 =
Option to enable email notifications and perform custom actions on user expiry.

= 0.3 =
Important security update!

= 0.2 =
Ready for translation. Fixed "Expire Date In..." settings.

= 0.1 =
First release.