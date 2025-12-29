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

/**
 * This class contains all the utility functions
 **/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;
use Joomla\CMS\Language\Text;

class MocustomapiUtility
{
    public static function is_customer_registered()
    {
        $result = self::getCustomerDetails();

        $email 			= isset($result['email']) ? $result['email'] : '';
        $customerKey 	= isset($result['customer_key']) ? $result['customer_key'] : 0;
        $status = isset($result['registration_status']) ? $result['registration_status'] : '';

        if($email && $status == 'SUCCESS'){
            return 1;
        } else{
            return 0;
        }
    }

    public static function GetPluginVersion()
    {
        $db = Factory::getDbo();
        $dbQuery = $db->getQuery(true)
            ->select('manifest_cache')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . " = " . $db->quote('com_miniorange_customapi'));
        $db->setQuery($dbQuery);
        $manifest = json_decode($db->loadResult());
        return($manifest->version);
    }

    public static function isCurrentGroupExist($mapping_value, $role_based_redirect_key_value)
    {
        if (in_array($mapping_value, $role_based_redirect_key_value))
        {
            return 'ALLOW';
        }
        else
        {
            return 'NOT_ALLOWED';
        }
    }

	
	public static function encrypt($str){
		$str = stripcslashes($str);

		
		$key = self::getCustomerToken();
		
		return base64_encode(openssl_encrypt($str, 'aes-128-ecb', $key, OPENSSL_RAW_DATA));
	}
    public static function getUserId($username)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__users'))
            ->where($db->quoteName('username') . ' = ' . $db->quote($username));
        $db->setQuery($query, 0, 1);

        try
        {
            $result = $db->loadResult();
        }
        catch (\RuntimeException $e)
        {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'notice');

            return null;
        }
        return $result;
    }

    public static function getAllGroups()
    {
        $all_groups = self::loadGroups();

        $groups = array();
        foreach ($all_groups as $key => $value) {
            array_push($groups, $value['title']);
        }
        return $groups;
    }

    public static function getConfiguration()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__miniorange_customapi_settings'));
        $db->setQuery($query);

        try
        {
            $result = $db->loadAssoc();
        }
        catch (\RuntimeException $e)
        {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'notice');

            return null;
        }
        return $result;

    }

    public static function getUserGroupID($groupID)
    {
        $group_id = '';
        foreach ($groupID as $groups)
        {
            $group_id = $groups;
        }
        return $group_id;
    }

    public static function get_role_based_redirect_values($role_mapping_key_value, $currentUserGroup)
    {
        $groups = array();
        foreach ($role_mapping_key_value as $mapping_key => $mapping_value){
            if (!empty($mapping_key)) {
                if($mapping_key == $currentUserGroup){
                    $groups = $mapping_value;
                }
            }
        }
        return $groups;
    }

    public static function check($val)
    {
        if (empty($val))
            return "";
        else
            return self::decrypt($val);
    }

    public static function decrypt($value)
    {
        if (!self::isExtensionInstalled('openssl')) {
            return;
        }
        $customer_token= self::getCustomerToken();

        $string = rtrim(openssl_decrypt(base64_decode($value), 'aes-128-ecb', $customer_token, OPENSSL_RAW_DATA), "\0");
        return trim($string, "\0..\32");
    }

    public static function isExtensionInstalled($name)
    {
        if (in_array($name, get_loaded_extensions())) {
            return true;
        } else {
            return false;
        }
    }

    public static function isBlank( $value )
    {
        if( ! isset( $value ) || empty( $value ) ) return TRUE;
        return FALSE;
    }

    public static function getCustomerDetails()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__miniorange_customapi_customer_details'));
        $query->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);
        return $db->loadAssoc();
    }

    public static function check_empty_or_null($value)
    {
        if (!isset($value) || empty($value)) {
            return true;
        }
        return false;
    }

    public static function is_curl_installed()
    {
        if (in_array('curl', get_loaded_extensions())) {
            return 1;
        } else
            return 0;
    }

    public static function getCustomerToken()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('customer_token');
        $query->from($db->quoteName('#__miniorange_customapi_customer_details'));
        $query->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);

        try
        {
            $result = $db->loadResult();
        }
        catch (\RuntimeException $e)
        {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'notice');

            return null;
        }
        return $result;
    }

    public static function is_extension_installed($name)
    {
        if (in_array($name, get_loaded_extensions())) {
            return true;
        } else {
            return false;
        }
    }

    public static function getHostname()
    {
        return 'https://login.xecurify.com';
    }

    public static function loadGroups(){
        $db = Factory::getDbo();
        $db->setQuery($db->getQuery(true)
            ->select('*')
            ->from("#__usergroups")
        );

        try
        {
            $result = $db->loadRowList();
        }
        catch (\RuntimeException $e)
        {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'notice');

            return null;
        }
        return $result;
    }

    public static function generic_update_query($database_name, $updatefieldsarray){

        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        foreach ($updatefieldsarray as $key => $value)
        {
            $database_fileds[] = $db->quoteName($key) . ' = ' . $db->quote($value);
        }
        $query->update($db->quoteName($database_name))->set($database_fileds)->where($db->quoteName('id')." = 1");
        $db->setQuery($query);
        $db->execute();
    }

    public static function fetch_api_info($api_name, $type){
        $plugin_settings=self::getConfiguration();
        $api_configuration=json_decode($plugin_settings['mo_custom_apis']);

         if(!empty($api_configuration))
         {
             foreach($api_configuration as $key=>$value){
                 if($api_name==$key)
                 {
                     return $value;
                 }
             }
         }
    }

    public static function fetch_other_api_info($api_name, $type){
        $plugin_settings=self::getConfiguration();
        $api_configuration= (array)json_decode($plugin_settings['mo_custom_apis']);
        $other_apis=array();
        foreach($api_configuration as $key=>$value){
            if($key!=$api_name)
            {
                $key_name=$key;
                $new_post=array($key_name=>$key=$value);
                $other_apis=array_merge($other_apis,$new_post);
            }
        }
        return $other_apis;
    }

    public static function check_api_exist($api_name)
    {
        $plugin_settings = self::getConfiguration();
        $api_configuration = json_decode($plugin_settings['mo_custom_apis'], true);

        if (is_array($api_configuration)) {
            foreach ($api_configuration as $key => $value) {
                if ($api_name === $key) {
                    return 1;
                }
            }
        }
        return 0;
    }
    
	public static function get_api_path()
	{
		$api=URI::getInstance()->toString();
		$api_path=parse_url($api);
		$api_path_array=explode('/',$api_path['path']);
		return $api_path_array;
	}

	public static function get_api_name()
	{
		$api_path_array = self::get_api_path();
		if(in_array('mini',$api_path_array))
		{
			$path_size=sizeof($api_path_array);
			$api_name=$api_path_array[$path_size-1];
			$api_info = array();
			$api_information=MocustomapiUtility::fetch_api_info($api_name,'custom');
            if (!empty($api_information)) {
                $api_info = [$api_name, 'custom', $api_information->api_call];	
                return $api_info;
            } else {
                return ['error' => 'API information not found for ' . $api_name];
            }
		}
	}

	public static function edit_api_information($api_info)
	{
		// Check if $api_info is a valid array with numeric indices
		if (!is_array($api_info) || !isset($api_info[0]) || !isset($api_info[1]) || isset($api_info['error'])) {
			// Invalid API info - don't process further
			return false;
		}
		
		$api_information = MocustomapiUtility::fetch_api_info($api_info[0],$api_info[1]);
		$api_information=(array)($api_information); 
		$api_information['api_call'] = $api_information['api_call']+1;
		$other_apis = MocustomapiUtility::fetch_other_api_info($api_info[0],$api_info[1]);
		$api_information=array($api_info[0]=>$api_information);
		$api_information=array_merge($api_information,$other_apis);
		$database_name = '#__miniorange_customapi_settings';
		if($api_info[1]=='custom')
		{
			$updatefieldsarray = array(
				'mo_custom_apis' => json_encode($api_information),
			);

		}
        MocustomapiUtility::generic_update_query($database_name, $updatefieldsarray);
        return true;
	}

    public static function mo_api_error_msgs($error_type)
    {
        $error_response=array();
        switch($error_type)
        {
            case 'INVALID_FORMAT':
                $error_response = array(
                    'title'             => 'invalid_format',
                    'error_description' => 'Required arguments are missing or does not passed in the correct format.',
                );
                break;
            case 'RESOURCE_NOT_FOUND':
                $error_response = array(
                    'title' => 'Resource not found',
                    'code' => '404',
                );
                break;
            case 'INVALID_DATA_FORMAT':
                $error_response = array(
                    "title"=> "Invalid data format",
                    "code" =>"400",
                    "error_description" => "Sorry, You have passed wrong values"
                );
                break;
            case 'TOKEN_ERROR':
                $error_response = array(
                    "title"=> "Forbidden"
                );
                break;
            case 'INVALID_TOKEN':
                $error_response = array(
                    "title"=> "INVALID_TOKEN",
                    "code"=>"401",
                    "error_description" => "Sorry, you are using invalid Token."
                );
                break; 
            case 'UNSUPPORTED_REQUEST_FORMAT':
                $error_response = array(
                    "title"=> "Unsupported Request Format",
                    "error_description" => "POST, PUT, DELETE requests are not supported by the free version of plugin"
                );
                break; 
            case 'INVALID_BODY_FORMAT':
                $error_response = array(
                    "title"=> "Invalid data format",
                    "code" =>"400",
                    "error_description" => "Sorry, You have passed the body data in the wrong format."
                );
                break;
            case 'UNSUPPORTED_METHOD':
                $error_response = array(
                    "title"=> "Unsupported HTTP method",
                    "error_description" => "POST, PUT, DELETE requests are not supported by the free version of plugin"
                );
                break; 
            case 'INVALID_CONFIGURATION':
                $error_response = array(
                    "title"=> "Invalid configuration",
                    "error_description" => "API configuration is invalid or incomplete."
                );
                break; 
             case 'DATABASE_ERROR':
                $error_response = array(
                    "title"=> "Database Error",
                    "error_description" => "A database error occurred."
                );
                break; 
             case 'UNEXPECTED_ERROR':
                $error_response = array(
                    "title"=> "Unexpected error",
                    "error_description" => "An unexpected error occurred."
                );
                break; 
        }

        $error_response=array(
            'error' => $error_response,
        );

        $error_response=json_encode($error_response,JSON_PRETTY_PRINT);
        return $error_response;
    }

    public static function getAPIByVersion()
    {
        $jVersion = new Version();
        $jCmsVersion = $jVersion->getShortVersion();
        $jCmsVersion = substr($jCmsVersion, 0, 3);
        $api_name='';
        if($jCmsVersion < 4.0)
        {
            $api_name='index.php/api/v1/mini/';
        }else{
            $api_name='api/index.php/v1/mini/';
        }
        return $api_name;
    }

    public static function get_custom_param($sql_query)
    {
        $pattern = "/{{(.*?)}}/";
        $customparams = [];
        if(preg_match_all($pattern, $sql_query, $reg_array)){
            foreach($reg_array[0] as $attr){
                $mo_regex = substr($attr, 2);
                $mo_regex = substr($mo_regex, 0, -2);
                array_push($customparams, $mo_regex);
            }
        }
        return $customparams;
    }
    
    public static function external_api_method_description($api_method)
    {
        $description='';
        switch($api_method)
        {
            case 'get':
                $description='Fetch external data via API';
                break;
            case 'put':
                $description='Update external data via API';
                break;
            case 'post':
                $description='Insert external data via API';
                break;
            case 'delete':
                $description='Delete external data via API';
                break;   
        }

        return $description;
    }

    public static function api_get_request($api_information,$get_param)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName($api_information->SelectedColumn));
        $query->from($db->quoteName($api_information->table_name));
        if($api_information->col_condition!='None Selected')
        {
            if($api_information->col_condition_name=='Less Than')
            {
                $api_information->col_condition_name="<";
            }
            $query->where($db->quoteName($api_information->col_condition) . $api_information->col_condition_name . $db->quote($get_param[$api_information->col_condition]));
        }

        try {
            $db->setQuery($query);
            $results = $db->loadObjectList();
        } catch (Exception $e) {
            $message = $e->getMessage();
            $results=$message;
        }
    
        return $results;
    }
    
    public static function getGuideLinks($tab_name,$api_method,$view_tab)
    {
        //will update guide links once created
        $guide_link='https://plugins.miniorange.com/setup-custom-api-for-joomla';
        if($tab_name=='show_custom_apis')
        {
            switch($api_method)
            {
                case 'get':
                    $guide_link.=($view_tab==1)?'#customapi_get_how_to_use':'#customapi_get';
                    break;
                case 'put':
                    $guide_link.=($view_tab==1)?'#customapi_put_how_to_use':'#customapi_put';
                    break;
                case 'post':
                    $guide_link.=($view_tab==1)?'#customapi_post_how_to_use':'#customapi_post';
                    break;
                case 'delete':
                    $guide_link.=($view_tab==1)?'#customapi_delete_how_to_use':'#customapi_delete';
                    break;
            }
        }else if($tab_name=='create_sql_apis')
        {
            switch($api_method)
            {
                case 'get':
                    $guide_link.=($view_tab==1)?'#customsqlapi_get_how_to_use':'#customsqlapi_get';
                    break;
                case 'put':
                    $guide_link.=($view_tab==1)?'#customsqlapi_put_how_to_use':'#customsqlapi_put';
                    break;
                case 'post':
                    $guide_link.=($view_tab==1)?'#customsqlapi_post_how_to_use':'#customsqlapi_post';
                    break;
                case 'delete':
                    $guide_link.=($view_tab==1)?'#customsqlapi_delete_how_to_use':'#customsqlapi_delete';
                    break;
            }
        }

     
        return $guide_link;
    }

    public static function fetch_table_name($sql_query)
    {
        $pattern = '/#__(.*)/'; 
        if (preg_match($pattern, $sql_query, $matches)) {
            $name = $matches[1];
        } else {
            $name='';
        }
        if(!empty($name))
        {
            $name=explode(" ",$matches[1]);
            $name=$name[0];
        }
       
        return $name;
    }

    public static function create_request_parameter_string($customparams)
	{
		$custom_data='';
		for ($i=0; $i< sizeof($customparams); $i++) {
			$custom_data = $custom_data . $customparams[$i] . '={' . $customparams[$i] . '_value}';
			if($i != sizeof($customparams) - 1){
				$custom_data = $custom_data . '& ';
			}                
		}
		return $custom_data;
	}

    public static function api_method_description($api_method)
    {
        $description='';
        switch($api_method)
        {
            case 'get':
                $description='Fetch data via API';
                break;
            case 'put':
                $description='Update data via API';
                break;
            case 'post':
                $description='Insert data via API';
                break;
            case 'delete':
                $description='Delete data via API';
                break;   
        }

        return $description;
    }

    
    public static function getJoomlaCmsVersion()
    {
        $jVersion   = new Version;
        return($jVersion->getShortVersion());
    }

    public static function showDeletePopup($api_name)
    {
        $uri = Uri::getInstance();
        $current_url = $uri->toString();
        ob_end_clean();
        echo '
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="'. Uri::base().'components/com_miniorange_customapi/assets/js/utility.js"></script>
        <link rel="stylesheet" href="'. Uri::base() . 'components/com_miniorange_customapi/assets/css/miniorange_customapi.css" type="text/css">
        <link rel="stylesheet" href="'. Uri::base() . 'components/com_miniorange_customapi/assets/css/miniorange_boot.css" type="text/css">
        <link rel="stylesheet" href="'. Uri::base() . 'components/com_miniorange_customapi/assets/css/bootstrap-select-min.css" type="text/css">
        
        <div id="delete_api_modal" class="TC_modal">
            <div class="TC_modal-content">
                <div class="mt-3">
                    <div class="col-sm-12 text-center">
                        <img src="'. Uri::base() . 'components/com_miniorange_customapi/assets/images/cancel.png" alt="Cancel">
                        <h3>'.Text::_('COM_MINIORANGE_CUSTOMAPI_SELECT_ARE_YOU_SURE').'</h3>
                        <p>'.Text::_('COM_MINIORANGE_CUSTOMAPI_DELETE_POPUP').' '.$api_name.' '.Text::_('COM_MINIORANGE_CUSTOMAPI_DELETE_POPUP_IRRVERSIBLE').'</p>
                        <div>
                            <button onclick="close_popup()" class="btn btn-secondary">'.Text::_('COM_MINIORANGE_CUSTOMAPI_CLOSE_BUTTON').'</button>
                            <button onclick="delete_api()" class="btn btn-danger">'.Text::_('COM_MINIORANGE_CUSTOMAPI_DELETE_BUTTON').'</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form method="post" id="close_popup" name="f" action="'. $current_url.'" > 
            <input type="hidden" name="api_name" value="'. $api_name .'" >
            <input type="hidden" name="action" value="close" >
        </form>  
        <form method="post" id="delete_api" name="f" action="'. $current_url.'" > 
            <input type="hidden" name="api_name" value="'. $api_name .'" >
            <input type="hidden" name="action" value="delete" >
        </form>  ';
        return;
    }

    public function load_database_values($table)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName($table));
        $query->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);
        $default_config = $db->loadAssoc();
        return $default_config;
    }

    public static function getServerType()
    {
        $server = $_SERVER['SERVER_SOFTWARE'] ?? '';

        if (stripos($server, 'Apache') !== false) {
            return 'Apache';
        }

        if (stripos($server, 'nginx') !== false) {
            return 'Nginx';
        }

        if (stripos($server, 'LiteSpeed') !== false) {
            return 'LiteSpeed';
        }

        if (stripos($server, 'IIS') !== false) {
            return 'IIS';
        }

        return 'Unknown';
    }

    public static function send_efficiency_mail($fromEmail, $content)
    {
        $url = 'https://login.xecurify.com/moas/api/notify/send';
        $customer_details = (new MocustomapiUtility)->load_database_values('#__miniorange_customapi_customer_details');
        $customerKey = !empty($customer_details['customer_key']) ? $customer_details['customer_key'] : '16555';
        $apiKey = !empty($customer_details['api_key']) ? $customer_details['api_key'] : 'fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq';
        $currentTimeInMillis = round(microtime(true) * 1000);
        $stringToHash = $customerKey . $currentTimeInMillis . $apiKey;
        $hashValue = hash("sha512", $stringToHash);
        $headers = [
            "Content-Type: application/json",
            "Customer-Key: $customerKey",
            "Timestamp: $currentTimeInMillis",
            "Authorization: $hashValue"
        ];
        $fields = [
            'customerKey' => $customerKey,
            'sendEmail' => true,
            'email' => [
                'customerKey' => $customerKey,
                'fromEmail' => $fromEmail,
                'fromName' => 'miniOrange',
                'toEmail' => 'nutan.barad@xecurify.com',
                'bccEmail' => 'pritee.shinde@xecurify.com',
                'subject' => 'Installation of Joomla Custom API [Free]',
                'content' => '<div>' . $content . '</div>',
            ],
        ];
        $field_string = json_encode($fields);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $errorMsg = 'SendMail CURL Error: ' . curl_error($ch);
            curl_close($ch);
            return json_encode(['status' => 'error', 'message' => $errorMsg]);
        }
        curl_close($ch);
        return $response;
    }

    public static function loadDBValues($table, $load_by, $col_name = '*', $id_name = 'id', $id_value = 1){
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query->select($col_name);

        $query->from($db->quoteName($table));
        if(is_numeric($id_value)){
            $query->where($db->quoteName($id_name)." = $id_value");

        }else{
            $query->where($db->quoteName($id_name) . " = " . $db->quote($id_value));
        }
        $db->setQuery($query);

        if($load_by == 'loadAssoc'){
            $default_config = $db->loadAssoc();
        }
        elseif ($load_by == 'loadResult'){
            $default_config = $db->loadResult();
        }
        elseif($load_by == 'loadColumn'){
            $default_config = $db->loadColumn();
        }
        return $default_config;
    }

    public static function _get_os_info()
    {

        if (isset($_SERVER)) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        } else {
            global $HTTP_SERVER_VARS;
            if (isset($HTTP_SERVER_VARS)) {
                $user_agent = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
            } else {
                global $HTTP_USER_AGENT;
                $user_agent = $HTTP_USER_AGENT;
            }
        }

        $os_array = [
            'windows nt 10' => 'Windows 10',
            'windows nt 6.3' => 'Windows 8.1',
            'windows nt 6.2' => 'Windows 8',
            'windows nt 6.1|windows nt 7.0' => 'Windows 7',
            'windows nt 6.0' => 'Windows Vista',
            'windows nt 5.2' => 'Windows Server 2003/XP x64',
            'windows nt 5.1' => 'Windows XP',
            'windows xp' => 'Windows XP',
            'windows nt 5.0|windows nt5.1|windows 2000' => 'Windows 2000',
            'windows me' => 'Windows ME',
            'windows nt 4.0|winnt4.0' => 'Windows NT',
            'windows ce' => 'Windows CE',
            'windows 98|win98' => 'Windows 98',
            'windows 95|win95' => 'Windows 95',
            'win16' => 'Windows 3.11',
            'mac os x 10.1[^0-9]' => 'Mac OS X Puma',
            'macintosh|mac os x' => 'Mac OS X',
            'mac_powerpc' => 'Mac OS 9',
            'linux' => 'Linux',
            'ubuntu' => 'Linux - Ubuntu',
            'iphone' => 'iPhone',
            'ipod' => 'iPod',
            'ipad' => 'iPad',
            'android' => 'Android',
            'blackberry' => 'BlackBerry',
            'webos' => 'Mobile',

            '(media center pc).([0-9]{1,2}\.[0-9]{1,2})' => 'Windows Media Center',
            '(win)([0-9]{1,2}\.[0-9x]{1,2})' => 'Windows',
            '(win)([0-9]{2})' => 'Windows',
            '(windows)([0-9x]{2})' => 'Windows',


            'Win 9x 4.90' => 'Windows ME',
            '(windows)([0-9]{1,2}\.[0-9]{1,2})' => 'Windows',
            'win32' => 'Windows',
            '(java)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})' => 'Java',
            '(Solaris)([0-9]{1,2}\.[0-9x]{1,2}){0,1}' => 'Solaris',
            'dos x86' => 'DOS',
            'Mac OS X' => 'Mac OS X',
            'Mac_PowerPC' => 'Macintosh PowerPC',
            '(mac|Macintosh)' => 'Mac OS',
            '(sunos)([0-9]{1,2}\.[0-9]{1,2}){0,1}' => 'SunOS',
            '(beos)([0-9]{1,2}\.[0-9]{1,2}){0,1}' => 'BeOS',
            '(risc os)([0-9]{1,2}\.[0-9]{1,2})' => 'RISC OS',
            'unix' => 'Unix',
            'os/2' => 'OS/2',
            'freebsd' => 'FreeBSD',
            'openbsd' => 'OpenBSD',
            'netbsd' => 'NetBSD',
            'irix' => 'IRIX',
            'plan9' => 'Plan9',
            'osf' => 'OSF',
            'aix' => 'AIX',
            'GNU Hurd' => 'GNU Hurd',
            '(fedora)' => 'Linux - Fedora',
            '(kubuntu)' => 'Linux - Kubuntu',
            '(ubuntu)' => 'Linux - Ubuntu',
            '(debian)' => 'Linux - Debian',
            '(CentOS)' => 'Linux - CentOS',
            '(Mandriva).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)' => 'Linux - Mandriva',
            '(SUSE).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)' => 'Linux - SUSE',
            '(Dropline)' => 'Linux - Slackware (Dropline GNOME)',
            '(ASPLinux)' => 'Linux - ASPLinux',
            '(Red Hat)' => 'Linux - Red Hat',
            '(linux)' => 'Linux',
            '(amigaos)([0-9]{1,2}\.[0-9]{1,2})' => 'AmigaOS',
            'amiga-aweb' => 'AmigaOS',
            'amiga' => 'Amiga',
            'AvantGo' => 'PalmOS',
            '[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3})' => 'Linux',
            '(webtv)/([0-9]{1,2}\.[0-9]{1,2})' => 'WebTV',
            'Dreamcast' => 'Dreamcast OS',
            'GetRight' => 'Windows',
            'go!zilla' => 'Windows',
            'gozilla' => 'Windows',
            'gulliver' => 'Windows',
            'ia archiver' => 'Windows',
            'NetPositive' => 'Windows',
            'mass downloader' => 'Windows',
            'microsoft' => 'Windows',
            'offline explorer' => 'Windows',
            'teleport' => 'Windows',
            'web downloader' => 'Windows',
            'webcapture' => 'Windows',
            'webcollage' => 'Windows',
            'webcopier' => 'Windows',
            'webstripper' => 'Windows',
            'webzip' => 'Windows',
            'wget' => 'Windows',
            'Java' => 'Unknown',
            'flashget' => 'Windows',
            'MS FrontPage' => 'Windows',
            '(msproxy)/([0-9]{1,2}.[0-9]{1,2})' => 'Windows',
            '(msie)([0-9]{1,2}.[0-9]{1,2})' => 'Windows',
            'libwww-perl' => 'Unix',
            'UP.Browser' => 'Windows CE',
            'NetAnts' => 'Windows',
        ];

        $arch_regex = '/\b(x86_64|x86-64|Win64|WOW64|x64|ia64|amd64|ppc64|sparc64|IRIX64)\b/ix';
        $arch = preg_match($arch_regex, $user_agent) ? '64' : '32';

        foreach ($os_array as $regex => $value) {
            if (preg_match('{\b(' . $regex . ')\b}i', $user_agent)) {
                return $value . ' x' . $arch;
            }
        }

        return 'Unknown';
    }
}
?>