<?php

class util {

	public static function podcast_url($feed_type = false) 
	{ 
		if ($feed_type == false)
		{ //return URL to feed page 
			return home_url() . '/feed/podcast'; 
		} 
		else 
		{ //return URL to itpc itunes-loaded feed page 
			$itunes_url = str_replace("http", "itpc", home_url() ); 
			return $itunes_url . '/feed/podcast'; 
		} 
	}
	
	//Get the filesize of a remote file, used for Podcast data
	public static function mp3_filesize( $url, $timeout = 10 ) {
		// Create a curl connection
		$getsize = curl_init();

		// Set the url we're requesting
		curl_setopt($getsize, CURLOPT_URL, $url);

		// Set a valid user agent
		curl_setopt($getsize, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11");

		// Don't output any response directly to the browser
		curl_setopt($getsize, CURLOPT_RETURNTRANSFER, true);

		// Don't return the header (we'll use curl_getinfo();
		curl_setopt($getsize, CURLOPT_HEADER, false);

		// Don't download the body content
		curl_setopt($getsize, CURLOPT_NOBODY, true);

		// Follow location headers
		curl_setopt($getsize, CURLOPT_FOLLOWLOCATION, true);

		// Set the timeout (in seconds)
		curl_setopt($getsize, CURLOPT_TIMEOUT, $timeout);

		// Run the curl functions to process the request
		$getsize_store = curl_exec($getsize);
		$getsize_error = curl_error($getsize);
		$getsize_info = curl_getinfo($getsize);

		// Close the connection
		curl_close($getsize); // Print the file size in bytes

		return $getsize_info['download_content_length'];
	}

	public static function youtube( $media_youtube = '', $type = 'url' ) 
	{
	    if ( $media_youtube ) 
	    {
	      switch ( $media_youtube ) 
	      {
	        case 'iframe':
	          return 'https://www.youtube.com/embed/'. $media_youtube;
	        case 'embed':
	          return 'https://www.youtube.com/v/'. $media_youtube;
	        case 'short':
	          return 'https://youtu.be/'. $media_youtube;
	        case 'url':
	        default:
	          return 'https://www.youtube.com/watch?v='. $media_youtube;
	      }
	    }
	}

	/* @method			teaser
	 * @description		creater a teaser from string
	 * @param			string		$string
	 * @param			int			$length		the character length of the teaser
	 * @return			string
	 * @author			imss team
	 * */
	public static function teaser($string, $length)
	{
		return substr($string, 0, $length) . (($length < strlen($string)) ? " .." : null);
	}
	
	/* @method			printr
	 * @description		wrap output of the print_r() function in <PRE> html tags to enable easier debug
	 * 					selects either the last posted value or the db value (selected value)
	 * @author			imss team
	 * */
	public static function printr($var)
	{
		echo "<pre>" . print_r($var, 1) . "</pre>";
	}

	public static function bootstrap_menu($menuName, $dropDownOption = "click")
	{
	    $menu = $menuName; //Nav menu name
	    $level = 0;
	    $last_title = "";
	    $last_url = "";
	    $objectID_stack = array();
	    $objectIDStackTop = 0;
	    $output = "";
		$active = False;
	    $items = (wp_get_nav_menu_items($menu)) ? wp_get_nav_menu_items($menu) : array(); // Get nav menu items list
		global $post;
	    foreach ($items as $list)
	    {
	        if(isset($list->menu_item_parent) && $list->menu_item_parent == "0")
	        {
	            while(count($objectID_stack))
	            {
	                array_pop($objectID_stack);
	            }
	            if($level == 1)
	            {
					if($active)
						$class = " class=\"active\"";
					else
						$class = "";
					$output = $output."<li".$class."><a href=\"".$last_url."\">".$last_title."</a></li>";
	            }
	            if($level == 3)
	            {
					if($active)
						$class = " class=\"active\"";
					else
						$class = "";
	                $output = $output."<li".$class."><a href=\"".$last_url."\">".$last_title."</a></li></ul></li></ul></li>";
	            }
	            if($level == 2)
	            {
					if($active)
						$class = " class=\"active\"";
					else
						$class = "";
	                $output = $output."<li".$class."><a href=\"".$last_url."\">".$last_title."</a></li></ul></li>";
	            }
	            $level = 1 ;
	            array_push($objectID_stack, $list->object_id);
	            $last_title = $list->title;
	            $last_url = $list->url;
				if(isset($post->ID) && $post->ID == $list->object_id)
					$active = True;
				else
					$active = False;
	        }
	        else
	        {
	            $stackTop = count($objectID_stack)-1;
	            if($list->menu_item_parent == $objectID_stack[$stackTop])
	            {
	                if($level == 1)
	                {
					    $class = ($active) ? " active" : "";

	                    $output =  ($dropDownOption == "click") ? 
	                        $output."<li class=\"dropdown".$class."\"><a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\">".$last_title."<b class=\"caret\"></b></a><ul class=\"dropdown-menu\">" :
	                        $output."<li class=\"dropdown".$class."\"><a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"".$last_url."\">".$last_title."<b class=\"caret\"></b></a><ul class=\"dropdown-menu\">"
	                    ;
	                    
	                    $last_title = $list->title;
	                    $last_url = $list->url;
						$active = (isset($post->ID) && $post->ID == $list->object_id) ? True : False;
	                    
	                    $level = 2;
	                    array_push($objectID_stack, $list->object_id);
	                }
	                else if ($level == 2)
	                {
	                    $class = ($active) ? " active" : "";

	                    $output = ($dropDownOption == "click") ? 
	                        $output."<li class=\"dropdown-submenu".$class."\"><a href=\"#\">".$last_title."</a><ul class=\"dropdown-menu sub-menu\">" :
	                        $output."<li class=\"dropdown-submenu".$class."\"><a href=\"".$last_url."\">".$last_title."</a><ul class=\"dropdown-menu sub-menu\">"
	                    ;
	                    
	                    $last_title = $list->title;
	                    $last_url = $list->url;
	                    $active = (isset($post->ID) && $post->ID == $list->object_id) ? True : False;
	                    
	                    $level = 3;
	                }
	                else if($level == 3)
	                {
	                    $class = ($active) ? " class=\"active\"" : "";

	                    $output = $output."<li".$class."><a href=\"".$last_url."\">".$last_title."</a></li>";
	                    $last_title = $list->title;
	                    $last_url = $list->url;
	                    $active = (isset($post->ID) && $post->ID == $list->object_id) ? True : False;
	                    
	                    $level = 3;
	                }       
	            }
	            else
	            {
	                if($level == 2)
	                {
	                    $class = ($active) ? " class=\"active\"" : "";

	                    array_pop($objectID_stack);
	                    $output = $output."<li".$class."><a href=\"".$last_url."\">".$last_title."</a></li>";
	                    $last_title = $list->title;
	                    $last_url = $list->url;
						
	                    $active = (isset($post->ID) && $post->ID == $list->object_id) ? True : False;

	                    $level = 2;
	                    array_push($objectID_stack, $list->object_id);
	                }
	                if($level == 3)
	                {
						$class = ($active) ? " class=\"active\"" : "";

	                    array_pop($objectID_stack);
	                    $output = $output."<li".$class."><a href=\"".$last_url."\">".$last_title."</a></li></ul></li>";
	                    $last_title = $list->title;
	                    $last_url = $list->url;
						$active = (isset($post->ID) && $post->ID == $list->object_id) ? True : False;

	                    $level = 2;
	                    array_push($objectID_stack, $list->object_id);
	                }
	                
	            }
	        }
	    }
	    
	    $class = ($active) ? " class=\"active\"" : "";

	    if($level == 1) //If is parent and not printed.
	        $output = $output."<li".$class."><a href=\"".$last_url."\">".$last_title."</a></li>";
	    else if($level == 2) //If is sub and not printed.
	        $output = $output."<li".$class."><a href=\"".$last_url."\">".$last_title."</a></li></ul></li>";
	    else if($level == 3) //If is sub of sub and not printed.
	        $output = $output."<li".$class."><a href=\"".$last_url."\">".$last_title."</a></li></ul></li></ul></li>";
	    return $output;
	}


	public static function prepare_date($format = '', $date = '')
	{
		return date($format, strtotime($date));
	}

	public static function html_date( $date = '' ) 
	{
		$out = '<div class="event-weekday">'.self::prepare_date( 'D', $date ).'</div>';
		$out .= '<div class="event-day">'.self::prepare_date( 'd', $date ).'</div>';
		$out .= '<div class="event-month">'.self::prepare_date( 'M', $date ).'</div>';
		$out .= '<div class="event-year">'.self::prepare_date( 'Y', $date ).'</div>';
		return $out;
	}
}