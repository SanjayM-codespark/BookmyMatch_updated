<?php
use App\Libraries\Authentication;
if(! function_exists('_render'))
{
	function _render(string $view, array $data = [])
	{
		echo view($view, $data);
	}
}

if (! function_exists('flash_errors'))
{
    function flash_errors(): array
    {
        if (! session()->has('errors')) return [];
        $errors = session('errors');
        session()->remove('errors');
        return (array) $errors;
    }
}

if (! function_exists('safe_set_value'))
{
	function safe_set_value(string $field, string $default = ''): string
	{
		$v = set_value($field, $default);
		return is_array($v) ? $default : esc($v);
	}
}
/***************** Auth ******************/
if (! function_exists('logged_in'))
{
	/**
	 * Checks to see if the user is logged in.
	 *
	 * @return bool
	 */
	function logged_in()
	{
		$authenticate = new Authentication();
		return $authenticate->check();
	}
}

if (! function_exists('user'))
{
	/**
	 * Returns the User instance for the current logged in user.
	 *
	 * @return User|null
	 */
	function user()
	{
		$authenticate = new Authentication();
		$authenticate->check();
		return $authenticate->user();
	}
}

if (! function_exists('user_id'))
{
	/**
	 * Returns the User ID for the current logged in user.
	 *
	 * @return int|null
	 */
	function user_id()
	{
		$authenticate = new Authentication();
		$authenticate->check();
		return $authenticate->id();
	}
}
/************** common *********************/
if ( ! function_exists('print_array'))
{
	
	function print_array($array)
	{
		
		echo '<pre>'; print_r($array); echo '</pre>';
		
	}
}


//Unique code
if ( ! function_exists('get_unique_code'))
{
	
	function get_unique_code($type ,$len)
	{
		helper('text');
        return random_string($type, $len);
	}
}


if ( ! function_exists('create_folder'))
{
	
	function create_folder($path)
	{
		if (!is_dir($path)) {
			$oldmask = umask(0);	
		    mkdir($path, 0777, TRUE);
		    umask($oldmask);
			return $path;
		}
		return FALSE;
		
	}
}

if ( ! function_exists('xss_clean'))
{
    function xss_clean($data)
    {
        // Fix &entity\n;
        $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do
        {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        }
        while ($old_data !== $data);

        // we are done...
        return $data;
    }
}


if ( ! function_exists('getTimeAgo'))
{
    function getTimeAgo($timestamp){
           
        $time_ago        = strtotime($timestamp);
        $current_time = strtotime(date("Y-m-d H:i:s")); 
        // $current_time    = time();
        $time_difference = $current_time - $time_ago;
        $seconds         = $time_difference;
        
        $minutes = round($seconds / 60); // value 60 is seconds  
        $hours   = round($seconds / 3600); //value 3600 is 60 minutes * 60 sec  
        $days    = round($seconds / 86400); //86400 = 24 * 60 * 60;  
        $weeks   = round($seconds / 604800); // 7*24*60*60;  
        $months  = round($seconds / 2629440); //((365+365+365+365+366)/5/12)*24*60*60  
        $years   = round($seconds / 31553280); //(365+365+365+365+366)/5 * 24 * 60 * 60
               
        if ($seconds <= 60){
    
        return "just now";
    
        } else if ($minutes <= 60){
    
        if ($minutes == 1){
    
            return "one minute ago";
    
        } else {
    
            return "$minutes minutes ago";
    
        }
    
        } else if ($hours <= 24){
    
        if ($hours == 1){
    
            return "an hour ago";
    
        } else {
    
            return "$hours hrs ago";
    
        }
    
        } else if ($days <= 7){
    
        if ($days == 1){
    
            return "yesterday";
    
        } else {
    
            return "$days days ago";
    
        }
    
        } else if ($weeks <= 4.3){
    
        if ($weeks == 1){
    
            return "a week ago";
    
        } else {
    
            return "$weeks weeks ago";
    
        }
    
        } else if ($months <= 12){
    
        if ($months == 1){
    
            return "a month ago";
    
        } else {
    
            return "$months months ago";
    
        }
    
        } else {
        
        if ($years == 1){
    
            return "one year ago";
    
        } else {
    
            return "$years years ago";
    
        }
        }
    }

}

if ( ! function_exists('bmm_round_name'))
{
    /**
     * Knockout stage label for round $r of $R in a bracket.
     * Badminton: rounds -> Semi-Final -> Final (no Quarter-Final)
     * Cricket  : Group Stage -> Semi-Final -> Final (no Quarter-Final)
     */
    function bmm_round_name(int $r, int $R, string $sport = ''): string
    {
        if ($r === $R) {
            return 'Final';
        }

        if ($r === $R - 1) {
            return 'Semi-Final';
        }

        $sport = strtolower($sport);

        if (strpos($sport, 'badminton') !== false) {
            return 'Round ' . $r;
        }

        if (strpos($sport, 'cricket') !== false) {
            return ($r === 1) ? 'Group Stage' : 'Round ' . $r;
        }

        return ($r === $R - 2) ? 'Quarter-Final' : 'Round ' . $r;
    }
}
