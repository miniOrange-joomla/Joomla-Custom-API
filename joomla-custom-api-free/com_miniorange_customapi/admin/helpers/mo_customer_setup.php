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
 * This library is miniOrange Authentication Service.
 * Contains Request Calls to Customer service.
 **/

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class MocustomapiCustomer
{

    public $email;
    public $phone;
    public $customerKey;
    public $transactionId;

    /*
    ** Initial values are hardcoded to support the miniOrange framework to generate OTP for email.
    ** We need the default value for creating the OTP the first time,
    ** As we don't have the Default keys available before registering the user to our server.
    ** This default values are only required for sending an One Time Passcode at the user provided email address.
    */

    //auth
    private $defaultCustomerKey = "16555";
    private $defaultApiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";

    function create_customer()
    {
        if (!MocustomapiUtility::is_curl_installed()) {
            return json_encode(array("apiKey"=>'CURL_ERROR','token'=>'<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
        }
        $hostname = MocustomapiUtility::getHostname();

        $url = $hostname . '/moas/rest/customer/add';
       
        $current_user = Factory::getUser();
        $customer_details = MocustomapiUtility::getCustomerDetails();

        $this->email = isset($customer_details['email']) ? $customer_details['email'] : '';
        $this->phone = isset($customer_details['admin_phone']) ? $customer_details['admin_phone'] : '';
        $password = isset($customer_details['password']) ? $customer_details['password'] : '';

        $fields = array(
            'companyName' => $_SERVER['SERVER_NAME'],
            'areaOfInterest' => 'Joomla Custom API',
            'firstname' => $current_user->name,
            'lastname' => '',
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $password
        );
        $field_string = json_encode($fields);

        return self::curl_call($url,$field_string);
    }

    function check_status($code){
		
		$hostname = MocustomapiUtility::getHostname();
		$url = $hostname . '/moas/api/backupcode/verify';

		$customer_details = MocustomapiUtility::getCustomerDetails();
    
		$customerKey = $customer_details['customer_key'];
		$apiKey = $customer_details['api_key'];
	
		$fields = '';
		$fields = array(
			'code' => $code ,
			'customerKey' => $customerKey,
			'additionalFields' => array(
				'field1' => URI::root()	
			)
		);
	
		$field_string = json_encode($fields);

        return self::curl_call($url,$field_string);

	}


    function get_customer_key($email, $password)
    {
        if (!MocustomapiUtility::is_curl_installed()) {
            return json_encode(array("apiKey" => 'CURL_ERROR', 'token' => '<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
        }

        $hostname = MocustomapiUtility::getHostname();

        $url = $hostname . "/moas/rest/customer/key";

        $fields = array(
            'email' => $email,
            'password' => $password
        );
        $field_string = json_encode($fields);

        return self::curl_call($url,$field_string);
    }

    function submit_contact_us($q_email, $q_phone, $query)
    {
        if (!MocustomapiUtility::is_curl_installed()) {
            return json_encode(array("status" => 'CURL_ERROR', 'statusMessage' => '<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
        }
        $j_cms_version = MocustomapiUtility::getJoomlaCmsVersion();
        $mo_plugin_version = MocustomapiUtility::GetPluginVersion();
        $php_version = phpversion();
        $hostname = MocustomapiUtility::getHostname();
        $url = $hostname . "/moas/rest/customer/contact-us";
        $current_user = Factory::getUser();
        $subject = "Query for miniOrange Joomla Custom API Free  - " .$q_email;
        $query = '[Joomla Custom API Free: Joomla version-'. $j_cms_version . '| Plugin version-'. $mo_plugin_version . '|PHP version-'. $php_version . ':' . $query;
        $fields = array(
            'firstName' => $current_user->username,
            'lastName' => '',
            'company' => $_SERVER['SERVER_NAME'],
            'email' => $q_email,
            'ccEmail' => 'joomlasupport@xecurify.com',
            'phone' => $q_phone,
            'subject' => $subject,
            'query' => $query
        );
        $field_string = json_encode($fields);

        return self::curl_call($url,$field_string);
    }

    function check_customer($email)
    {
        if (!MocustomapiUtility::is_curl_installed()) {
            return json_encode(array("status" => 'CURL_ERROR', 'statusMessage' => '<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
        }
        $hostname = MocustomapiUtility::getHostname();
        $url = $hostname . "/moas/rest/customer/check-if-exists";

        $fields = array(
            'email' => $email,
        );
        $field_string = json_encode($fields);

        return self::curl_call($url,$field_string);
    }

    function send_otp_token($auth_type, $phone)
    {
        $hostname = MocustomapiUtility::getHostname();
        $url = $hostname . '/moas/api/auth/challenge';
        $customer_details = MocustomapiUtility::getCustomerDetails();
        $username= $customer_details['email'];
        
        if ($auth_type == "EMAIL") {
            $fields = array(
                'customerKey' => $this->defaultCustomerKey,
                'email' => $username,
                'authType' => $auth_type,
                'transactionName' => 'Joomla Custom API Free'
            );
        } else {
            $fields = array(
                'customerKey' => $this->defaultCustomerKey,
                'phone' => $phone,
                'authType' => $auth_type,
                'transactionName' => 'Joomla Custom API Free'
            );
        }
        $field_string = json_encode($fields);
        return self::curl_call($url,$field_string);
    }

    function validate_otp_token($transactionId, $otpToken)
    {
        $hostname = MocustomapiUtility::getHostname();
        $url = $hostname . '/moas/api/auth/validate';

        $fields = '';

        //*check for otp over sms/email
        $fields = array(
            'txId' => $transactionId,
            'token' => $otpToken,
        );

        $field_string = json_encode($fields);

        return self::curl_call($url,$field_string); 
    }

    function submit_feedback_form($action)
    {

        $hostname = MocustomapiUtility::getHostname();
        $url = $hostname . '/moas/api/notify/send';

        $customerKey = "16555";
        $apiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";
        $customer_details=MocustomapiUtility::getCustomerDetails();
        $dVar=new JConfig();
        $check_email = $dVar->mailfrom;
        $admin_email = !empty($customer_details['admin_email']) ? $customer_details['admin_email'] :$check_email;
        $admin_phone  = isset($details ['admin_phone']) ? $details ['admin_phone'] : '';
        $j_cms_version = MocustomapiUtility::getJoomlaCmsVersion();
        $mo_plugin_version = MocustomapiUtility::GetPluginVersion();
        $php_version = phpversion();

        $ccEmail='pritee.shinde@xecurify.com'; 
        $bccEmail='nutan.barad@xecurify.com';
        $sys_information ='[ Plugin Name '.$mo_plugin_version.' | Joomla ' . $j_cms_version.' | PHP ' . $php_version.'] ';
        $content = '<div >Hello, <br><br>
                    <strong>Company :<a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" ></strong>' . $_SERVER['SERVER_NAME'] . '</a><br><br>
                    <strong>Phone Number :<strong>' . $admin_phone . '<br><br>
                    <strong>Admin Email :<a href="mailto:' . $admin_email . '" target="_blank">' . $admin_email . '</a></strong><br><br>
                    <strong>Action:</strong> '.$action .'<br><br>
                    <strong>System Information:</strong> '.$sys_information .'<br><br>';
        $subject = "miniOrange Joomla Custom API [Free] for Efficiency";

        $fields = array(
            'customerKey' => $customerKey,
            'sendEmail' => true,
            'email' => array(
                'customerKey' 	=> $customerKey,
                'fromEmail' 	=> $admin_email,
                'bccEmail' 		=> $bccEmail,
                'fromName' 		=> 'miniOrange',
                'toEmail' 		=> $ccEmail,
                'toName' 		=> $bccEmail,
                'subject' 		=> $subject,
                'content' 		=> $content
            ),
        );
        $field_string = json_encode($fields);

        self::curl_call($url,$field_string);

    }

    public static function submit_uninstall_feedback_form($email, $phone, $query,$cause)
    {
        $url = 'https://login.xecurify.com/moas/api/notify/send';

        $customerKey = "16555";
        $apiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";
        $fromEmail = $email;
        $phpVersion = phpversion();
        $dVar=new JConfig();
        $check_email = $dVar->mailfrom;
        $jCmsVersion =  MocustomapiUtility::getJoomlaCmsVersion();;
        $moPluginVersion =  MocustomapiUtility::GetPluginVersion();
        $os_version    = MocustomapiUtility::_get_os_info();
        $pluginName    = 'Custom API Free Plugin';
        $admin_email   = !empty($email)?$email:$check_email;
        
        $query1 = '['.$pluginName.' | Plugin '.$moPluginVersion.' | PHP ' . $phpVersion.' | Joomla ' . $jCmsVersion.' | OS ' . $os_version.'] ';
        
        $ccEmail = 'joomlasupport@xecurify.com';
        $bccEmail = 'joomlasupport@xecurify.com';
        $content = '<div>Hello, <br><br>'
                . '<strong>Company: </strong><a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank">' . $_SERVER['SERVER_NAME'] . '</a><br><br>'
                . '<strong>Phone Number: </strong>' . $phone . '<br><br>'
                . '<strong>Admin Email: </strong><a href="mailto:' .$admin_email . '" target="_blank">' . $admin_email . '</a><br><br>'
                . '<strong>Feedback: </strong>' . $query . '<br><br>'
                . '<strong>Additional Details: </strong>' . $cause . '<br><br>'
                . '<strong>System Information: </strong>' . $query1 
                . '</div>';
        
        $subject = "Feedback for miniOrange Joomla Custom API Free Plugin";

        $fields = array(
            'customerKey' => $customerKey,
            'sendEmail' => true,
            'email' => array(
                'customerKey' 	=> $customerKey,
                'fromEmail' 	=> $fromEmail,
                'bccEmail' 		=> $bccEmail,
                'fromName' 		=> 'miniOrange',
                'toEmail' 		=> $ccEmail,
                'toName' 		=> $bccEmail,
                'subject' 		=> $subject,
                'content' 		=> $content
            ),
        );
        $field_string = json_encode($fields);

        return self::curl_call($url,$field_string);
    }

    public static function curl_call($url,$field_string)
    {
        $ch = curl_init($url);
        $customer_details = (new MocustomapiUtility)->load_database_values('#__miniorange_customapi_customer_details');
        $customerKey = !empty($customer_details['customer_key'])?$customer_details['customer_key']:'16555';
        $apiKey = !empty($customer_details['api_key'])?$customer_details['api_key']:'fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq';
        /* Current time in milliseconds since midnight, January 1, 1970 UTC. */
        $currentTimeInMillis = round(microtime(true) * 1000);
     
        /* Creating the Hash using SHA-512 algorithm */
        $stringToHash = $customerKey . number_format($currentTimeInMillis, 0, '', '') . $apiKey;
        $hashValue = hash("sha512", $stringToHash);

        $customerKeyHeader = "Customer-Key: " . $customerKey;
        $timestampHeader = "Timestamp: " . number_format($currentTimeInMillis, 0, '', '');
        $authorizationHeader = "Authorization: " . $hashValue;
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls

        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader,
            $timestampHeader, $authorizationHeader));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        $content = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = 'Request Error: ' . curl_error($ch);
            return $error;
        }
        curl_close($ch);
        
        return $content;
    }

    function request_for_trial($email, $plan,$demo,$description = '', $phone = '')
    {
        $hostname = MocustomapiUtility::getHostname();
        $url = $hostname . '/moas/api/notify/send';
        $customerKey = "16555";
        $apiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";
        $fromEmail = $email;
        $subject = 'miniOrange Custom API Request for '.$demo;
        $phpVersion = phpversion();
        $jCmsVersion = MocustomapiUtility::getJoomlaCmsVersion();
        $moPluginVersion =  MocustomapiUtility::GetPluginVersion();

        $pluginInfo = '[Plugin '.$moPluginVersion.'| Joomla ' . $jCmsVersion.' | PHP ' . $phpVersion.'] : ' .$plan;

        $phoneInfo = !empty($phone) ? '<strong>Phone Number: </strong>' . $phone . '<br><br>' : '';

        $content = '<div >Hello, <br>
                        <br><strong>Company :</strong><a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" >' . $_SERVER['SERVER_NAME'] . '</a><br><br>
                        <strong>Email :</strong><a href="mailto:' . $fromEmail . '" target="_blank">' . $fromEmail . '</a><br><br>
                        ' . $phoneInfo . '
                        <strong>Plugin Info: </strong>'.$pluginInfo.'<br><br>
                        <strong>Description: </strong>' . $description . '</div>';

        $fields = array(
            'customerKey' => $this->defaultCustomerKey,
            'sendEmail' => true,
            'email' => array(
                'customerKey' =>$this->defaultCustomerKey,
                'fromEmail' => $fromEmail,
                'fromName' => 'miniOrange',
                'toEmail' => 'joomlasupport@xecurify.com',
                'toName' => 'joomlasupport@xecurify.com',
                'subject' => $subject,
                'content' => $content
            ),
        );
        $field_string = json_encode($fields);

        return self::curl_call($url,$field_string,0);
    }
    
} ?>
