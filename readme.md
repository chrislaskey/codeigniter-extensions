About
================================================================================

A collection of core extensions to CodeIgniter.

Installation
================================================================================

The first step is download and install the `ci-extensions` directory into the
CodeIgniter `applications/third_party` directory.

This project extends core functionality of CodeIgniter. To ensure everything is
loaded on each request, add the following line to
`application/config/autoload.php` in the packages section:

	$autoload['packages'] = array(APPPATH.'third_party/ci-extensions');

This will automatically load everything in the `helpers` and `libraries`
directories.

### Loading `core` files ###

When CodeIgniter 2.0 was released, some of the core libraries were taken out of
`applications/libraries` and placed into a new `applications/core` directory.

But support for autoloading `third_party/core` apps was not extended to `core`
files.

To fix this, the simplest way is to load third_party core files directly inside the
`application/core/MY_*.php` files.

For example, to load the `CIE_Loader.php`
the `application/core/MY_Loader.php` file should look like:

	<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	require APPPATH."third_party/ci-extensions/core/CIE_Loader.php";

	class MY_Loader extends CIE_Loader { }

	/* End of file MY_Loader */

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
