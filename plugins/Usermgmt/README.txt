Last Modification of this file is 10-Oct-2020

Usermgmt is a User Management Plugin (Premium) for cakephp 4.x
Plugin Premium version 4.0.4 (stable)
with Twitter Bootstrap 4.x

Website- https://ektanjali.com
Plugin Demo and It's features- https://cakephp4-user-management.ektanjali.com/

For Documentations go to https://developers.ektanjali.com/docs/umpremium/version4.0/index.html

For Social Application check out our blog https://blog.ektanjali.com/

INSTALLATION
------------
Install cakephp 4.x if you have not installed yet. For cakephp 4.x installation please go to https://book.cakephp.org/4.0/en/installation.html


------------------------------------------- Step 1 ------------------------------------------------

Download the latest version of plugin from https://cakephp4-user-management.ektanjali.com/
go to yourapp/plugins
extract	here
name it	Usermgmt

Directory structure should look like
yourapp/plugins/Usermgmt/config
----------------------- /src
----------------------- /templates etc


------------------------------------------- Step 2 ------------------------------------------------

MySQL Database import (use your favorite mysql tool to import the database sql)

you can download the mysql file from https://www.ektanjali.com/products/downloadDatabaseSql/umpremium4.0


------------------------------------------- Step 3 ------------------------------------------------

Configure your AppController class

you can download the app controller from https://www.ektanjali.com/products/downloadAppController/umpremium4.0


------------------------------------------- Step 4 ------------------------------------------------

add plugin in yourapp/src/Application.php
bootstrap function should include this line

// load user management plugin
$this->addPlugin('Usermgmt', ['routes' => true]);

//also you need to add plugin in yourapp/composer.json file then use composer dumpautoload command
https://book.cakephp.org/4/en/plugins.html#manually-autoloading-plugin-classes

for e.g.
"autoload": {
	"psr-4": {
		"App\\": "src/",
		"Usermgmt\\": "plugins/Usermgmt/src/"
	}
},


------------------------------------------- Step 5 ------------------------------------------------

Create a folder "plugins" without quotes in yourapp/webroot
Directory structure should look like
yourapp/webroot/plugins

you can download the vendor plugins from https://www.ektanjali.com/products/downloadExternalPlugins/umpremium4.0/bootstrap4

extract zip into yourapp/webroot/plugins
Directory structure should look like
yourapp/webroot/plugins/bootstrap
-----------------------/bootstrap-ajax-typeahead
-----------------------/ckeditor etc

------------------------------------------- Step 6 ------------------------------------------------

Add all plugins, bootstrap and other css and js files in your layout file, for example yourapp/templates/layout/default.php
	
you can download the default layout from https://www.ektanjali.com/products/downloadLayout/umpremium4.0/bootstrap4
	

------------------------------------------- Step 7 ------------------------------------------------

open yourapp/src/View/AppView.php
add following lines in initialize() function

/* user management plugin helpers */
$this->loadHelper('Usermgmt.UserAuth');
$this->loadHelper('Usermgmt.Tinymce');
$this->loadHelper('Usermgmt.Ckeditor');
$this->loadHelper('Usermgmt.Image');
$this->loadHelper('Usermgmt.Search');


------------------------------------------- Step 8 ------------------------------------------------

If you want use Recaptha and social logins like facebook. twitter etc then download the vendor files from https://www.ektanjali.com/products/downloadVendor/umpremium4.0

Extract zip file into yourapp/plugins/Usermgmt/vendor directory

Directory structure should look like
yourapp/plugins/Usermgmt/vendor/docblock
-------------------------------/facebook
-------------------------------/google	etc

All set??

Go to yourdomain/login
Default	user name password
username- admin
password- 123456

ALL DONE !