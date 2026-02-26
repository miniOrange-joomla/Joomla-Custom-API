<?php
/**
 * @package     Joomla.Library
 * @subpackage  lib_minicustomapi
 *
 * @author      miniOrange Security Software Pvt. Ltd.
 * @copyright   Copyright (C) 2015 miniOrange (https://www.miniorange.com)
 * @license     GNU General Public License version 3; see LICENSE.txt
 * @contact     info@xecurify.com
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class Utilities_CustomAPI
{
    public static function getAPIInfo()
	{
		require_once JPATH_SITE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_miniorange_customapi' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_customapi_utility.php';
		$api=URI::getInstance()->toString();
		$api_path=parse_url($api);
		$api_path_array=explode('/',$api_path['path']);
		$customer_details = MocustomapiUtility::getCustomerDetails();
		$status = $customer_details['status'];
		if(in_array('mini',$api_path_array))
		{
			$path_size=sizeof($api_path_array);
			$api_name=$api_path_array[$path_size-1];
			$api_query=isset($api_path['query'])?$api_path['query']:'';
			$api_information=MocustomapiUtility::fetch_api_info($api_name,'custom');
			// Backward compatibility for Joomla 3/4/5/6
			$app = Factory::getApplication();
			$input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
			$get_param = ($input && $input->get) ? $input->get->getArray() : [];
			$server = ($input && $input->server) ? $input->server->getArray() : [];
			$request_body=file_get_contents('php://input');
			$request_body_array=array();
  			parse_str($request_body,$request_body_array);
			if (!empty($api_information)) {
				if ($server['REQUEST_METHOD'] != "GET") {
					return MocustomapiUtility::mo_api_error_msgs('UNSUPPORTED_REQUEST_FORMAT');
				}

				try {
					return self::custom_api_data($api_information, $get_param, $request_body_array, $api_query);
				} catch (\Exception $e) {
					return MocustomapiUtility::mo_api_error_msgs('UNEXPECTED_ERROR', $e->getMessage());
				}
			} else {
				return MocustomapiUtility::mo_api_error_msgs('RESOURCE_NOT_FOUND');
			}
		}
		else
		{
			return false;
		}
	}

	public static function custom_api_data($api_information, $get_param, $post_param, $api_query)
	{
		// Validate API configuration structure
		if (empty($api_information->api_method) || empty($api_information->table_name)) {
			return MocustomapiUtility::mo_api_error_msgs('INVALID_CONFIGURATION');
		}

		// Handle col_condition - can be string or array (for backward compatibility)
		$col_condition = is_array($api_information->col_condition) ? 
			(isset($api_information->col_condition[0]) ? $api_information->col_condition[0] : 'None Selected') : 
			$api_information->col_condition;

		if ($col_condition == 'None Selected' && !empty($api_query)) {
			return MocustomapiUtility::mo_api_error_msgs('INVALID_FORMAT');
		}

		if ($col_condition != 'None Selected' && empty($api_query)) {
			return MocustomapiUtility::mo_api_error_msgs('INVALID_FORMAT');
		}

		try {
			switch (strtolower($api_information->api_method)) {
				case "get":
					if (!empty($post_param)) {
						return MocustomapiUtility::mo_api_error_msgs('INVALID_FORMAT');
					}

					$responseData = MocustomapiUtility::api_get_request($api_information, $get_param);

					$response = json_encode([
						'data' => $responseData,
					], JSON_PRETTY_PRINT);

					return $response;

				default:
					return MocustomapiUtility::mo_api_error_msgs('UNSUPPORTED_METHOD');
			}
		} catch (\RuntimeException $e) {
			return MocustomapiUtility::mo_api_error_msgs('DATABASE_ERROR', $e->getMessage());
		} catch (\Exception $e) {
			return MocustomapiUtility::mo_api_error_msgs('UNEXPECTED_ERROR', $e->getMessage());
		}
	}

}