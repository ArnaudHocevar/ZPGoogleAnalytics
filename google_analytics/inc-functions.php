<?php 
	require_once('inc-config.php');
	class GAToolbox {
		/**
		 * Checks the validity of the Google Analytics identifier
		 *
		 * @return boolean true for a valid pattern
		 */
		public static function validateAnalyticsId($aid) {
			if(preg_match('/^UA-[0-9]{3,9}-[0-9]$/', $aid))
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
			if($input == 0)
				return false;
			else
				return true;
		}
		
		public static function bool2bin($input) {
			if(!empty($input) && $input)
				return 1;
			else
				return 0;
		}
		
		public static function buildCreateParams() {
			$t_param = '';
			foreach(GAConfig::getConf() as $p_confitem) {
				if(array_key_exists('ga', $p_confitem)) {
					foreach($p_confitem['ga'] as $p_confitem_ga) {
						if(array_key_exists('ga_category', $p_confitem_ga) && $p_confitem_ga['ga_category'] == 'create') {
							// Convert some datatypes if needed
							switch($p_confitem_ga['ga_value_type']) {
								case 'numeric':
									$t_normalized_param = intval(getOption($p_confitem['property_name']));
									break;
								case 'boolean':
									if(self::bin2bool(getOption($p_confitem['property_name'])))
										$t_normalized_param = 'true';
									else
										$t_normalized_param = 'false';
									break;
								default:
									$t_normalized_param = "'".getOption($p_confitem['property_name'])."'";
									break;
							}	
							// Append to parameter array
							$t_param .= "'".$p_confitem_ga['ga_parameter']."': ".$t_normalized_param.", ";
						}
					}
				}
			}
			return $t_param;
		}
	}
?>