<?php

/**
 * IMPORTANTE!!
 *
 * CONFIGURACIÓN DEL MÓDULO:
 * Al instalar este módulo, hay que editar el template del CMS del tema en uso.
 * - El fichero en cuestión se encuentra en la ruta themes/[nombre-del-tema]/cms.tpl
 * - Justo encima del siguiente bloque de texto....
 * <div class="rte{if $content_only} content_only{/if}">
 *      {$cms->content}
 * </div>
 * - Se debe añadir la siguiente línea:
 * {hook h='displayCMSHistory'}
 * - Quedando algo parecido a:
 * {hook h='displayCMSHistory'}
 * <div class="rte{if $content_only} content_only{/if}">
 *      {$cms->content}
 * </div>
 *
 * La ejecución de este hook mostrará el histórico de cambios del CMS en cuestión.
 */

require_once('wim_gdpr_tools.php');
require_once('classes/WimGdprCmsVersion.php');
require_once('classes/WimGdprActionAcceptance.php');
require_once('classes/WimGdprUserAcceptance.php');

if (!defined('_PS_VERSION_')) {
    exit;
}

class Wim_gdpr extends Module
{
    protected $config_form = false;
    public $doubleHook = false; // En PS1.5 ejecutaba dos veces el hook "hookDisplayAdminForm" y se crea esta variable para controlarlo.

    public function __construct()
    {
        $this->name = 'wim_gdpr';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'WebImpacto';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('WebImpacto GDPR');
        $this->description = $this->l('WebImpacto General Data Protection Regulation');

        $this->confirmUninstall = $this->l('Va a proceder a desistalar el módulo Wim_gdpr. ¿Esta seguro?');

        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('WIM_GDPR_LIVE_MODE', false);

        include(dirname(__FILE__) . '/sql/install.php');

        return parent::install() &&
        $this->registerHook('header') &&
        $this->registerHook('backOfficeHeader') &&
        $this->registerHook('displayHeader') &&
        $this->registerHook('displayAdminForm') &&
        $this->registerHook('displayCMSHistory') &&
        $this->registerHook('displayWimGdprChecks') &&
        $this->registerHook('actionObjectCmsUpdateBefore') &&
        $this->registerHook('actionDispatcher');
    }

    public function uninstall()
    {
        Configuration::deleteByName('WIM_GDPR_LIVE_MODE');

        include(dirname(__FILE__) . '/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitWim_gdprModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign(array(
            'token' => Tools::getAdminTokenLite('AdminModules'),
        ));

        $this->context->smarty->assign('gdpr_list', GdprTools::getGDPRList());
        if (version_compare(_PS_VERSION_, '1.7', '<') === true) {// Prestashop 1.6 / 1.5
            $this->context->smarty->assign('ps_version', "1.6");
        } else {// Prestashop 1.7
            $this->context->smarty->assign('ps_version', "1.7");
        }
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'WIM_GDPR_CMS_LIST' => Configuration::get('WIM_GDPR_CMS_LIST'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            Configuration::updateGlobalValue($key, Tools::getValue($key));
        }
        $this->context->smarty->assign('gdpr_list', GdprTools::getGDPRList());
    }

    /**
     * Si existen errores en las validaciones establecidas devuelve mensaje de error correspondiente
     * @param $data //formulario serializado
     * @return array|bool|null|object
     */
    public function  validationForm($data)
    {
        $errors = array();
        parse_str($data, $outputForm);
        $cmsShops = GdprTools::getCMSshop($outputForm['id_cms']);
        $langs = LanguageCore::getLanguages();
        // Comprobar que se ha insertado un motivo de modificacion
        foreach ($langs as $input) {
            if (!GdprTools::AreCmsEquals($outputForm['id_cms'], $input['id_lang'], $outputForm)) {
                if ($outputForm['modification_reason_for_a_new_' . $input['id_lang']] == "") {
                    $errors [] = Tools::displayError($this->l('Debe indicar el motivo de la modificación de este CMS para el idioma ' . $input['name'] . '.'));
                }
            }
        }

        // Comprobar que no se desactive un CMS protegido
        if ($outputForm['active'] == 0) {
            $errors [] = Tools::displayError($this->l('El CMS que intenta desactivar se encuentra protegido. Para poder desactivarlo tiene que anular dicha protección en el módulo correspondiente (WebImpacto GDPR)'));
        }

        if (count($cmsShops) > 1) { // Si es multitienda...
            foreach ($cmsShops as $key => $shop) {
                if (!in_array($shop['id_shop'], $outputForm['itemShopSelected'])) {
                    $errors [] = Tools::displayError($this->l('No pude desmarcar una tienda que ha sido marcada con contenido protegido (Tienda : ' . $shop['name'] . ')'));
                }
            }
        }
        return $errors;
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        // Si está en el listado de CMS:
        if (Tools::getValue('controller') == "AdminCmsContent" && !Tools::getValue('id_cms')) {
            $this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path . 'views/js/cms_list.js');
        }
        // Si está editando un CMS:
        if (Tools::getValue('controller') == "AdminCmsContent" && Tools::getValue('id_cms')) {
            $this->context->controller->addJquery();

            $allShops = GdprTools::getContextShop();

            if (GdprTools::isCMSProtected(Tools::getValue('id_cms'), $allShops)) {
                $this->context->controller->addJS($this->_path . 'views/js/back.js');
            }
        }
        // Si está en la configuración del módulo:
        if (Tools::getValue('configure') == $this->name) {
            $this->context->smarty->assign('gdpr_list', GdprTools::getGDPRList());
            $this->context->controller->addJS($this->_path . 'views/js/module_admin.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    public function hookActionObjectCmsUpdateBefore()
    {
        $shop = Shop::getContextShopID();
        $shopList = [];
        if ($shop == null) {// "Todas las tiendas"/"Default group" seleccionado
            foreach (Shop::getShops() as $shopElement) {
                $shopList[] = $shopElement["id_shop"];
            }
        } else {
            $shopList[] = $shop;
        }

        $languageList = LanguageCore::getLanguages();

        // Tras la validación realizada por AJAX, aquí sólo nos queda comprobar si el CMS está protegido. De ser así, se guardará su versión en BBDD.
        if (count($languageList) > 0 && GdprTools::isCMSProtected((int)Tools::getValue('id_cms'))) {
            foreach ($languageList as $language) {
                foreach ($shopList as $shop) {
                    $listaAuxiliarShop[0] = $shop;
                    if (GdprTools::isCMSProtected((int)Tools::getValue('id_cms'), $listaAuxiliarShop)) {
                        $newCms = array(
                            'id_cms' => Tools::getValue('id_cms'),
                            'id_shop' => $shop,
                            'id_lang' => $language["id_lang"],
                            'id_employee' => Tools::getValue('id_employee'),
                            'new_meta_title' => Tools::getValue('meta_title_' . $language["id_lang"]),
                            'new_meta_description' => Tools::getValue('meta_description_' . $language["id_lang"]),
                            'new_meta_keywords' => Tools::getValue('meta_keywords_' . $language["id_lang"]),
                            'new_content' => Tools::getValue('content_' . $language["id_lang"]),
                            'new_link_rewrite' => Tools::getValue('link_rewrite_' . $language["id_lang"]),
                            'modification_reason_for_a_new' => Tools::getValue('modification_reason_for_a_new_' . $language["id_lang"]),
                            'show_to_users' => Tools::getValue('show_to_users'),
                        );
                        if (!GdprTools::AreCmsEquals(Tools::getValue('id_cms'), $language["id_lang"], null, $shop)) { // Cuando son identicos no se actualiza.
                            if (!WimGdprCmsVersion::add($newCms)) {
                                $this->errors[] = Tools::displayError($this->l('No se ha podido actualizar la tabla \' wim_gdpr_cms_versions\'.'));
                                return false;
                            }
                        } else {
                            $newShowToUsers = Tools::getValue('show_to_users');
                            $lastCmsVersion = WimGdprCmsVersion::getLast(Tools::getValue('id_cms'), $language["id_lang"], $shop);
                            if ($newShowToUsers != $lastCmsVersion["show_to_users"]) {
                                $newCms['modification_reason_for_a_new'] = $this->l('Apartado \'Mostrar a usuarios\' modificado');
                                if (!WimGdprCmsVersion::add($newCms)) {
                                    $this->errors[] = Tools::displayError($this->l('No se ha podido actualizar la tabla \' wim_gdpr_cms_versions\'.'));
                                    return false;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Muestra al usuario el popup para aceptar los cambios en los CMS si corresponde
     * @return mixed
     */
    public function hookDisplayHeader()
    {

        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
        $this->context->controller->addCSS($this->_path . '/views/css/gdpr_checks.css');

        $cmsToAccept = WimGdprCmsVersion::getCmsToShowToUser();
        if (count($cmsToAccept) > 0) {
            $this->context->smarty->assign('cmsList', $cmsToAccept);
            if (version_compare(_PS_VERSION_, '1.6', '<') === true) {// Prestashop 1.5
                $this->context->controller->addCSS($this->_path . '/views/css/tingle.min.css');
                $this->context->controller->addJS($this->_path . '/views/js/tingle.min.js');
                $this->context->controller->addJS($this->_path . '/views/js/1.5/front.js');
                return $this->display(__FILE__, 'views/templates/front/modal.tpl');
            } else { // Prestashop 1.6 en adelante
                $this->context->controller->addCSS($this->_path . '/views/css/modal.css');
                $this->context->controller->addJS($this->_path . '/views/js/front.js');
                return $this->display(__FILE__, 'modal.tpl');
            }
        }
    }

    public function hookDisplayAdminForm($params)
    {
        if (isset($this->doubleHook) && $this->doubleHook) {
            return "";
        }
        $this->doubleHook = true;

        $selectedShopList = GdprTools::getContextShop();
        if (GdprTools::isCMSProtected(AdminCmsControllerCore::getFieldValue($this->object, 'id_cms'), $selectedShopList)) {
            $languageList = LanguageCore::getLanguages();
            $this->smarty->assign('languageList', $languageList);
            $this->smarty->assign('show_to_users', WimGdprCmsVersion::getCmsShowToUserValue(AdminCmsControllerCore::getFieldValue($this->object, 'id_cms'), GdprTools::getContextShop()));
            $this->smarty->assign('url', __PS_BASE_URI__);
            $this->smarty->assign('current_id_shop', $this->context->shop->id);

            if (version_compare(_PS_VERSION_, '1.6', '<') === true) {// Prestashop <= 1.5
                return $this->display(__FILE__, 'views/templates/admin/cms_fields_1.5.tpl');
            } else { // Prestashop >= 1.6
                return $this->display(__FILE__, 'views/templates/admin/cms_fields.tpl');
            }
        }
    }

    public function hookDisplayCMSHistory($params)
    {
        $id_cms = Tools::getValue('id_cms');
        $selectedShop = GdprTools::getContextShop();
        if (GdprTools::isCMSProtected($id_cms, $selectedShop)) {
            $this->context->smarty->assign('cmsVersionHistory', WimGdprCmsVersion::getHistory($id_cms));
            return $this->context->smarty->fetch($this->local_path . 'views/templates/front/cms_history.tpl');
        }
    }

    /**
     * @param $params
     * @return mixed
     * Por defecto devuelve un listado de los CMS protegidos por WIM_GDPR para el lenguaje en el que esté abierta la web y para la tienda que esté abierta
     * Por parametro se podrá pasar una lista de IDs (de CMS) para devolver sólo esos
     */
    public function hookDisplayWimGdprChecks($params)
    {
        if ($params["id"] != null) {
            $params["id"] = preg_replace('/\s+/', '', $params["id"]);
            $params["id"] = explode(",", $params["id"]);
        }
        $link = Context::getContext()->link;

        $rows = GdprTools::getCmsList();
        $list = array();
        foreach ($rows as $row) {
            if (GdprTools::isCMSProtected($row["id_cms"])) {
                if ($params["id"] != null) {
                    if (!in_array($row['id_cms'], $params["id"])) {
                        continue;
                    }
                }
                $row['link'] = $link->getCMSLink((int)$row['id_cms'], $row['link_rewrite']);
                $list[] = $row;
            }
        }

        $this->smarty->assign('gdprList', $list);
        $this->smarty->assign('gdprListCount', count($list));
        return $this->display(__FILE__, 'views/templates/front/gdpr_checks.tpl');
    }

    public function hookActionDispatcher(){
        $list = Tools::getValue('check_cms_list');
        if(!$list){$list = null;}

        $total = Tools::getValue('check_cms_list_count');
        if(count($list) < $total){
            // Error: Debe aceptar las condiciones (hay que interrumpir la acción!!)
            error_log("--------------Debe aceptar las condiciones");
        }

        foreach($list as $id_cms) {
            $lastVersion = WimGdprCmsVersion::getLast($id_cms, Context::getContext()->language->id, Context::getContext()->shop->id);
            $id_gdpr_cms_version = $lastVersion[WimGdprCmsVersion::$ddbb_field_id_gdpr_cms_version];

            $actionAcceptance = new WimGdprActionAcceptance($id_gdpr_cms_version );
            $actionAcceptance->save();
        }
    }
}