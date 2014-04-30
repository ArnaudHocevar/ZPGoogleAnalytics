<?php
/**
 * google_analytics -- Places the tracking registation code for google analytics in a photo gallery.
 * This code was modeled after the google_maps plugin by Dustin Brewer (mankind) and Stephen Billard (sbillard)
 *
 *
 * Version 2.0 enhancements 
 *         Added support for Asynchronous Google Analytics 
 * 
 *         2.5 enhancements
 *         Split Analytics tracking code to enable faster page loading.
 *
 *         3.0 enhancements
 *         Added several customisation options
 *         3.0.2 enhancements
 *         Added anonymization and pageviews options 
 *         3.0.3 enhancements
 *         Added colorbox image tracking
 *
 *		   4.0 enhancements
 *         Support of new google script for universal analytics
 *         Added extra configuration options
 *
 * @author Jeff Smith (j916) up to 2.5.0 - http://www.moto-treks.com/zenPhoto/google-analytics-for-zenphoto.html
 * @author Arnaud Hocevar starting 3.0.0
 * @version 4.0-alpha1
 * @package plugins
 */

$plugin_is_filter = THEME_PLUGIN;
$plugin_description = gettext("Support for providing Google Analytics tracking");
$plugin_author = 'Arnaud Hocevar (original plugin from Jeff Smith)';
$plugin_version = '4.0';
$plugin_URL = "http://github.com/ArnaudHocevar/ZPGoogleAnalytics";
$option_interface = "GoogleAnalytics";

// Include all the dependencies
require_once(SERVERPATH . '/' . USER_PLUGIN_FOLDER . '/google_analytics/inc-functions.php');
require_once(SERVERPATH . '/' . USER_PLUGIN_FOLDER . '/google_analytics/inc-config.php');


zp_register_filter('theme_head', 'GoogleAnalytics::GAinitialize',0);
zp_register_filter('theme_body_close', 'GoogleAnalytics::GAColorboxHook',0);


class GoogleAnalytics {

	// Constructor: set all the option defaults
	public function GoogleAnalytics() {
		//$conflist = GAConfig::getConf();
		foreach (GAConfig::getConf() as $sub) {
			setOptionDefault($sub['property_name'],$sub['default']);
			}
		}

	// Set up configuration options in administration panel
	public function getOptionsSupported() {
		$t_arr = array();
		foreach (GAConfig::getConf() as $sub) {
			$t_sarr = array(
				'order' => $sub['option_order'],
				'key' => $sub['property_name'],
				'type' => $sub['option_type'],
				'desc' => $sub['option_desc']
			);
			if($sub['option_type'] == OPTION_TYPE_SELECTOR)
				$t_sarr['selections'] = $sub['option_selections'];
			$t_arr[$sub['option_header']] = $t_sarr;
		}
		return $t_arr;
	}

	function handleOption($option, $currentValue) {}
	
	public static function GAinitialize() {
		// Only enable analytics if a valid UA identifier is available
		if(GAToolbox::validateAnalyticsId(getOption(GAConfig::getConfItem('AnalyticsId','property_name')))
				&& ((zp_loggedin() && getOption('adminTracking')) 
						|| !zp_loggedin()) ) {
			// Analytics JS header
			echo "<script type=\"text/javascript\">
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');\n";
	
			echo GAToolbox::buildAdditionalParams('require');
			echo GAToolbox::buildAdditionalParams('other');
			echo GAToolbox::buildAdditionalParams('set');
			if(getOption(GAConfig::getConfItem('TrackPageViews','property_name')) == 1) {
				echo "    ga('send', 'pageview');\n";
				}
			echo "</script>\n";
		}
	}
	
	public static function GAColorboxHook() {
		// Only enable analytics if a valid UA identifier is available AND we want to track image view
		if(GAToolbox::validateAnalyticsId(getOption(GAConfig::getConfItem('AnalyticsId','property_name')))
				&& ((zp_loggedin() && getOption('adminTracking')) 
						|| !zp_loggedin()) 
				&& getOption(GAConfig::getConfItem('TrackImageViews','property_name')) == 1) {
			echo "<script type=\"text/javascript\">
	$(document).ready(function () {
		$(document).bind('cbox_complete', function(){
			var href = this.href;
			if (href) {
				ga('send', 'pageview', href]);
			}
		});
	});
	</script>\n";
		}
	}

}

?>