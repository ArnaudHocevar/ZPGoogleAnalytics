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


zp_register_filter('theme_head', 'GoogleAnalytics::GAinitialize',0);
zp_register_filter('theme_body_close', 'GoogleAnalytics::GAColorboxHook',0);


class GoogleAnalytics {

	static $GARateList = array('0 %' => 0,
		'1 %' => 1,
		'2 %' => 2,
		'3 %' => 3,
		'4 %' => 4,
		'5 %' => 5,
		'10 %' => 10,
		'15 %' => 15,
		'25 %' => 25,
		'33 %' => 33,
		'50 %' => 50,
		'66 %' => 66,
		'75 %' => 75,
		'100 %' => 100);
		
	function GoogleAnalytics() {
		setOptionDefault('analyticsId', 'UA-xxxxxx-x');
		setOptionDefault('admintracking', 0);
		setOptionDefault('domainName', '');
		setOptionDefault('trackPageLoadSampleRate', '100');
		setOptionDefault('trackPageLoadSpeedSampleRate', '5');
		setOptionDefault('anonymizeIp','1');
		setOptionDefault('trackPageViews','1');
		setOptionDefault('trackImageViews','1');
		setOptionDefault('alwaysSendReferrer','1');
		setOptionDefault('forceSSL','1');
		setOptionDefault('trackDemographics','0');
		setOptionDefault('enhancedLink','0');
	}

	function getOptionsSupported() {
		return array(  gettext('Google Analytics Web Property ID') => array(
									'order' => 0,
									'key' => 'analyticsId',
									'type' => OPTION_TYPE_TEXTBOX,
									'desc' => gettext("If you're going to be using Google Analytics,").' <a	href="http://www.google.com/analytics/" target="_blank"> '.gettext("get a Web Property ID</a> and enter it here.")
						),
						gettext('Enable multiple sub-domain tracking') => array(
									'order' => 1,
									'key' => 'domainName',
									'type' => OPTION_TYPE_TEXTBOX,
									'desc' => gettext("Specify all domains and subdomains to consider. (separated by comma)")
						),
						gettext('Enable Admin tracking') => array (
									'order' => 2,
									'key' => 'admintracking',
									'type' => OPTION_TYPE_CHECKBOX,
									'desc' => gettext('Controls if you want Google Analytics tracking for users logged in as admin. Default is not selected.')
						),
						gettext('Always send referrer') => array (
									'order' => 3,
									'key' => 'alwaysSendReferrer',
									'type' => OPTION_TYPE_CHECKBOX,
									'desc' => gettext('Forces send of referrer even if coming from a similar domain (useful to track internal links)')
						),
						gettext('Enable IP anonymizing') => array (
									'order' => 4,
									'key' => 'anonymizeIp',
									'type' => OPTION_TYPE_CHECKBOX,
									'desc' => gettext('When present, the IP address of the sender will be anonymized.')
						),
						gettext('Force SSL') => array (
									'order' => 5,
									'key' => 'forceSSL',
									'type' => OPTION_TYPE_CHECKBOX,
									'desc' => gettext('By default, tracking beacons sent from https pages will be sent using https while beacons sent from http pages will be sent using http. Setting forceSSL to true will force http pages to also send all beacons using https.')
						),
						gettext('Enable Demographics') => array (
									'order' => 6,
									'key' => 'trackDemographics',
									'type' => OPTION_TYPE_CHECKBOX,
									'desc' => gettext('Demographics and Interest Reports make Age, Gender, and Interest data available so you can better understand who your users are.')
						),
						gettext('Use enhanced link attribution') => array (
									'order' => 7,
									'key' => 'enhancedLink',
									'type' => OPTION_TYPE_CHECKBOX,
									'desc' => gettext('Enable better tracking of links by Google.')
						),
						gettext('Enable page view tracking') => array (
									'order' => 8,
									'key' => 'trackPageViews',
									'type' => OPTION_TYPE_CHECKBOX,
									'desc' => gettext('Controls if you want Google Analytics to track page views.')
						),
						gettext('Enable image view tracking') => array (
									'order' => 9,
									'key' => 'trackImageViews',
									'type' => OPTION_TYPE_CHECKBOX,
									'desc' => gettext('Controls if you want Google Analytics to track image displayed with colorbox.')
						),
						gettext('Page load sample rate') => array (
									'order' => 10,
									'key' => 'trackPageLoadSampleRate',
									'type' => OPTION_TYPE_SELECTOR,
									'selections' => GoogleAnalytics::$GARateList,
									'desc' => gettext('Controls the sample rate (i.e. percentage of sampled visits) of the page.')
						),
						gettext('Page load speed sample rate') => array (
									'order' => 11,
									'key' => 'trackPageLoadSpeedSampleRate',
									'type' => OPTION_TYPE_SELECTOR,
									'selections' => GoogleAnalytics::$GARateList,
									'desc' => gettext('Controls the sample rate of page load speed.')
						),
		);
	}

	function handleOption($option, $currentValue) {}
	
	static function GAinitialize() {
		$analyticUserId = getOption('analyticsId');
		if (!empty($analyticUserId) && ((zp_loggedin() && getOption('admintracking')) || !zp_loggedin()) ) {
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
		$analyticUserId = getOption('analyticsId');
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
		$analyticUserId = getOption('analyticsId');
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