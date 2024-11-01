=== Usercloud24 ===
Contributors: Christian Leu
Donate link: http://leumund.ch
Tags: users, statz, cloud, display, widget, information
Requires at least: 2.7
Tested up to: 2.7
Stable tag: 2.0

Usercloud24 display the known visitors in a cloud. Known users are identified by Wordpress Cookies after Comment or registered Users
== Description ==

Usercloud24 is a plugin to give credit to active Visitors. Once a visitor has a user account or wrote a approved comment his name and the url is shown in the Usercloud24.

Why you should install uercloud24?
* to give credit to active users with a backlink on your blog
* to see who has visited  your blog 
* to show to others who is visiting your Blog
* just because 
* to have another stats plugin


If you have better translation for this poor text please send to me(at)relab(dot)ch




== Installation ==

Installation

1. Upload `usercloud24.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add the Widget in Widget Menu or place `<? get_usercloud24(); ?>` in your templates

After Installation there is a message until one known visitor is in the database!

Installation DE

1. Datei `usercloud24.php` in das Verzeichnis `/wp-content/plugins/` hochladen
2. Das Plugin im 'Plugins' Menu von Wordpress aktivieren
3. Das Widget unter Darstellung/Widget aktivieren und Titel angeben oder `<? get_usercloud24(); ?>` in einem Template hinzufügen. 


== Frequently Asked Questions ==

= will usercloud24 stores any data =

Only the Name and the URL of a Visitor is stored for 24 hours

= Is there a other Plugin required to run Usercloud24? =

No, Usercloud24 is running without any other plugin

== Screenshots ==

1. Usercloud24 in Action
2. Usercloud24 in Action
3. Usercloud24 in Action
4. Usercloud24 in Action
5. Usercloud24 in Action
6. Usercloud24 in Action


== Planned  ==

* More possibilities to format the usercloud
* Flat output

== Version History ==

2.0 New Version with Lot of Improvements for customisation
	Widget Control Panel for Settings: Title, Nb. of Days, Style= Cloud or List, Font-size, Min, Max, Unit

1.0.3 Included Spam Check. Only visitors with at least 1 approved comments are registered!
		changed rounding to only full values, changed output so only if 
		
		Thanks to Michel for the information about spam:
		http://www.rouge.ch/blog/die-usercloud-und-die-spammer/

1.0.2 Integrated the delete function. So, all entries in Database older than 24 hours are deleted. 

1.0.1 Changed Database field for Ugugus Blog because of the enormous length of this domain name

1.0 Initial release of Usercloud24 Standalone. Own Database Table!





before 1.0 the usercloud24 had no own database and was only working together with Semmelstatz Plugin. 

