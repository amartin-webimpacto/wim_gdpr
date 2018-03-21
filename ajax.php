<?php

require_once(dirname(__FILE__) . '../../../config/config.inc.php');
require_once(dirname(__FILE__) . '../../../init.php');
require_once(dirname(__FILE__) . '/wim_gdpr.php');

$module = ModuleCore::getInstanceByName('wim_gdpr');

switch (Tools::getValue('action')) {
    case 'acceptCms' :
        acceptCms(Tools::getValue("id_gdpr_cms_version"));
        break;
    case 'getCms' :
        $cmsVersion = $module->getCmsVersion(Tools::getValue("id_gdpr_cms_version"));
        die($cmsVersion["new_content"]);
        break;
    case 'validationForm' :
        $validationForm = $module->validationForm(Tools::getValue("data"));
        exit(json_encode($validationForm));
        break;
    case 'canDeleteCms' :
        canDeleteCms(Tools::getValue("cms"));
        break;

    default:
        exit;
}
exit;

function canDeleteCms($cms)
{
    if (is_array($cms)) {
        if (!Wim_gdpr::canDeleteMultipleCMS($cms)) {
            die(Tools::jsonEncode(array('result' => "false")));
        }
    } else {
        if (Wim_gdpr::isCMSProtected($cms)) {
            die(Tools::jsonEncode(array('result' => "false")));
        }
    }
    die(Tools::jsonEncode(array('result' => "true")));
}

function acceptCms($list)
{
    foreach ($list as $id_gdpr_cms_version) {
        if (!Wim_gdpr::addWimGdprUserAceptance($id_gdpr_cms_version)) {
            die(Tools::jsonEncode(array('result' => 'error')));
        }
    }
    die(Tools::jsonEncode(array('result' => 'ok')));
}
