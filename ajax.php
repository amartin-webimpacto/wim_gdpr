<?php

require_once(dirname(__FILE__) . '../../../config/config.inc.php');
require_once(dirname(__FILE__) . '../../../init.php');
require_once(dirname(__FILE__) . '/wim_gdpr.php');

switch (Tools::getValue('action')) {
    case 'acceptCms' :
        acceptCms(Tools::getValue("id_gdpr_cms_version"));
        break;
    case 'getCms' :
        $cmsVersion = Wim_gdpr::getCmsVersion(Tools::getValue("id_gdpr_cms_version"));
        die($cmsVersion["new_content"]);
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
        if (!Wim_gdpr::canDeleteCMS($cms)) {
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
