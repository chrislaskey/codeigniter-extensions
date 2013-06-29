<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Extending CI_Output
 *
 * @author: Chris Laskey
 * @source: http://chrislaskey.com/
 * @version: 1.0.1
 *
 * Adds a simple check that output is not null before caching a page.
 * The default CodeIgniter class does not check, and occaisionally will
 * cache a blank page.
 *
 * This class should be kept up to date with CI_Output.
 */

class CIE_Output extends CI_Output {

	function __construct(){
		parent::__construct();
	}
	
	/* The original _display function does not check for null output before creating cache
	 * Occasionally this leads to a blank ouput. Added in a simple check.
	*/
	function _display($output = '')
	{	
		// Note:  We use globals because we can't use $CI =& get_instance()
		// since this function is sometimes called by the caching mechanism,
		// which happens before the CI super object is available.
		global $BM, $CFG;
	
		// --------------------------------------------------------------------
	
		// Set the output data
		if ($output == '')
		{
			$output =& $this->final_output;
		}
	
		// --------------------------------------------------------------------
	
		// Do we need to write a cache file?
		if ($this->cache_expiration > 0)
		{
			if( $output != NULL ){
				$this->_write_cache($output);
			}
		}
		
		// --------------------------------------------------------------------
		
		// Parse out the elapsed time and memory usage,
		// then swap the pseudo-variables with the data
		
		$elapsed = $BM->elapsed_time('total_execution_time_start', 'total_execution_time_end');		
		$output = str_replace('{elapsed_time}', $elapsed, $output);
		
		$memory	 = ( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).'MB';
		$output = str_replace('{memory_usage}', $memory, $output);		
		
		// --------------------------------------------------------------------
		
		// Is compression requested?
		if ($CFG->item('compress_output') === TRUE)
		{
			if (extension_loaded('zlib'))
			{
				if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
				{
					ob_start('ob_gzhandler');
				}
			}
		}
		
		// --------------------------------------------------------------------
		
		// Are there any server headers to send?
		if (count($this->headers) > 0)
		{
			foreach ($this->headers as $header)
			{
				@header($header[0], $header[1]);
			}
		}
		
		// --------------------------------------------------------------------
		
		// Does the get_instance() function exist?
		// If not we know we are dealing with a cache file so we'll
		// simply echo out the data and exit.
		if ( ! function_exists('get_instance'))
		{
			echo $output;
			log_message('debug', "Final output sent to browser");
			log_message('debug', "Total execution time: ".$elapsed);
			return TRUE;
		}
		
		// --------------------------------------------------------------------
		
		// Grab the super object.  We'll need it in a moment...
		$CI =& get_instance();
		
		// Do we need to generate profile data?
		// If so, load the Profile class and run it.
		if ($this->enable_profiler == TRUE)
		{
			$CI->load->library('profiler');				
									
			// If the output data contains closing </body> and </html> tags
			// we will remove them and add them back after we insert the profile data
			if (preg_match("|</body>.*?</html>|is", $output))
			{
				$output  = preg_replace("|</body>.*?</html>|is", '', $output);
				$output .= $CI->profiler->run();
				$output .= '</body></html>';
			}
			else
			{
				$output .= $CI->profiler->run();
			}
		}
		
		// --------------------------------------------------------------------
		
		// Does the controller contain a function named _output()?
		// If so send the output there.  Otherwise, echo it.
		if (method_exists($CI, '_output'))
		{
			$CI->_output($output);
		}
		else
		{
			echo $output;  // Send it to the browser!
		}
		
		log_message('debug', "Final output sent to browser");
		log_message('debug', "Total execution time: ".$elapsed);
	}
	
}

/* End of file CL_Output.php */
