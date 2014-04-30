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
			if($input == 0 && strlen($input) <= 1)
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
		
		public static function buildAdditionalParams($cat) {
			$t_param = '';
			foreach(GAConfig::getConf() as $p_confitem) {
				if(array_key_exists('ga', $p_confitem)) {
					foreach($p_confitem['ga'] as $p_confitem_ga) {
						if(array_key_exists('ga_category', $p_confitem_ga) && $p_confitem_ga['ga_category'] == $cat) {
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
								case 'list': // Simply implode list to a string
									$t_normalized_param = implode(', ', $p_confitem_ga['ga_value']);
									break;
								case 'callback':
									// by default, pass configuration value as parameter
									if(!array_key_exists('ga_callback_parameter', $p_confitem_ga) ^ $p_confitem_ga['ga_callback_parameter'] == NULL)
										$t_normalized_param = call_user_func_array( 'GAConfig::'.$p_confitem_ga['ga_callback_name'] , array(getOption($p_confitem['property_name']) ));
									else
										$t_normalized_param = call_user_func_array( $p_confitem_ga['ga_callback_name'] , $p_confitem_ga['ga_callback_parameter'] );
									break;
								default:
									$t_normalized_param = "'".getOption($p_confitem['property_name'])."'";
									break;
							}
							// Exception rule: if ga_value_type is set to 'none' or 'callback', we only append the output if a content is filled
							if($p_confitem_ga['ga_category'] == 'other')
								$t_param .= "    ga('" . $p_confitem_ga['ga_parameter']."', " . $t_normalized_param . ")\n";
							elseif($p_confitem_ga['ga_value_type'] == 'none'
								&& (strlen(getOption($p_confitem['property_name'])) > 0 
									|| self::bin2bool(getOption($p_confitem['property_name']))))
								$t_param .= "    ga('" . $p_confitem_ga['ga_category'] . "', '" . $p_confitem_ga['ga_parameter'] ."')\n";
							elseif ($p_confitem_ga['ga_value_type'] != 'none'
									&& $p_confitem_ga['ga_value_type'] != 'callback')
								$t_param .= "    ga('" . $p_confitem_ga['ga_category'] . "', '" . $p_confitem_ga['ga_parameter']."', " . $t_normalized_param . ")\n";
						}
					}
				}
			}
			return $t_param;
		}
	}
?>