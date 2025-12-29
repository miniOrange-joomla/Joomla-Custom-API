<?php
/**
 * @package     Joomla.Component
 * @subpackage  com_miniorange_customapi
 *
 * @author      miniOrange Security Software Pvt. Ltd.
 * @copyright   Copyright (C) 2015 miniOrange (https://www.miniorange.com)
 * @license     GNU General Public License version 3; see LICENSE.txt
 * @contact     info@xecurify.com
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

require_once JPATH_COMPONENT . '/helpers/mo_customer_setup.php';
require_once JPATH_COMPONENT . '/helpers/mo_customapi_utility.php';

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_miniorange_customapi'))
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

$controller = BaseController::getInstance('MiniorangeCustomapi');
 
// Perform the Request task
// Backward compatibility for Joomla 3/4/5/6
$app = Factory::getApplication();
$input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
$task = ($input && method_exists($input, 'get')) ? $input->get('task') : '';
$controller->execute($task);
 
// Redirect if set by the controller
$controller->redirect();