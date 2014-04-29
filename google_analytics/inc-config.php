<?php
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
					}
				else {
					$const['OPTION_TYPE_TEXTBOX'] = 0;
					$const['OPTION_TYPE_CHECKBOX'] = 1;
					$const['OPTION_TYPE_SELECTOR'] = 2;
					}
				// Definition of all the properties
				self::$conf = array (
					'AnalyticsId' => array(
						'default' => 'UA-xxxxxx-x',
						'property_name' => 'analyticsId',
						'option_header' => gettext('Google Analytics Web Property ID'),
						'option_order' => 0,
						'option_type' => $const['OPTION_TYPE_TEXTBOX'],
						'option_desc' => gettext("If you're going to be using Google Analytics,").' <a	href="http://www.google.com/analytics/" target="_blank"> '.gettext("get a Web Property ID</a> and enter it here."),
						),
					'DomainNameList' => array(
						'default' => '',
						'property_name' => 'domainName',
						'option_header' => gettext('Enable multiple sub-domain tracking'),
						'option_order' => 1,
						'option_type' => $const['OPTION_TYPE_TEXTBOX'],
						'option_desc' => gettext("Specify all domains and subdomains to consider. (separated by comma)"),
						),
					'AdminTrackingEnabled' => array(
						'default' => self::bool2bin(false),
						'property_name' => 'adminTracking',
						'option_header' => gettext('Enable Admin tracking'),
						'option_order' => 2,
						'option_type' => $const['OPTION_TYPE_CHECKBOX'],
						'option_desc' => gettext('Controls if you want Google Analytics tracking for users logged in as admin. Default is not selected.'),
						),
					'AlwaysSendReferrer' => array(
						'default' => self::bool2bin(false),
						'property_name' => 'alwaysSendReferrer',
						'option_header' => gettext('Always send referrer'),
						'option_order' => 3,
						'option_type' => $const['OPTION_TYPE_CHECKBOX'],
						'option_desc' => gettext('Forces send of referrer even if coming from a similar domain (useful to track internal links)'),
						),
					'TrackPageLoadingSampleRate' => array(
						'default' => 100,
						'property_name' => 'trackPageLoadSampleRate',
						'option_header' => gettext('Page load sample rate'),
						'option_order' => 10,
						'option_type' => $const['OPTION_TYPE_SELECTOR'],
						'option_selections' => self::$rateList,
						'option_desc' => gettext('Controls the sample rate (i.e. percentage of sampled visits) of the page.'),
						),
					'TrackPageSpeedSampleRate' => array(
						'default' => 10,
						'property_name' => 'trackPageLoadSpeedSampleRate',
						'option_header' => gettext('Page load speed sample rate'),
						'option_order' => 11,
						'option_type' =>  $const['OPTION_TYPE_SELECTOR'],
						'option_selections' => self::$rateList,
						'option_desc' => gettext('Controls the sample rate of page load speed.'),
						),
					'IPAnonymizeEnabled' => array(
						'default' => self::bool2bin(true),
						'property_name' => 'anonymizeIp',
						'option_header' => gettext('Enable IP anonymizing'),
						'option_order' => 4,
						'option_type' => $const['OPTION_TYPE_CHECKBOX'],
						'option_desc' => gettext('When present, the IP address of the sender will be anonymized.'),
						),
					'TrackPageViews' => array(
						'default' => self::bool2bin(true),
						'property_name' => 'trackPageViews',
						'option_header' => gettext('Enable page view tracking'),
						'option_order' => 8,
						'option_type' => $const['OPTION_TYPE_CHECKBOX'],
						'option_desc' => gettext('Controls if you want Google Analytics to track page views.'),
						),
					'TrackImageViews' => array(
						'default' => self::bool2bin(true),
						'property_name' => 'trackImageViews',
						'option_header' => gettext('Enable image view tracking'),
						'option_order' => 9,
						'option_type' => $const['OPTION_TYPE_CHECKBOX'],
						'option_desc' => gettext('Controls if you want Google Analytics to track images shown in colorbox.'),
						),
					'ForceSSL' => array(
						'default' => self::bool2bin(false),
						'property_name' => 'forceSSL',
						'option_header' => gettext('Force SSL'),
						'option_order' => 5,
						'option_type' => $const['OPTION_TYPE_CHECKBOX'],
						'option_desc' => gettext('By default, tracking beacons sent from https pages will be sent using https while beacons sent from http pages will be sent using http. Setting forceSSL to true will force http pages to also send all beacons using https.'),
						),
					'TrackDemographics' => array(
						'default' => self::bool2bin(false),
						'property_name' => 'trackDemographics',
						'option_header' => gettext('Enable Demographics'),
						'option_order' => 6,
						'option_type' => $const['OPTION_TYPE_CHECKBOX'],
						'option_desc' => gettext('Demographics and Interest Reports make Age, Gender, and Interest data available so you can better understand who your users are.'),
						),
					'UseEnhancedLinks' => array(
						'default' => self::bool2bin(false),
						'property_name' => 'enhancedLink',
						'option_header' => gettext('Use enhanced link attribution'),
						'option_order' => 7,
						'option_type' => $const['OPTION_TYPE_CHECKBOX'],
						'option_desc' => gettext('Enable better tracking of links by Google.'),
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
		
		/**
		* Checks the validity of the Google Analytics identifier
		*
		* @return boolean true for a valid pattern
		*/
		public static function validateAnalyticsId($aid) {
			if(preg_match('/^UA-[0-9]{6}-[0-9]$/', $aid))
				return true;
			else
				return false;
		}
		
		/**
		* Checks the validity of a rate
		*
		* @return boolean true for a valid rate (in [0,100])
		*/
		public static function validateRate($rval) {
			if(is_numeric($rval) && 0 <= intval($rval) && intval($rval) <= 100)
				return true;
			else
				return false;
		}
		
		public static function bin2bool($input) {
			if($input == 1)
				return true;
			else
				return false;
		}
		
		public static function bool2bin($input) {
			if(!empty($input) && $input)
				return 1;
			else
				return 0;
		}
	}
?>