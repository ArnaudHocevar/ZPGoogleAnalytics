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
$plugin_version = '4.0-alpha2';
$plugin_URL = "http://blog.zepsikopat.net/2014/04/24/zenphoto-analytics-plugin-4-0/";
$option_interface = "GoogleAnalytics";

// Include all the dependencies
require_once(SERVERPATH . '/' . USER_PLUGIN_FOLDER . '/google_analytics/inc-config.php');


zp_register_filter('theme_head', 'GoogleAnalytics::GAinitialize',0);
zp_register_filter('theme_body_close', 'GoogleAnalytics::GAColorboxHook',0);


class GoogleAnalytics {

	// Constructor: set all the option defaults
	function GoogleAnalytics() {
		//$conflist = GAConfig::getConf();
		foreach (GAConfig::getConf() as $sub) {
			setOptionDefault($sub['property_name'],$sub['default']);
			}
		}

	// Set up configuration options in administration panel
	function getOptionsSupported() {
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
	
	static function GAinitialize() {
	$analyticUserId = getOption(GAConfig::getConfItem('AnalyticsId','property_name'));
		if (!empty($analyticUserId) && ((zp_loggedin() && getOption('adminTracking')) || !zp_loggedin()) ) {
			echo "<script type=\"text/javascript\">
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');\n";
	
			GoogleAnalytics::printGAOptions();
		
			echo "</script>\n";
		}
	}
	
	static function GAColorboxHook() {
		$analyticUserId = getOption(GAConfig::getConfItem('AnalyticsId','property_name'));
		if (!empty($analyticUserId) && ((zp_loggedin() && getOption('admintracking')) || !zp_loggedin()) && getOption('trackImageViews') == 1) {
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
	
	static function printGAOptions() {
		if (getOption('alwaysSendReferrer') == 1) {
			$alwayssendreferrer = "true";
			}
		else {
			$alwayssendreferrer = "false";
			}
		$analyticUserId = getOption(GAConfig::getConfItem('AnalyticsId','property_name'));
		$domainListParam = getOption('domainName');
		if (!empty($analyticUserId) && 
			((zp_loggedin() && getOption('admintracking')) 
				|| !zp_loggedin())) {
			/* Initialisation of tracking code */
			echo "    ga('create', '" . $analyticUserId . "', 'auto', {" .
				"'sampleRate': " . getOption('trackPageLoadSampleRate') . ", " .
				"'siteSpeedSampleRate': " . getOption('trackPageLoadSpeedSampleRate') . ", " .
				"'alwaysSendReferrer': " . $alwayssendreferrer . ", ";
			if(!empty($domainListParam)) {
				echo "'allowLinker': true,";
				}
			echo "});\n";
				
				/* Additional options */
			if(getOption('trackDemographics') == 1) {
				echo "    ga('require', 'displayfeatures');\n";
			}
			if(getOption('enhancedLink') == 1) {
				echo "    ga('require', 'linkid', 'linkid.js');\n";
				}
			if(!empty($domainListParam)) {
				echo "    ga('require', 'linker');\n";
				$tok = strtok($domainListParam, " ,");
				$domainList = "";
				while ($tok !== false) {
					if(!empty($tok))
						$domainList = $domainList . "'" . $tok . "', ";
						$tok = strtok(" ,");
					}
				echo "    ga('linker:autoLink', [" . $domainList . "], false, true);\n";
				}
			if(getOption('anonymizeIp') == 1) {
				echo "    ga('set', 'anonymizeIp', true);\n";
				}
			if(getOption('forceSSL') == 1) {
				echo "    ga('set', 'forceSSL', true);\n";
			}
			if(getOption('trackPageViews') == 1) {
				echo "    ga('send', 'pageview');\n";
				}
		}
}
}
?>