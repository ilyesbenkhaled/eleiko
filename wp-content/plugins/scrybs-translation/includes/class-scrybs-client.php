<?php
namespace Scrybs;
use ScrybsSDP;
class Scrybs_Client
{
	protected $api_key;
	
	function __construct($apikey, $websitekey, $autotranslate, $translated_urls) {
		$this->api_key = $apikey;
		$this->website_key = $websitekey;
		$this->auto_translate = $autotranslate;
		$this->translated_urls = $translated_urls;
		$this->home_dir = $this->getClientHomeDirectory();
				
		// HASH FILE
		if(!file_exists(SCRYBS_CONTENT_HASH)){
				$fp = fopen(SCRYBS_CONTENT_HASH,"w"); 
			    fwrite($fp,""); 
			    fclose($fp);
		}
		$hash_json = file_get_contents(SCRYBS_CONTENT_HASH);
		$hashes = json_decode($hash_json,true);
		if(!empty($hashes)){
			$this->hashes = $hashes;
		}else{
			$this->hashes = array();
		}
		
		if( get_option( 'scrybs_url_exclusion' ) ) {
			$this->excluded_url_list = explode(',', get_option('scrybs_url_exclusion'));
		}else{
			$this->excluded_url_list = array();
		}
		
        if ($this->api_key == null || mb_strlen($this->api_key) == 0) {
            return null;
        }
	}
	
	/* This function was forked from Weglot */
	public function translateDomFromTo($dom,$source,$target,$request_url) {
		$html = ScrybsSDP\str_get_html($dom, true, true, DEFAULT_TARGET_CHARSET, false, DEFAULT_BR_TEXT, DEFAULT_SPAN_TEXT);

		
		$exceptions = array('.author', '.post_author_link', '.comment', '.comment-author-link', 'span.amount', 'span.price', '#wpadminbar', '.sku_wrapper', '.sku');
		foreach ($exceptions as $exception) {
			foreach ($html->find($exception) as $k => $row) 
			{ 
				$attribute = "notranslate";
				$row->$attribute="";
			}
		}
		
		$words = array();
		$nodes  = array();
		
		$stop_words = array('&nbsp;', '.', '*', ')', '(', 'WordPress.org', 'Wordpress', '/', ',', '?', '!');
		
		foreach ($html->find('text') as $k => $row) 
		{
			if($this->full_trim($row->outertext)!="" && $row->parent()->tag!="script" && $row->parent()->tag!="style" && !is_numeric($this->full_trim($row->outertext)) && !preg_match('/^\d+%$/',$this->full_trim($row->outertext)) 
				&& !$this->hasAncestorAttribute($row,'notranslate') && !in_array($this->full_trim($row->outertext), $stop_words) )
			{ 
				if(strpos($row->outertext,'[vc_') !== false) {
				}else {
					array_push($words,array("t"=>"1","w"=>$row->outertext)); 
					array_push($nodes,array('node'=>$row,'type'=>'text'));
				}
			}
		}
		
		foreach ($html->find('input[type=\'submit\'],input[type=\'button\']') as $k => $row) 
		{
			if($this->full_trim($row->value)!="" && !is_numeric($this->full_trim($row->value)) && !preg_match('/^\d+%$/',$this->full_trim($row->value)) && !$this->hasAncestorAttribute($row,'notranslate') && !in_array($this->full_trim($row->value), $stop_words))
			{
				array_push($words,array("t"=>"2","w"=>html_entity_decode($row->value)));
				array_push($nodes,array('node'=>$row,'type'=>'submit'));
			}
		}
		
		foreach ($html->find('input[type=\'text\'],input[type=\'password\'],input[type=\'search\'],input[type=\'email\'],input:not([type]),textarea') as $k => $row) 
		{
			if($this->full_trim($row->placeholder)!=""
				&& !is_numeric($this->full_trim($row->placeholder)) && !preg_match('/^\d+%$/',$this->full_trim($row->placeholder)) 
				&& !$this->hasAncestorAttribute($row,'notranslate')
				&& !in_array($this->full_trim($row->placeholder), $stop_words))
			{
				array_push($words,array("t"=>"3","w"=>html_entity_decode($row->placeholder)));
				array_push($nodes,array('node'=>$row,'type'=>'placeholder'));
			}
		}
		
		foreach ($html->find('meta[name="description"],meta[property="og:title"],meta[property="og:description"],meta[property="og:site_name"],meta[name="twitter:title"],meta[name="twitter:description"]') as $k => $row) 
		{
			if($this->full_trim($row->content)!="" && !is_numeric($this->full_trim($row->content)) && !preg_match('/^\d+%$/',$this->full_trim($row->content)) && !$this->hasAncestorAttribute($row,'notranslate') && !in_array($this->full_trim($row->content), $stop_words))
			{
				array_push($words,array("t"=>"4","w"=>$row->content));
				array_push($nodes,array('node'=>$row,'type'=>'meta_desc'));
			}
		}
		
		foreach ($html->find('img') as $k => $row) 
		{
			if($this->full_trim($row->alt)!="" && !$this->hasAncestorAttribute($row,'notranslate') && !in_array($this->full_trim($row->alt), $stop_words))
			{
				array_push($words,array("t"=>"5","w"=>$row->alt));
				array_push($nodes,array('node'=>$row,'type'=>'img_alt'));
			}
		}
		
		foreach ($html->find('a') as $k => $row) 
		{
			if($this->full_trim($row->href)!="" && substr($this->full_trim($row->href),-4)==".pdf" && !$this->hasAncestorAttribute($row,'notranslate') && !in_array($this->full_trim($row->href), $stop_words))
			{
				array_push($words,array("t"=>"6","w"=>$row->href));
				array_push($nodes,array('node'=>$row,'type'=>'a_pdf'));
			}
			if($this->full_trim($row->title)!="" && !$this->hasAncestorAttribute($row,'notranslate') && !in_array($this->full_trim($row->title), $stop_words))
			{
				array_push($words,array("t"=>"7","w"=>$row->title));
				array_push($nodes,array('node'=>$row,'type'=>'a_title'));
			}
		}
		
		$title = "";
		foreach ($html->find('title') as $k => $row) {
			$title = $row->innertext;
		}
		
		// HASH CONTENT
		$hashes = $this->hashes;
		if (function_exists('array_column')) {
			$w_s = array_column($words, 'w');
		}else{
			$w_s = $words;
		}
		$curr_content_hash = md5(serialize($w_s));
        	
		if(array_key_exists($request_url, $hashes[$target])){
			$old_hash = $hashes[$target][$request_url];
			if($old_hash == $curr_content_hash){
				$is_new_content = 0;
			}else{
				$is_new_content = 1;
				$this->store_update_hash_content($request_url, $target, $curr_content_hash);
			}
		}else{
			$is_new_content = 1;
			$this->store_update_hash_content($request_url, $target, $curr_content_hash);
		}
		
		if($is_new_content > 0){
			$action = 'page_translation';
			$parameters = array("api_key"=>$this->api_key, "website_key"=>$this->website_key, "action"=>$action, "auto_translate"=>$this->auto_translate, "source"=>$source,"target"=>$target,"request_url"=>$request_url,"title"=>$title,"words"=>$words);
			$results = doRequest(SCRYBS_API_URL,$parameters);
		}else{
			$cdnurl = 'https://d1zbkuig56xub3.cloudfront.net/translations/'.$this->api_key.'/'.$this->website_key.'/'.$target.'.scrybs?v='.$curr_content_hash;
			$req_return = doRequest($cdnurl, array());
			$results = $this->translateArrayFromTo( $words, $req_return );  
		}
				
		if(!isset($results) || $results == ''){
			$cdnurl = 'https://d1zbkuig56xub3.cloudfront.net/translations/'.$this->api_key.'/'.$this->website_key.'/'.$target.'.scrybs?v='.$curr_content_hash;
			$req_return = doRequest($cdnurl);
			$results = $this->translateArrayFromTo( $words, $req_return );
		}
				
		$json = json_decode($results, true);		
        
        //file_put_contents( SCRYBS_PLUGIN_PATH . '/php.log', $results, FILE_APPEND); //for debug purposes
		
		if(json_last_error() == JSON_ERROR_NONE) 
		{
			if(isset($json['success']) && ($json['success']==0 || $json['success']==1)) {
				if($json['success']==1) {
					if(isset($json['to_words'])) {    
						$translated_words = $json['to_words'];
						//file_put_contents( SCRYBS_PLUGIN_PATH . '/php.log', count($nodes).' / '.count($translated_words), FILE_APPEND);
						if(count($nodes)>0 && count($translated_words)>0) {
							for($i=0;$i<count($nodes);$i++) {
								if($nodes[$i]['type']=='text') {
									$nodes[$i]['node']->outertext = $translated_words[$i];
								}
								if($nodes[$i]['type']=='submit') {
									$nodes[$i]['node']->setAttribute('value',$translated_words[$i]);
								}
								if($nodes[$i]['type']=='placeholder') {
									$nodes[$i]['node']->setAttribute('placeholder',$translated_words[$i]);
								}
								if($nodes[$i]['type']=='meta_desc') {
									$nodes[$i]['node']->content =  $translated_words[$i];
								}
								if($nodes[$i]['type']=='img_alt') {
									$nodes[$i]['node']->alt =  $translated_words[$i];
								}
								if($nodes[$i]['type']=='a_pdf') {
									$nodes[$i]['node']->href =  $translated_words[$i];
								}
								if($nodes[$i]['type']=='a_title') {
									$nodes[$i]['node']->title =  $translated_words[$i];
								}
							}
							if(isLanguageRTL($target)){
								$html->find('html', 0)->setAttribute('dir', 'rtl');
							}
							
							$translated_urls = $this->translated_urls;
							if(array_key_exists($target, $translated_urls)){
								$target_targeted_urls = $translated_urls[$target];
							}else{
								$target_targeted_urls = array();
							}
														
							foreach ($html->find('a') as $element) {
								if ( !$this->hasAncestorAttribute($element,'notranslate') ) {								
									if ( substr($element->href, 0, 1) != '#' && 
										!scrybs_endsWith($element->href,'.jpg') && 
										!scrybs_endsWith($element->href,'.pdf') && 
										!scrybs_endsWith($element->href,'.jpeg') && 
										!scrybs_endsWith($element->href,'.png') && 
										!scrybs_endsWith($element->href,'.gif') &&
										!scrybs_endsWith($element->href,'.svg') &&
										!scrybs_endsWith($element->href,'.css') &&
										!scrybs_endsWith($element->href,'.js') &&
										!strpos($element->href,'wp-login')
									) {
								
										$finalurl = $this->treatLinkUrl($element->href, $source, $target, $target_targeted_urls);
										if($finalurl !== false){
											$element->href = $finalurl;
										}
														
									}
								}
							}
							
							foreach ($html->find('head link[rel=canonical]') as $element) {
								if ( (substr($element->href, 0, 1) !== '#') && !scrybs_endsWith($element->href,'.jpg') && !scrybs_endsWith($element->href,'.pdf') && !scrybs_endsWith($element->href,'.jpeg') && !scrybs_endsWith($element->href,'.png') && strpos($element->href,'wp-login') === false ) {
								
									$finalurl = $this->treatLinkUrl($element->href, $source, $target, $target_targeted_urls);
									if($finalurl !== false){
										$element->href = $finalurl;
									}
																	
								}
							}
							
							foreach ($html->find('head meta[property=og:url]') as $element) {
								if ( (substr($element->content, 0, 1) !== '#') && !scrybs_endsWith($element->content,'.jpg') && !scrybs_endsWith($element->content,'.pdf') && !scrybs_endsWith($element->content,'.jpeg') && !scrybs_endsWith($element->content,'.png') && strpos($element->content,'wp-login') === false ) {
									$finalurl = $this->treatLinkUrl($element->content, $source, $target, $target_targeted_urls);
									if($finalurl !== false){
										$element->content = $finalurl;
									}								
								}
							}
							
							foreach ($html->find('form') as $element) {
								if ( (substr($element->action, 0, 1) !== '#') && !scrybs_endsWith($element->action,'wp-comments-post.php') && strpos($element->content,'wp-login') === false ) {
									$finalurl = $this->treatLinkUrl($element->action, $source, $target, $target_targeted_urls);
									if($finalurl !== false){
										$element->action = $finalurl;
									}
								}
							}
							
							foreach ($html->find('p') as $element) {
								$element->outertext = preg_replace('/\s?<(a|strong|b|em|i|span)(?=[\s>])(?:[^>=]|=(?:\'[^\']*\'|"[^"]*"|[^\'"\s]*))*\s?\/?>.*?<\/\1>(?=[\s,.;?!<]|(?=.*?(\s)))/', " $0$2", $element->outertext);
							}
							foreach ($html->find('li') as $element) {
								$element->outertext = preg_replace('/\s?<(a|strong|b|em|i|span)(?=[\s>])(?:[^>=]|=(?:\'[^\']*\'|"[^"]*"|[^\'"\s]*))*\s?\/?>.*?<\/\1>(?=[\s,.;?!<]|(?=.*?(\s)))/', " $0$2", $element->outertext);
							}

							$returnResults['header'] = "success";							
							$returnResults['body'] = $html->save();
							return $returnResults;
							
						}else{
							global $wp_query;
						    $wp_query->set_404();
						    status_header(404);
							return $returnResults;
						}
					}
				}else{
				    $returnResults['header'] = "failed";
				    $returnResults['body'] = "Unknown error with Scrybs Api (0002)";
					return $returnResults;
				}
			}
		}// End
	}
	
	public function treatLinkUrl($thisurl, $source, $target, $target_targeted_urls){
	
		if ( (substr($thisurl, 0, 1) !== '#') && !scrybs_endsWith($thisurl,'.jpg') && !scrybs_endsWith($thisurl,'.pdf') && !scrybs_endsWith($thisurl,'.jpeg') && !scrybs_endsWith($thisurl,'.gif') && !scrybs_endsWith($thisurl,'.png') && !scrybs_endsWith($thisurl,'.svg') && !strpos($thisurl,'wp-login')) {
			if(is_internal_link($thisurl)){
				if(strpos($thisurl,"/".$target."/") === false){
						
						if(strpos($thisurl,"/") == '0'){ // URL STARTS WITH SLASH
																
							if(strpos($thisurl,"#")){
								$urlnoelmts = explode('#', $thisurl);
								$urltomatch = $urlnoelmts[0];
								$urlend		= '#'.$urlnoelmts[1];
							}else if(strpos($thisurl,"?")){
								$urlnoelmts = explode('?', $thisurl);
								$urltomatch = $urlnoelmts[0];
								$urlend		= '?'.$urlnoelmts[1];
							}else{
								$urltomatch = $thisurl;
								$urlend		= '';
							}
							$translated_url = array_search($urltomatch, $target_targeted_urls);
							if(!empty($translated_url)){
									return '/'.$target.$translated_url.$urlend;
							}else{
								if( !$this->is_excluded_url_link( '/'.$target.$thisurl ) ){
									return '/'.$target.$thisurl;
								}
							}
												
						}else{
							$parsed_url = parse_url($thisurl);
							$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : ''; 
							$host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
							$port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : ''; 
							$path     = isset($parsed_url['path']) ? $parsed_url['path'] : '/'; 
							$query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : ''; 
							$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
							$home_dir = $this->home_dir;
							
							$slashes = explode('/', $path);
			
							if( scrybs_endsWith($home_dir, $slashes[1]) ){
								$f = '/'.$slashes[1].'/';
								$r = '/';
								$path = str_replace($f, $r, $path);
							}
							
							if(!empty($target_targeted_urls)){										
								$translated_url = array_search($path, $target_targeted_urls);
								if(!empty($translated_url)){
									return $home_dir.'/'.$target.$translated_url.$query.$fragment;
								}
							}
							
							if(strpos($thisurl, '/'.$source.'/') !== false) {
								$urltmp = str_replace('/'.$source.'/', '/', $thisurl);
								$finalurl = $this->addLangtoUrl($urltmp, $target);
								return $finalurl;
							}else{
								$finalurl = $this->addLangtoUrl($thisurl, $target);
								return $finalurl;
							}
							
						}
											
				}// DESTINATION URL
			}// IS INTERNAL LINK					
		}
		return false;
	}
	
	public function getAndUpdateUrlList($source, $languages){
		$action = 'get_translated_url';
		$parameters = array("api_key"=>$this->api_key, "website_key"=>$this->website_key, "action"=>$action, "source"=>$source,"languages"=>$languages);
		$results = doRequest(SCRYBS_API_URL,$parameters);
		$json = json_decode($results,true); 
		if(json_last_error() == JSON_ERROR_NONE){
			if(isset($json['success']) && ($json['success']==0 || $json['success']==1)) {
				if($json['success']==1) {
					if(isset($json['url_list'])) {
						$url_list = array();
						$url_list = $json['url_list'];
						
						$newjson = json_encode($url_list, JSON_UNESCAPED_UNICODE);
						file_put_contents(SCRYBS_PLUGIN_PATH.'/res/urllist.json', $newjson);
						return $url_list;
					}else{
						return false;
					}
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}
	
	/* This function was forked from Weglot */
	public function hasAncestorAttribute($node, $attribute) {
		$currentNode = $node;
		while($currentNode->parent() && $currentNode->parent()->tag!="html") {
			if(isset($currentNode->parent()->$attribute))
				return true;
			else
				$currentNode = $currentNode->parent();
		}
		return false;
	}
	
	/* This function was forked from Weglot */
	public function addLangtoUrl($url, $l) {
			$parsed_url = parse_url($url);
			$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : ''; 
			$host     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
			$port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : ''; 
			$user     = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
			$pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : ''; 
			$pass     = ($user || $pass) ? "$pass@" : ''; 
			$path     = isset($parsed_url['path']) ? $parsed_url['path'] : '/'; 
			$query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : ''; 
			$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : ''; 
			$home_dir = $this->home_dir;
						
			$slashes = explode('/', $path);
			
			if( scrybs_endsWith($home_dir, $slashes[1]) ){
				$f = '/'.$slashes[1].'/';
				$r = '/';
				$path = str_replace($f, $r, $path);
			}
			
			if($l=='') { 
				return $url;
			}else {
				if( !$this->is_excluded_url_link( $path ) && $path != '/wp-admin/'){
					$newurl = $home_dir.'/'.$l.$path.$query.$fragment;
					return $newurl;
					//return (strlen($path)>2 && substr($path,0,4)=="/$l/") ? "$scheme$user$pass$host$port$path$query$fragment":"$scheme$user$pass$host$port/$l$path$query$fragment";
				}else{
					return $url;
				}
			}
	}
	
	public function translateArrayFromTo( $words, $trfile ){
		$node = array();
		$trnode = array();
		$tocheck = array();
		$duplicates = array();
		$alreadytranslated = array();
		
		$node = json_decode($trfile, true);
		
		foreach($node as $key => $value){
    		foreach($value as $val => $vali){
    			$word =  $vali[0][0];
    			if( !in_array($word, $tocheck) ){				
    				$tocheck[] = $word;
    			}else{
    				$duplicates[] = $key.'-#-'.$val;
    			}
    		}
    	}
    	
    	if(count($duplicates)>0){
    		foreach($duplicates as $key => $index){
    			$tmp = explode('-#-', $index);
    			$node[$tmp[0]][$tmp[1]] = 'null';
			}
		}
						
		foreach($words as $key => $word){
			$source_word = $word['w'];
			
			$akeys = array_keys($node);
			foreach($akeys as $akey){
				for($i=0; $i<count($node[$akey]); $i++){

                    // Allways declare variables even when they are empty, no need to fill up the log files with notices                    
                    $isNodeOneEmpty = isset($node[$akey][$i][1][0]) ? $node[$akey][$i][1][0] : "";
                    $isNodeTwoEmpty = isset($node[$akey][$i][0][0]) ? $node[$akey][$i][0][0] : "";
                    
					if( $isNodeOneEmpty != "null" ) {
						
							if( $source_word == $isNodeTwoEmpty ){
								
									if($isNodeOneEmpty != ""){
										$trnode[] = $isNodeOneEmpty;
									}else if($isNodeOneEmpty == ''){
										$trnode[] = $source_word;
									} else {
                                        $trnode[] = $source_word; // if everything fails then fill it with the source word no need for blancs @TODO: keep this under watch
                                    }
								
							}
							
					}
				}
			}
		}
		if(!empty($trnode)){
			$jsonreturn['to_words'] = $trnode;
			$jsonreturn['success'] = 1;
			$jsonreturn['return'] = 'true';
			$jsonreturn['function'] = 'translateArrayFromTo';
			return json_encode($jsonreturn, JSON_UNESCAPED_UNICODE);
		}else{
			return false;
		}
	}
	
	public function translateArrayFromToCheck( $words, $trfile ){
		$node = array();
		$trnode = array();
		
		$node = json_decode($trfile, true);
						
		foreach($words as $key => $word){
			$source_word = $word['w'];
			
			$akeys = array_keys($node);
			foreach($akeys as $akey){
				for($i=0; $i<count($node[$akey]); $i++){

                    // Allways declare variables even when they are empty, no need to fill up the log files with notices    
                    $isNodeOneEmpty = isset($node[$akey][$i][1][0]) ? $node[$akey][$i][1][0] : "";
                    $isNodeTwoEmpty = isset($node[$akey][$i][0][0]) ? $node[$akey][$i][0][0] : "";
                                    
					if( $isNodeOneEmpty != "null" ) {
						
							if( $source_word == $isNodeTwoEmpty ){
																	
									if($isNodeOneEmpty != ""){
										$trnode[] = $i.' / '.$isNodeOneEmpty;
									}else if($isNodeOneEmpty == ''){
										$trnode[] = $i.' / '.$source_word;
									} else {
                                        $trnode[] = $i.' / '.$source_word; // if everything fails then fill it with the source word no need for blancs @TODO: keep this under watch
                                    }
								
							}
							
					}
				}
			}
		}
		if(!empty($trnode)){
			$jsonreturn['to_words'] = $trnode;
			$jsonreturn['success'] = 1;
			$jsonreturn['return'] = 'true';  
			$jsonreturn['function'] = 'translateArrayFromToCheck';
			return json_encode($jsonreturn, JSON_UNESCAPED_UNICODE);
		}else{
			return false;
		}
	}
	public function store_update_hash_content($url, $lang, $hash) {
		$current_hashes = $this->hashes;
		$current_hashes[$lang][$url] = $hash;
		$hashes_json = json_encode( $current_hashes, JSON_UNESCAPED_UNICODE );
		file_put_contents(SCRYBS_CONTENT_HASH, $hashes_json);
	}
	
	public function is_excluded_url_link( $requesturl ) {
	    $excluded_urls = $this->excluded_url_list;
	    $folders_to_exclude = array();
	    $urls_to_exclude = array();
	    if(!empty($excluded_urls)){
		    foreach($excluded_urls as $url){
		    	$url = trim($url);
			    if (strpos($url, '*') !== false) {
			    	$url = str_replace('*', '', $url);
				    $folders_to_exclude[] = $url;
				}else{
					$urls_to_exclude[] = $url;
				}
		    }
		    // URLS
		    if(in_array($requesturl, $urls_to_exclude)){
			    return true;
		    }
		    if (strpos($url, '?replytocom=') !== false) {
			    return true;
			}
		    // FOLDERS
		    $url_folders = explode('/', $requesturl);
		    foreach($url_folders as $url_folder){
		    	$foldr = '/'.$url_folder.'/';
				if($foldr!='//'){
					if(in_array($foldr, $folders_to_exclude)){
					    return true;
				    }
				}
			}
			$dirname = dirname($requesturl).'/';
			if(in_array($dirname, $folders_to_exclude)){
				return true;
			}
		    return false;
		 }else{
			return false;
		 }
	}
	
	public function getClientHomeDirectory() {
		$opt_siteurl = trim(get_option("siteurl"),'/');
		$opt_home = trim(get_option("home"),'/');
		if($opt_siteurl!="" && $opt_home!="") {
			if( (substr($opt_home,0,7) == "http://" && strpos(substr($opt_home,7),'/') !== false) ||  (substr($opt_home,0,8) == "https://" && strpos(substr($opt_home,8),'/') !== false) ) {
				return $opt_home;
			}
		}
		return null;
	}
	
	function full_trim($word) {
		return trim($word," \t\n\r\0\x0B\xA0 ");
	}
}
?>