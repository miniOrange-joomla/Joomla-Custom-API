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
defined('_JEXEC') or die('Restricted Access');
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Version;
use Joomla\CMS\Router\Route;

$document = Factory::getDocument();
$document->addScript(Uri::base() . 'components/com_miniorange_customapi/assets/js/bootstrap-multiselect.js');
$document->addScript(Uri::base() . 'components/com_miniorange_customapi/assets/js/bootstrap-select-min.js');
$document->addScript(Uri::base() . 'components/com_miniorange_customapi/assets/js/bootstrap-min.js');
$document->addScript(Uri::base() . 'components/com_miniorange_customapi/assets/js/countries.js');
$document->addScript(Uri::base() . 'components/com_miniorange_customapi/assets/js/utility.js');
$document->addScript(Uri::base() . 'components/com_miniorange_customapi/assets/js/bootstrap.js');
$document->addScript('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js');
$document->addStyleSheet(Uri::base() . 'components/com_miniorange_customapi/assets/css/miniorange_customapi.css');
$document->addStyleSheet(Uri::base() . 'components/com_miniorange_customapi/assets/css/miniorange_boot.css');
$document->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
$document->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
HTMLHelper::_('jquery.framework');


$jsonFile = Uri::base() . 'components/com_miniorange_customapi/assets/json/tabs.json';

function getJsonData($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log('cURL error: ' . curl_error($ch));
        return null;
    }
    curl_close($ch);
    return $response;
}

$tabsJson = getJsonData($jsonFile);
$tabs = json_decode($tabsJson, true);

if (MocustomapiUtility::is_curl_installed() == 0){ ?>
    <p class="mo_customapi_color-red">(<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_WARNING');?><a href="http://php.net/manual/en/curl.installation.php" target="_blank"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_EXTENSION');?></a> <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_EXTENSION_TEXT');?>)</p>
    <?php
}
// Backward compatibility for Joomla 3/4/5/6
$app = Factory::getApplication();
$input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
$get = ($input && $input->get) ? $input->get->getArray() : [];
$tab_name = 'custom_api_overview';
$active_tab = ($input && $input->get) ? $input->get->getArray() : [];
if (isset($active_tab['tab-panel']) && !empty($active_tab['tab-panel'])) {
    $tab_name = $active_tab['tab-panel'];
}

$jVersion = new Version();
$jCmsVersion = $jVersion->getShortVersion();
$jCmsVersion = substr($jCmsVersion, 0, 3);
if ($jCmsVersion > 4.0) {
    ?>
    <script>
        jQuery(document).ready(function() {
            jQuery('.btn-group').css("width", "60%");
    });
    </script>
    <?php
}

?>
<div class="mo_boot_row mo_boot_p-3">
    <div class="mo_boot_col-sm-12">
        <a class="mo_boot_btn mo_boot_px-4 mo_boot_py-1 mo_heading_export_btn" href="index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=trial_request"><i class="fa fa-envelope mo_boot_mx-1"></i>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TRIAL_REQUEST');?></a>
        <a class="mo_boot_btn mo_boot_px-4 mo_boot_py-1 mo_heading_export_btn" href="index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=trial_demo"><i class="fa fa-phone mo_boot_mx-1"></i>&nbsp;<?php echo Text::_('COM_MINIORANGE_API_TAB6_SUPPORT');?></a>
    </div>
</div>
<div class="mo_boot_container-fluid mo_boot_m-0 mo_boot_p-0">
    <div class="mo_boot_row mo_boot_m-0 mo_boot_p-0">
        <div class="mo_boot_col-sm-12 mo_boot_m-0 mo_boot_p-0 mo_customapi_navbar">
            <?php foreach ($tabs as $key => $tab): ?>
                <a id="<?php echo $tab['id']; ?>"
                   class="mo_boot_py-3 mo_customapi_nav-tab <?php echo ($tab_name === 'overview' && $tab['label'] === 'COM_MINIORANGE_API_PLUGIN_OVERVIEW') || ($tab_name === $tab['label']) ? 'mo_customapi_nav_tab_active' : ''; ?>"
                   href="<?php echo $tab['href']; ?>"
                   onclick="add_css_tab('#<?php echo $tab['id']; ?>');"
                   data-toggle="tab">
                   <span class="mo_nav_tab_icon"><i class="fa <?php echo str_replace('fas ', 'fa-', $tab['icon']); ?>"></i></span>
                    <span class="tab-label"><?php echo Text::_($tab['label']); ?></span>
                </a>
            <?php endforeach; ?>
            <a id="upgrade_tab" class="mo_boot_py-3 mo_customapi_support-tab <?php echo ($tab_name === 'upgrade_tab') ? 'mo_customapi_nav_tab_active' : ''; ?>"
                href="#upgrade_plans" onclick="add_css_tab('#upgrade_tab');" data-toggle="tab">
                <span class="mo_nav_tab_icon"><i class="fa fa-arrow-up"></i></span>
                <span class="tab-label"><?php echo Text::_('COM_MINIORANGE_API_PLUGIN_UPGRADE'); ?></span>
            </a>
            <a id="view_custom_api" href="#view_current_custom_api" data-toggle="tab"></a> 
        </div>
    </div>
</div>
<?php
$tabClasses = [
    'overview' => 'customapi_plugin_overview',
    'show_custom_apis' => 'view_all_apis',
    'create_custom_api' => 'create_custom_apis',
    'view_custom_api' => 'view_current_custom_api',
    'add_authentication' => 'add_authentication_to_api',
    'custom_external_apis' => 'connect_external_apis',
    'custom_api_upgrade' => 'upgrade_plans',
    'trial_demo' => 'mo_customapi_trial_demo',
    'trial_request'=>'trial_request'
];
?>
<div class="mo_boot_container-fluid mo_customapi_tab-content mo_boot_mt-2">
    <div class="tab-content" id="myTabContent">
        <?php foreach ($tabClasses as $tabKey => $tabId): ?>
            <div id="<?php echo $tabId; ?>" class="tab-pane <?php echo $tab_name == $tabKey ? 'active' : ''; ?>" <?php echo ($tabId === 'create_custom_apis' || $tabId === 'view_current_custom_api') ? 'style="display:' . ($tab_name == $tabKey ? 'block' : 'none') . ';"' : ''; ?>>
                <div class="mo_boot_row mo_boot_m-0">
                    <div class="mo_boot_col-sm-12 mo_boot_p-3 mo_customapi_bg_white">
                        <?php
                        switch ($tabKey) {
                            case 'overview':
                                custom_api_plugin_overview();
                                break;
                            case 'show_custom_apis':
                                show_all_custom_apis();
                                break;
                            case 'create_custom_api':
                                create_custom_apis();
                                break;
                            case 'view_custom_api':
                                view_current_custom_api();
                                break;
                            case 'add_authentication':
                                add_authentication_to_api();
                                break;
                            case 'custom_external_apis':
                                connect_external_apis();
                                break;
                            case 'custom_api_upgrade':
                                custom_api_licensing_plans();
                                break;
                            case 'trial_demo':
                                support_form();
                                break;
                            case 'trial_request':
                                request_for_demo();
                                break;
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php

function custom_api_plugin_overview()
{
    ?>
    <div class="mo_boot_container-fluid mo_boot_m-0 mo_boot_p-0">
        <div class="mo_boot_row mo_boot_m-0">
            <div class="mo_boot_col-12">
                <h3><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_OVERVIEW');?></h3>
                <hr>
                <p><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_OVERVIEW_TXT1');?><strong>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_OVERVIEW_TXT2');?>&nbsp;</strong><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_OVERVIEW_TXT3');?></p>
                <p><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_OVERVIEW_TXT4');?></p>
                <p>
                    <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_OVERVIEW_TXT5');?>&nbsp;<strong><a href="index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=custom_api_upgrade"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_OVERVIEW_TXT6');?></a></strong>
                </p>
                <a class="mo_boot_btn btn-users_sync mo_boot_px-3 mo_boot_mx-1" target="_blank" href="https://plugins.miniorange.com/custom-api-for-joomla"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_VISIT_SITE');?></a>
                <a class="mo_boot_btn btn-users_sync mo_boot_px-3 mo_boot_mx-1" href="<?php echo Uri::root().'administrator/index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=custom_api_upgrade';?>"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_LICENSE_PLANS');?></a>
                <a class="mo_boot_btn btn-users_sync mo_boot_px-3 mo_boot_mx-1" target="_blank" href="https://plugins.miniorange.com/setup-custom-api-for-joomla"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_GUIDES');?></a>
                <a class="mo_boot_btn btn-users_sync mo_boot_px-3 mo_boot_mx-1" target="_blank" href="https://faq.miniorange.com/kb/joomla/">FAQ</a>
                <div class="mo_customapi_highlight">
                    <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_OVERVIEW_TXT7');?>
                    <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_OVERVIEW_TXT8');?>&nbsp;<strong><a href="index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=trial_request"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_OVERVIEW_TXT9');?></a></strong>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_OVERVIEW_TXT10');?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function support_form()
{
    $current_user = Factory::getUser();
    $result       = MocustomapiUtility::getCustomerDetails();
    $admin_email  = isset($result['email']) ? $result['email'] : '';
    if($admin_email == '')
        $admin_email = $current_user->email;
    ?>
    <div class="mo_boot_container mo_customapi_main_section">
        <div class="mo_boot_row mo_boot_justify-content-center">
            <div class="mo_boot_col-md-8">
                <h4 class="mo_boot_text-justify mo_boot_mb-3 mo_boot_mt-2"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SUPPORT_HEADLINE');?></h4>
                <div class="mo_customapi_form_wrapper">
                    <div class="mo_boot_p-3">
                        <form name="f" method="post" action="<?php echo Route::_('index.php?option=com_miniorange_customapi&view=accountsetup&task=accountsetup.contactUs'); ?>">
                            <div class="mo_boot_col-12 mo_boot_mb-3">
                                <div class="mo_boot_row mo_boot_mt-2">
                                    <div class="mo_boot_col-6 mo_boot_px-2">
                                        <input type="radio" id="support_general" name="support_type" value="general_query" checked onclick="toggleCallTimeField()" style="display: none;">
                                        <label for="support_general" class="support-type-btn" id="general_query_btn">
                                            <strong><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_GENERAL_QUERY');?></strong>
                                        </label>
                                    </div>
                                    <div class="mo_boot_col-6 mo_boot_px-2">
                                        <input type="radio" id="support_call" name="support_type" value="setup_call" onclick="toggleCallTimeField()" style="display: none;">
                                        <label for="support_call" class="support-type-btn" id="setup_call_btn">
                                            <strong><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SETUP_CALL');?></strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mo_boot_mb-3">
                                <h4 class="mo_boot_mb-1"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_EMAIL');?>:<span class="mo_required_field">*</span></h4>
                                <input type="email" class="mo-form-control" name="query_email" value="<?php echo $admin_email; ?>" placeholder="<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_EMAIL_PLACEHOLDER');?>" required>
                            </div>

                            <div class="mo_boot_mb-3">
                                <h4 class="mo_boot_mb-1"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_PHONE_NUMBER');?>:</h4>
                                <div class="mo_boot_row mo-phone-inline-row" data-mo-phone-dropdown>
                                    <div class="mo_boot_col-4">
                                        <div class="mo-phone-card">
                                            <div class="mo-country-select">
                                                <span class="flag" aria-hidden="true"></span>
                                                <span class="dial-code">+91</span>
                                                <span class="arrow">▾</span>
                                            </div>
                                            <ul class="mo-country-list"></ul>

                                            <input type="hidden" name="country_code" class="mo-country-code" value="91">
                                            <input type="hidden" name="client_timezone" class="mo-client-timezone" value="">
                                            <input type="hidden" name="client_timezone_offset" class="mo-client-timezone-offset" value="">
                                        </div>
                                    </div>
                                    <div class="mo_boot_col-8">
                                        <input type="tel" class="mo-form-control mo_boot_flex-fill" name="query_phone" placeholder="<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_PHONE_PLACEHOLDER');?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mo_boot_mb-3" id="call_date_field" style="display: none;">
                                <h4 class="mo_boot_mb-1"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_DATE');?>:<span class="mo_required_field">*</span></h4>
                                <input type="date" class="mo-form-control" id="call_date" name="call_date" placeholder="<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_DATE_PLACEHOLDER');?>"/>
                            </div>

                            <div class="mo_boot_mb-3" id="call_time_field" style="display: none;">
                                <h4 class="mo_boot_mb-1"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TIME');?>:<span class="mo_required_field">*</span></h4>
                                <input type="time" class="mo-form-control" id="call_time" name="call_time" placeholder="<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TIME_PLACEHOLDER');?>"/>
                            </div>

                            <div class="mo_boot_mb-3">
                                <h4 class="mo_boot_mb-1"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_QUERY');?>:<span class="mo_required_field">*</span></h4>
                                <textarea class="mo-form-control-textarea" name="query_support" rows="4" placeholder="<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_QUERY_PLACEHOLDER');?>" required></textarea>
                            </div>

                            <input type="hidden" name="option1" value="mo_usync_login_send_query">
                            <div class="mo_boot_text-center">
                                <button type="submit" class="mo_boot_btn btn-users_sync">
                                    <i class="fa fa-envelope mo_boot_me-2"></i>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SEND_REQUEST');?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="mo_boot_mt-3 mo_boot_text-justify">
                    <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_REACH_OUT');?>&nbsp;<a href="mailto:joomlasupport@xecurify.com">joomlasupport@xecurify.com</a>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_WITH_DETAILS');?>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateButtonStates();
        });
        
        function toggleCallTimeField() {
            var callDateField = document.getElementById('call_date_field');
            var callTimeField = document.getElementById('call_time_field');
            var callDateInput = document.getElementById('call_date');
            var callTimeInput = document.getElementById('call_time');
            var setupCallRadio = document.getElementById('support_call');
            
            // Update button visual states
            updateButtonStates();
            
            if (setupCallRadio.checked) {
                callDateField.style.display = 'block';
                callTimeField.style.display = 'block';
                callDateInput.setAttribute('required', 'required');
                callTimeInput.setAttribute('required', 'required');
            } else {
                callDateField.style.display = 'none';
                callTimeField.style.display = 'none';
                callDateInput.removeAttribute('required');
                callTimeInput.removeAttribute('required');
                callDateInput.value = '';
                callTimeInput.value = '';
            }
        }
        
        function updateButtonStates() {
            var generalBtn = document.getElementById('general_query_btn');
            var callBtn = document.getElementById('setup_call_btn');
            var generalRadio = document.getElementById('support_general');
            var callRadio = document.getElementById('support_call');
            
            if (generalRadio.checked) {
                generalBtn.classList.add('active');
                callBtn.classList.remove('active');
            } else if (callRadio.checked) {
                callBtn.classList.add('active');
                generalBtn.classList.remove('active');
            }
        }
    </script>
    <?php
    
}

function custom_api_licensing_plans()
{
    $upgradeURL="https://portal.miniorange.com/initializePayment?requestOrigin=joomla_custom_api_premium_plan";
   ?>
    <div class="mo_boot_container mo_customapi_main_section">
        <div class="mo_boot_row mo_boot_justify-content-center">
            <div class="mo_boot_col-12">
                <div class="mo_boot_mb-4">
                    <h3><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_FEATURE_COMPARISON'); ?></h3>
                </div>
                
                <div class="mo_customapi_pricing-container">
                    <div class="mo_customapi_pricing-card">
                        <h3 class="mo_boot_py-1"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_FREE');?></h3>
                        <div><h3 class="mo_boot_py-1"><strong>$0</strong></h3></div>
                        <button class="mo_customapi_contact-btn"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CURRENT_PLAN'); ?></button>
                        
                        <div class="mo_customapi_feature-section">
                            <div class="mo_customapi_feature-header" onclick="toggleFeatures('free-included')">
                                <div><i class="fa-solid fa-square-check mo_customapi_square_check"></i>&emsp;
                                <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_INCLUDED_FEATURES'); ?></div>
                                <div><span class="mo_customapi_feature_arrow"><i class="fa-solid fa-chevron-down"></i> </span></div>
                            </div>
                            <div id="free-included-list" class="mo_customapi_feature-list">
                                <ul>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ACTIVE_PLAN_1');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ACTIVE_PLAN_NEW');?></li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mo_customapi_feature-section">
                            <div class="mo_customapi_feature-header" onclick="toggleFeatures('free-not-included')">
                                <div><i class="fa-solid fa-square-xmark mo_customapi_square_xmark"></i>&emsp;
                                <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_NOT_INCLUDED_FEATURES'); ?></div>
                                <div><span class="mo_customapi_feature_arrow"><i class="fa-solid fa-chevron-down"></i> </span></div>
                            </div>
                            <div id="free-not-included-list" class="mo_customapi_feature-list">
                                <ul>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_PREMIUM_PLAN_2');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_PREMIUM_PLAN_3');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_PREMIUM_PLAN_4');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ACTIVE_PLAN_6');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ACTIVE_PLAN_7');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOM_API_ACTIVE_PLAN_FE');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ACTIVE_PLAN_9');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ACTIVE_PLAN_10');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ACTIVE_PLAN_11');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ACTIVE_PLAN_12');?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- BASIC Plan -->
                    <div class="mo_customapi_pricing-card">
                        <h3 class="mo_boot_py-1"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_PREMIUM_PLAN_NAME'); ?></h3>
                        <div><h3 class="mo_boot_py-1"><strong>$199</strong></h3></div>
                        <a class="mo_customapi_contact-btn" target="_blank" href="<?php echo $upgradeURL ?>"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_UPGRADE_NOW_BTN'); ?></a>
                        
                        <div class="mo_customapi_feature-section">
                            <div class="mo_customapi_feature-header" onclick="toggleFeatures('basic-included')">
                                <div><i class="fa-solid fa-square-check mo_customapi_square_check"></i>&emsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_INCLUDED_FEATURES'); ?></div>
                                <div><span class="mo_customapi_feature_arrow"><i class="fa-solid fa-chevron-down"></i> </span></div>
                            </div>
                            <div id="basic-included-list" class="mo_customapi_feature-list">
                                <ul>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_PREMIUM_PLAN_1');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ACTIVE_PLAN_2');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_PREMIUM_PLAN_2');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_PREMIUM_PLAN_3');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_PREMIUM_PLAN_4');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ACTIVE_PLAN_6');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ACTIVE_PLAN_7');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOM_API_ACTIVE_PLAN_FE');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ACTIVE_PLAN_9');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ACTIVE_PLAN_10');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ACTIVE_PLAN_11');?></li>
                                    <li>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ACTIVE_PLAN_12');?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="mo_boot_col-sm-12 mo_boot_mt-4 mo_customapi_form_wrapper">
                    <div class="mo_customapi_tab_header mo_boot_d-flex mo_boot_justify-content-between mo_boot_align-items-center mo_boot_p-3"
                         onclick="toggleCollapse('mo_customapi_how_to_upgrade', this.querySelector('.mo_toggle_icon'))">
                        <div class="mo_customapi_tab_title">
                            <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_UPGRADE_PLAN'); ?>
                        </div>
                        <div class="mo_toggle_icon"> + </div>
                    </div>

                    <div id="mo_customapi_how_to_upgrade" class="mo_boot_col-sm-12 mo_boot_mt-3" style="display: none;">
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-6 mo_customapi_upgarde_step">
                                <div><strong>1</strong></div>
                                <p><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_UPGRADE_STEP1');?></p>
                            </div>
                            <div class="mo_boot_col-sm-6 mo_customapi_upgarde_step">
                                <div ><strong>4</strong></div>
                                <p><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_UPGRADE_STEP4');?></p>
                            </div>            
                        </div>
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-6 mo_customapi_upgarde_step">
                                <div ><strong>2</strong></div>
                                <p> <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_UPGRADE_STEP2');?></p>
                            </div>
                            <div class="mo_boot_col-sm-6 mo_customapi_upgarde_step">
                                <div ><strong>5</strong></div>
                                <p><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_UPGRADE_STEP5');?> </p>
                            </div>         
                        </div>
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-6 mo_customapi_upgarde_step">
                                <div ><strong>3</strong></div>
                                <p><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_UPGRADE_STEP3');?></p>
                            </div>
                            <div class="mo_boot_col-sm-6  mo_customapi_upgarde_step">
                                <div ><strong>6</strong></div>
                                <p><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_UPGRADE_STEP6');?></p>
                            </div>       
                        </div> 
                    </div>
                </div>

                <div class="mo_boot_col-sm-12 mo_boot_mt-4 mo_customapi_form_wrapper">
                    <div class="mo_customapi_tab_header mo_boot_d-flex mo_boot_justify-content-between mo_boot_align-items-center mo_boot_p-3"
                         onclick="toggleCollapse('mo_customapi_return_policy', this.querySelector('.mo_toggle_icon'))">
                        <div class="mo_customapi_tab_title">
                            <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_RETURN_POLICY'); ?>
                        </div>
                        <div class="mo_toggle_icon"> + </div>
                    </div>
                    <div id="mo_customapi_return_policy" class="mo_boot_col-sm-12 mo_boot_pb-2" style="display: none;">
                        <div>
                            <p>
                                <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_RETURN_POLICY_TEXT'); ?>
                            </p>
                        </div>
                        <div>
                            <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_RETURN_POLICY_TEXT1'); ?></h4>
                            <ul>
                                <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_RETURN_POLICY_TEXT2'); ?><br/>
                                <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_RETURN_POLICY_TEXT3'); ?>
                            </ul>
                        </div>
                        <div>
                            <p>
                                <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_RETURN_POLICY_TEXT4'); ?>
                                <a href="mailto:joomlasupport@xecurify.com" class="mo_customapi_email_link"><span class="mo_customapi_word_wrap">joomlasupport@xecurify.com</span>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function show_all_custom_apis()
{
    $plugin_settings=MocustomapiUtility::getConfiguration();
    $is_api_exists = !empty($plugin_settings['mo_custom_apis']) && $plugin_settings['mo_custom_apis'] !== '[]';

    $can_create_api = true;

    if ($is_api_exists) {
        $existingApis = json_decode($plugin_settings['mo_custom_apis'], true);
        if (is_array($existingApis) && count($existingApis) >= 5) {
            $can_create_api = false;
        }
    }
    ?>
    <div class="mo_boot_container mo_customapi_main_section">
        <div class="mo_boot_row mo_boot_mo_boot_justify-content-center">
            <div class="mo_boot_col-md-12">
                <div class="mo_boot_d-flex mo_boot_justify-content-between mo_boot_align-items-center">
                    <div class="mo_boot_d-flex mo_boot_align-items-center">
                        <h3 class="mo_boot_mb-0 mo_boot_me-2">
                            <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_LIST_CUSTOM_API');?>
                        </h3>
                    </div>
                    <div class="mo_custom_api_info_link mo_tooltip mo_boot_mx-2">
                        <a href="https://plugins.miniorange.com/setup-custom-api-for-joomla#step2" target="_blank">
                            <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SETUP_GUIDE'); ?>
                        </a>
                        <span class="mo_tooltiptext">
                            <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_LIST_CUSTOM_API_TEXT'); ?>
                        </span>
                    </div>
                    <div class="mo_boot_col-4">
                        <a 
                            class="mo_boot_btn btn-users_sync mo_customapi_float_right <?php echo !$can_create_api ? 'disabled-api-btn' : ''; ?>"
                            href="<?php echo $can_create_api ? Uri::root().'administrator/index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=create_custom_api' : 'javascript:void(0);'; ?>"
                            style="<?php echo $is_api_exists ? 'display:block;' : 'display:none;'; ?><?php echo !$can_create_api ? ' cursor:not-allowed;' : ''; ?>"
                            <?php echo !$can_create_api ? 'onclick="return false;"' : ''; ?>>
                            <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_LIST_CREATE_API'); ?>
                        </a>
                    </div>
                </div>
                <hr>
                <?php if (!$can_create_api): ?>
                        <div class="mo_boot_col-sm-12 ">
                            <div class="mo_api_warning_msg">
                                <span style="font-weight: bold; margin-right: 8px;"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_WARNING');?></span>
                                <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_LIMIT_REACHED_MESSAGE') ?: 'You have reached the maximum limit of 5 APIs in the free version. Please upgrade to create more.'; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php 
                if($is_api_exists)
                {
                    $custom_api_config=json_decode($plugin_settings['mo_custom_apis']);
                    ?>
                    <div class="mo_boot_mt-5 mo_boot_table-responsive mo_customapi_form_wrapper">
                        <table class="mo_boot_table mo_boot_table-bordered mo_boot_table-hover mo_boot_table-bordered">
                            <tr>
                                <td><strong><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_API_NAME');?></strong></td>
                                <td><strong><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ACTIONS');?></strong></td>
                            </tr>
                            <?php foreach($custom_api_config as $key => $value): ?>
                                <tr>
                                    <td style="word-break: break-word;"><?php echo $key; ?></td>
                                    <td>
                                        <div class="mo_boot_d-flex mo_boot_flex-wrap mo_boot_align-items-center mo_boot_gap-2">
                                            <a class="mo_boot_px-1"
                                            href="<?php echo Uri::root().'administrator/index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=create_custom_api&api_name='.$key; ?>">
                                            <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_EDIT');?>
                                            </a> |

                                            <a class="mo_boot_px-1"
                                            href="<?php echo Uri::root().'administrator/index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=view_custom_api&api_name='.$key; ?>">
                                            <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_VIEW');?>
                                            </a> |

                                            <form name="f" method="post" class="mo_boot_px-1" action="<?php echo Route::_('index.php?option=com_miniorange_customapi&view=accountsetup&task=accountsetup.deleteCurrentAPI'); ?>">
                                                <input type="hidden" name="api_name" value="<?php echo $key; ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <button type="button" class="mo_boot_btn mo_boot_btn-danger mo_boot_btn-sm" onclick="showDeleteModal(this.form)">
                                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <?php
                    }else{
                    ?>
                    <div class="mo_boot_col-sm-12 mo_boot_p-0">
                        <h4 class="mo_boot_mt-2"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CREATE');?></h4>
                    </div>
                    <div class="mo_boot_col-sm-12 mo_boot_text-center">
                        <a class="mo_boot_btn btn-users_sync mo_boot_p-2 mo_customapi_font-size" href="<?php echo Uri::root().'administrator/index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=create_custom_api'; ?>">&plus;&nbsp;<?php echo Text::_('COM_MINIORANGE_API_TAB2_SETTINGS');?></a>
                    </div>
                    <?php 
                }?>
            </div>
        </div>
    </div>
    <?php 
}

//Create the custom API
function create_custom_apis()
{
    $db = MocustomapiUtility::moGetDatabase();
    $allTables = $db->getTableList();
    $prefix = $db->getPrefix();
    $disablePrefixes = ['user_','users','usergroups',];
    $disableFullPrefixes = array_map(function($p) use ($prefix) {
        return $prefix . $p;
    }, $disablePrefixes);

    $plugin_settings=MocustomapiUtility::getConfiguration();
    // Backward compatibility for Joomla 3/4/5/6
    $app = Factory::getApplication();
    $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
    $get = ($input && $input->get) ? $input->get->getArray() : [];
    $edit=0;
    if(isset($get['api_name']))
    {
        $api_configuration=MocustomapiUtility::fetch_api_info($get['api_name'], 'custom');
        $edit=1;
    }

    if (!empty($api_configuration->table_name) && $api_configuration->table_name !== 'None Selected') {
        $columnArr = $db->getTableColumns($api_configuration->table_name);
        $columnArr = array_slice($columnArr, 0, 5, true);
    } else {
        $columnArr = [];
    }
    $tab_name='show_custom_apis';
    ?>
    <div class="mo_boot_container mo_customapi_main_section">
        <div class="mo_boot_row mo_boot_mo_boot_justify-content-center">
            <div class="mo_boot_col-md-12">
                <div class="mo_boot_d-flex mo_boot_justify-content-between mo_boot_align-items-center mo_boot_mb-3">
                    <div class="mo_boot_d-flex">
                         <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CREATE_CUSTOM_API');?></h4>
                         <div class="mo_custom_api_info_link mo_tooltip mo_boot_mx-2">
                            <a href="https://plugins.miniorange.com/setup-custom-api-for-joomla#step2" target="_blank">
                                <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SETUP_GUIDE'); ?>
                            </a>
                            <span class="mo_tooltiptext">
                                <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_LIST_CUSTOM_API_TEXT'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="mo_boot_col-sm-4">
                        <a class="mo_boot_btn mo_boot_btn-danger mo_customapi_float_right" href="<?php echo Uri::root().'administrator/index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel='.$tab_name.''; ?>"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CLOSE');?></a>
                    </div>
                </div>

                <form id="create_api_form" name="f" method="post" action="<?php echo Route::_('index.php?option=com_miniorange_customapi&view=accountsetup&task=accountsetup.createAPI');?>">
                    <div class="mo_customapi_form_wrapper">
                        <div class="mo_boot_p-4">
                            <div class="mo_boot_row mo_boot_p-2">
                                <div class="mo_boot_col-12 alert alert-info">
                                    <i class="fa fa-info-circle" style="color: #17a2b8;"></i>
                                    <strong><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_NOTE'); ?></strong> <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_FREE_VERSION_LIMIT'); ?>
                                </div>
                            </div>
                            <div class="mo_boot_row mo_boot_mt-3">
                                <div class="mo_boot_col-4">
                                    <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_API_NAME');?>:<span class="mo_required_field">*</span></h4>
                                </div>
                                <div class="mo_boot_col-8">
                                    <input type="text" class="mo_custom_api_textbox mo-form-control" id="api_name" name="api_name" value="<?php echo !empty($api_configuration->api_name)?$api_configuration->api_name:''; ?>" <?php echo ($edit==1)?'readonly':''; ?> required>
                                </div>
                            </div>
                            <div class="mo_boot_row mo_boot_mt-4">
                                <div class="mo_boot_col-4">
                                <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SELECT_METHOD');?><span class="mo_required_field">*</span></h4>
                                </div>
                                <div class="mo_boot_col-8">
                                    <select name="api_method" id="api_method" class="mo_custom_api_textbox mo-form-control mo-form-control-select" required>
                                        <option value="get" selected >GET</option>
                                        <option value="post" disabled>POST&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_GET_PREMIUM_BRACKET'); ?></option>
                                        <option value="put" disabled>PUT&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_GET_PREMIUM_BRACKET'); ?></option>
                                        <option value="delete" disabled>DELETE&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_GET_PREMIUM_BRACKET'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="mo_boot_row mo_boot_mt-4">
                                <div class="mo_boot_col-4">
                                    <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SELECT_API_TYPE'); ?></h4>
                                </div>
                                <div class="mo_boot_col-8">
                                    <div class="mo_boot_row">
                                        <div class="mo_boot_form-check mo_boot_form-check-inline mo_boot_col-4">
                                            <input class="mo_boot_form-check-input" type="radio" name="auth_type" id="opt1" value="parameter_based" checked onchange="toggleApiType()">
                                            <label class="mo_boot_form-check-label" for="opt1"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_BY_PARAMETERS'); ?></label>
                                        </div>
                                        <div class="mo_boot_form-check mo_boot_form-check-inline mo_boot_col-4">
                                            <input class="mo_boot_form-check-input" type="radio" name="auth_type" id="opt2" value="sql_based" onchange="toggleApiType()">
                                            <label class="mo_boot_form-check-label" for="opt2"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CREATE_SQL_API'); ?> <sup style="vertical-align: super; padding:0 px;">
                                                <a class="mo_custom_api_info_link mo_tooltip" onclick="moCustomUpgrade()" style="text-decoration: none;">
                                                    <i class="fa-solid fa-crown" style="color: #f1c40f; font-size: 18px;"></i>
                                                    <span class="mo_tooltiptext"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CONDITION_PREMIUM_FEATURE'); ?></span>
                                                </a>
                                            </sup></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- SQL Query Textarea (initially hidden) -->
                            <div class="mo_boot_row mo_boot_mt-4" id="sql_query_section" style="display: none;">
                                <div class="mo_boot_col-4">
                                    <h4>SQL Query:<span class="mo_required_field">*</span></h4>
                                </div>
                                <div class="mo_boot_col-6">
                                    <textarea name="sql_query" id="sql_query" class="mo-form-control-textarea" rows="4" disabled>SELECT * FROM #__users WHERE id='{{id}}' AND email='{{email}}';</textarea>
                                </div>
                            </div>
                            <div class="mo_boot_row mo_boot_mt-4" id="table_selection_section">
                                <div class="mo_boot_col-4">
                                    <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SELECT_TABLE');?><span class="mo_required_field">*</span></h4>
                                </div>
                                <div class="mo_boot_col-8">
                                    <select name="mo_table_name" id="select_table_name" class="mo_custom_api_textbox mo-form-control mo-form-control-select" onchange="save_table_name()" required>
                                        <option value="None Selected"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_NONE_SELECTED'); ?></option>
                                            <?php foreach ($allTables as $table_name): 
                                                $isDisabled = false;
                                                foreach ($disableFullPrefixes as $disabledPrefix) {
                                                    if (strpos($table_name, $disabledPrefix) === 0) {
                                                        $isDisabled = true;
                                                        break;
                                                    }
                                                }
                                            ?>
                                            <option value="<?php echo $table_name; ?>"
                                                <?php
                                                    echo (!empty($api_configuration->mo_table_name) && $table_name == $api_configuration->mo_table_name) ? 'selected' : '';
                                                    echo $isDisabled ? ' disabled style="color: gray;"' : '';
                                                ?>>
                                                <?php echo $table_name . ($isDisabled ? ' ' . Text::_('COM_MINIORANGE_CUSTOMAPI_PREMIUM_SUFFIX') : ''); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <!-- Custom Multi-Select Dropdown -->
                            <div class="mo_boot_row mo_boot_mt-4" id="columns_selection_section">
                                <div class="mo_boot_col-4">
                                    <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SELECTED_COLUMNS'); ?><span class="mo_required_field">*</span></h4>
                                </div>
                                <div class="mo_boot_col-8">
                                    <div class="custom-multiselect mo_custom_api_textbox mo-form-control mo-form-control-select" tabindex="0">
                                        <div class="selected-options" id="selected-count">
                                            <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TABLE_REQUIRED'); ?>
                                        </div>
                                        <div class="options-list" id="options-list">
                                            <input type="text" id="search-box" placeholder="<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SEARCH_BOX');?>" class="mo-form-control mo_boot_mb-2" autocomplete="off">

                                            <div class="option-item">
                                                <input type="checkbox" id="select-all">
                                                <label for="select-all"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SELECT_ALL_COLUMNS');?></label>
                                            </div>

                                            <?php if (!empty($columnArr)) { ?>
                                                <?php foreach ($columnArr as $column_key => $column_value) { ?>
                                                    <div class="option-item">
                                                        <input type="checkbox" class="option-checkbox" data-value="<?php echo htmlspecialchars($column_key, ENT_QUOTES, 'UTF-8'); ?>" id="<?php echo htmlspecialchars($column_key, ENT_QUOTES, 'UTF-8'); ?>" <?php echo (!empty($api_configuration->SelectedColumn) && in_array($column_key, $api_configuration->SelectedColumn)) ? 'checked' : ''; ?>>
                                                        <label for="<?php echo htmlspecialchars($column_key, ENT_QUOTES, 'UTF-8'); ?>">
                                                            <?php echo htmlspecialchars($column_key, ENT_QUOTES, 'UTF-8'); ?>
                                                        </label>
                                                    </div>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <p><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SELECT_TABLE_MESSAGE'); ?></p>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <select id="hidden-select" name="SelectedColumn[]" multiple style="display:none">
                                        <?php if (!empty($columnArr)) { ?>
                                            <?php foreach ($columnArr as $column_key => $column_value) { ?>
                                                <option value="<?php echo htmlspecialchars($column_key, ENT_QUOTES, 'UTF-8'); ?>" <?php echo (!empty($api_configuration->SelectedColumn) && in_array($column_key, $api_configuration->SelectedColumn)) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($column_key, ENT_QUOTES, 'UTF-8'); ?>
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () 
                                {
                                    // Multi-select functionality
                                    const multiselect = document.querySelector('.custom-multiselect');
                                    const optionsList = document.getElementById('options-list');
                                    const selectedCountSpan = document.getElementById('selected-count');
                                    const searchBox = document.getElementById('search-box');
                                    const selectAllCheckbox = document.getElementById('select-all');
                                    const hiddenSelect = document.getElementById('hidden-select');
                                    const checkboxes = document.querySelectorAll('.option-checkbox');
                                    const tableSelect = document.getElementById('select_table_name');
                                    const apiNameInput = document.getElementById('api_name');
                                    const createApiForm = document.getElementById('create_api_form');

                                    function updateSelectedCount() {
                                        const selectedCheckboxes = document.querySelectorAll('.option-checkbox:checked').length;
                                        if (tableSelect.value === 'None Selected') {
                                            selectedCountSpan.textContent = '<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SELECT_TABLE_MESSAGE'); ?>';
                                        } else if (selectedCheckboxes === 0) {
                                            selectedCountSpan.textContent = '<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_NONE_SELECTED'); ?>';
                                        } else {
                                            selectedCountSpan.textContent = `${selectedCheckboxes} column${selectedCheckboxes !== 1 ? 's' : ''} selected`;
                                        }
                                    }

                                    function updateHiddenSelect() {
                                        hiddenSelect.innerHTML = '';
                                        const selectedCheckboxes = document.querySelectorAll('.option-checkbox:checked');
                                        selectedCheckboxes.forEach(checkbox => {
                                            const option = document.createElement('option');
                                            option.value = checkbox.getAttribute('data-value');
                                            option.selected = true;
                                            hiddenSelect.appendChild(option);
                                        });
                                    }

                                    function handleSelectAll(isChecked) {
                                        if (isChecked) {
                                            // Free version limit: maximum 5 columns
                                            let checkedCount = 0;
                                            checkboxes.forEach(checkbox => {
                                                if (checkedCount < 5) {
                                                    checkbox.checked = true;
                                                    checkedCount++;
                                                } else {
                                                    checkbox.checked = false;
                                                }
                                            });
                                        } else {
                                            checkboxes.forEach(checkbox => {
                                                checkbox.checked = false;
                                            });
                                        }
                                        updateSelectedCount();
                                        updateHiddenSelect();
                                    }

                                    // Multi-select event listeners
                                    if (multiselect) {
                                        multiselect.addEventListener('click', function (e) {
                                            if (e.target === searchBox || e.target === selectAllCheckbox || e.target.classList.contains('option-checkbox') || e.target.tagName === 'LABEL') {
                                                return;
                                            }
                                            multiselect.classList.toggle('active');
                                        });
                                    }

                                    if (optionsList) {
                                        optionsList.addEventListener('click', function (e) {
                                            e.stopPropagation();
                                        });
                                    }

                                    checkboxes.forEach(checkbox => {
                                        checkbox.addEventListener('change', function () {
                                            const checked = document.querySelectorAll('.option-checkbox:checked').length;
                                            
                                            // Free version limit: maximum 5 columns
                                            if (this.checked && checked > 5) {
                                                this.checked = false;
                                                return;
                                            }
                                            
                                            updateSelectedCount();
                                            updateHiddenSelect();
                                            const total = checkboxes.length;
                                            const finalChecked = document.querySelectorAll('.option-checkbox:checked').length;
                                            if (selectAllCheckbox) {
                                                selectAllCheckbox.checked = finalChecked === total;
                                                selectAllCheckbox.indeterminate = finalChecked > 0 && finalChecked < total;
                                            }
                                        });
                                    });

                                    if (selectAllCheckbox) {
                                        selectAllCheckbox.addEventListener('change', function () {
                                            handleSelectAll(this.checked);
                                        });
                                    }

                                    if (searchBox) {
                                        searchBox.addEventListener('input', function () {
                                            const searchValue = this.value.toLowerCase();
                                            checkboxes.forEach(checkbox => {
                                                const label = checkbox.nextElementSibling.textContent.toLowerCase();
                                                const optionItem = checkbox.parentElement;
                                                if (label.includes(searchValue)) {
                                                    optionItem.style.display = 'flex';
                                                } else {
                                                    optionItem.style.display = 'none';
                                                }
                                            });
                                        });
                                    }

                                    document.addEventListener('click', function (e) {
                                        if (multiselect && !multiselect.contains(e.target)) {
                                            multiselect.classList.remove('active');
                                        }
                                    });

                                    // Form handling
                                    if (createApiForm) {
                                        // Prevent form submission on enter key
                                        createApiForm.addEventListener('keypress', function(e) {
                                            if (e.key === 'Enter') {
                                                e.preventDefault();
                                                return false;
                                            }
                                        });
                                    }

                                    // Table selection handling
                                    if (tableSelect) {
                                        tableSelect.addEventListener('change', function() {
                                            if (this.value === 'None Selected') {
                                                // Clear all checkboxes
                                                const checkboxes = document.querySelectorAll('.option-checkbox');
                                                checkboxes.forEach(checkbox => {
                                                    checkbox.checked = false;
                                                });
                                                
                                                // Update selected count
                                                if (selectedCountSpan) {
                                                    selectedCountSpan.textContent = '<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_NONE_SELECTED');?>';
                                                }
                                                
                                                // Clear hidden select
                                                if (hiddenSelect) {
                                                    hiddenSelect.innerHTML = '';
                                                }
                                                
                                                // Reset select all checkbox
                                                if (selectAllCheckbox) {
                                                    selectAllCheckbox.checked = false;
                                                    selectAllCheckbox.indeterminate = false;
                                                }
                                            }
                                            updateSelectedCount();
                                        });
                                    }

                                    // API name input validation
                                    if (apiNameInput) {
                                        apiNameInput.addEventListener('input', function(e) {
                                            // Remove any non-alphanumeric characters except underscore
                                            this.value = this.value.replace(/[^a-zA-Z0-9_]/g, '');
                                        });
                                    }

                                    // Initialize
                                    updateSelectedCount();
                                    updateHiddenSelect();
                                    const total = checkboxes.length;
                                    const checked = document.querySelectorAll('.option-checkbox:checked').length;
                                    if (selectAllCheckbox) {
                                        selectAllCheckbox.checked = checked === total;
                                        selectAllCheckbox.indeterminate = checked > 0 && checked < total;
                                    }
                                });
                            </script>
                        </div>
                    </div>
                    <div class="mo_customapi_form_wrapper mo_boot_mt-2" id="conditions_section">
                        <div class="mo_boot_p-4">
                            <div class="mo_boot_row">
                                <div class="mo_boot_col-5 mo_boot_m-0">
                                    <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CONDITION');?>
                                        <sup style="vertical-align: super; padding:0 px;">
                                            <a class="mo_custom_api_info_link mo_tooltip" onclick="moCustomUpgrade()" style="text-decoration: none;">
                                                <i class="fas fa-crown" style="color: #f1c40f; font-size: 18px;"></i>
                                                <span class="mo_tooltiptext"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CONDITION_PREMIUM_FEATURE'); ?></span>
                                            </a>
                                        </sup>
                                    </h4>
                                </div>
                                <div class="mo_boot_col-5 mo_boot_m-0">
                                    <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CHOOSE_CONDITION');?><sup style="vertical-align: super; padding:0 px;">
                                        <a class="mo_custom_api_info_link mo_tooltip" onclick="moCustomUpgrade()" style="text-decoration: none;">
                                            <i class="fa-solid fa-crown" style="color: #f1c40f; font-size: 18px;"></i>
                                            <span class="mo_tooltiptext"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CONDITION_PREMIUM_FEATURE'); ?></span>
                                        </a>
                                    </sup></h4>
                                </div>
                                <div class="mo_boot_col-2 mo_boot_m-0 mo_boot_text-center">
                                    <button class="mo_boot_btn btn-users_sync mo_tooltip" id="add_api_cond" disabled>+ Add<span class="mo_tooltiptext"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_URL_TEXT1');?></span> </button>
                                </div>
                            </div>
                            <div class="mo_boot_row mo_boot_mt-2">
                                <div class="mo_boot_col-5 mo_boot_m-0">
                                    <select name="col_condition"  id="mo_condition_select" class="mo-form-control mo-form-control-select">
                                        <option value="None Selected"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_NONE_SELECTED');?></option>
                                        <?php 
                                        foreach($columnArr as $column_key => $column_value)
                                        {
                                            ?>
                                            <option  value="<?php echo $column_key ?>" disabled><?php echo $column_key ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mo_boot_col-5 mo_boot_m-0">
                                    <select name="col_condition_name" id="mo_query_condition"  class="mo-form-control mo-form-control-select test" readonly>
                                        <option value="no condition"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_NO_CONDITION');?></option>
                                        <option value="=" disabled><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_EQUAL');?></option>
                                        <option value="Like" disabled><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_LIKE');?></option>
                                        <option value=">" disabled><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_GREATER_THAN');?></option>
                                        <option value="Less Than" disabled><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_LESS_THAN');?></option>
                                        <option value="!=" disabled><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_NOT_EQUAL');?></option>
                                    </select>
                                </div>
                                <div class="mo_boot_col-2 mo_boot_m-0 mo_boot_text-center">
                                    <button class="mo_boot_btn btn_red_usersync" id="rm_api_cond" disabled><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mo_customapi_form_wrapper mo_boot_mt-2" id="filters_section">
                        <div class="mo_boot_p-4">
                                <div class="mo_boot_row">
                                    <div class="mo_boot_col-4">
                                        <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SELECT_FILTER');?> <sup style="vertical-align: super; padding:0 px;">
                                        <a class="mo_custom_api_info_link mo_tooltip" onclick="moCustomUpgrade()" style="text-decoration: none;">
                                            <i class="fa-solid fa-crown" style="color: #f1c40f; font-size: 18px;"></i>
                                            <span class="mo_tooltiptext"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CONDITION_PREMIUM_FEATURE'); ?></span>
                                        </a>
                                    </sup></h4>
                                    </div>
                                    <div class="mo_boot_col-4">
                                        <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SELECT_COLUMN');?> <sup style="vertical-align: super; padding:0 px;">
                                        <a class="mo_custom_api_info_link mo_tooltip" onclick="moCustomUpgrade()" style="text-decoration: none;">
                                            <i class="fa-solid fa-crown" style="color: #f1c40f; font-size: 18px;"></i>
                                            <span class="mo_tooltiptext"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CONDITION_PREMIUM_FEATURE'); ?></span>
                                        </a>
                                    </sup></h4>
                                    </div>
                                    <div class="mo_boot_col-4">
                                        <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SELECT_ORDER');?><sup style="vertical-align: super; padding:0 px;">
                                        <a class="mo_custom_api_info_link mo_tooltip" onclick="moCustomUpgrade()" style="text-decoration: none;">
                                            <i class="fa-solid fa-crown" style="color: #f1c40f; font-size: 18px;"></i>
                                            <span class="mo_tooltiptext"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CONDITION_PREMIUM_FEATURE'); ?></span>
                                        </a>
                                    </sup></h4>
                                    </div>
                                </div>
                                <div class="mo_boot_row mo_boot_mt-4">
                                    <div class="mo_boot_col-4">
                                        <select class="mo-form-control mo-form-control-select" name="filter_option" readonly>
                                            <option value="No Condition"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_NO_CONDITION');?></option>
                                            <option value="ORDER BY"  disabled><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ORDER_BY');?></option>
                                        </select>
                                    </div>
                                    <div class="mo_boot_col-4">
                                        <select name="filter_col"  class="mo-form-control mo-form-control-select" readonly>
                                                <option value="None Selected"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_NONE_SELECTED');?></option>
                                                <?php
                                                    foreach($columnArr as $column_key => $column_value)
                                                    {
                                                        ?>
                                                        <option value="<?php echo $column_key?>" disabled><?php echo  $column_key ?></option>
                                                        <?php
                                                    }
                                                ?>
                                        </select>
                                    </div>
                                    <div class="mo_boot_col-4">
                                        <select class="mo-form-control mo-form-control-select" name="filter_order" readonly>
                                            <option value="No Condition"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_NO_CONDITION');?></option>
                                            <option value="ASC" disabled>ASC</option>
                                            <option value="DESC" disabled>DESC</option>
                                        </select>
                                    </div>
                                </div>
                        </div>
                    </div>    
                    <div class="mo_customapi_form_wrapper mo_boot_mt-2">
                        <div class="mo_boot_p-4">
                            <div class="mo_boot_mb-4">
                                <h3 class="mo_boot_mb-3"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_AUTHENTICATION'); ?><sup style="vertical-align: super; padding:0 px;">
                                        <a class="mo_custom_api_info_link mo_tooltip" onclick="moCustomUpgrade()" style="text-decoration: none;">
                                            <i class="fa-solid fa-crown" style="color: #f1c40f; font-size: 18px;"></i>
                                            <span class="mo_tooltiptext"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CONDITION_PREMIUM_FEATURE'); ?></span>
                                        </a>
                                    </sup></h3>
                                <div class="mo_boot_row mo_boot_mb-3">
                                    <div class="mo_boot_col-3">
                                        <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ENABLE_AUTHENTICATION'); ?></h4>
                                    </div>
                                    <div class="mo_boot_col-8">
                                        <div class="mo_boot_d-flex mo_boot_align-items-center">
                                            <div class="mo_switcher_square">
                                            <input type="checkbox" id="enable_authentication" name="enable_authentication" disabled>
                                            <label for="enable_authentication"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mo_boot_row">
                                    <div class="mo_boot_form-check mo_boot_form-check-inline mo_boot_col-3">
                                        <input class="mo_boot_form-check-input" type="radio" name="auth_type" id="bearer_token" value="bearer" disabled>
                                        <label class="mo_boot_form-check-label" for="opt1">Bearer Token</label>
                                    </div>

                                    <div class="mo_boot_form-check mo_boot_form-check-inline mo_boot_col-3">
                                        <input class="mo_boot_form-check-input" type="radio" name="auth_type" id="basic_auth" value="basic" disabled>
                                        <label class="mo_boot_form-check-label" for="opt2">Basic Auth</label>
                                    </div>

                                    <div class="mo_boot_form-check mo_boot_form-check-inline mo_boot_col-3">
                                        <input class="mo_boot_form-check-input" type="radio" name="auth_type" id="jwt" value="jwt" disabled>
                                        <label class="mo_boot_form-check-label" for="opt3">JWT Bearer</label>
                                    </div>

                                    <div class="mo_boot_form-check mo_boot_form-check-inline mo_boot_col-3">
                                        <input class="mo_boot_form-check-input" type="radio" name="auth_type" id="api_key" value="apikey" disabled>
                                        <label class="mo_boot_form-check-label" for="opt4">API Key </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden"  name="table_name" value="<?php echo !empty($api_configuration->table_name)?$api_configuration->table_name:''; ?>">
                    <div class=" mo_boot_text-center mo_boot_mt-5">
                        <input type="button" id="save_api_button" value="<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SAVE'); ?>" class="mo_boot_btn btn-users_sync" onclick="check_values()" <?php echo !empty($api_configuration->api_name)?'':''; ?>>
                    </div>
                </form>

                <form  method="post" action="<?php echo Route::_('index.php?option=com_miniorange_customapi&view=accountsetup&task=accountsetup.saveAPIInformation');?>">
                    <input type="hidden" id="mo_api_name" name="api_name" >
                    <input type="hidden" id="mo_method_name" name="api_method" >
                    <input type="hidden" id="mo_table_name" name="mo_table_name">
                    <input type="hidden" name="edit_api"  value="<?php echo $edit?>">
                    <input type="submit" id="SubmitForm1" name="SubmitForm1" class="mo_customapi_visibility">
                </form>
            </div>
        </div>
    </div>
    <?php
}

function connect_external_apis()
{
    ?>
    <div class="mo_boot_container mo_customapi_main_section">
        <div class="mo_boot_row mo_boot_mo_boot_justify-content-center">
            <div class="mo_boot_col-md-12">
                <div class="mo_boot_d-flex mo_boot_justify-content-between mo_boot_align-items-center mo_boot_mb-3">
                     <div class="mo_boot_d-flex mo_boot_align-items-center">
                         <h4 class="mo_boot_mb-0 mo_boot_me-2">
                             <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CONNECT_EXTERNAL_API'); ?>
                         </h4>
                         <sup style="vertical-align: super; padding: 0;">
                             <a class="mo_custom_api_info_link mo_tooltip" onclick="moCustomUpgrade()" style="text-decoration: none;">
                                 <i class="fa-solid fa-crown" style="color: #f1c40f; font-size: 18px;"></i>
                                 <span class="mo_tooltiptext">
                                     <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CONDITION_PREMIUM_FEATURE'); ?>
                                 </span>
                             </a>
                         </sup>
                     </div>

                     <div class="mo_custom_api_info_link mo_tooltip">
                         <a href="https://plugins.miniorange.com/setup-custom-api-for-joomla#step5" target="_blank">
                            <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SETUP_GUIDE'); ?>
                         </a>
                         <span class="mo_tooltiptext">
                             <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_LIST_CUSTOM_API_TEXT'); ?>
                         </span>
                     </div>
                </div>


                <div class="mo_customapi_form_wrapper">
                    <div class="mo_boot_p-4">
                        <form name="external_api_form" method="post">
                            <div class="mo_boot_row mo_boot_my-4 mo_boot_mb-1">
                                <div class="mo_boot_col-3">
                                    <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_API_NAME');?>:<span class="mo_required_field">*</span></h4>
                                </div>
                                <div class="mo_boot_col-8">
                                    <input type="text" class="mo-form-control" name="api_name" placeholder="<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ENTER_API_NAME_1');?>" value="" disabled>
                                </div>
                            </div>

                            <div class="mo_boot_row mo_boot_my-4 mo_boot_mb-1">
                                <div class="mo_boot_col-3">
                                    <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SELECTED_METHOD');?>:<span class="mo_required_field">*</span></h4>
                                </div>
                                <div class="mo_boot_col-8">
                                    <select class="mo-form-control mo-form-control-select" id="external_api_method_selected" name="api_method" disabled>
                                        <option value="Select Method" readonly><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SELECTED_METHOD');?></option>
                                        <option value="get" disabled>GET&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_GET_PREMIUM_BRACKET'); ?></option>
                                        <option value="post" disabled>POST&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_GET_PREMIUM_BRACKET'); ?></option>
                                        <option value="put" disabled>PUT&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_GET_PREMIUM_BRACKET'); ?></option>
                                        <option value="delete" disabled>DELETE&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_GET_PREMIUM_BRACKET'); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="mo_boot_row mo_boot_my-4 mo_boot_mb-1">
                                <div class="mo_boot_col-3">
                                    <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_EXTERNAL_API_NAME');?><span class="mo_required_field">*</span></h4>
                                </div>
                                <div class="mo_boot_col-8">
                                    <input type="text" class="mo-form-control" id="external_api_val" name="external_api_val" placeholder="<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ENTER_EXTERNAL_API');?>" value="" disabled>
                                </div>
                            </div>

                            <div class="mo_boot_my-4 mo_boot_mb-1" id="before_query_params">
                                <div class="mo_boot_row" >
                                    <div class="mo_boot_col-3">
                                        <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_QUERY_PARAM');?></h4>
                                    </div>
                                    <div class="mo_boot_col-4">
                                        <input type="text" class="mo-form-control" id="external_api_query_key" name="external_api_query_key[0]" placeholder="<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_KEY_PLACEHOLDER');?>" value="" disabled>
                                    </div>
                                    <div class="mo_boot_col-4">
                                        <input type="text" class="mo-form-control" id="external_api_query_val" name="external_api_query_val[0]" placeholder="<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_VALUE_PLACEHOLDER');?>"  value="" disabled>
                                    </div>
                                    <div class="mo_boot_col-1">
                                            <input type="button" class="mo_boot_btn btn-users_sync" value="+" disabled/>
                                    </div>
                                </div>
                            </div>

                            <div class="mo_boot_my-4 mo_boot_mb-1" id="before_api_header">
                                <div class="mo_boot_row" >
                                    <div class="mo_boot_col-3">
                                        <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_HEADER');?></h4>
                                    </div>
                                    <div class="mo_boot_col-4">
                                        <input type="text" class=" mo-form-control" id="external_api_header_key" name="external_api_header_key[0]" placeholder="<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_KEY_PLACEHOLDER');?>" value="" disabled>
                                    </div>
                                    <div class="mo_boot_col-4">
                                        <input type="text" class=" mo-form-control" id="external_api_header_val" name="external_api_header_val[0]" placeholder="<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_VALUE_PLACEHOLDER');?>"  value="" disabled>
                                    </div>
                                    <div class="mo_boot_col-1">
                                            <input type="button" class="mo_boot_btn btn-users_sync" value="+" disabled/>
                                    </div>
                                </div>
                            </div>

                            <div class="mo_boot_row mo_boot_my-4 mo_boot_mb-1" >
                                <div class="mo_boot_col-3">
                                    <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_REQUEST_BODY');?></h4>
                                </div>
                                <div class="mo_boot_col-8">
                                    <select name="request_body_type" id="request_body_type" class="mo-form-control mo-form-control-select" readonly>
                                        <option value="Select body type" readonly><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SELECT_BODY_TYPE');?></option>
                                        <option value="x-www-form-urlencode" disabled>x-www-form-urlencode</option>
                                        <option value="JSON" disabled>JSON</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mo_boot_row mo_boot_my-4 mo_boot_mb-1" id="">
                                <div class="mo_boot_col-3">
                                    <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_RESPONSE_DATA');?></h4>
                                </div>
                                <div class="mo_boot_col-8">
                                    <select name="response_data_type" id="response_data_type" class="mo-form-control mo-form-control-select" readonly>
                                        <option value="Select response type" readonly><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SELECT_RESPONSE_TYPE');?></option>
                                        <option value="JSON" disabled>JSON</option>
                                        <option value="XML" disabled>XML</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mo_boot_text-center mo_boot_mt-4" style="gap: 12px;">
                                <button class="mo_boot_btn btn-users_sync" disabled><i class="fa-solid fa-check"></i>&nbsp;&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SAVE_CONFIGURATION');?></button>
                                <a id='test-config'class="mo_custom_api_test_config mo_boot_mx-2" disabled><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TEST_CONFIGURATION');?></a>
                            </div>
                        </form>
                </div>
            </div>
            </div>
        </div>
    </div>
    <?php
}

function add_authentication_to_api()
{
    $bearer_token = bin2hex(random_bytes(32));
    ?>
    <div class="mo_boot_container mo_customapi_main_section">
        <div class="mo_boot_row mo_boot_mo_boot_justify-content-center">
            <div class="mo_boot_col-md-12">
                <!-- Header -->
                <div class="mo_boot_d-flex mo_boot_justify-content-between mo_boot_align-items-center mo_boot_mb-3">
                     <div class="mo_boot_d-flex mo_boot_align-items-center">
                         <h4 class="mo_boot_mb-0 mo_boot_me-2">
                             <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_AUTHENTICATION_FOR_REST_API'); ?>
                         </h4>
                         <sup style="vertical-align: super; padding: 0;">
                             <a class="mo_custom_api_info_link mo_tooltip" onclick="moCustomUpgrade()" style="text-decoration: none;">
                                 <i class="fa-solid fa-crown" style="color: #f1c40f; font-size: 18px;"></i>
                                 <span class="mo_tooltiptext">
                                     <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CONDITION_PREMIUM_FEATURE'); ?>
                                 </span>
                             </a>
                         </sup>
                     </div>

                     <div class="mo_custom_api_info_link mo_tooltip">
                         <a href="https://plugins.miniorange.com/setup-custom-api-for-joomla#step4" target="_blank">
                            <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SETUP_GUIDE'); ?>
                         </a>
                         <span class="mo_tooltiptext">
                             <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_LIST_CUSTOM_API_TEXT'); ?>
                         </span>
                     </div>
                </div>

                <div class="mo_customapi_form_wrapper">
                    <div class="mo_boot_p-4">
                        <div class="mo_boot_mb-2">
                            <h4 class="mo_boot_mb-3"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CONFIGURE_API_DETAILS'); ?></h4>
                        
                            <p class="alert alert-info"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_AUTHENTICATION_NOTE'); ?></p>
                            
                            <div class="mo_boot_row mo_boot_mb-4 mo_boot_mt-3">
                                <div class="mo_boot_col-3">
                                    <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_API_NAME'); ?>:</h4>
                                </div>
                                <div class="mo_boot_col-8">
                                    <input type="text" class="mo-form-control" id="api_name" name="api_name" placeholder="<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_API_NAME_PLACEHOLDER'); ?>" value="" disabled>
                                </div>
                            </div>
                            <div class="mo_boot_row mo_boot_mb-4 mo_boot_mt-2">
                                <div class="mo_boot_col-3">
                                    <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_API_URL'); ?></h4>
                                </div>
                                <div class="mo_boot_col-8">
                                    <div class="mo_boot_d-flex">
                                        <input type="text" class="mo-form-control" id="api_endpoint" name="api_endpoint" placeholder="<joomla_base_url>/api/index.php/v1/banners" value="" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="mo_boot_row">
                                <div class="mo_boot_col-3">
                                    <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_HTTP_METHOD_LABEL'); ?></h4>
                                </div>
                                <div class="mo_boot_col-8">
                                    <div class="mo_boot_d-flex">
                                        <select class="mo-form-control-select mo-form-control" id="http_method" name="http_method">
                                            <option value=""><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SELECT_HTTP_METHOD'); ?></option>
                                            <option value="GET" disabled>GET&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_GET_PREMIUM_BRACKET'); ?></option>
                                            <option value="POST" disabled>POST&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_GET_PREMIUM_BRACKET'); ?></option>
                                            <option value="PUT" disabled>PUT&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_GET_PREMIUM_BRACKET'); ?></option>
                                            <option value="DELETE" disabled>DELETE&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_GET_PREMIUM_BRACKET'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mo_customapi_form_wrapper mo_boot_mt-2">
                    <div class="mo_boot_p-4">
                        <div class="mo_boot_mb-4">
                            <h3 class="mo_boot_mb-3"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_AUTHENTICATION'); ?></h3>
                            <div class="mo_boot_row mo_boot_mb-3">
                                <div class="mo_boot_col-3">
                                    <h4><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_ENABLE_AUTHENTICATION'); ?></h4>
                                </div>
                                <div class="mo_boot_col-8">
                                    <div class="mo_boot_d-flex mo_boot_align-items-center">
                                        <div class="mo_switcher_square">
                                        <input type="checkbox" id="enable_authentication" name="enable_authentication" disabled>
                                        <label for="enable_authentication"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mo_boot_row">
                                <div class="mo_boot_form-check mo_boot_form-check-inline mo_boot_col-3">
                                    <input class="mo_boot_form-check-input" type="radio" name="auth_type" id="opt1" value="bearer" disabled>
                                    <label class="mo_boot_form-check-label" for="opt1">Bearer Token</label>
                                </div>

                                <div class="mo_boot_form-check mo_boot_form-check-inline mo_boot_col-3">
                                    <input class="mo_boot_form-check-input" type="radio" name="auth_type" id="opt2" value="basic" disabled>
                                    <label class="mo_boot_form-check-label" for="opt2">Basic Auth</label>
                                </div>

                                <div class="mo_boot_form-check mo_boot_form-check-inline mo_boot_col-3">
                                    <input class="mo_boot_form-check-input" type="radio" name="auth_type" id="opt3" value="jwt" disabled>
                                    <label class="mo_boot_form-check-label" for="opt3">JWT Bearer</label>
                                </div>

                                <div class="mo_boot_form-check mo_boot_form-check-inline mo_boot_col-3">
                                    <input class="mo_boot_form-check-input" type="radio" name="auth_type" id="opt4" value="apikey" disabled>
                                    <label class="mo_boot_form-check-label" for="opt4">API Key</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mo_boot_text-center mo_boot_mt-4">
                    <button type="submit" class="mo_boot_btn btn-users_sync" disabled>
                        <i class="fa fa-check mo_boot_me-1"></i>
                        <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SAVE'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function view_current_custom_api()
{
    $plugin_settings=MocustomapiUtility::getConfiguration();
    // Backward compatibility for Joomla 3/4/5/6
    $app = Factory::getApplication();
    $input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
    $get = ($input && $input->get) ? $input->get->getArray() : [];
    $api_url='';
    if(isset($get['api_name']))
    {
        $api_configuration=MocustomapiUtility::fetch_api_info($get['api_name'],'custom');
        $api_url=Uri::root().MocustomapiUtility::getAPIByVersion().$api_configuration->api_name;
        $tab_name='show_custom_apis';
        $table_name=$api_configuration->table_name;
        $custom_data='';
        // Handle col_condition - can be string or array (for backward compatibility)
        $col_condition = 'None Selected';
        if (isset($api_configuration->col_condition)) {
            if (is_array($api_configuration->col_condition)) {
                $col_condition = isset($api_configuration->col_condition[0]) ? $api_configuration->col_condition[0] : 'None Selected';
            } else {
                $col_condition = $api_configuration->col_condition;
            }
        }
        if(!empty($col_condition) && ($col_condition!='None Selected'))
        {
            $custom_data.=$col_condition.'={' . $col_condition . '_value}';
        }
    }
    ?>

    <div class="mo_boot_container mo_customapi_main_section">
        <div class="mo_boot_row mo_boot_mo_boot_justify-content-center">
            <div id="show_current_api" class="mo_boot_col-md-12">
                <!-- Header -->
                <div class="mo_boot_d-flex mo_boot_justify-content-between mo_boot_align-items-center mo_boot_mb-3">
                    <div class="mo_boot_d-flex">
                         <h3><?php echo strtoupper($api_configuration->api_method) ?>/<span><?php if(!empty($api_configuration->api_name)){ echo $api_configuration->api_name; }?></h3>
                         <div class="mo_custom_api_info_link mo_tooltip mo_boot_mx-2">
                            <a href="https://plugins.miniorange.com/setup-custom-api-for-joomla#step2" target="_blank">
                                <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SETUP_GUIDE'); ?>
                            </a>
                            <span class="mo_tooltiptext">
                                <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_LIST_CUSTOM_API_TEXT'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="mo_boot_col-sm-4">
                        <a class="mo_boot_btn mo_boot_btn-danger mo_customapi_float_right" href="<?php echo Uri::root().'administrator/index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel='.$tab_name.''; ?>"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CLOSE');?></a>
                    </div>
                </div>

                <div class="mo_boot_row mo_boot_mt-3">
                    <div class="mo_boot_col-10">
                        <input id="mo_custom_api_copy_text1" class="mo-form-control" value="<?php echo $api_url?>" readonly>
                    </div>
                    <div class="mo_boot_col-1">
                        <i class="fa fa-copy mo_boot_pt-2" onclick="copyToClipboard();" style="cursor: pointer;"></i>
                    </div>
                </div>

                <div class="mo_boot_row mo_boot_mt-3 mo_boot_m-0 mo_boot_p-0">
                    <div class="mo_cusotm_view_api_table_top">
                        <h4><strong><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_API_TABLE');?></strong></h4>
                    </div>
                    <table class="mo_customapi_table_wrapper mo_boot_table mo_boot_table-bordered mo_boot_col-12 mo_boot_p-2">
                        <tr>
                            <td class="mo_boot_col-sm-2"><strong><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TABLE_NAME');?></strong></td>
                            <td class="mo_boot_col-sm-8"> '<?php echo  $table_name; ?>'</td>
                        </tr>
                    </table>
                </div>

                <div class="mo_boot_row mo_boot_mt-3 mo_boot_m-0 mo_boot_p-0">
                    <div class="mo_cusotm_view_api_table_top">
                        <h4><strong><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_EXAMPLE');?></strong></h4>
                    </div>
                    <table class="mo_customapi_table_wrapper mo_boot_table mo_boot_table-bordered mo_boot_col-12 mo_boot_p-2">
                        <tr>
                            <td class="mo_boot_col-sm-2"><strong><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_REQUEST');?></strong></td>
                            <td class="mo_boot_col-sm-8"><strong><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_FORMAT');?></strong></td>
                        </tr>
                        <tr>
                            <td class="mo_boot_col-sm-2">cURL</td>
                            <td class="mo_boot_col-sm-8">curl -X <?php echo strtoupper($api_configuration->api_method) ?> <?php echo $api_url?><?php if($api_configuration->api_method=='get' && !empty($custom_data)){ echo '?'.$custom_data;}else if($api_configuration->api_method!='get' && !empty($custom_data)){ echo '<br><strong>'.$api_configuration->api_method.' ' . Text::_('COM_MINIORANGE_CUSTOMAPI_VARIABLES') . ' </strong>:'.$custom_data; } ?> </td>
                        </tr>
                    </table>
                </div>
        
                <?php 
                if($tab_name=='show_custom_apis')
                {
                ?>
                <div class=" mo_boot_mt-2 mo_boot_text-center">
                    <a class="mo_boot_btn btn-users_sync mo_boot_ml-2" href="<?php echo URI::root().'administrator/index.php?option=com_miniorange_customapi&view=accountsetup&tab-panel=create_custom_api&api_name='.$api_configuration->api_name; ?>"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_EDIT_API');?></a>
                </div>
                <?php
                }
                ?>

                <div class="mo_customapi_form_wrapper mo_boot_mt-4 mo_boot_p-4">
                    <div class="api-help-header">
                        <h3><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TESTING_STEP_TITLE');?></h3>
                    </div>

                    <ol class="api-help-steps">
                        <li><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TESTING_STEP1');?>&nbsp;<a href="https://www.postman.com/" target="_blank">Postman</a>.</li>
                        <li><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TESTING_STEP2');?>&nbsp;<code>GET</code>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TESTING_STEP3');?></li>
                        <li><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TESTING_STEP4');?>
                            <pre><code id="api-url">{joomla_base_url}/api/index.php/v1/mini/{your_api_name}</code></pre>
                        </li>
                        <li><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TESTING_STEP5');?>&nbsp;<code>Headers</code>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TESTING_AND');?>&nbsp;<code>Body</code>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TESTING_STEP6');?></li>
                        <li><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TESTING_STEP7');?>&nbsp;<code>Send</code>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TESTING_STEP8');?></li>
                    </ol>

                    <div class="api-help-response">
                        <strong><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TESTING_STEP9');?></strong>
                        <pre><code>{
    "data": [
        {
            "id": 1,
            "parent_id": 0,
            "lft": 0,
            "rgt": 225,
            "level": 0
        },
        {
            "id": 2,
            "parent_id": 1,
            "lft": 1,
            "rgt": 2,
            "level": 1
        },
        {
            "id": 3,
            "parent_id": 1,
            "lft": 3,
            "rgt": 6,
            "level": 1
        }
    ]
}</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function request_for_demo()
{
    $current_user = Factory::getUser();
    $result = new MocustomapiUtility();
    $result = $result->load_database_values('#__miniorange_customapi_customer_details');
    $admin_email = isset($result['email']) ? $result['email'] : '';
    if ($admin_email == '') $admin_email = $current_user->email;
  
    ?>
    <div class="mo_boot_container mo_customapi_main_section">
        <div class="mo_boot_row mo_boot_justify-content-center">
            <div class="mo_boot_col-md-8">
                <h4 class="mo_boot_text-justify mo_boot_mb-3 mo_boot_mt-2"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TRIAL_TAB'); ?></h4>
                <div class="mo_customapi_form_wrapper">
                    <div class="mo_boot_p-3">
                        <form name="demo_request" method="post" action="<?php echo Route::_('index.php?option=com_miniorange_customapi&task=accountsetup.requestForTrialPlan'); ?>">
                            <input type="hidden" name="option1" value="mo_customapi_trial_demo" />
                            <div class="mo_boot_mb-3">
                                <h4 class="mo_boot_mb-1"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_EMAIL');?>:<span class="mo_required_field">*</span></h4>
                                <input type="email" class="mo-form-control" name="email" value="<?php echo $admin_email; ?>" placeholder="<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_EMAIL_PLACEHOLDER');?>" required>
                            </div>

                            <div class="mo_boot_mb-3">
                                <h4 class="mo_boot_mb-1"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_PHONE_NUMBER');?>:</h4>
                                <div class="mo_boot_row mo-phone-inline-row" data-mo-phone-dropdown>
                                    <div class="mo_boot_col-4">
                                        <div class="mo-phone-card">
                                            <div class="mo-country-select">
                                                <span class="flag" aria-hidden="true"></span>
                                                <span class="dial-code">+91</span>
                                                <span class="arrow">▾</span>
                                            </div>
                                            <ul class="mo-country-list"></ul>

                                            <input type="hidden" name="country_code" class="mo-country-code" value="91">
                                            <input type="hidden" name="client_timezone" class="mo-client-timezone" value="">
                                            <input type="hidden" name="client_timezone_offset" class="mo-client-timezone-offset" value="">
                                        </div>
                                    </div>
                                    <div class="mo_boot_col-8">
                                        <input type="tel" class="mo-form-control mo_boot_flex-fill" name="query_phone" placeholder="<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_PHONE_PLACEHOLDER');?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mo_boot_mb-3">
                                <h4 class="mo_boot_mb-1"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_REQUEST_TRIAL'); ?>:</h4>
                                <input type="text" class="mo-form-control" name="plan" value="Custom API premium plugin" readonly>
                            </div>

                            <div class="mo_boot_mb-3">
                                <h4 class="mo_boot_mb-1"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_DESCRIPTION'); ?>:<span class="mo_required_field">*</span></h4>
                                <textarea class="mo-form-control-textarea" name="description" rows="4" placeholder="<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TRIAL_ASSISTANCE'); ?>" required></textarea>
                            </div>

                            <input type="hidden" name="option1" value="mo_usync_login_send_query">
                            <div class="mo_boot_text-center">
                                <button type="submit" class="mo_boot_btn btn-users_sync">
                                    <i class="fa fa-envelope mo_boot_me-2"></i>&nbsp;<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TC_BTN'); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="mo_boot_mt-3 mo_boot_text-justify">
                    <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TRIAL_DESC'); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

<div id="delete_api_modal" class="TC_modal">
    <div class="TC_modal-content">
        <div class="mo_boot_mt-3">
            <div class="mo_boot_col-sm-12 mo_boot_text-center">
                <img src="<?php echo Uri::base(); ?>components/com_miniorange_customapi/assets/images/cancel.png" alt="Cancel">
                <h3><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_SELECT_ARE_YOU_SURE'); ?></h3>
                <p><span id="api_name_placeholder"></span> <?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_DELETE_POPUP_IRRVERSIBLE'); ?></p>
                <div>
                    <button onclick="hideDeleteModal()" class="btn btn-secondary"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_CLOSE_BUTTON'); ?></button>
                    <button onclick="submitDeleteForm()" class="btn btn-danger"><?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_DELETE_BUTTON'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="delete_api_form" method="post" action="<?php echo Route::_('index.php?option=com_miniorange_customapi&view=accountsetup&task=accountsetup.deleteCurrentAPI'); ?>">
    <input type="hidden" name="api_name" id="delete_api_name" value="">
    <input type="hidden" name="action" value="delete">
</form>

<script>

function validateAndSave() {
    let isValid = true;
    const errorMessages = {
        api_name: '<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_API_NAME_REQUIRED'); ?>',
        select_table_name: '<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_TABLE_REQUIRED'); ?>',
        selected_columns: '<?php echo Text::_('COM_MINIORANGE_CUSTOMAPI_COLUMNS_REQUIRED'); ?>'
    };

    // Clear previous errors
    document.querySelectorAll('.mo_field_error').forEach(error => {
        error.classList.remove('mo_field_error_show');
    });
    document.querySelectorAll('.mo_input_highlight').forEach(input => {
        input.classList.remove('mo_input_highlight');
    });

    // Check API Name
    const apiName = document.getElementById('api_name');
    if (!apiName.value.trim()) {
        isValid = false;
        showError('api_name', errorMessages.api_name);
    }

    const tableSelect = document.getElementById('select_table_name');
    const selectedValue = tableSelect.value.trim();

    if (!selectedValue || selectedValue === 'None Selected') {
        isValid = false;
        showError('select_table_name', errorMessages.select_table_name);
    }

    // Check Column Selection
    const selectedColumns = document.querySelectorAll('.option-checkbox:checked');
    const customMultiselect = document.querySelector('.custom-multiselect');
    if (selectedColumns.length === 0 && tableSelect.value !== 'None Selected' && tableSelect.value !== '') {
        isValid = false;
        
        // Create error message element
        const errorDiv = document.createElement('div');
        errorDiv.className = 'mo_field_error mo_field_error_show';
        errorDiv.innerHTML = errorMessages.selected_columns;
        
        // Remove existing error message if any
        const existingError = customMultiselect.parentElement.querySelector('.mo_field_error');
        if (existingError) {
            existingError.remove();
        }
        
        // Highlight the field and add error message
        customMultiselect.classList.add('mo_input_highlight');
        customMultiselect.parentElement.appendChild(errorDiv);
        
        // Scroll to first error if this is the first one
        if (!window.firstError) {
            window.firstError = customMultiselect;
            customMultiselect.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    if (isValid) {
        document.getElementById('create_api_form').submit();
    }
}
    
</script>