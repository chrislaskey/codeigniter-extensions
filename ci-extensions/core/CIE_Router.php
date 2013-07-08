<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Extending CI_Router
 *
 * @author: Chris Laskey
 * @source: http://chrislaskey.com/
 * @version: 1.0.0
 */

/**
 * Extending Router class to allow multiple directories in the controllers folder
 * @param controller_depth_limit
 */

class CIE_Router extends CI_Router {

	var $controller_depth_limit = 5;

	/**
	 * CodeIgniter 2.0 changed _validate_request. It no longer sets:
	 * 		$this->set_class()
	 * 		$this->set_method()
	 * 	These functions are handled in $this->_set_request() instead.
	 *
	 * @return
	 * 	This function should return an array
	 * 	$arg[0] is used in $this->set_class()
	 * 	$arg[1] is used in $this->set_method()
	 * 	Or call show_404 if controller does not exist
	 */
	function _validate_request($segments){
		if( $return = $this->_controller_exists_recursive($segments) ){
			$this->set_directory($return[0]);
			$method = ($return[1] == end($segments)) ? 'index' : end($segments);
			return array($return[1], $method);
		}else{ show_404(implode("/", $segments)); }
	}

	//CodeIgniter 2.0 added filtering of sub directories to this function. This turns if back off.
	function set_directory($dir){
		$this->directory = $dir.'/';
	}

	//Used to recursively check if a controller exists
	function _controller_exists_recursive($segments, $index = 0){
		if( $index >= $this->controller_depth_limit || $index >= count($segments) ){ return FALSE; }

		$directory = ($index > 0 ) ? implode("/", array_slice($segments, 0, $index)).'/' : '';

		if( file_exists(APPPATH.'controllers/'.$directory.$segments[$index].EXT) ){ return array(substr($directory, 0, -1), $segments[$index]); }
		else{ return $this->_controller_exists_recursive($segments, ++$index); }
	}

}

/* End of file CL_Router.php */
