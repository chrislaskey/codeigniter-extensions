About
=====

A collection of core extensions to CodeIgniter.

Router
------

**Adds support for loading PHP classes outside the universal CI object**
**Adds support for extending the DB class**

By default the Loader function loads assets into the universal CodeIgniter
object which is then referenced via `$this` or by calling `&get_instance()`. If
the resource is a class, one object is instantiated and added to the
CodeIgniter object.

This works great for the majority of uses cases, but it does restrict the
functionality of PHP Classes in two ways. First it makes it harder to
instantiate multiple instances. Second it offers no separation between when
a class is loaded into the namespace and when an object is instantiated. Having
control to delay instantiation until later is helpful for particularly resource
heavy objects.

This extension adds the `$this->load->class()` method. It offers a clean way to
load classes into the current namespace without instantiating an object and adding
it to the universal CodeIgniter object.

By default CodeIgniter restricts the extension of the `system/core/DB.php`
file. This extension also adds support for extending this file using the
standard `application/core/MY_DB.php` method. 

To add this extension follow the installation instructions and include the
`core/CIE_Loader.php` file.

Router
------

**Adds support for nested controller files**

By default CodeIgniter only supports a maximum depth of one directory. This
extension modifies the core Router method to allow `controllers` to have
multiple directory levels.

For example the URI `/multiple/levels/before/controller` will be automatically
routed to the directory
`application/controllers/multiple/levels/before/controller.php`.

To add this extension follow the installation instructions and include the `core/CIE_Router.php` file.

URI
---

**Adds `$this->uri->depth()` method**

The default CodeIgniter URI library includes methods to return a specific URI segment `$this->uri->segment(3)` or the entire string `$this->uri->uri_string()`. This extension adds a new method `depth` which is an intermediary method between the two. The `depth` method returns a URI string up to a specific URI segment length.

For example, given the URI:

```php
// URI: /admin/accounts/people/203/edit

$this->uri->segment(2) == "accounts";
$this->uri->uri_string() == "/admin/accounts/people/203/edit";

$this->uri->depth(2) == "/admin/accounts/";
$this->uri->depth(3) == "/admin/accounts/people/";
```

The `depth` function can be used to avoid hard coding URIs in controllers and views.

To add this extension follow the installation instructions and include the `core/CIE_URI.php` file.

Installation
============

It is recommended to use CodeIgniter's third party support to install these
extensions. This allows this project's files to be updated separately from
application specific code.

The first step is download and install the `ci-extensions` directory into the
CodeIgniter `applications/third_party` directory.

This project extends core functionality of CodeIgniter. To ensure everything is
loaded on each request, add the following line to
`application/config/autoload.php` in the packages section:

```php
$autoload['packages'] = array(APPPATH.'third_party/ci-extensions');
```

This will automatically load everything in the `helpers` and `libraries`
directories.

#### Autoloading `core` files ####

When CodeIgniter 2.0 was released, some of the core libraries were taken out of
`applications/libraries` and placed into a new `applications/core` directory.
But support for autoloading `third_party/core` apps was not extended to `core`
files.

To fix this, the simplest way is to load third_party core files directly inside the
`application/core/MY_*.php` files. For example, to load the `CIE_Loader.php`
the `application/core/MY_Loader.php` file should look like:

```php
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH."third_party/ci-extensions/core/CIE_Loader.php";

class MY_Loader extends CIE_Loader { }

/* End of file MY_Loader */
```

Notice the `ci-extensions/core` file is included with a `require` call, and the
`MY_Loader` class now extends `CIE_Loader`.

This solutions loads the core file while maintaining the ability to extend core
classes with application specific code inside `MY_*` definitions.

**Note** example files for autoloading each core library is included in
`installation-example-files` directory.

License
================================================================================

All code written by me is released under MIT license. See the attached
license.txt file for more information, including commentary on license choice.
