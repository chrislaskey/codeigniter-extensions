<?php

/**
 * Extending CI_Loader
 *
 * @author: Chris Laskey
 * @source: http://chrislaskey.com/
 * @version: 0.9.0
 * @updated: 2012.05.03
 *
 * Changelog v0.9.0
 * ========================================================================
 * Added a new include_class() function. By default CodeIgniter
 * instantiates an instance of a class and adds it to the global $CI
 * object. This function works like a PHP include() but returns a Boolean
 * instead of a Fatal Error.
 * 
 * Changelog v0.8.0
 * ========================================================================
 * Enabled extending of database class. This class is not extendable by
 * default in CodeIgniter
 */

class CIE_Loader extends CI_Loader {

	function __construct(){
		parent::__construct();
	}

	//Based directly on $this->library(),
	//Added existence check and call to $this->_ci_include_class() instead of
	//a call to $this->_ci_load_class()
	function include_class($class, $params = NULL){
		
		if( class_exists($class) ){ return TRUE; }

		if (is_array($class))
		{
			foreach ($class as $one_class)
			{
				$this->library($one_class, $params);
			}

			return;
		}

		if ($class == '' OR isset($this->_base_classes[$class]))
		{
			return FALSE;
		}

		if ( ! is_null($params) && ! is_array($params))
		{
			$params = NULL;
		}

		return $this->_ci_include_class($class, $params);
	}

	//Based directly on $this->_ci_load_class(),
	//minus the instantiation and assignment to the CI object.
	protected function _ci_include_class($class, $params = NULL, $object_name = NULL)
	{
		// Get the class name, and while we're at it trim any slashes.
		// The directory path can be included as part of the class name,
		// but we don't want a leading slash
		$class = str_replace('.php', '', trim($class, '/'));

		// Was the path included with the class name?
		// We look for a slash to determine this
		$subdir = '';
		if (($last_slash = strrpos($class, '/')) !== FALSE)
		{
			// Extract the path
			$subdir = substr($class, 0, $last_slash + 1);

			// Get the filename from the path
			$class = substr($class, $last_slash + 1);
		}

		// We'll test for both lowercase and capitalized versions of the file name
		foreach (array(ucfirst($class), strtolower($class)) as $class)
		{
			$subclass = APPPATH.'libraries/'.$subdir.config_item('subclass_prefix').$class.'.php';

			// Is this a class extension request?
			if (file_exists($subclass))
			{
				$baseclass = BASEPATH.'libraries/'.ucfirst($class).'.php';

				if ( ! file_exists($baseclass))
				{
					log_message('error', "Unable to include the requested class: ".$class);
					return FALSE;
				}

				include_once($baseclass);
				include_once($subclass);
				return TRUE;
			}

			// Lets search for the requested library file and load it.
			$is_duplicate = FALSE;
			foreach ($this->_ci_library_paths as $path)
			{
				$filepath = $path.'libraries/'.$subdir.$class.'.php';

				// Does the file exist?  No?  Bummer...
				if ( ! file_exists($filepath))
				{
					continue;
				}

				include_once($filepath);
				return TRUE;
			}

		} // END FOREACH

		// One last attempt.  Maybe the library is in a subdirectory, but it wasn't specified?
		if ($subdir == '')
		{
			$path = strtolower($class).'/'.$class;
			return $this->_ci_include_class($path, $params);
		}

		// If we got this far we were unable to find the requested class.
		// We do not issue errors if the load call failed due to a duplicate request
		if ($is_duplicate == FALSE)
		{
			log_message('error', "Unable to include the requested class: ".$class);
			return FALSE;
		}
	}

	/**
	 * Overwrite CI_Loader::database() Method
	 *
	 * This method is used the unique entry point to everything DB related.
	 * This is a shell method that simple checks if DB already exists, if not it creates it.
	 * It creates it by loading CI_DB.php, which contains the function db().
	 * The db() function initializes CI_DB_Driver, a class that provides the skeleton for any DB connection.
	 * CI_DB_Driver then loads the specific database driver (MySQL, MSSQL, etc).
	 *
	 * The tricky part is the db() function initializes the CI_DB_Driver class by creating a new CI_DB class.
	 * It uses eval() to create the new CI_DB class, which extends either the CI_DB_Driver directly, or
	 * if active record is turned on, it extends CI_DB_active_rec (which itself extends CI_DB_Driver).
	 *
	 * This is not an ideal implementation, but PHP is not very flexible with OOP inheritence, making 'tricks'
	 * like using eval tempting. As the Active Record class is +2k lines of code, its not trivial to load
	 * it if it is not needed. However, I still believe there are alternative solutions:
	 *
	 * 		1. Flatten the inheritence to CI_DB_Loader->CI_DB_Active_Rec->CI_DB by default.
	 * 			Then toggled whether to "turn on" active record in the CI_DB_Active_Rec constructor.
	 * 			This would solve the need to use Eval to make CI_DB as it would always extend the same class
	 * 			so it could be definded in a file.
	 * 			The downside is of course loading a 2k line class into memory. But the class footprint could be
	 * 			minimized when turned off if optimized for that behavior.
	 *
	 * Documentation to be continued...
	*/

	function database($params = '', $return = FALSE, $active_record = NULL){

		/**
		 * This is an override, not an extension.
		 * Therefore the entire loader::database method is copied below.
		 * If this was an extension, only modified code would be included
		 * with a call to parent::database($params, $return, $active_record);
		 */

		// Grab the super object
		$CI =& get_instance();

		// Do we even need to load the database class?
		if (class_exists('CI_DB') AND $return == FALSE AND $active_record == NULL AND isset($CI->db) AND is_object($CI->db))
		{
			return FALSE;
		}

		/**
		 * Load a custom DB.php file, containing a custom db() function if it exists.
		 * If not, load the default one.
		 */

		if( file_exists(APPPATH.'core/'.config_item('subclass_prefix').'DB'.EXT) )
		{
			require_once(APPPATH.'core/'.config_item('subclass_prefix').'DB'.EXT);
		}
		else
		{
			require_once(BASEPATH.'database/DB'.EXT);
		}

		if ($return === TRUE)
		{
			return DB($params, $active_record);
		}

		// Initialize the db variable.  Needed to prevent
		// reference errors with some configurations
		$CI->db = '';

		// Load the DB class
		$CI->db =& DB($params, $active_record);

	}

}

/* End of file CL_loader */
