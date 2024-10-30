=== Hellodialog ===
Contributors: HoltesDesign
Donate link: https://webreact.nl
Tags: hellodialog, emailmarketing, api-connect, newsletter, automation
Requires at least: 4.8
Tested up to: 6.6
Stable tag: 1.7.15
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Wordpress plugin to include opt-in forms for Hellodialog's email marketing application.

== Description ==

This plugin was developed by Webreact for Hellodialog. The plugin has the following features:

* Connect WordPress to the Hellodialog API
* Show available fields and datatypes
* Create forms on your WordPress website
* Select Hellodialog fields to include in your form
* Show the created forms with a shortcode
* Send submissions to Hellodialog via the API


== Installation ==

1. Upload the plugin to the `/wp-content/plugins` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Hellodialog -> Settings screen to configure the API key
4. Use the Hellodialog -> Forms screen to generate a signup form based on the available fields and save the shortcode
5. Use the shortcode in your WordPress website


== Frequently Asked Questions ==

= Where can I find my API key =

In your Hellodialog dashboard, http://app.hellodialog.com

= Where can I find the API error logs? =

They are located in /wp-content/plugins/hellodialog/error.log

= How many API calls are made? =

The available fields are cached for 2 hours, after that the cache will be rebuild. The cache will also be rebuild on updating or saving a form in the admin menu.


== Changelog ==
= 1.7.14 =
* WordPress 6.6 compatibility.
* Format WordPress Plugin metadata based on WordPress guidelines.

= 1.7.13 =
* Fixed type error on array_map() argument two with default php casting.

= 1.7.12 =
* Fixed type error on array_map() argument two.

= 1.7.11 =
* WordPress 6.4 compatibility.

= 1.7.10 =
* Added a fix where usage of the shortcode would throw warnings.

= 1.7.9 =
* WordPress 6.1 compatibility.

= 1.7.8 =
* Correct usage of add_submenu_page.
* WordPress 6.0 compatibility.

= 1.7.6 =
* String translations.
* Updated JS libs.

= 1.7.5 =
* WordPress 5.3 fixes.

= 1.7.4 =
* Added class field to submit button.

= 1.7.3 =
* WordPress 5.1 compatibility.

= 1.7.2 =
* WordPress 5.0 compatibility.

= 1.7.1 =
* WooCommerce checkout subscriptions can now also be pushed with single opt in.

= 1.7.0 =
* Added an option where the user is allowed to pick add a hidden '_language' field that is pushed in to the API'.

= 1.6.0 =
* Added an option where the user is allowed to pick the 'state' a contact is pushed in to the API'.

= 1.5.4 =
* Fixed a bug where private groups would still count as groups.

= 1.5.3 =
* Bootstrap 4 requires Popper JS.

= 1.5.2 =
* WordPress 4.9 compatibility fixes.

= 1.5.1 =
* Fixed a settings field.

= 1.5.0 =
* Implemented dynamic placeholders for all forms. In the near future you'll be able to set those for individual forms.

= 1.4.0 =
* Updated some libs.

= 1.3.1 =
* Fixed double contact message.

= 1.3.0 =
* Form title & subtitle now available.

= 1.2.1 =
* Private lists are no longer visible in the plugin.

= 1.2.0 =
* Added support for newsletters while adding contacts to the API.

= 1.1.1 =
* Added success message to settings.

= 1.1.0 =
* Changed the way the shortcode is now returned. Shortcode can now be used in widgets.

= 1.0.14 =
* CSS fixed.

= 1.0.13 =
* CSS fixed. Default yes on newsletter signup.

= 1.0.12 =
* Multiselect fixed. Default yes on newsletter signup.

= 1.0.11 =
* Fixed validation. Direct upgrade recommended.

= 1.0.10 =
* Fixed validation. Direct upgrade recommended.

= 1.0.9 =
* Fixed validation. Direct upgrade recommended.

= 1.0.8 =
* Fixed validation. Direct upgrade recommended.

= 1.0.7 =
* Hellodialogs required fields are now always set required in front-end forms.

= 1.0.6 =
* Hellodialogs required fields are now always set in new forms.

= 1.0.5 =
* Added some search tags

= 1.0.4 =
* Fixed author and version number

= 1.0.3 =
* Security update, added HTML escape to translation strings

= 1.0.2 =
* Added WooCommerce label support

= 1.0.1 =
* JSON cached object does not comply to the JSON standard and was renamed.

= 1.0 =
* Added WooCommerce integration.
* Added admin form generator.
* Fixed caching.

== Upgrade Notice ==

= 1.0.14 =
* CSS fixed.

= 1.0.13 =
* CSS fixed. Default yes on newsletter signup.

= 1.0.12 =
* Multiselect fixed. Default yes on newsletter signup.

= 1.0.11 =
* Fixed validation. Direct upgrade recommended.

= 1.0.10 =
* Fixed validation. Direct upgrade recommended.

= 1.0.9 =
* Fixed validation. Direct upgrade recommended.

= 1.0.8 =
* Fixed validation. Direct upgrade recommended.

= 1.0.7 =
* Fixed required fields. Direct upgrade recommended.

= 1.0.6 =
* Fixed required fields. Direct upgrade recommended.

= 1.0.5 =
* Added some search tags. No direct upgrade needed.

= 1.0.4 =
* Author changed. No direct upgrade needed.

= 1.0.3 =
* Security improved. Direct upgrade recommended.

= 1.0.1 =
* Naming convention of cached objects has changed. Upgrade recommended.

= 1.0 =
* No upgrade notices.
