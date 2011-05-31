<?php

$to_replace = array();

class TemplateOptimizer extends StdClass
{
	
	function template($file)
	{
		return $file;
	}

	function display($file, $compress)
	{
		global $to_replace;
		$to_replace['js'] = array();
		$to_replace['css'] = array();

		require_once('JS_MIN/jsmin.php');
		ob_start();
		include($file);
		$html = ob_get_contents();
		$saved_html = $html;
		ob_end_clean();
		
		$isHTML = (stristr($html, "<!-- END FOOTER -->") && stristr($html, "<html") )?true:false;
		
		if($compress && $isHTML && $GLOBALS['config']->compression)
		{
			function process_js($matches) {
				
				global $to_replace;
				
				if(strlen($matches[2]) > 10)
				{
					if(stristr($matches[1], 'src')||stristr($matches[1], 'd_n_m')||stristr($matches[2], 'document.write')||stristr($matches[2], 'google')||stristr($matches[2], 'addthis_config'))
					{
						return (stristr($matches[1], 'd_n_m'))?JSMin::minify($matches[0]):$matches[0];
					}
					else
					{
//						$code = JSMin::minify($matches[2]);
						$code = $matches[2];
						array_push($to_replace['js'], $code);
						return '';

					}
				}
				else
				{
					return $matches[0];
				}

			}
			$html= preg_replace_callback("/<script(\s*[^>]*)>(.*?)<\/script>/is",'process_js',$html);
						
			$extrajs = implode("\n",$to_replace['js']);
			$jshash = md5($extrajs);
			$jsFileName = $jshash.".js";
			$jsWriteFileName = "/tmp/".$jsFileName;
			
			if(!file_exists ( $writeFileName))
			{
				$jsFileHandle = fopen($jsWriteFileName, 'w') or die("can't open file");
				fwrite($jsFileHandle,$extrajs);
				fclose($jsFileHandle);
			}
			$jsLink =  '/cachedAssets.php?js&asset='.$jsFileName; 
			$extrajs = "\n".'<script type="text/javascript" src="' . $jsLink .  '" ></script> '."\n";
		
		
			$html = str_replace("<!-- END FOOTER -->", $extrajs."<!-- END FOOTER -->", $html);

		}

		if($compress && $isHTML && $GLOBALS['config']->compression)
		{
			function process_css($matches) {
				
				global $to_replace;
				
				if(strlen($matches[2]) > 10)
				{
						$css = preg_replace("/([\r\n\t]|<!--|-->)+/i","", $matches[2]);
						array_push($to_replace['css'], $css);
						return '';
				}
				else
				{
					return $matches[0];
				}

			}
			$html= preg_replace_callback("/<style(\s*[^>]*)>(.*?)<\/style>/is",'process_css',$html);
			
			
			
			$extracss = implode("",$to_replace['css']);
			$csshash = md5($extracss);
			$cssFileName = $csshash.".css";
			$cssWriteFileName = "/tmp/".$cssFileName;
			
			if(!file_exists ( $writeFileName))
			{
				$cssFileHandle = fopen($cssWriteFileName, 'w') or die("can't open file");
				fwrite($cssFileHandle,$extracss);
				fclose($cssFileHandle);
			}
			$cssLink = '/cachedAssets.php?css&asset='.$cssFileName; 
			$extracss = "\n".'<link rel="stylesheet" type="text/css" href="' . $cssLink .  '" /> '."\n";
			
			$html = str_replace("</head>", $extracss."</head>", $html);
			

		}

		$html = str_replace("\t","", $html);
		$html = preg_replace("/[\r\n]+/i","\n", $html);


		echo $html;
	} 

}

