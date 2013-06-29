<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Extends CI_URI
 *
 * @author: Chris Laskey
 * @source: http://chrislaskey.com/
 * @updated: 2012.10.31
 *
 * Adds a number of helpful URI related functions.
 */

class CIE_URI extends CI_URI {

	function __construct(){
		parent::__construct();
	}

	/**
	 * Truncates the URI segment array at a set inveral from the START of the array
	 * Returns the shortened uri as a string
	 */

	function uri_depth($num){
		$CI = &get_instance();
		$uri = array_slice($CI->uri->segment_array(), 0, $num);
		return '/'.implode($uri, '/');
	}
	
	/**
	 * Removes $num segments from beginning of URI array
	 * Returns the shortened uri as a string
	 */

	function uri_shift($num){
		$CI = &get_instance();
		$uri = $CI->uri->segment_array();
		if( $num >= count($uri) ){ return array(); }
		else{
			return implode(array_slice($uri, $num), '/');
		}
	}

	/**
	 * Similar to uri_depth, this removes the given length from the URI
	 * And returns the shortened uri as a string
	 */

	function uri_remove($num){
		$CI = &get_instance();
		$uri = array_slice($CI->uri->segment_array(), 0, count($CI->uri->segment_array())-$num);
		return '/'.implode($uri, '/');
	}
	
	/**
	 * Override of CodeIgniter function.
	 * Guarantees uri_string will always end with a /
	 */

	function uri_string(){
		return '/'.trim($this->uri_string, '/');
	}

	/**
 	 * Add in uri pattern matching
 	 * Modeled after route matching Router::_parse_routes();
 	 */

	function match($match_string){
		$uri = $this->uri_string();
		$uri_matcher = new URI_Matcher();
		return $uri_matcher->match($match_string, $uri);
	}
	
}

class URI_Matcher {

	public function match($raw_match_string, $uri){
		$match_string = $this->parse_match_string_pseudo_selectors($raw_match_string);
		return $this->is_match($match_string, $uri);
	}

	private function parse_match_string_pseudo_selectors($raw_match_string){
		$match_string = str_replace(':num', '[0-9]+', $raw_match_string);
		$match_string = str_replace(':any', '.+', $match_string);
		return $match_string;
	}

	private function is_match($match_string, $uri){
		return preg_match('#^'.$match_string.'$#', $uri) === 1;
	}

}

/* End of file MY_URI.php */
