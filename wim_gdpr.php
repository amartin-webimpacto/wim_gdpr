<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Wim_gdpr extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'wim_gdpr';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'WebImpacto';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('WebImpacto GDPR');
        $this->description = $this->l('WebImpacto General Data Protection Regulation');

        $this->confirmUninstall = $this->l('');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
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
        $this->registerHook('actionObjectCmsAddBefore') &&
        $this->registerHook('actionObjectCmsAddAfter') &&
        $this->registerHook('actionObjectCmsUpdateBefore') &&
        $this->registerHook('actionObjectCmsUpdateAfter') &&
        $this->registerHook('actionObjectCmsDeleteBefore') &&
        //$this->registerHook('actionObjectCmsAddBefore') &&
        $this->registerHook('actionObjectCmsDeleteAfter') &&
        $this->registerHook('displayAdminForm');
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

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        return false;
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitWim_gdprModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        // return $helper->generateForm(array($this->getConfigForm()));
    }


    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        /* return array(
             'form' => array(
                 'legend' => array(
                     'title' => $this->l('Settings'),
                     'icon' => 'icon-cogs',
                 ),
                 'input' => array(
                     array(
                         'type' => 'switch',
                         'label' => $this->l('Live mode'),
                         'name' => 'WIM_GDPR_LIVE_MODE',
                         'is_bool' => true,
                         'desc' => $this->l('Use this module in live mode'),
                         'values' => array(
                             array(
                                 'id' => 'active_on',
                                 'value' => true,
                                 'label' => $this->l('Enabled')
                             ),
                             array(
                                 'id' => 'active_off',
                                 'value' => false,
                                 'label' => $this->l('Disabled')
                             )
                         ),
                     ),
                     array(
                         'col' => 3,
                         'type' => 'text',
                         'prefix' => '<i class="icon icon-envelope"></i>',
                         'desc' => $this->l('Enter a valid email address'),
                         'name' => 'WIM_GDPR_ACCOUNT_EMAIL',
                         'label' => $this->l('Email'),
                     ),
                     array(
                         'type' => 'password',
                         'name' => 'WIM_GDPR_ACCOUNT_PASSWORD',
                         'label' => $this->l('Password'),
                     ),
                 ),
                 'submit' => array(
                     'title' => $this->l('Save'),
                 ),
             ),
         );*/
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
        $this->context->smarty->assign('gdpr_list', $this->getGDPRList());
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->smarty->assign('gdpr_list', $this->getGDPRList());
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }


    /**
     * @param $id_cms
     * @return bool
     * Comprueba si se está intentando desasociar alguna tienda de un CMS
     */
    public function isDeletingShops($id_cms)
    {
        // Get current shops
        $current_shop_list = $this->getCmsShopList($id_cms);

        // Get new shops
        $new_shop_list = Tools::getValue('checkBoxShopAsso_cms');

        // Diff
        foreach ($current_shop_list as $current_shop) {
            foreach ($current_shop as $key => $current_shop_id) {
                if (!in_array($current_shop_id, $new_shop_list)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $id_cms
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     * Devuelve un listado de tiendas asociado a un cms
     */
    public function getCmsShopList($id_cms)
    {
        $sql = 'SELECT id_shop
                FROM ' . _DB_PREFIX_ . 'cms_shop
                WHERE id_cms = ' . (int)$id_cms . ';';
        return Db::getInstance()->ExecuteS($sql);
    }


    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    public function hookActionObjectCmsAddBefore($params)
    {
        //ddd($params['object']);
    }


    public function hookActionObjectCmsDeleteBefore($params)
    {
        //ddd($params['object']);
    }

    public function hookActionObjectCmsUpdateBefore($params)
    {
        // Update CMS
        if ((Tools::isSubmit('submitAddcms') || Tools::isSubmit('submitAddcmsAndPreview')) && $this->isCMSProtected((int)Tools::getValue('id_cms'))) {
            // Si el CMS esta protegido, se debe insertar un motivo para su actualizacion.
            $languageList = LanguageCore::getLanguages();
            if (count($languageList) > 0) {
                foreach ($languageList as $language) {
                    if (!$this->AreCmsEquals(Tools::getValue('id_cms'), $language["id_lang"])) { // Cuando son identicos no se actualiza.
                        if (strlen(Tools::getValue('modification_reason_for_a_new_' . $language["id_lang"])) < 1) {
                            $this->context->controller->errors[] = Tools::displayError('Debe indicar el motivo de la modificación de este CMS.');
                        }
                    }
                }
            }

            if ($this->isDeletingShops((int)Tools::getValue('id_cms'))) {
                $this->context->controller->errors[] = Tools::displayError('El CMS está protegido por WebImpacto GDPR y no se puede desasociar de una tienda.');
            }

            // Si el CMS esta protegido, siempre debe estar activo.
            if (Tools::getValue('active') == 0) {
                $this->context->controller->errors[] = Tools::displayError('El CMS está protegido por WebImpacto GDPR y debe estar activo.');
            }
            if (count($this->context->controller->errors) > 0) {
                
                /*
                $this->context->controller->errors[] = $this->l('Custom Error');
                $errors[] = $this->l('Custom Error');

                error_log("------ UPDATE BEFORE");
                $this->smarty->assign('languageList', "languageList");

                return $this->display = 'edit_page';
                (__FILE__, 'views/templates/admin/cms_fields.tpl');
                die($this->context->controller->display(__FILE__, 'edit_page'));
                die($this->display(__FILE__, 'edit_page'));
                throw new PrestaShopException('ESTE ES MI ERROR PERSONALIZADO');
                $this->errors[] "asdf"; // ERROR
                return $this->context->controller->display(__FILE__, 'edit_page');
                return $this->display = 'edit_page';

                return $this->context->controller->errors;
                ddd($this->context->controller->errors);
                return $this->context->controller->display(__FILE__, 'edit_page');
                throw new PrestaShopException('ESTE ES MI ERROR PERSONALIZADO');
                return false;
                */
            }
        }
        // Delete CMS: No se puede eliminar un CMS protegido
        if (Tools::isSubmit('deletecms') && $this->isCMSProtected((int)Tools::getValue('id_cms'))) {
            $this->context->controller->errors[] = Tools::displayError('El CMS está protegido por WebImpacto GDPR y no se puede eliminar.');
            return false;
        }

        // Delete Multiple CMS: No se puede eliminar un CMS protegido
        if (Tools::isSubmit('submitBulkdeletecms') && (!$this->canDeleteMultipleCMS($this->boxes))) {
            $this->context->controller->errors[] = Tools::displayError('Alguno de los CMS seleccionados está protegido por WebImpacto GDPR y no se puede eliminar. Ninguna acción se ha llevado a cabo.');
            return false;
        }

        // Insert wim_gdpr_cms_versions
        if ((Tools::isSubmit('submitAddcms') || Tools::isSubmit('submitAddcmsAndPreview')) && $this->isCMSProtected((int)Tools::getValue('id_cms'))) {
            $languageList = LanguageCore::getLanguages();
            if (count($languageList) > 0) {
                foreach ($languageList as $language) {
                    if (!$this->AreCmsEquals(Tools::getValue('id_cms'), $language["id_lang"])) { // Cuando son identicos no se actualiza.
                        $newCms = array(
                            'id_cms' => Tools::getValue('id_cms'),
                            'id_shop' => Tools::getValue('id_shop'),
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
                        //($this->getFieldValue($this->object, 'id_cms'))
                        if (!$this->addWimGdprCmsVersions($newCms)) {
                            $this->errors[] = Tools::displayError('No se ha podido actualizar la tabla \' wim_gdpr_cms_versions\'.');
                            return false;
                        }
                    }
                }
            }
        }

    }


    public function hookDisplayHeader()
    {
        $cmsToAccept = $this->getCmsToShowToUser();
        if (count($cmsToAccept) > 0) {
            $this->context->controller->addCSS($this->_path . '/views/css/modal.css');
            $this->context->controller->addJS($this->_path . '/views/js/front.js');
            $this->context->smarty->assign('cms', $cmsToAccept);
            $this->context->smarty->assign('id_gdpr_cms_version', '1');
            return $this->display(__FILE__, 'modal.tpl');
        }
    }

    public function getGDPRList()
    {
        $language_id = $this->context->language->id;
        $data = [];
        $sql = 'SELECT c.*, l.name AS languageName, s.name AS shopName
                FROM ' . _DB_PREFIX_ . 'lang l, ' . _DB_PREFIX_ . 'cms_lang c, ' . _DB_PREFIX_ . 'shop s
                WHERE c.id_lang = l.id_lang
                AND c.id_shop = s.id_shop
                AND c.id_lang = ' . $language_id . '
                ORDER BY id_shop ASC, id_cms ASC;';
        $rows = Db::getInstance()->ExecuteS($sql);
        foreach ($rows as $row) {
            $data[$row["id_shop"]]["id"] = $row["id_shop"];
            $data[$row["id_shop"]]["name"] = $row["shopName"];
            $data[$row["id_shop"]]["cms"][$row["id_cms"]] = ["id" => $row["id_cms"], "meta_title" => $row["meta_title"], "checked" => "0"];
        }

        $configCmsList = json_decode(Configuration::get('WIM_GDPR_CMS_LIST'), true);

        foreach ($configCmsList as $row) {
            foreach ($row as $shop_id => $shop) {
                foreach ($shop as $cms) {
                    foreach ($cms as $cms_id) {
                        $data[$shop_id]["cms"][$cms_id]["checked"] = 1;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param $cms_id
     * @return bool
     * Comprueba si un cms esta controlado en el json de configuracion
     */
    public function isCMSProtected($cms_id)
    {
        $json = json_decode(Configuration::get('WIM_GDPR_CMS_LIST'));

        foreach ($json->shop as $object) {
            foreach ($object as $cms_list) {
                foreach ($cms_list as $cms) {
                    if ($cms_id == $cms) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function addWimGdprUserAceptance($id_gdpr_cms_version)
    {
        $id_customer = $this::getCurrentCustomer();

        $wim_gdpr_user_aceptance = array(
            'id_customer' => pSQL($id_customer),
            'id_gdpr_cms_version' => pSQL($id_gdpr_cms_version),
            'date_add' => date('Y-m-d H:i:s')
        );
        return (Db::getInstance()->insert('wim_gdpr_user_aceptance', $wim_gdpr_user_aceptance));
    }

    public function addWimGdprCmsVersions($newCms)
    {
        $id_cms = $newCms["id_cms"];
        $oldCms = $this->getCms($id_cms, $newCms["id_lang"]);

        $win_gdpr_cms_version = array('id_gdpr_cms_version' => 0,
            'id_cms' => pSQL($id_cms),
            'id_shop' => pSQL($oldCms["id_shop"]),
            'id_lang' => pSQL($oldCms["id_lang"]),
            'id_employee' => pSQL($this->context->employee->id),
            'old_meta_title' => pSQL($oldCms["meta_title"]),
            'old_meta_description' => pSQL($oldCms["meta_description"]),
            'old_meta_keywords' => pSQL($oldCms["meta_keywords"]),
            'old_content' => pSQL($oldCms["content"]),
            'old_link_rewrite' => pSQL($oldCms["link_rewrite"]),
            'new_meta_title' => pSQL($newCms["new_meta_title"]),
            'new_meta_description' => pSQL($newCms["new_meta_description"]),
            'new_meta_keywords' => pSQL($newCms["new_meta_keywords"]),
            'new_content' => pSQL($newCms["new_content"]),
            'new_link_rewrite' => pSQL($newCms["new_link_rewrite"]),
            'modification_reason_for_a_new' => pSQL($newCms["modification_reason_for_a_new"]),
            'show_to_users' => pSQL($newCms["show_to_users"]),
            'date_add' => date('Y-m-d H:i:s'),);

        return (Db::getInstance()->insert('wim_gdpr_cms_versions', $win_gdpr_cms_version));
    }

    /**
     * @param $id_cms
     * @param null $id_lang
     * @return array|bool|null|object
     * Obtiene un CMS
     */
    public function getCms($id_cms, $id_lang = null)
    {
        if (is_null($id_lang)) {
            $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }

        $sql = '
			SELECT *
			FROM `' . _DB_PREFIX_ . 'cms_lang`
			WHERE `id_cms` = ' . (int)$id_cms . ' AND `id_lang` = ' . (int)$id_lang;

        return Db::getInstance()->getRow($sql);
    }

    /**
     * @return bool
     * Devuelve el id del cliente conectado.
     */
    public function getCurrentCustomer()
    {
        $id_customer = false;

        if (Context::getContext()->customer->id) {
            $id_customer = Context::getContext()->customer->id;
        }

        return $id_customer;
    }

    /*public function dummy($cms_id)
    {
        $lang_id = $this->context->language->id;
        $customer_id = Context::getContext()->customer->id;
        $shop_id = Context::getContext()->shop->id;

        // 1: Comprobar si hay actualizaciones en wim_gdpr_cms_versions para el cms recibido
        $sql = 'SELECT id_gdpr_cms_version
        FROM `' . _DB_PREFIX_ . 'wim_gdpr_cms_versions`
        WHERE `id_cms` = ' . (int)$cms_id . '
        AND `id_shop` = ' . (int)$shop_id . '
        AND `id_lang` = ' . (int)$lang_id . '
        ORDER BY `id_gdpr_cms_version` DESC;';

        $row = Db::getInstance()->getRow($sql);
        $id_gdpr_cms_version = $row["id_gdpr_cms_version"];

        // 2: Con el id_gdpr_cms_version, buscar en el campo de usuarios si ya se le ha mostrado al usuario en sesion
        $sql = 'SELECT COUNT(*) AS cont
        FROM `' . _DB_PREFIX_ . 'wim_gdpr_user_aceptance`
        WHERE `id_customer` =' . (int)$customer_id . '
        AND `id_gdpr_cms_version` =  ' . (int)$id_gdpr_cms_version;

        $row = Db::getInstance()->getRow($sql);
        if ($row["cont"] == 0) {
            return true;
        }
        return false;
    }*/

    /**
     * Obtiene un listado de cms_lang para el lenguaje en el que esté abierta la web y para la tienda que esté abierta
     */
    public function getCmsList()
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;

        $sql = 'SELECT *
                FROM ' . _DB_PREFIX_ . 'cms_lang
                WHERE id_lang = ' . (int)$id_lang . '
                AND id_shop = ' . (int)$id_shop . '
                ORDER BY id_cms ASC;';

        $rows = Db::getInstance()->ExecuteS($sql);

        return $rows;
    }

    /**
     * Obtiene un listado de los CMS protegidos por WIM_GDPR para el lenguaje en el que esté abierta la web y para la tienda que esté abierta
     */
    public function getProtectedCmsList()
    {
        $rows = $this->getCmsList();
        $list = array();
        foreach ($rows as $row) {
            if ($this->isCMSProtected($row["id_cms"])) {
                $list[] = $row["id_cms"];
            }
        }
        return $list;
    }

    /**
     * A partir de un listado de cms protegidos, se obtiene la última actualización en la tabla "wim_gdpr_cms_versions"
     * para cada cms y comprueba si se deben mostrar o no al usuario:
     * if "wim_gdpr_cms_versions.show_to_users" == 1 || "wim_gdpr_cms_versions.show_to_users" == 2
     *
     */
    public function getCmsToShowToUser()
    {
        $data = array();
        $protectedCmsList = $this->getProtectedCmsList();

        $sql = 'SELECT *
                FROM ' . _DB_PREFIX_ . 'wim_gdpr_cms_versions
                WHERE id_gdpr_cms_version IN(
                    SELECT max(id_gdpr_cms_version)
                    FROM ' . _DB_PREFIX_ . 'wim_gdpr_cms_versions
                    WHERE id_cms IN (' . implode(",", $protectedCmsList) . ')
                    AND id_lang = ' . $this->context->language->id . '
                    GROUP BY id_cms
                )
                AND show_to_users IN (1,2)
                AND id_gdpr_cms_version NOT IN (
                    SELECT id_gdpr_cms_version
                    FROM ' . _DB_PREFIX_ . 'wim_gdpr_user_aceptance
                    WHERE id_customer = ' . $this->getCurrentCustomer() . '
                );';

        $rows = Db::getInstance()->ExecuteS($sql);
        foreach ($rows as $row) {
            $data[] = array(
                "id_gdpr_cms_version" => $row["id_gdpr_cms_version"],
                "id_cms" => $row["id_cms"],
                "show_to_users" => $row["show_to_users"],
                "content" => $row["new_content"],
                "title" => $row["new_meta_title"],
                "modification_reason_for_a_new" => $row["modification_reason_for_a_new"]
            );
        }
        return $data;
    }

    /**
     * @param $id_cms
     * @return int
     * A partir de un "id_cms", devuelve el útlimo valor "show_to_users" asociado a este
     */
    public function getCmsShowToUserValue($id_cms)
    {
        $sql = 'SELECT show_to_users
                FROM ' . _DB_PREFIX_ . 'wim_gdpr_cms_versions
                WHERE id_cms = ' . $id_cms . '
                ORDER BY id_gdpr_cms_version DESC';
        if ($row = Db::getInstance()->getRow($sql)) {
            return $row['show_to_users'];
        }
        return 0;
    }

    /**
     * @param $cmsList
     * @return bool
     * Al realizar un borrado multiple, comprueba uno por uno si el CMS está progedio
     */
    public function canDeleteMultipleCMS($cmsList)
    {
        foreach ($cmsList as $cms) {
            if ($this->isCMSProtected($cms)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $id_cms
     * @param $id_lang
     * @return bool
     * Esta funcion se debe llamar sólo al añadir/editar un CMS.
     * Comparará el CMS antes de editarse con el CMS que quedaría después de editarse y devolverá el resultado.
     */
    public function AreCmsEquals($id_cms, $id_lang)
    {
        // Get old CMS
        $sql = 'SELECT `meta_title`, `meta_description`, `meta_keywords`, `content`, `link_rewrite`
                FROM ' . _DB_PREFIX_ . 'cms_lang
                WHERE `id_cms` = ' . (int)$id_cms . '
                AND `id_lang` = ' . (int)$id_lang . '
                AND `id_shop` = ' . Context::getContext()->shop->id;
        if ($old_cms = Db::getInstance()->getRow($sql)) {
            // Get new CMS
            $new_cms = array(
                'meta_title' => Tools::getValue('meta_title_' . $id_lang),
                'meta_description' => Tools::getValue('meta_description_' . $id_lang),
                'meta_keywords' => Tools::getValue('meta_keywords_' . $id_lang),
                'content' => Tools::getValue('content_' . $id_lang),
                'link_rewrite' => Tools::getValue('link_rewrite_' . $id_lang),
            );

            // Compare
            if ($old_cms == $new_cms) {// No se realiza comparación binaria por si los tipos de datos fueran distintos
                return true;
            } else {
                return false;
            }
        } else {// Se está creando uno nuevo
            return false;
        }
    }

    /**
     * Devuelve un registro de la tabla 'wim_gdpr_cms_versions' a partir de un id recibido
     * @param $id_gdpr_cms_version
     * @return array|bool|null|object
     */
    public function getCmsVersion($id_gdpr_cms_version)
    {
        $sql = '
			SELECT *
			FROM `' . _DB_PREFIX_ . 'wim_gdpr_cms_versions`
			WHERE `id_gdpr_cms_version` = ' . (int)$id_gdpr_cms_version;

        return Db::getInstance()->getRow($sql);
    }

    public function hookDisplayAdminForm($params)
    {
        if ($this->isCMSProtected(AdminCmsControllerCore::getFieldValue($this->object, 'id_cms'))) {
            $languageList = LanguageCore::getLanguages();
            $this->smarty->assign('languageList', $languageList);
            $this->smarty->assign('show_to_users', $this->getCmsShowToUserValue(AdminCmsControllerCore::getFieldValue($this->object, 'id_cms')));

            return $this->display(__FILE__, 'views/templates/admin/cms_fields.tpl');
        }
    }
}