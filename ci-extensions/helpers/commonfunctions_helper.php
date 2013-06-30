<?php

/**
 * Commonfunctions Helper
 *
 * @author: Chris Laskey
 * @source: http://chrislaskey.com
 * @version: 3.0.5
 * @updated: 2013.06.29
 *
 * A collection of simple functions used in the application
 */

if( ! function_exists('nav') ){

	/* Echo Nav Function
	 * Similar to the e() function, checks if the current nav item is set in the URI.
	 * Does existence check first, so E_ALL usable w/o throwing notices
	 * Accepts the level to search at (example.com/level1/level2/level3 etc.)
	 * Returns the string 'selected' if true (to be used as a class name)
	 * Use: n('var', 1);
	*/

	function nav($var, $i = 1){
		$CI = &get_instance();
		if( !is_numeric($i) || $i <= 0 ){ return NULL; }
		if( isset($CI->uri->segments[$i]) && $CI->uri->segments[$i] == $var){ echo 'selected'; }
		elseif( $var == NULL && !isset($CI->uri->segments[$i]) ){ echo 'selected'; }
		else{ return NULL; }
	}

}

if( ! function_exists('_clean_page_titles') ){

	function _clean_page_titles($input){
		if( !is_numeric($input) ){
			return ucwords( trim( strtr( strtr( $input, "_", " " ), "-", " " ) ) );
		}else{ return NULL; }
	}

}

if( ! function_exists('require_local') ){

	function require_local($redirect = TRUE, $exit = TRUE){
		if( ! isset($_SERVER['REMOTE_ADDR']) || ! in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'localhost', $_SERVER['SERVER_ADDR'])) ){
			if( $redirect === TRUE ){ header('Status:404 Not Found', TRUE, 404); }
			if( $exit === TRUE ){ exit(); }
			else{ return FALSE; }
		}
		return TRUE;
	}

}

if( ! function_exists('CI_create_page_title') ){

	//WordPress also has a create_page_title function. This is to play nice
	//in case both are loaded.
	function CI_create_page_title($echo = TRUE){
		$CI = &get_instance();
		$uri = array_reverse($CI->uri->segment_array());
		$exceptions = $CI->config->item('page_titles');
		return create_page_title($uri, $exceptions, $echo);
	}

}

if( ! function_exists('create_page_title') ){

	function create_page_title($uri = array(), $exceptions = NULL, $echo = TRUE){
		if( ! empty($uri) ){
			foreach( $uri as $key => $val ){
				$val = _clean_page_titles($val);
				if( isset($exceptions) && ! empty($exceptions) ){
					foreach( $exceptions as $k => $v ){
						$val = str_ireplace($k, $v, $val);
					}unset($k, $v);
				}
				if( $val != NULL ){ $uri[$key] = $val; }
				else{ unset($uri[$key]); }
			}
		}
		$page_title = ( empty($uri) ) ? APPLICATION_TITLE_LONG : implode($uri, ' | ') . ' | ' . APPLICATION_TITLE_SHORT;
		if( $echo === TRUE ){ echo $page_title; }
		else{ return $page_title; }
	}

}

if( ! function_exists('breadcrumbs') ){

	function breadcrumbs($limit = NULL, $echo = TRUE){
		// if( strpos($_SERVER['REQUEST_URI'], '?') === FALSE ){ $request_uri = $_SERVER['REQUEST_URI']; }
		// else{ $request_uri = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?')); }
		$CI = &get_instance();
		$uri = array_values($CI->uri->segment_array()); // Return zero index instead of index starting at 1
		$uri = (count($uri) > 6) ? array_slice($uri, 0, 6) : $uri;
		$uri_depth = count($uri);
		$url = '/';
		$separator = '&nbsp;&nbsp;&raquo;&nbsp;&nbsp;';
		$breadcrumbs = array();
		$replacements = array('?s' => 'Search');
		$substr_replacements = array('bama' => 'BA/MA');
		
		$breadcrumbs[] = ($uri_depth > 0) ? '<a class="home" href="/">Home</a>' : '<a class="home" href="/">Home</a>';
		for($i = 0; $i < $uri_depth; $i++){
			$url .= $uri[$i].'/';
		
			foreach($replacements as $key=>$val){
				if( strpos(strtolower($uri[$i]), $key) !== FALSE ){ $uri[$i] = $val; }
			}unset($key, $val);
		
			foreach($substr_replacements as $key=>$val){
				if( strpos(strtolower($uri[$i]), $key) !== FALSE ){ $uri[$i] = str_replace($key, $val, $uri[$i]); }
			}unset($key, $val);
		
			$val = ucwords( trim( strtr( strtr( $uri[$i], "_", " " ), "-", " " ) ) );
			if( $i == ($uri_depth-1) ){ $breadcrumbs[] = '<span class="last">'.$val.'</span>'; }
			else{ $breadcrumbs[] = '<a href="'.$url.'">'.$val.'</a>'; }
		}unset($i);
	
		if( is_numeric($limit) ){ $breadcrumbs = array_slice($breadcrumbs, 0, $limit); }
	
		if( $echo === TRUE ){ echo (count($breadcrumbs) > 0) ? '<div id="breadcrumbs">'.implode($separator, $breadcrumbs).'</div>' : ''; }
		else{ return (count($breadcrumbs) > 1) ? '<div id="breadcrumbs">'.implode($separator, $breadcrumbs).'</div>' : ''; }
	}

}

if( ! function_exists('utf8_to_word') ){

	// Once a Microsoft Word encoded string is converted to UTF-8, it can not
	// be imported back into Word via HTML with HTMLEntities. The entities
	// are recognized as strings, and the special characters like smart
	// quotes are no longer parsable in Word's character encoding, even
	// though UTF-8 is capable of encoding and storing them. It's a one
	// way street.
	//
	// This function simply replaces common Word characters with their simpler
	// versions.
	function utf8_to_word($input, $echo = TRUE){
		$table = array('“'=>'"', '”'=>'"', '‘'=>"'", '’'=>"'", '…'=>'...', '—'=>'-', '–'=> '-');
		$result = strtr($input, $table);
		if( $echo === TRUE ){
			echo $result;
		}else{
			return $result;
		}
	}

}

if( ! function_exists('ent') ){

	//TODO: SEE PARSE LIBRARY INSTEAD OF THIS FUNCTIONS
	function ent($input, $echo = TRUE, $double = TRUE){

		//TODO: see parse library instead of these functions?

		//Fourth parameter, double encode, for htmlentities() added in 5.2.3
		list($major, $minor, $minorminor) = explode('.', phpversion());
		if( $major >= 5 && $minor >= 2 && $minorminor >= 3 ){
			if( $echo === TRUE ){ echo htmlentities($input, ENT_COMPAT, 'UTF-8', $double); }
			else{ return htmlentities($input, ENT_COMPAT, 'UTF-8', $double); }
		}else{
			if( $echo === TRUE ){ echo htmlentities($input, ENT_COMPAT, 'UTF-8'); }
			else{ return htmlentities($input, ENT_COMPAT, 'UTF-8'); }
		}
	}

}

if( ! function_exists('encode_email_address') ){

	function encode_email_address($email, $text = "", $extra = "") {
		preg_match('!^(.*)(\?.*)$!',$email,$match);
		if(!empty($match[2])) {
			return false;
		}
		$address_encode = '';
		for ($x=0; $x < strlen($email); $x++) {
			if(preg_match('!\w!',$email[$x])) {
				$address_encode .= '%' . bin2hex($email[$x]);
			} else {
				$address_encode .= $email[$x];
			}
		}
		if (empty($text))
			$text = $email;
		$text_encode = "";
		for ($x=0; $x < strlen($text); $x++) {
			$text_encode .= '&#x' . bin2hex($text[$x]).';';
		}
		$mailto = "&#109;&#97;&#105;&#108;&#116;&#111;&#58;";
		return '<a href="'.$mailto.$address_encode.'" '.$extra.'>'.$text_encode.'</a>';
	}

}

if( ! function_exists('_clean_body_class') ){

	function _clean_body_class($text){
		$text = trim($text);
		$return = '';
		$match = '';
		$count = strlen($text);
		for($i = 0; $i < $count; $i++ ){
			$match = substr($text, $i, 1);
			if( $match == '-' ){ $match = '_'; }
			if( $match == ' ' ){ $match = '_'; }
			if( preg_match('/[a-zA-Z_]/', $match) !== 1 ){ $match = ''; }
			$match = ( substr($return, strlen($return)-1, 1) == '_' && $match == '_') ? '' : $match; //Remove duplicate __'s
			$match = strtolower($match);
			$return .= $match;
		}
		return $return;
	}

}

if( ! function_exists('CI_create_body_class') ){

	function CI_create_body_class($echo = TRUE){
		$CI = &get_instance();
		$uri = $CI->uri->segment_array();
		return create_body_class($uri, $echo);
	}

}

if( ! function_exists('create_body_class') ){

	function create_body_class($uri, $echo = TRUE){
		$class = array();

		foreach($uri as $k=>$v){
			$class[] = _clean_body_class($v);
		}
		$class = implode($class, ' ');

		if( $echo === FALSE ){ return $class; }
		else{ echo $class; }
	}

}

if( ! function_exists('add_trailing_slash') ){

	function add_trailing_slash($string){
		return rtrim($string, '/').'/';
	}

}

if( ! function_exists('help_icon') ){

	function help_icon($text = '?'){
		echo '<span class="help_icon_container">(<span class="help_icon">'.$text.'</span>)</span>';
	}

}

if( ! function_exists('helper_text') ){

	function helper_text($msg, $width = 160){
		echo  '<div class="helperContainer">
					<a class="helperIcon" href="#">?</a>
					<div class="helperTextContainer">
						<div class="helperText" style="width:'.$width.'px;">'.$msg.'</div>
					</div>
				</div>';
	}

}

if( ! function_exists('return_files') ){

	function return_files($directory){
		$directory = (strpos($directory, $_SERVER['DOCUMENT_ROOT']) !== 0) ? $_SERVER['DOCUMENT_ROOT'].$directory : $directory;
		if( ! is_dir($directory) ){ return FALSE; }
		if( ! is_readable($directory) ){ return FALSE; }
		if( ! $dh = opendir($directory) ){ return FALSE; }

		$files = array();
		while( ($file = readdir($dh)) !== false ) {
			if( filetype($directory . $file) == 'file'){
				$files[] = $file;
			}
		}
		closedir($dh);

		return $files;
	}

}

if( ! function_exists('last_char') ){

	function last_char($string){
		return $string[strlen($string)-1];
	}

}

if( ! function_exists('has_punctuation') ){

	function has_punctuation($string){
		$last_char = last_char($string);
		$punctuation = array('.', ',', '?', '!');
		return in_array($last_char, $punctuation);
	}

}

if( ! function_exists('break_at_next_word') ){

	function break_at_next_word($limit, $input, $dots = TRUE){
		if( $limit <= strlen( $input ) ){
			$dots = $dots ? " ..." : "";
			$strpos = (strpos( $input, " ", $limit ) !== FALSE ) ? strpos( $input, " ", $limit ) : $limit;
			return trim(substr( $input, 0, $strpos+1)) . $dots;
		}else{ return $input; }
	}

}

if( ! function_exists('break_at_length') ){

	function break_at_length($limit, $input, $dots = TRUE){
		if( $limit <= strlen( $input ) ){
			$dots = $dots ? "..." : "";
			return substr( $input, 0, $limit ) . $dots;
		}else{ return $input; }
	}

}

/* From php.net. User comment, http://www.php.net/manual/en/function.array-slice.php#94138 */
if( ! function_exists('array_split') ){

	function array_split($array, $pieces=2){
		if ($pieces < 2)
			return array($array);
		$newCount = ceil(count($array)/$pieces);
		$a = array_slice($array, 0, $newCount);
		$b = array_split(array_slice($array, $newCount), $pieces-1);
		return array_merge(array($a),$b);
	}

}

/* From php.net. User comment, http://www.php.net/manual/en/function.strip-tags.php#93567 */
if( ! function_exists('strip_only') ){

	function strip_only($str, $tags) {
		if(!is_array($tags)) {
			$tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
			if(end($tags) == '') array_pop($tags);
		}
		foreach($tags as $tag) $str = preg_replace('#</?'.$tag.'[^>]*>#is', '', $str);
		return $str;
	}

}

if( ! function_exists('create_slug') ){

	function create_slug($text){
		$text = trim(strtolower($text));
		$slug = '';
		$match = '';
		$count = strlen($text);
		for($i = 0; $i < $count; $i++ ){
			$match = substr($text, $i, 1);
			if( $match == '_' ){ $match = '-'; }
			if( $match == ' ' ){ $match = '-'; }
			if( preg_match('/[a-zA-Z0-9-]/', $match) !== 1 ){ $match = ''; }
			$match = ( substr($slug, strlen($slug)-1, 1) == '-' && $match == '-') ? '' : $match; //Remove duplicate --'s
			$match = strtolower($match);
			$slug .= $match;
		}
		return $slug;
	}

}

if( ! function_exists('create_file_name') ){

	function create_file_name($text){
		$text = trim($text);
		$name = '';
		$match = '';
		$count = strlen($text);
		for($i = 0; $i < $count; $i++ ){
			$match = substr($text, $i, 1);
			if( $match == '-' ){ $match = '_'; }
			if( $match == ' ' ){ $match = '_'; }
			if( preg_match('/[a-zA-Z0-9_]/', $match) !== 1 ){ $match = ''; }
			$match = ( substr($name, strlen($name)-1, 1) == '_' && $match == '_') ? '' : $match; //Remove duplicate __'s
			$match = strtolower($match);
			$name .= $match;
		}
		return $name;
	}

}

if( ! function_exists('create_name_from_slug') ){

	function create_name_from_slug($text){
		$text = trim($text);
		$name = '';
		$match = '';
		$count = strlen($text);
		for($i = 0; $i < $count; $i++ ){
			$match = substr($text, $i, 1);
			if( $match == '-' ){ $match = ' '; }
			if( $match == '_' ){ $match = ' '; }
			if( preg_match('/[a-zA-Z0-9\s]/', $match) !== 1 ){ $match = ''; }
			$match = ( substr($name, strlen($name)-1, 1) == ' ' && $match == ' ') ? '' : $match; //Remove duplicate spaces
			$match = strtolower($match);
			$name .= $match;
		}
		return ucwords($name);
	}

}

if( ! function_exists('create_random_string') ){

	function create_random_string($length = 40){
		if( !is_numeric($length) ){ return FALSE; }
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$string = '';
		for($i = 0; $i < $length; $i++ ){
			$random = rand(0, strlen($characters)-1);
			$string .= substr($characters, $random, 1);
		}
		return $string;
	}

}

if( ! function_exists('create_password_salt') ){

	function create_password_salt($length = 10){
		if( !is_numeric($length) ){ return FALSE; }
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*';
		$salt = '';
		for($i = 0; $i < $length; $i++ ){
			$random = rand(0, strlen($characters)-1);
			$salt .= substr($characters, $random, 1);
		}
		return $salt;
	}

}

if( ! function_exists('return_last_name') ){

	function return_last_name($string){
		$exploded = explode("and", $string); //Split on ands, only take first returned value.
		$exploded = explode(",", trim($exploded[0])); //Split on commas, only take first returned value.
		$exploded = explode(" ", trim($exploded[0])); //Split on spaces.
		return (count($exploded) > 0) ? array_shift(array_slice($exploded, -1, 1)) : NULL; //Return last in array
	}

}

//Generic String Rotate 13 (str_rot13)
// Thanks shaunspiller at spammenotgmail dot com.
// http://www.php.net/manual/en/function.str-rot13.php#107475
if( ! function_exists('str_rot') ){

	function str_rot($s, $n = 13) {
		static $letters = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz';
		$n = (int)$n % 26;
		if (!$n) return $s;
		if ($n < 0) $n += 26;
		if ($n == 13) return str_rot13($s);
		$rep = substr($letters, $n * 2) . substr($letters, 0, $n * 2);
		return strtr($s, $letters, $rep);
	}
}

if( ! function_exists('divider') ){

	function divider($divider = '&nbsp;&nbsp;|&nbsp;&nbsp;', $echo = TRUE){
		if( $echo === TRUE ){ echo $divider; }
		else{ return $divider; }
	}

}

if( ! function_exists('return_mime_type_by_extension') ){

	function return_mime_type_by_extension($file){

		$extension = strtolower(substr(strrchr($file, '.'), 1));

		global $mimes;

		if ( ! is_array($mimes)) {
			if( defined('ENVIRONMENT') && is_file(APPPATH.'config/'.ENVIRONMENT.'/mimes'.EXT) ) {
				include(APPPATH.'config/'.ENVIRONMENT.'/mimes'.EXT);
			}elseif( is_file(APPPATH.'config/mimes'.EXT) ){
				include(APPPATH.'config/mimes'.EXT);
			}

			if( ! is_array($mimes) ){
				return FALSE;
			}
		}

		if( array_key_exists($extension, $mimes) ){

			if (is_array($mimes[$extension])) {

				// Multiple mime types, just give the first one
				return current($mimes[$extension]);

			}else{ return $mimes[$extension]; }

		}else{ return FALSE; }

	}
}

if( ! function_exists('is_iterable') ){

	function is_iterable($item){
		$is_a_collection = is_array($item) || is_object($item);
		$has_values = ! empty($item);
		return $is_a_collection && $has_values;
	}

}

if( ! function_exists('date_time') ){

	function date_time($string){
		if( !($str = strtotime($string)) ){ return '0000-00-00 00:00:00'; } //If invalid, null, etc, return 0's instead of 1969 date.
		return date('Y-m-d H:i:s', $str);
	}

}

if( ! function_exists('rss_date') ){

	function rss_date($string){
		return date('D, d M Y H:i:s T', strtotime($string));
	}

}

//Makes a remote request and returns only the body (no header info, no complex parameters). Mimics a wget call for webpages.
if( ! function_exists('simple_curl') ){

	function simple_curl($url, $send_cookies = FALSE){

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 7);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

		if( $send_cookies === TRUE ){
			$cookie_string = '';
			foreach( $_COOKIE as $key => $value ) {
				if( $key != 'PHPSESSID' ){
					//NOTE: + signs are not sent correctly by cURL. It converts them to spaces.
					//have to encode them first.
					$encoded = str_replace('+', '%2B', $key.'='.$value.';');
					$cookie_string .= $encoded;
				}
			}

			//By default + signs will be converted to spaces. Since Cookie
			//data is not URI data, we want to prevent this behavior by
			//encoding the + into a URI encoded entity.
			curl_setopt($ch, CURLOPT_COOKIE, str_replace('+', '%2B', $cookie_string) );
		}

		$data = curl_exec($ch);
		curl_close($ch);

		return $data;

	}

}

if( ! function_exists('return_html_list_from_array') ){

	function return_html_list_from_array($array_items, $ul_classes = '', $li_classes = ''){
		$i = 0;
		$list_items = array();
		foreach($array_items as $item){
			$li_classes += ($i % 2) ? ' even' : ' odd';
			$list_items[] = '<li class="'.$li_classes.'">'.$item.'</li>';
			$i++;
		}
		$list_items_string = implode("\n", $list_items);
		$list = '<ul class="'.$ul_classes.'">'.$list_items_string.'</ul>';
		return $list;
	}

}

if( ! function_exists('pagination') ){

	function pagination($count, $per, $current, $link = ''){

		//Create Variables
		$pagination = array();
		$current = ($current != NULL) ? $current : 1;
		$ceil = ceil($count / $per);
		if( $ceil == 1 ){ return FALSE; }
		$link = rtrim($link, '/');
		$percentages = array('25', '50', '75');
		$page_numbers = array('1', $ceil);

		//Get Numbers
		foreach( $percentages as $p ){
			$number = ceil($ceil * ($p / 100));
			if( !in_array($number, $page_numbers) ){ $page_numbers[] = $number; }
		}unset($p, $percentages, $number);

		//Create Numbers
		ksort($page_numbers);
		foreach( $page_numbers as $page ){
			$pagination[$page] = '<a href="'.$link.'/page/'.$page.'">'.$page.'</a>';
		}unset($page, $page_numbers);

		//Create Next Page
		if( count($pagination) > 1 && $current < $ceil ){
			$number = $current+1;
			$page = '<a href="'.$link.'/page/'.$number.'">&raquo;</a>';
			$pagination[] = $page;
		}unset($page, $number);

		//Create Previous Page
		if( count($pagination) > 1 && $current > 1 ){
			$number = $current-1;
			$page = '<a href="'.$link.'/page/'.$number.'">&laquo;</a>';
			$pagination[0] = $page;
		}unset($page, $number);

		//Create Current Page
		$pagination[$current] = '<span>'.$current.'</span>';

		ksort($pagination);
		return $pagination;

	}

}

if( ! function_exists('return_page_number') ){

	function return_page_number($uri){
		if( is_array($uri) ){
			foreach($uri as $key => $val ){
				if( $val == 'page' ){
					return ( is_numeric($uri[$key+1]) ) ? $uri[$key+1] : FALSE;
				}
			}unset($key, $val);
		}else{ return FALSE; }
	}

}

if( ! function_exists('return_time_past') ){

	function return_time_past($start, $end = NULL, $return_largest = TRUE, $trailing = ' ago'){
		if( $end == NULL ){ $end = time(); }
		$start = (!is_numeric($start)) ? strtotime($start) : $start;
		$end = (!is_numeric($end)) ? strtotime($end) : $end;
		$remainder = $end - $start;

		if( $remainder > 31536000 ){
			$years = floor($remainder / 31536000);
			$remainder = $remainder - (31536000 * $years);
			$years = ( $years > 1) ? $years.' years' : $years.' year';
		}else{ $years = NULL; }

		if( $remainder > 604800 ){
			$weeks = floor($remainder / 604800);
			$remainder = $remainder - (604800 * $weeks);
			$weeks = ( $weeks > 1 ) ? $weeks.' weeks' : $weeks.' week';
		}else{ $weeks = NULL; }

		if( $remainder > 86400 ){
			$days = floor($remainder / 86400);
			$remainder = $remainder - (86400 * $days);
			$days = ( $days > 1 ) ? $days.' days' : $days.' day';
		}else{ $days = NULL; }

		if( $remainder > 3600 ){
			$hours = floor($remainder / 3600);
			$remainder = $remainder - (3600 * $hours);
			$hours = ( $hours > 1 ) ? $hours.' hours' : $hours.' hour';
		}else{ $hours = NULL; }

		if( $remainder > 60 ){
			$minutes = floor($remainder / 60);
			$remainder = $remainder - (60 * $minutes);
			$minutes = ( $minutes > 1 ) ? $minutes.' minutes' : $minutes.' minute';
		}else{ $minutes = NULL; }

		if( $remainder > 0 ){
			$seconds = ( $remainder > 1 ) ? $remainder.' seconds' : $remainder.' second';
		}else{ $seconds = NULL; }

		if( $return_largest === TRUE ){
			if( $years != NULL ){ return $years . $trailing; }
			elseif( $weeks != NULL ){ return $weeks . $trailing; }
			elseif( $days != NULL ){ return $days . $trailing; }
			elseif( $hours != NULL ){ return $hours . $trailing; }
			elseif( $minutes != NULL ){ return $minutes . $trailing; }
			elseif( $seconds != NULL ){ return $seconds . $trailing; }
			else{ return FALSE; }
		}else{
			$return = array();
			if( $years != NULL ){ $return[] = $years; }
			if( $weeks != NULL ){ $return[] = $weeks; }
			if( $days != NULL ){ $return[] = $days; }
			if( $hours != NULL ){ $return[] = $hours; }
			if( $minutes != NULL ){ $return[] = $minutes; }
			if( $seconds != NULL ){ $return[] = $seconds; }
			if( count($return) > 1 ){ $return[(count($return)-1)] = 'and '.$return[(count($return)-1)]; }
			if( count($return) > 0 ){ return implode(', ', $return) . $trailing; }
			else{ return FALSE; }
		}

	}

}

if( ! function_exists('return_columns') ){

	function return_columns($array, $columns = 3){

		if( empty($array) ){ return $array; }

		//Set Variables
		$column = array();
		$total = count($array);
		$per_column = floor($total/$columns);
		$remainder = $total % $columns;

		$on_remainder = FALSE;
		$current = 0;
		$i = 1;

		//Create Array
		foreach($array as $key => $val ){

			$column[$current][$key] = $val;

			if( $i >= $per_column ){
				if( $remainder > 0  && $on_remainder === FALSE ){
					$i++;
					$remainder--;
					$on_remainder = TRUE;
				}else{
					$i = 1;
					$current++;
					$on_remainder = FALSE;
				}
			}else{ $i++; }

		}

		return $column;

	}

}

if( ! function_exists('create_rss') ){

	function create_rss($data){

		if( is_array($data) ){ $data = (object) $data; }
		if( !is_object($data) ){ return FALSE; }

		$items = array();
		foreach($data->items as $k=>$v){
			if( is_array($v) ){ $v = (object) $v; }
			$items[] = '<item>
							<title>'.$v->title.'</title>
							<link>'.$v->link.'</link>
							<author>'.APPLICATION_EMAIL.'</author>
							<description>'.htmlentities($v->description).'</description>
							<pubDate>'.$v->pubDate.'</pubDate>
							<guid>'.$v->guid.'</guid>
						</item>';
		}unset($k, $v);

		$application = ( defined(APPLICATION_NAME) ) ? APPLICATION_NAME : $_SERVER['SERVER_NAME'];
		$rss = '<?xml version="1.0"?>
				<rss version="2.0">
					<channel>
						<title>'.$data->title.'</title>
						<link>'.$data->link.'</link>
						<description>'.$data->description.'</description>
						<language>en-us</language>
						<copyright>'.date('Y', @mktime()).' '.$application.'</copyright>
						<pubDate>'.date('D, d M Y H:i:s T', @mktime()).'</pubDate>
						<lastBuildDate>'.date('D, d M Y H:i:s T', @mktime()).'</lastBuildDate>
						<docs>http://cyber.law.harvard.edu/rss/rss</docs>
						<generator>'.$application.' Website RSS Generator</generator>
						<managingEditor>'.APPLICATION_EMAIL.'</managingEditor>
						<webMaster>'.APPLICATION_EMAIL.'</webMaster>
						'.implode($items).'
					</channel>
				</rss>';

		return $rss;

	}

}

/**
 * Application Utilities
 */

if( ! function_exists('is_database_group') ){

	//Taken from system/database/DB.php
	function is_database_group($group){
		// Is the config file in the environment folder?
		if ( ! defined('ENVIRONMENT') OR ! file_exists($file_path = APPPATH.'config/'.ENVIRONMENT.'/database.php'))
		{
			if ( ! file_exists($file_path = APPPATH.'config/database.php'))
			{
				show_error('The configuration file database.php does not exist.');
			}
		}

		include($file_path);
		return isset($db[$group]);
	}

}

/* End of File commonfunctions_helper.php */
