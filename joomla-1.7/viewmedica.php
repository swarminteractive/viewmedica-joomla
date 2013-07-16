<?php
/**
  * @version	1.3
  * @package	Joomla
  * @copyright	Copyright (C) 2011, Swarm Interactive
  * @license	GNU/GPL
  */

//no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
  * ViewMedica 6 Plugin
  *
  * @package	Joomla
  * @subpackage	Content
  */
  
class plgContentViewMedica extends JPlugin
{
	function onContentPrepare( $context, &$row, $page=0 ) {
		
		// expression to search for
 		$regex = '/{vm\s*.*?}/i';

		// find all instances of plugin and put in $matches
		preg_match_all( $regex, $row->text, $matches );
		
		//number of plugins
		$count = count($matches[0]);
		
		// plugin only processes if there are any instances of the plugin in the text
	 	if ( $count ) {
			// Get plugin parameters
	 		$this->_process( $row, $matches, $count, $regex );
		}
		
	}
	
	protected function _process( &$row, &$matches, $count, $regex )
	{
		$client_id = $this->params->get('cid', '');
		$client_str = 'client="' . $client_id . '";';
		
		$secure = $this->params->get('secure', '');
		$lang = $this->params->get('lang', '');
		$fullscreen = $this->params->get('fullscreen', '');
		$enableaudio = $this->params->get('enableaudio', '');
		$brochure = $this->params->get('brochure', '');
		$disclaimer = $this->params->get('disclaimer', '');
		$width = $this->params->get('size', '');
		$width_str = 'width="' . $width . '";';
		$menuAccess = $this->params->get('menu', '');
		
		//menu param done before checking the embed
		if ($menuAccess != '0')
		{
			$menu_str = 'menuaccess=false;';
		} else {
			$menu_str = '';
		}
		
			
		for ( $i=0; $i < $count; $i++ )
		{
	 		$load = str_replace( '{vm="', '', $matches[0][$i] );
	 		$load = str_replace( '"}', '', $load );
	 		
	 		$fullstring = $load;
	 		
	 		if (strpos($load, ',') !== false)
	 		{
	 			$ex = explode(',', $load);
	 			$load = trim($ex[0]); 			
	 			
	 			if (is_numeric(trim($ex[1])))
	 			{
	 				$width = trim($ex[1]);
	 				$width_str = 'width="' . $width . '";';
	 				//$menu_str = '';
	 				
	 			} else if ((trim($ex[1]) == 'nomenu') && ($load != 'all')) {
	 				$menu_str = 'menuaccess=false;';
	 				//$width_str = '';
	 			
	 			} else {
	 				//$width_str = '';
	 				//$menu_str = '';
	 			}
	 			
	 			if (count($ex) > 2)
	 			{
	 				if (is_numeric(trim($ex[2])))
	 				{
	 					$width = trim($ex[2]);
	 					$width_str = 'width="' . $width . '";';
	 					
	 				} else if ((trim($ex[2]) == 'nomenu') && ($load != 'all')) {
	 					$menu_str = 'menuaccess=false;';
	 				
	 				} else {
	 					//$width_str = '';
	 					//$menu_str = '';
	 				}
	 			} 			
	 				
	 		} else {
	 			//trim white space
	 			$load = trim($fullstring);
	 			$div = 'vm';
	 			$openthis = 'vm';
	 			//$menu_str = '';
	 			//$width_str = '';
	 		}
	 					
			//deal with parameters
			if ($load == 'all')
			{
				$div = 'vm';
				$openthis_str = '';
			} else {
				$openthis = $div = $load;
				$openthis_str = 'openthis="'. $openthis .'";';
			}
			
			//check security param
			if ($secure != '0')
			{
				$secure_str = 'secure=true;';
			} else {
				$secure_str = '';
			}
			
			//check language param
			if ($lang != '0')
			{
				if ($lang != '1')
				{
					$lang_str = 'lang="de";';
				}
				
				else {
					$lang_str = 'lang="es";';
				}
			} else {
				$lang_str = '';
			}
			
			//check fullscreen param	
			if ($fullscreen != '0')
			{
				$fullscreen_str = 'fullscreen=false;';
			} else {
				$fullscreen_str = '';
			}
				
			//check audio param	
			if ($enableaudio != '0')
			{
				$enableaudio_str = 'ignoreaudio=true;';
			} else {
				$enableaudio_str = '';	
			}
			
			//check brochure param	
			if ($brochure != '0')
			{
				$brochure_str = 'brochures=false;';
			} else {
				$brochure_str = '';
			}
			
			//check disclaimer param	
			if ($disclaimer != '0')
			{
				$disclaimer_str = 'disclaimer=false;';
			} else {
				$disclaimer_str = '';
			}
 			
 			//set up div to be injected
 			$vm = '<div><!-- ViewMedica Embed Start --><div id="'. $div .'"></div><script type="text/javascript" src="http://www.swarminteractive.com/js/vm.js"></script><script type="text/javascript">' . $client_str . $width_str . $openthis_str . $menu_str . $secure_str .  $lang_str . $fullscreen_str . $enableaudio_str . $brochure_str . $disclaimer_str . ' vm_open();</script><!-- ViewMedica Embed End --></div>'; 
			
			//replace the {vm=" ... "} with the injection div
			$row->text = str_replace( '{vm="'. $fullstring .'"}', $vm, $row->text );
				
 		}
	}	
}	

?>