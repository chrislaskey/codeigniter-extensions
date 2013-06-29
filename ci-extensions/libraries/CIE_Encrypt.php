<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Extended options to always use less secure CI method
 *
 * @author: Chris Laskey
 * @source: http://chrislaskey.com
 * @version: 0.0.3
 * @updated: 2011.09.10
 *
 * Added option to always use the CI _xor_encrypt() method. This aids
 * in portability as mcrypt may not be available on some servers. It also
 * supports legacy _xor_encrypt()'d keys.
 *
 * Also removed unneeded second instantiation of $CI =& get_instance() in
 * the get_key() method
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Encryption Class
 *
 * Provides two-way keyed encoding using XOR Hashing and Mcrypt
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/encryption.html
 */
class CIE_Encrypt extends CI_Encrypt {

	var $CI;
	var $encryption_key	= '';
	var $_hash_type	= 'sha1';
	var $_mcrypt_exists = FALSE;
	var $_mcrypt_cipher;
	var $_mcrypt_mode;

	/**
	 * Constructor
	 *
	 * Simply determines whether the mcrypt library exists.
	 *
	 */
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->_mcrypt_exists = ( ! function_exists('mcrypt_encrypt') || $this->CI->config->item('encryption_use_mcrypt') === FALSE ) ? FALSE : TRUE;
		log_message('debug', "Encrypt Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the encryption key
	 *
	 * Returns it as MD5 in order to have an exact-length 128 bit key.
	 * Mcrypt is sensitive to keys that are not the correct length
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function get_key($key = '')
	{
		if ($key == '')
		{
			if ($this->encryption_key != '')
			{
				return $this->encryption_key;
			}

			$key = $this->CI->config->item('encryption_key');

			if ($key == FALSE)
			{
				show_error('In order to use the encryption class requires that you set an encryption key in your config file.');
			}
		}

		return md5($key);
	}

}

// END CI_Encrypt class

/* End of file CL_Encrypt.php */
