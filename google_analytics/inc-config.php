<?php
	require_once('inc-functions.php');
	/** This class file contains the static values used in the module */
	class GAConfig {
		// List of rates used in the drop-down
		private static $rateList = array('0 %' => 0, '1 %' => 1, '2 %' => 2, '3 %' => 3, '4 %' => 4, '5 %' => 5, '10 %' => 10, '15 %' => 15, '25 %' => 25, '33 %' => 33, '50 %' => 50, '66 %' => 66, '75 %' => 75, '100 %' => 100);
		
		private static $conf = null;
		
		public static function getConf() {
			if(self::$conf == null) {
				// Fast hack to ignore constants when called outside of administration
				if(defined('OPTION_TYPE_TEXTBOX')) {
					$const['OPTION_TYPE_TEXTBOX'] = OPTION_TYPE_TEXTBOX;
					$const['OPTION_TYPE_CHECKBOX'] = OPTION_TYPE_CHECKBOX;
					$const['OPTION_TYPE_SELECTOR'] = OPTION_TYPE_SELECTOR;
					$const['OPTION_TYPE_RADIO'] = OPTION_TYPE_RADIO;
					}
				else {
					$const['OPTION_TYPE_TEXTBOX'] = 0;
					$const['OPTION_TYPE_CHECKBOX'] = 1;
					$const['OPTION_TYPE_SELECTOR'] = 2;
					$const['OPTION_TYPE_RADIO'] = 1;
					}
				// Definition of all the properties
				self::$conf = array (
					'AnalyticsId' => array(
						'default' => 'UA-xxxxxx-x',
						'property_name' => 'zp_google_analytics_analyticsId',
						'option_header' => gettext_pl('Universal Analytics Web Property ID','google_analytics'),
						'option_order' => 0,
						'option_type' => $const['OPTION_TYPE_TEXTBOX'],
						'option_desc' => gettext_pl("Please enter your Universal Analytics Web Property identifier.",'google_analytics') . '<br />'. 
										gettext_pl("If you do not have one, you can create an account for free on <a href=\"http://www.google.com/analytics/\" target=\"_blank\">the Universal Analytics website</a> and enter it here.",'google_analytics'),
						),
					'DomainNameList' => array(
						'default' => '',
						'property_name' => 'zp_google_analytics_domainName',
						'option_header' => gettext_pl('Enable tracking of multiple sub-domain','google_analytics'),
						'option_order' => 1,
						'option_type' => $const['OPTION_TYPE_TEXTBOX'],
						'option_desc' => gettext_pl("If your gallery can be accessed by multiple addresses, and you want to track your users regardless of the inbound domain, fill the list of domains to be linked (comma-separated).",'google_analytics') . '<br />' .
						'<em>' . gettext_pl("For more technical information, visit ",'google_analytics') . '<a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/cross-domain#autolink" target="_blank">Cross Domain Auto Linking</a></em>',
						'ga' => array(
									array(
										'ga_category' => 'create',
										'ga_parameter' => 'allowLinker',
										'ga_value_type' => 'boolean',
									),
									array(
										'ga_category' => 'require',
										'ga_parameter' => 'linker',
										'ga_value_type' => 'none',
									),
									array(
										'ga_category' => 'other',
										'ga_parameter' => 'linker:autoLink',
										'ga_value_type' => 'buildLinkerParam',
									)
								),
						),
					'AdminTrackingEnabled' => array(
						'default' => GAToolbox::bool2bin(false),
						'property_name' => 'zp_google_analytics_adminTracking',
						'option_header' => gettext_pl('Enable administrator tracking','google_analytics'),
						'option_order' => 2,
						'option_type' => $const['OPTION_TYPE_CHECKBOX'],
						'option_desc' => gettext_pl('Allows you to track administrator users logged in. As administrators can generate lots of gallery page hits (administration pages are not tracked), it might be wise to ignore them from the production statistics. Be aware that enabling this option will hide the tracking code from the gallery page.','google_analytics'),
						),
					'TrackPageViews' => array(
						'default' => GAToolbox::bool2bin(true),
						'property_name' => 'zp_google_analytics_trackPageViews',
						'option_header' => gettext_pl('Enable page view tracking','google_analytics'),
						'option_order' => 3,
						'option_type' => $const['OPTION_TYPE_CHECKBOX'],
						'option_desc' => gettext_pl('Allows you to enable or disable per-page tracking. You will still be able to track image views, but with less details.','google_analytics') . '<br />' .
						'<em>' . gettext_pl("For more technical information, visit ",'google_analytics') . '<a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/pages" target="_blank">Page Tracking</a></em>',
						),
					'TrackPageViewsPosition' => array(
						'default' => 'theme_head',
						'property_name' => 'zp_google_analytics_trackPageViewsPosition',
						'option_header' => gettext_pl('Position of the page tracking code','google_analytics'),
						'option_order' => 4,
						'option_type' => $const['OPTION_TYPE_RADIO'],
						'option_button' => array(
								gettext_pl('HTML header','google_analytics') => 'theme_head',
								gettext_pl('HTML body opening','google_analytics') => 'theme_body_open',
								gettext_pl('HTML body closing','google_analytics') => 'theme_body_close',
								),
						'option_desc' => gettext_pl('Allows you to select where the page tracking code will be inserted. Possible choices are:','google_analytics') . '<ul>' .
						'<li>' . gettext_pl('HTML header','google_analytics') . '</li>' .
						'<li>' . gettext_pl('HTML body opening','google_analytics') . '</li>' .
						'<li>' . gettext_pl('HTML body closing','google_analytics') . '</li></ul>',
						),
					'TrackImageViews' => array(
						'default' => GAToolbox::bool2bin(true),
						'property_name' => 'zp_google_analytics_trackImageViews',
						'option_header' => gettext_pl('Enable image view tracking','google_analytics'),
						'option_order' => 5,
						'option_type' => $const['OPTION_TYPE_CHECKBOX'],
						'option_desc' => gettext_pl('Allows you to track hits on images displayed through the colorbox library.','google_analytics'),
						),
					'TrackImageViewsPosition' => array(
						'default' => 'theme_body_close',
						'property_name' => 'zp_google_analytics_trackImageViewsPosition',
						'option_header' => gettext_pl('Position of the image tracking code','google_analytics'),
						'option_order' => 6,
						'option_type' => $const['OPTION_TYPE_RADIO'],
						'option_button' => array(
								gettext_pl('HTML header','google_analytics') => 'theme_head',
								gettext_pl('HTML body opening','google_analytics') => 'theme_body_open',
								gettext_pl('HTML body closing','google_analytics') => 'theme_body_close',
								),
						'option_desc' => gettext_pl('Allows you to select where the image tracking code will be inserted. Possible choices are:','google_analytics') . '<ul>' .
						'<li>' . gettext_pl('HTML header','google_analytics') . '</li>' .
						'<li>' . gettext_pl('HTML body opening','google_analytics') . '</li>' .
						'<li>' . gettext_pl('HTML body closing','google_analytics') . '</li></ul>',
						),						
					'AlwaysSendReferrer' => array(
						'default' => GAToolbox::bool2bin(false),
						'property_name' => 'zp_google_analytics_alwaysSendReferrer',
						'option_header' => gettext_pl('Always send referrer information','google_analytics'),
						'option_order' => 7,
						'option_type' => $const['OPTION_TYPE_CHECKBOX'],
						'option_desc' => gettext_pl('By default, the referrer information is only transmitted if the domain is different. Enabling this option will force the referrer to be sent in any case. Useful if you want to track sub-domain links.','google_analytics') . '<br />' .
						'<em>' . gettext_pl("For more technical information, visit ",'google_analytics') . '<a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/field-reference#alwaysSendReferrer" target="_blank">Always Send Referrer</a></em>',
						'ga' => array(
									array(
										'ga_category' => 'create',
										'ga_parameter' => 'alwaysSendReferrer',
										'ga_value_type' => 'boolean',
									)
								),
						),
					'TrackPageLoadingSampleRate' => array(
						'default' => 100,
						'property_name' => 'zp_google_analytics_trackPageLoadSampleRate',
						'option_header' => gettext_pl('Page load sample rate','google_analytics'),
						'option_order' => 12,
						'option_type' => $const['OPTION_TYPE_SELECTOR'],
						'option_selections' => self::$rateList,
						'option_desc' => gettext_pl('Controls the sample rate (i.e. percentage of sampled visits) of the page. You can keep a safe 100% in most cases, only reduce it if you have a high traffic website and the Universal Analytics processing speed cannot cope with your visitors volume.','google_analytics') . '<br />' .
						'<em>' . gettext_pl("For more technical information, visit ",'google_analytics') . '<a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/field-reference#sampleRate" target="_blank">Sample Rate</a></em>',
						'ga' => array(
									array(
										'ga_category' => 'create',
										'ga_parameter' => 'sampleRate',
										'ga_value_type' => 'numeric',
									)
								),
						),
					'TrackPageSpeedSampleRate' => array(
						'default' => 10,
						'property_name' => 'zp_google_analytics_trackPageLoadSpeedSampleRate',
						'option_header' => gettext_pl('Page load speed sample rate','google_analytics'),
						'option_order' => 13,
						'option_type' =>  $const['OPTION_TYPE_SELECTOR'],
						'option_selections' => self::$rateList,
						'option_desc' => gettext_pl('Controls the sample rate used for page load speed measurement. Note that Google will only consider the greater of 10k hits or 1% of your visitors to keep a fair distribution of resources.','google_analytics') . '<br />' .
						'<em>' . gettext_pl("For more technical information, visit ",'google_analytics') . '<a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/field-reference#siteSpeedSampleRate" target="_blank">Site Speed Sample Rate</a></em>',
						'ga' => array(
									array(
										'ga_category' => 'create',
										'ga_parameter' => 'siteSpeedSampleRate',
										'ga_value_type' => 'numeric',
									)
							),
						),
					'IPAnonymizeEnabled' => array(
						'default' => GAToolbox::bool2bin(true),
						'property_name' => 'zp_google_analytics_anonymizeIp',
						'option_header' => gettext_pl('Enable IP anonymization','google_analytics'),
						'option_order' => 8,
						'option_type' => $const['OPTION_TYPE_CHECKBOX'],
						'option_desc' => gettext_pl('When present, the IP address of the sender will be anonymized.','google_analytics') . '<br />' .
						'<em>' . gettext_pl("For more technical information, visit ",'google_analytics') . '<a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/field-reference#anonymizeIp" target="_blank">Anonymize IP</a></em>',
						'ga' => array(
									array(
										'ga_category' => 'set',
										'ga_parameter' => 'anonymizeIp',
										'ga_value_type' => 'boolean',
									)
							),
						),
					
					
					'ForceSSL' => array(
						'default' => GAToolbox::bool2bin(false),
						'property_name' => 'zp_google_analytics_forceSSL',
						'option_header' => gettext_pl('Force use of SSL beacons','google_analytics'),
						'option_order' => 9,
						'option_type' => $const['OPTION_TYPE_CHECKBOX'],
						'option_desc' => gettext_pl('By default, tracking beacons sent from https pages will be sent using https while beacons sent from http pages will be sent using http. Setting forceSSL to true will force http pages to also send all beacons using https.','google_analytics') . '<br />' .
						'<em>' . gettext_pl("For more technical information, visit ",'google_analytics') . '<a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/field-reference#forceSSL" target="_blank">Force SSL</a></em>',
						'ga' => array(
									array(
										'ga_category' => 'set',
										'ga_parameter' => 'forceSSL',
										'ga_value_type' => 'boolean',
									)
							),
						),
					'TrackDemographics' => array(
						'default' => GAToolbox::bool2bin(false),
						'property_name' => 'zp_google_analytics_trackDemographics',
						'option_header' => gettext_pl('Enable Demographics tracking','google_analytics'),
						'option_order' => 10,
						'option_type' => $const['OPTION_TYPE_CHECKBOX'],
						'option_desc' => gettext_pl('Demographics and Interest Reports make Age, Gender, and Interest data available so you can better understand who your users are.','google_analytics') . '<br />' .
						'<em>' . gettext_pl("For more technical information, visit ",'google_analytics') . '<a href="https://support.google.com/analytics/answer/3450482" target="_blank">Display Advertising features</a></em>',
						'ga' => array(
									array(
										'ga_category' => 'require',
										'ga_parameter' => 'displayfeatures',
										'ga_value_type' => 'none',
									)
							),
						),
					'UseEnhancedLinks' => array(
						'default' => GAToolbox::bool2bin(false),
						'property_name' => 'zp_google_analytics_enhancedLink',
						'option_header' => gettext_pl('Enable enhanced link attribution','google_analytics'),
						'option_order' => 11,
						'option_type' => $const['OPTION_TYPE_CHECKBOX'],
						'option_desc' => gettext_pl('Enable better tracking of links by Google. This allow for instance to differentiate multiple links targetting the same page.','google_analytics') . '<br />' .
						'<em>' . gettext_pl("For more technical information, visit ",'google_analytics') . '<a href="https://support.google.com/analytics/answer/2558867" target="_blank">Enhanced Link Attribution</a></em>',
						'ga' => array(
									array(
										'ga_category' => 'require',
										'ga_parameter' => 'linkid',
										'ga_value_type' => 'list',
										'ga_value' => array("'linkid.js'"),
									)
							),
						),
					);
				}
			return self::$conf;
			}

		public static function getConfItem($key,$sub = NULL) {
			$confItem = self::getConf();
			if(empty($sub))
				return $confItem[$key];
			else
				return $confItem[$key][$sub];
			}
		
		function GAConfig() {}
		
		
	}
?>
