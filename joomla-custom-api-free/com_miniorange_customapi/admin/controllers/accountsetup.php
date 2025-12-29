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

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class MiniorangeCustomapiControllerAccountsetup extends FormController
{
    function __construct()
    {
        $this->view_list = 'accountsetup';
        parent::__construct();
    }

    function contactUs()
    {

        // Backward compatibility for Joomla 3/4/5/6
        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];

        if (count($post) == 0) {
            $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=account');
            return;
        }

        $query_email = isset($post['query_email']) ? $post['query_email'] : '';
        $query = isset($post['query_support']) ? $post['query_support'] : '';
        $phone = isset($post['query_phone']) ? $post['query_phone'] : '';
        $country_code = isset($post['country_code']) ? $post['country_code'] : '';
        $support_type = isset($post['support_type']) ? $post['support_type'] : 'general_query';
        $call_date = isset($post['call_date']) ? $post['call_date'] : '';
        $call_time = isset($post['call_time']) ? $post['call_time'] : '';

        // Combine country code and phone number if both are provided
        if (!empty($country_code) && !empty($phone)) {
            $phone = $country_code . ' ' . $phone;
        }

        if (MocustomapiUtility::check_empty_or_null($query_email) || MocustomapiUtility::check_empty_or_null($query)) {
            $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=trial_demo', Text::_('COM_MINIORANGE_CUSTOMAPI_PLEASE_SUBMIT_QUERY'), 'error');
            return;
        } else {
            // Check if setup call is selected but call date or time is not provided
            if ($support_type == 'setup_call') {
                if (MocustomapiUtility::check_empty_or_null($call_date)) {
                    $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=trial_demo', Text::_('COM_MINIORANGE_CUSTOMAPI_MSG_SELECT_DATE'), 'error');
                    return;
                }
                if (MocustomapiUtility::check_empty_or_null($call_time)) {
                    $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=trial_demo', Text::_('COM_MINIORANGE_CUSTOMAPI_MSG_SELECT_CALL'), 'error');
                    return;
                }
            }
            
            $query_with_details = $query;
            
            if ($support_type == 'setup_call') {
                // Combine date and time for display
                $datetime_string = $call_date . ' ' . $call_time;
                $formatted_datetime = date('F j, Y \a\t g:i A', strtotime($datetime_string));
                $query_with_details .= "\n\n--- Support Call Reuest Details ---";
                $query_with_details .= "\nFull DateTime: " . $formatted_datetime;
            }

            $contact_us = new MocustomapiCustomer();
            $submited = $contact_us->submit_contact_us($query_email, $phone, $query_with_details);
            
            if($submited=='Query submitted.')
            {
                if ($support_type == 'setup_call') {
                    $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=overview', Text::_('COM_MINIORANGE_CUSTOMAPI_MSG_THANK_YOU'));
                } else {
                    $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=overview', Text::_('COM_MINIORANGE_CUSTOMAPI_THANKU_FEEDBACK'));
                }
                return;
            }else if($submited=='Invalid email.')
            {
                $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=overview', Text::_('COM_MINIORANGE_CUSTOMAPI_EMAIL_VERIFICATION'), 'error');
                return;
            }
            else 
            {
                $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=overview', Text::_('COM_MINIORANGE_CUSTOMAPI_QUERY_NOT_SUBMITTED'), 'error');
                return;
            }
        }
    }

    function saveAPIInformation()
    {
        // Backward compatibility for Joomla 3/4/5/6
        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];
        $check_api_exist=MocustomapiUtility::check_api_exist($post['api_name']);

        if(!isset($post['api_name']) || empty($post['api_name']))
        {
            $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=create_custom_api', Text::_('COM_MINIORANGE_CUSTOMAPI_ENTER_API_NAME'), 'error');
            return;
        }

        if($check_api_exist && (isset($post['edit_api']) && $post['edit_api']!=1))
        {
            $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=create_custom_api',Text::_('COM_MINIORANGE_CUSTOMAPI_NAME_EXISTS'), 'error');
            return;
        }
        else
        {
            $api_configuration =MocustomapiUtility::fetch_other_api_info($post['api_name'], 'custom');
            $db=Factory::getDBO();
            $prefix=$db->getPrefix();
            $table_name=str_replace($prefix,'#__',$post['mo_table_name']);
            $post["table_name"]=$table_name;
            $new_post=array($post['api_name']=>$post);
            $api_configuration=array_merge($api_configuration,$new_post);
            $database_name = '#__miniorange_customapi_settings';
            $updatefieldsarray = array(
                'mo_custom_apis' => json_encode($api_configuration ),
            );
    
            MocustomapiUtility::generic_update_query($database_name, $updatefieldsarray);
           $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=create_custom_api&api_name='.$post["api_name"].'');
        }
    }

    function createAPI()
    {
        // Backward compatibility for Joomla 3/4/5/6
        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];
        $post['api_call']=0;
        $api_configuration = MocustomapiUtility::fetch_other_api_info($post['api_name'],'custom');
        if(!isset($post['api_name']) || empty($post['api_name']))
        {
            $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=create_custom_api',Text::_('COM_MINIORANGE_CUSTOMAPI_ENTER_API_NAME'), 'error');
            return;
        }
       
        $new_post=array($post['api_name']=>$post);
        $api_configuration=array_merge($api_configuration,$new_post);
        
        $database_name = '#__miniorange_customapi_settings';
        $updatefieldsarray = array(
            'mo_custom_apis' => json_encode($api_configuration),
        );

        MocustomapiUtility::generic_update_query($database_name, $updatefieldsarray);
        $customer = new MocustomapiCustomer();
        if((isset($post['edit_api']) && $post['edit_api']!=1))
        {
            $customer->submit_feedback_form('Created Custom API');
        }else 
        {
            $customer->submit_feedback_form('Edited Custom API'); 
        }
        $message = Text::_('COM_MINIORANGE_CUSTOMAPI_SUCCESSFUL_API') . ' ' . $post['api_name'] . ' ' . Text::_('COM_MINIORANGE_CUSTOMAPI_CUSTOM_API_PLUGIN');
        $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=view_custom_api&api_name='.$post["api_name"].'',$message);
    }

    function deleteCurrentAPI()
    {
        // Backward compatibility for Joomla 3/4/5/6
        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];
        $api_name = $post['api_name'];
        
        if(isset($post['action']) && ('delete' == $post['action']))
        {
            $api_configuration = MocustomapiUtility::fetch_other_api_info($post['api_name'], 'custom');
            $database_name = '#__miniorange_customapi_settings';
            $updatefieldsarray = array(
                'mo_custom_apis' => json_encode($api_configuration),
            );
    
            $message = Text::_('COM_MINIORANGE_CUSTOMAPI_SUCCESSFULL_EXTERNAL_API_DELETION'). ' ' .$api_name. ' ' . Text::_('COM_MINIORANGE_CUSTOMAPI_CUSTOM_API_PLUGIN');
            MocustomapiUtility::generic_update_query($database_name, $updatefieldsarray);
            $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=show_custom_apis', $message);
            return;
        }

        $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=show_custom_apis');
    }

    function requestForTrialPlan()
    {
        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];
        if ((!isset($post['email'])) || (!isset($post['plan'])) || (!isset($post['description']))) {
            $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=trial_demo',Text::_('COM_MINIORANGE_CUSTOMAPI_MSG_A'), 'error');
            return;
        }
        $email = $post['email'];
        $plan = $post['plan'];
        $description = trim($post['description']);
        $demo = 'Trial';
        
        // Handle phone number data
        $phone = isset($post['query_phone']) ? $post['query_phone'] : '';
        $country_code = isset($post['country_code']) ? $post['country_code'] : '';
        
        // Combine country code and phone number if both are provided
        if (!empty($country_code) && !empty($phone)) {
            $phone = $country_code . ' ' . $phone;
        }
        
        if ( empty($email) ||empty($plan) || empty($description)) {
            $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=trial_request', Text::_('COM_MINIORANGE_CUSTOMAPI_MSG_A'), 'error');
            return;
        }

        $customer = new MocustomapiCustomer();
        $response = json_decode($customer->request_for_trial($email, $plan, $demo, $description, $phone));

        if ($response->status != 'ERROR')
        {
            $msg=($demo == 'Demo')? Text::sprintf('COM_MINIORANGE_CUSTOMAPI_MSG_B',$email):Text::sprintf('COM_MINIORANGE_CUSTOMAPI_MSG_C',$email);
            $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=trial_request', $msg);
        }
        else {
            $this->setRedirect('index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=trial_request',Text::_('COM_MINIORANGE_CUSTOMAPI_MSG_D'), 'error');
            return;
        }
    }
}