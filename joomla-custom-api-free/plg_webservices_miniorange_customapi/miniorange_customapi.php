<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  plg_webservices_miniorangecustomapi
 *
 * @author      miniOrange Security Software Pvt. Ltd.
 * @copyright   Copyright (C) 2015 miniOrange (https://www.miniorange.com)
 * @license     GNU General Public License version 3; see LICENSE.txt
 * @contact     info@xecurify.com
 */

 defined('_JEXEC') or die;

 use Joomla\CMS\Plugin\CMSPlugin;
 use Joomla\CMS\Router\ApiRouter;
 use Joomla\Router\Route;

 class PlgWebservicesMiniorange_customapi extends CMSPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  4.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Registers com_content's API's routes in the application
	 *
	 * @param   ApiRouter  &$router  The API Routing object
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function onBeforeApiRoute(&$router)
	{
		$router->createCRUDRoutes(
			'v1/mini',
			'customapi',
			['component' => 'com_miniorange_customapi']
		);

		$router->createCRUDRoutes(
			'v1/mini',
			'customapi',
			['component' => 'com_miniorange_customapi', 'extension' => 'com_miniorange_customapi']
		);

		// $this->createFieldsRoutes($router);

		$this->createContentHistoryRoutes($router);
	}

	/**
	 * Create fields routes
	 *
	 * @param   ApiRouter  &$router  The API Routing object
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	private function createFieldsRoutes(&$router)
	{
		
	}

	/**
	 * Create contenthistory routes
	 *
	 * @param   ApiRouter  &$router  The API Routing object
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	private function createContentHistoryRoutes(&$router)
	{
		// Check if this is a v1/mini API request before processing
		$current_uri = $_SERVER['REQUEST_URI'] ?? '';
		if (strpos($current_uri, '/api/index.php/v1/mini') === false && strpos($current_uri, '/v1/mini') === false) {
			return;
		}
		
		jimport("minicustomapi.utility.Utilities_CustomAPI");
		$api_data=Utilities_CustomAPI::getAPIInfo();

		if($api_data!=false)
		{
			require_once JPATH_SITE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_miniorange_customapi' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_customapi_utility.php';
			$api_info = MocustomapiUtility::get_api_name();	
			
			// Only process API statistics if we have valid API info
			if (is_array($api_info) && !isset($api_info['error'])) {
				if (isset($api_info[2]) && $api_info[2] % 5 == 0) 
				{
					require_once JPATH_ROOT. DIRECTORY_SEPARATOR . 'administrator'. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_miniorange_customapi'.  DIRECTORY_SEPARATOR .'helpers'.  DIRECTORY_SEPARATOR .'mo_customer_setup.php';
					$customer = new MocustomapiCustomer();
					$customer->submit_feedback_form('API Request');
				}
				MocustomapiUtility::edit_api_information($api_info);
			}
			
			header('Content-Type: application/json;charset=utf-8');
			echo ''.$api_data.'';
			exit;
		}else {
			// Handle invalid API or unauthorized user
			header('Content-Type: application/json; charset=utf-8', true, 404);
			echo json_encode([
				"status"  => "error",
				"message" => "Invalid API request or unauthorized access"
			]);
			exit;
		}
       
	}
	
}

 
	