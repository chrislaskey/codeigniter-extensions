About
=====

A collection of core extensions to CodeIgniter.

Loader
------

**Adds support for nested controller files**

By default CodeIgniter only supports a maximum depth of one directory. This
extension modifies the core Loader method to allow `controllers` to have
multiple directory levels.

For example the URI `/multiple/levels/before/controller` will be automatically
routed to the directory
`application/controllers/multiple/levels/before/controller.php`.

To add this extension include the `core/CIE_Loader.php` file.

URI
---

**Adds `$this->uri->depth()` function**

The default CodeIgniter URI library includes methods to return a specific URI segment (`$this->uri->segment(3)`) or the entire string (`$this->uri->uri_string()`). This extension adds a new method `depth` which is an intermediary method between the two. The `depth` method returns a URI string up to a specific URI segment length.

For example, given the URI:

```php
// URI: /admin/accounts/people/203/edit

$this->uri->segment(2) == "accounts";
$this->uri->uri_string() == "/admin/accounts/people/203/edit";

$this->uri->depth(2) == "/admin/accounts/";
$this->uri->depth(3) == "/admin/accounts/people/";
```

The `depth` function can be used to avoid hard coding URIs in controllers and views.

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
