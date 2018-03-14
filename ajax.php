<?php

require_once(dirname(__FILE__) . '../../../config/config.inc.php');
require_once(dirname(__FILE__) . '../../../init.php');
require_once(dirname(__FILE__) . '/wim_gdpr.php');

switch (Tools::getValue('action')) {
    case 'acceptCms' :
        $list = Tools::getValue("id_gdpr_cms_version");
        foreach ($list as $id_gdpr_cms_version) {
            if (!Wim_gdpr::addWimGdprUserAceptance($id_gdpr_cms_version)) {
                die(Tools::jsonEncode(array('result' => 'error')));
            }
        }
        die(Tools::jsonEncode(array('result' => 'ok')));
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
        error_log("------------------ ES ARRAY");
        if (!Wim_gdpr::canDeleteMultipleCMS($cms)) {
            error_log("------------------ NO PUEDE ELIMINARLO");
            die(Tools::jsonEncode(array('result' => "false")));
        }
    } else {
        if (!Wim_gdpr::canDeleteCMS($cms)) {
            die(Tools::jsonEncode(array('result' => "false")));
        }
    }
    die(Tools::jsonEncode(array('result' => "true")));
}
