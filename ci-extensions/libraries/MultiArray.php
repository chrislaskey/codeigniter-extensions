<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Add methods for dealing with dynamic multidimensional arrays in PHP
 *
 * @author: Chris Laskey
 * @source: http://chrislaskey.com
 * @version: 0.0.2
 * @updated: 2014.01.23
 *
 * Helpful class for building multidimensional arrays dynamically.
 * Takes either an array of keys, or a string with a divider value.
 * 
 * Example:
 *      $my_array = MultiArray();
 *      $my_array->set('one/two/five', 'my_value');
 *      $output = $my_array->get();
 * 
 * The value of $output is:
 *      array('one' => array('two' => array('five' => 'my_value')))
 *
 * And:
 *      $output['one']['two']['five'] == 'my_value' # True
 */

class MultiArray {

    private $divider;
    private $multi;

    public function __construct($array = NULL){
        $this->create($array);
        $this->set_divider('/');
    }

    public function create($array = NULL) {
        if ($array == NULL) {
            $array = array();
        }
        $this->multi = $array;
    }

    public function get_divider() {
        return $this->divider;
    }

    public function set_divider($divider) {
        if (empty($divider) || !is_string($divider)) {
            throw new Exception('Invalid divider, must be a non-empty string');
        }
        $this->divider = $divider;
    }

    public function exists($keys) {
        $keys = $this->parse_keys($keys);

        return $this->is_key_set($keys, $this->multi);
    }

    private function is_key_set($keys, $arr) {
        $head = array_shift($keys);
        $tail = $keys;
        $there_are_keys_left = count($tail) > 0;
        $key_exists = isset($arr[$head]);

        if ($there_are_keys_left && $key_exists){
            return $this->is_key_set($tail, $arr[$head]);
        } else {
            return $key_exists;
        }
    }

    public function get($keys = '') {
        if( $keys == NULL ) {
            return $this->multi;
        }

        $keys = $this->parse_keys($keys);

        return $this->get_key($keys, $this->multi);
    }

    private function get_key($keys, $arr) {
        $head = array_shift($keys);
        $tail = $keys;
        $there_are_keys_left = count($tail) > 0;
        $key_exists = isset($arr[$head]);

        if ($there_are_keys_left && $key_exists){
            return $this->get_key($tail, $arr[$head]);
        } else {
            return $key_exists ? $arr[$head] : False;
        }
    }

    public function set($keys, $value) {
        $keys = $this->parse_keys($keys);
        $this->multi = $this->set_key($keys, $value, $this->multi);
    }

    private function set_key($keys, $value, $arr) {
        $head = array_shift($keys);
        $tail = $keys;
        $there_are_keys_left = count($tail) > 0;
        $key_exists = isset($arr[$head]);

        if ($there_are_keys_left) {
            if ($key_exists){
                $arr[$head] = $this->set_key($tail, $value, $arr[$head]);
                return $arr;
            } else {
                $arr[$head] = $this->set_key($tail, $value, array());
                return $arr;
            }
        } else {
            $arr[$head] = $value;
            return $arr;
        }
    }

    public function increase($keys) {
        $keys = $this->parse_keys($keys);
        $this->multi = $this->increase_key($keys, $this->multi);
    }

    private function increase_key($keys, $arr) {
        $head = array_shift($keys);
        $tail = $keys;
        $there_are_keys_left = count($tail) > 0;
        $key_exists = isset($arr[$head]);

        if ($there_are_keys_left) {
            if ($key_exists) {
                $arr[$head] = $this->increase_key($tail, $arr[$head]);
                return $arr;
            } else {
                $arr[$head] = $this->increase_key($tail, array());
                return $arr;
            }
        } else {
            if ($key_exists) {
                $current_value = is_numeric($arr[$head]) ? intval($arr[$head]) : 0;
                $arr[$head] = $current_value + 1;
            } else {
                $arr[$head] = 1;
            }
            return $arr;
        }
    }

    // These functions belong to a helper class MultiArrayKeyParser, but
    // due to the way CodeIgniter loads classes into the shared namespace
    // only the main class can be loaded. Since this is just a helper
    // the functions have been merged back into the main class.

    public function parse_keys($keys) {
        $this->raise_if_keys_are_empty($keys);

        if (is_string($keys)){
            $parsed_keys = $this->parse_keys_from_string($keys);
        } elseif (is_array($keys)) {
            $parsed_keys = $this->parse_keys_from_array($keys);
        } else {
            $this->raise_invalid_key_type();
        }

        $this->raise_if_keys_are_empty($parsed_keys);

        return $parsed_keys;
    }

    private function raise_if_keys_are_empty($keys) {
        if (! $keys){
            throw new Exception(
                'Invalid key for multidimensional array. Must not be empty.'
            );
        }
    }

    private function raise_invalid_key_type() {
        throw new Exception(
            'Invalid key for multidimensional array. Must be either an
            array or string value.'
        );
    }

    private function parse_keys_from_array($keys) {
        $filtered = array_filter($keys);

        return $filtered;
    }

    private function parse_keys_from_string($keys) {
        $no_whitespace = trim($keys);
        $no_wrapping_dividers = trim($no_whitespace, $this->divider);
        $exploded = explode($this->divider, $no_wrapping_dividers);

        return $exploded;
    }

}

/* End of file MultiArray.php */
