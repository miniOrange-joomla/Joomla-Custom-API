<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  plg_system_miniorangecustomapi
 *
 * @author      miniOrange Security Software Pvt. Ltd.
 * @copyright   Copyright (C) 2015 miniOrange (https://www.miniorange.com)
 * @license     GNU General Public License version 3; see LICENSE.txt
 * @contact    info@xecurify.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Uri\Uri;
jimport('joomla.plugin.plugin');

include_once JPATH_SITE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_miniorange_customapi' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_customapi_utility.php';

class plgSystemMiniorangecustomapi extends CMSPlugin
{
    public function onAfterInitialise()
    {

        // Backward compatibility for Joomla 3/4/5/6
        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $get = ($input && $input->get) ? $input->get->getArray() : [];
        $post = ($input && $input->post) ? $input->post->getArray() : [];
        $tab = 0;
        $tables = MocustomapiUtility::moGetDatabase()->getTableList();
    

        foreach ($tables as $table) {
            if ((strpos($table, "miniorange_customapi_customer_details") !== FALSE) ||(strpos($table, "miniorange_customapi_settings") !== FALSE)  )
                $tab = $table;
        }
        if ($tab === 0)
            return;


        if (isset($post['mojsp_feedback']) || isset($post['mojspfree_skip_feedback'])) {
        
            if($tab)
            {
                $radio = isset($post['deactivate_plugin'])? $post['deactivate_plugin']:'';
                $data = isset($post['query_feedback'])?$post['query_feedback']:'';
                $feedback_email = isset($post['feedback_email'])? $post['feedback_email']:'';
    
                $database_name = '#__miniorange_customapi_settings';
                $updatefieldsarray = array(
                    'uninstall_feedback' => 1,
                );
                $result = new MocustomapiUtility();
                $result->generic_update_query($database_name, $updatefieldsarray);
                $current_user = Factory::getUser();
    
                 $customerResult = new MocustomapiUtility();
                 $customerResult = $customerResult->load_database_values('#__miniorange_customapi_customer_details');
    
                $dVar=new JConfig();
                $check_email = $dVar->mailfrom;
                $admin_email = !empty($customerResult['admin_email']) ? $customerResult['admin_email'] :$check_email;
                $admin_email = !empty($admin_email)?$admin_email:self::getSuperUser();
                $admin_phone = $customerResult['admin_phone'];
                $data1 = $radio . ' : ' . $data . '  <br><br><strong>Email:</strong>  ' . $feedback_email;

                // Timezone (browser -> user -> site)
                $client_timezone = isset($post['client_timezone']) ? (string) $post['client_timezone'] : '';
                $client_timezone_offset = null;
                if (isset($post['client_timezone_offset']) && preg_match('/^-?\d+$/', (string) $post['client_timezone_offset'])) {
                    $client_timezone_offset = (int) $post['client_timezone_offset'];
                }
                $user = Factory::getUser();
                $config = Factory::getConfig();
                $tzName = trim((string) $client_timezone);
                if ($tzName === '') {
                    $tzName = (string) $user->getParam('timezone');
                }
                if (trim((string) $tzName) === '') {
                    $tzName = (string) $config->get('offset');
                }
                $timezone = trim((string) MocustomapiUtility::format_timezone_with_utc_offset($tzName, $client_timezone_offset));
                
    
                if(isset($post['mojspfree_skip_feedback']))
                {
                    $data1='Skipped the feedback';
                }
    
                if(file_exists(JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_miniorange_customapi' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_customer_setup.php'))
                {
                    require_once JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_miniorange_customapi' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_customer_setup.php';
    
                    MocustomapiCustomer::submit_uninstall_feedback_form($admin_email, $admin_phone, $data1,'', $timezone);
                }
              
                require_once JPATH_SITE . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Installer' . DIRECTORY_SEPARATOR . 'Installer.php';
    
                foreach ($post['result'] as $fbkey) {
    
                    $result = MocustomapiUtility::loadDBValues('#__extensions', 'loadColumn','type',  'extension_id', $fbkey);
                    $identifier = $fbkey;
                    $type = 0;
                    foreach ($result as $results) {
                        $type = $results;
                    }
    
                    if ($type) {
                        $cid = 0;
                        try {
                            $installer = null;
                            // Try Joomla 4+ dependency injection container first
                            if (method_exists('Joomla\CMS\Factory', 'getContainer')) {
                                try {
                                    $container = Factory::getContainer();
                                    if ($container && method_exists($container, 'get')) {
                                        $installer = $container->get(Installer::class);
                                    }
                                } catch (Exception $e) {
                                    // Container approach failed, continue to fallback
                                }
                            }
                            
                            // Fallback: manual instantiation for all versions
                            if (!$installer) {
                                $installer = new Installer();
                                if (method_exists($installer, 'setDatabase')) {
                                    $installer->setDatabase(MocustomapiUtility::moGetDatabase());
                                }
                            }
                            
                            $installer->uninstall($type, $identifier, $cid);
                            
                        } catch (Exception $e) {
                            $app = Factory::getApplication();
                            if (method_exists($app, 'enqueueMessage')) {
                                $app->enqueueMessage('Error uninstalling extension: ' . $e->getMessage(), 'warning');
                            }
                        }
                    }
                }
            }
    
        }
    } 
    
    public static function getSuperUser()
    {
        $db = MocustomapiUtility::moGetDatabase();
        $query = $db->getQuery(true)->select('user_id')->from('#__user_usergroup_map')->where('group_id=' . $db->quote(8));
        $db->setQuery($query);
        $results = $db->loadColumn();
        return  $results[0];
    }
    

    function onExtensionBeforeUninstall($id)
    {
        // Backward compatibility for Joomla 3/4/5/6
        $app = Factory::getApplication();
        $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
        $post = ($input && $input->post) ? $input->post->getArray() : [];
        $tables = MocustomapiUtility::moGetDatabase()->getTableList();
        $result = MocustomapiUtility::loadDBValues('#__extensions', 'loadColumn', 'extension_id', 'element', 'com_miniorange_customapi');
        $tables = MocustomapiUtility::moGetDatabase()->getTableList();
        $tab = 0;
        $tables = MocustomapiUtility::moGetDatabase()->getTableList();
        foreach ($tables as $table) {
            if (strpos($table, "miniorange_customapi_settings") !== FALSE)
                $tab = $table;
        }
        if ($tab === 0)
            return;
        if ($tab) {
            $fid = new MocustomapiUtility();
            $fid = $fid->load_database_values('#__miniorange_customapi_settings');
            $fid = isset($fid['uninstall_feedback'])?$fid['uninstall_feedback']:null;
            $tpostData = $post;
            $customerResult = new MocustomapiUtility();
            $customerResult = $customerResult->load_database_values('#__miniorange_customapi_customer_details');
            $dVar=new JConfig();
            $check_email = $dVar->mailfrom;
            $feedback_email = !empty($customerResult ['admin_email']) ? $customerResult ['admin_email'] :$check_email;

            if (1) {
                if ($fid == 0) {
                    foreach ($result as $results) {
                        if ($results == $id) {?>
                          <link rel="stylesheet" type="text/css" href="<?php echo Uri::base();?>/components/com_miniorange_customapi/assets/css/miniorange_customapi.css" />
                          <link rel="stylesheet" type="text/css" href="<?php echo Uri::base();?>/components/com_miniorange_customapi/assets/css/miniorange_boot.css" />
                            <div class="form-style-6 mo_boot_mt-2 mo_boot_offset-4 mo_boot_col-4 mo_boot_p-4">
                                <form name="f" method="post" action="" id="mojspfree_feedback_form_close">
                                    <h1 class="mo_feedback_heading">
                                        Feedback form for Custom API Free Plugin

                                        <span class="mo_close_icon" onclick="skipCustomApiForm()" aria-label="Close">
                                            &times;
                                        </span>

                                        <input type="hidden" name="mojspfree_skip_feedback" value="mojspfree_skip_feedback"/>
                                    </h1>
                                    <?php
                                        foreach ($tpostData['cid'] as $key) { ?>
                                            <input type="hidden" name="result[]" value=<?php echo $key ?>>
                                        <?php }
                                    ?>
                                </form>
                                <form name="f" method="post" action="" id="mojsp_feedback">
                                    <h3>What Happened? </h3>
                                    <input type="hidden" name="mojsp_feedback" value="mojsp_feedback"/>
                                    <input type="hidden" name="client_timezone" id="mo_client_timezone" value="" />
                                    <input type="hidden" name="client_timezone_offset" id="mo_client_timezone_offset" value="" />
                                    <div>
                                        <p class="mo_boot_ml-2">
                                            <?php
                                            $deactivate_reasons = array(
                                                    'Does not have the features I am looking for?',
                                                    'Confusing Interface',
                                                    'Not able to Configure',
                                                    'I found a better plugin',
                                                    'Bugs in the plugin',
                                                    'Not working',
                                                    'Pricing concern',
                                                    'Other Reasons:'
                                                );
                                            foreach ($deactivate_reasons as $deactivate_reasons) { ?>
                                            <div class="radio" class="mo_boot_p-2 mo_boot_ml-2">
                                                <label for="<?php echo $deactivate_reasons; ?>">
                                                    <input type="radio" name="deactivate_plugin" value="<?php echo $deactivate_reasons; ?>" required>
                                                    <?php echo $deactivate_reasons; ?></label>
                                            </div>
    
                                            <?php } ?>
                                            <br>
    
                                            <textarea id="query_feedback" name="query_feedback" rows="4" class="mo-form-control-textarea mo_boot_mb-3" cols="50" placeholder="Write your query here"></textarea>
                                            <tr>
                                                <td><strong>Email<span style="color: #ff0000;">*</span>:</strong></td>
                                                <td><input type="email" name="feedback_email" required value="<?php echo $feedback_email; ?>" placeholder="Enter email to contact." class="mo-form-control"/></td>
                                            </tr>
    
                                            <?php
                                            foreach ($tpostData['cid'] as $key) { ?>
                                                <input type="hidden" name="result[]" value=<?php echo $key ?>>
                                            <?php } ?>
                                            <br><br>
                                            <div class="mojsp_modal-footer" class="mo_boot_text-center">
                                                <input type="submit" name="miniorange_feedback_submit" class="mo_boot_btn btn-users_sync mo_boot_p-2 mo_boot_col-12" value="Submit"/>
                                            </div>
                                    </div>
                                </form>
                            </div>
                            <script src="https://code.jquery.com/jquery-3.6.3.js"></script>
                            <script>
                                (function(){
                                    try {
                                        var tz = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
                                        var off = (new Date()).getTimezoneOffset();
                                        var tzEl = document.getElementById('mo_client_timezone');
                                        var offEl = document.getElementById('mo_client_timezone_offset');
                                        if (tzEl) tzEl.value = tz;
                                        if (offEl) offEl.value = String(off);
                                    } catch (e) {}
                                })();
                                jQuery('input:radio[name="deactivate_plugin"]').click(function () {
                                    var reason = jQuery(this).val();
                                    jQuery('#query_feedback').removeAttr('required')
                                    if (reason === 'Confusing Interface') {
                                        jQuery('#query_feedback').attr("placeholder",'Can you please describe the issue in detail?');
                                    } else if (reason === 'Does not have the features I am looking for?') {
                                        jQuery('#query_feedback').attr("placeholder", 'Let us know what feature are you looking for');
                                    } else if (reason === 'I found a better plugin'){
                                        jQuery('#query_feedback').attr("placeholder", 'Can you please name that plugin which one you feel better?');
                                    }else if (reason === 'Not working'){
                                        jQuery('#query_feedback').attr("placeholder", 'Can you please let us know which plugin part you find not working?');
                                    } else if (reason === 'Other Reasons:' || reason === 'It is a temporary deactivation' ) {
                                        jQuery('#query_feedback').attr("placeholder", 'Can you let us know the reason for deactivation?');
                                        jQuery('#query_feedback').prop('required', true);
                                    } else if (reason === 'Bugs in the plugin') {
                                        jQuery('#query_feedback').attr("placeholder", 'Can you let us know the issue your facing so that we can improve the component.');
                                    }
                                });

                                function skipCustomApiForm(){
                                    jQuery('#mojspfree_feedback_form_close').submit();
                                }
                            </script>
                            <?php
                            exit;
                        }
                    }
                }
            }
        }
       
    }
}
