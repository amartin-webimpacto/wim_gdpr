<?php

require_once(dirname(__FILE__) . '../../../config/config.inc.php');
require_once(dirname(__FILE__) . '../../../init.php');
/*require_once(dirname(__FILE__) . '/wim_gdpr.php');*/

$module = ModuleCore::getInstanceByName('wim_gdpr');

switch (Tools::getValue('action')) {
    case 'acceptCms' :
        $list = Tools::getValue("id_gdpr_cms_version");
        foreach ($list as $id_gdpr_cms_version) {
            if (!$module->addWimGdprUserAceptance($id_gdpr_cms_version)) {
                die(Tools::jsonEncode(array('result' => 'error')));
            }
        }
        die(Tools::jsonEncode(array('result' => 'ok')));
        break;
    case 'getCms' :
        $cmsVersion = $module->getCmsVersion(Tools::getValue("id_gdpr_cms_version"));
        die($cmsVersion["new_content"]);
        break;
    case 'validationForm' :
        $validationForm = $module->validationForm(Tools::getValue("data"));
        exit(json_encode($validationForm));
        break;
    default:
        exit;
}
exit;
