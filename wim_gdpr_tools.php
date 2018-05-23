<?php

class GdprTools extends ObjectModel
{

    /**
     * @param $cmsList
     * @return bool
     * Al realizar un borrado multiple, comprueba uno por uno si el CMS está protegido
     */
    public function canDeleteMultipleCMS($cmsList)
    {
        foreach ($cmsList as $id_cms) {
            if (!Wim_gdpr::canDeleteCMS($id_cms)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $cms_id
     * @return bool
     * Comprueba si un cms esta controlado en el json de configuracion
     */
    public function isCMSProtected($cms_id = "", $allShops = null)
    {
        $json = json_decode(Configuration::get('WIM_GDPR_CMS_LIST'));

        if (empty($allShops)) {// Viene del listado
            foreach ($json->shop as $object) {
                foreach ($object as $cms_list) {
                    foreach ($cms_list as $cms) {
                        if ($cms_id == $cms) {
                            return true;
                        }
                    }
                }
            }
        } else { // Viene del CMS
            foreach ($allShops as $shop_id) {
                $cms_list = $json->shop->$shop_id;
                foreach ($cms_list as $cms_item) {
                    foreach ($cms_item as $cms) {
                        if ($cms_id == $cms) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    public function canDeleteCMS($id_cms)
    {
        if (Wim_gdpr::isCMSProtected($id_cms)) {
            return false;
        }
        return true;
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
        $id_shop = Context::getContext()->shop->id;

        $sql = 'SELECT cl.*, c.`active`
                FROM `' . _DB_PREFIX_ . 'cms_lang` cl, `'._DB_PREFIX_.'cms` c
                WHERE cl.`id_cms` = c.`id_cms`
                AND cl.`id_cms` = ' . (int)$id_cms . '
                AND cl.`id_lang` = ' . (int)$id_lang . '
                AND cl.`id_shop` = ' . (int)$id_shop;

        if (version_compare(_PS_VERSION_, '1.6', '<') === true) { // Prestashop <= 1.5
            $sql = 'SELECT cl.*, c.`active`
                    FROM `' . _DB_PREFIX_ . 'cms_lang` cl, `'._DB_PREFIX_.'cms` c
                    WHERE cl.`id_cms` = c.`id_cms`
                    AND cl.`id_cms` = ' . (int)$id_cms . '
                    AND cl.`id_lang` = ' . (int)$id_lang;
        }
        return Db::getInstance()->getRow($sql);
    }

    /**
     * Obtiene un listado de cms_lang para el lenguaje en el que esté abierta la web y para la tienda que esté abierta
     */
    public function getCmsList()
    {
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;

        $sql = 'SELECT cl.*, c.`active`
                FROM ' . _DB_PREFIX_ . 'cms_lang cl, ' . _DB_PREFIX_ . 'cms c
                WHERE cl.id_cms = c.id_cms
                AND cl.id_lang = ' . (int)$id_lang . '
                AND cl.id_shop = ' . (int)$id_shop . '
                ORDER BY cl.id_cms ASC;';

        if (version_compare(_PS_VERSION_, '1.6', '<') === true) { // Prestashop <= 1.5
            $sql = 'SELECT c.*, s.id_shop, cms.`active`
                FROM ' . _DB_PREFIX_ . 'cms_lang c, ' . _DB_PREFIX_ . 'cms_shop s, ' . _DB_PREFIX_ . 'cms cms
                WHERE c.id_cms = s.id_cms
                AND cms.id_cms = c.id_cms
                AND c.id_lang = ' . (int)$id_lang . '
                AND s.id_shop = ' . (int)$id_shop . '
                ORDER BY c.id_cms ASC;';
        }
        $rows = Db::getInstance()->ExecuteS($sql);

        return $rows;
    }

    /**
     * @param $id_cms
     * @param $id_lang
     * @param $outputForm //data serialize form (validation)
     * @return bool
     * Esta funcion se debe llamar sólo al añadir/editar un CMS.
     * Comparará el CMS antes de editarse con el CMS que quedaría después de editarse y devolverá el resultado.
     */
    public function AreCmsEquals($id_cms, $id_lang, $outputForm = null, $id_shop = null)
    {
        if (isset($outputForm)) {
            $id_shop = $outputForm['current_id_shop'];
        }

        // Get old CMS
        if (version_compare(_PS_VERSION_, '1.6', '<') === true) { // Prestashop <= 1.5
            $sql = 'SELECT `meta_title`, `meta_description`, `meta_keywords`, `content`, `link_rewrite`
                FROM ' . _DB_PREFIX_ . 'cms_lang
                WHERE `id_cms` = ' . (int)$id_cms . '
                AND `id_lang` = ' . (int)$id_lang;
        } else { // Prestashop >= 1.6
            $sql = 'SELECT `meta_title`, `meta_description`, `meta_keywords`, `content`, `link_rewrite`
                FROM ' . _DB_PREFIX_ . 'cms_lang
                WHERE `id_cms` = ' . (int)$id_cms . '
                AND `id_lang` = ' . (int)$id_lang . '
                AND `id_shop` = ' . (int)$id_shop;
        }

        if ($old_cms = Db::getInstance()->getRow($sql)) {
            // Get new CMS
            if (!empty($outputForm)) {
                $new_cms = array(
                    'meta_title' => $outputForm['meta_title_' . $id_lang],
                    'meta_description' => $outputForm['meta_description_' . $id_lang],
                    'meta_keywords' => $outputForm['meta_keywords_' . $id_lang],
                    'content' => $outputForm['content_' . $id_lang],
                    'link_rewrite' => $outputForm['link_rewrite_' . $id_lang],
                );
            } else {
                $new_cms = array(
                    'meta_title' => Tools::getValue('meta_title_' . $id_lang),
                    'meta_description' => Tools::getValue('meta_description_' . $id_lang),
                    'meta_keywords' => Tools::getValue('meta_keywords_' . $id_lang),
                    'content' => Tools::getValue('content_' . $id_lang),
                    'link_rewrite' => Tools::getValue('link_rewrite_' . $id_lang),
                );
            }
            $old_cms['content'] = strtolower(preg_replace('/[^a-zA-Z0-9-_\.]/', '', preg_replace("/(\r\n)+|\r+|\n+|\t+/i", " ", $old_cms['content'])));
            $new_cms['content'] = strtolower(preg_replace('/[^a-zA-Z0-9-_\.]/', '', preg_replace("/(\r\n)+|\r+|\n+|\t+/i", " ", $new_cms['content'])));

            // Compare
            if ($old_cms['content'] == $new_cms['content']) {// No se realiza comparación binaria por si los tipos de datos fueran distintos
                return true;
            } else {
                return false;
            }
        } else {// Se está creando uno nuevo
            return false;
        }
    }

    /**
     * Comprueba si se está intentando desasociar alguna tienda de un CMS
     * @param $id_cms
     * @param $new_shop_list
     * @return bool
     */
    public function isDeletingShops($id_cms, $new_shop_list)
    {
        // Get current shops
        $current_shop_list = GdprTools::getCmsShopList($id_cms);

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
     * Obtiene un listado de los CMS protegidos por WIM_GDPR para el lenguaje en el que esté abierta la web y para la tienda que esté abierta
     */
    public function getProtectedCmsList()
    {
        $rows = GdprTools::getCmsList();
        $list = array();
        foreach ($rows as $row) {
            if (GdprTools::isCMSProtected($row["id_cms"])) {
                $list[] = $row["id_cms"];
            }
        }
        return $list;
    }

    public function getCMSshop($id_cms)
    {
        $sql = '
			SELECT *
			FROM `' . _DB_PREFIX_ . 'cms_shop` cs
            LEFT JOIN `' . _DB_PREFIX_ . 'shop` s ON cs.`id_shop` = s.`id_shop`
            WHERE cs.`id_cms` = ' . (int)$id_cms;

        return Db::getInstance()->ExecuteS($sql);
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

    public function getUserIpAdress()
    {
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } else {
            $headers = $_SERVER;
        }

        if (array_key_exists('X-Forwarded-For', $headers)) {
            $_SERVER['HTTP_X_FORWARDED_FOR'] = $headers['X-Forwarded-For'];
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && (!isset($_SERVER['REMOTE_ADDR'])
                || preg_match('/^127\..*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^172\.16.*/i', trim($_SERVER['REMOTE_ADDR']))
                || preg_match('/^192\.168\.*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^10\..*/i', trim($_SERVER['REMOTE_ADDR'])))
        ) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                return $ips[0];
            } else {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    public function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    public function getUserBrowser()
    {
        $tools = new Tools();
        if (isset($tools->_user_browser)) {
            return $tools->_user_browser;
        }

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $_user_browser = 'unknown';

        if (preg_match('/MSIE/i', $user_agent) && !preg_match('/Opera/i', $user_agent)) {
            $_user_browser = 'Internet Explorer';
        } elseif (preg_match('/Firefox/i', $user_agent)) {
            $_user_browser = 'Mozilla Firefox';
        } elseif (preg_match('/Chrome/i', $user_agent)) {
            $_user_browser = 'Google Chrome';
        } elseif (preg_match('/Safari/i', $user_agent)) {
            $_user_browser = 'Apple Safari';
        } elseif (preg_match('/Opera/i', $user_agent)) {
            $_user_browser = 'Opera';
        } elseif (preg_match('/Netscape/i', $user_agent)) {
            $_user_browser = 'Netscape';
        }

        return $_user_browser;
    }

    public function getUserPlatform()
    {
        $tools = new Tools();
        if (isset($tools->_user_plateform)) {
            return $tools->_user_plateform;
        }

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $_user_plateform = 'unknown';

        if (preg_match('/linux/i', $user_agent)) {
            $_user_plateform = 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $user_agent)) {
            $_user_plateform = 'Mac';
        } elseif (preg_match('/windows|win32/i', $user_agent)) {
            $_user_plateform = 'Windows';
        }

        return $_user_plateform;
    }

    public function getURLOnAcceptance()
    {
        $url = "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $escaped_url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

        return $escaped_url;
    }

    public function getGDPRList()
    {
        $language_id = Context::getContext()->language->id;
        $data = [];
        $sql = 'SELECT c.*, l.name AS languageName, s.name AS shopName
                FROM ' . _DB_PREFIX_ . 'lang l, ' . _DB_PREFIX_ . 'cms_lang c, ' . _DB_PREFIX_ . 'shop s
                WHERE c.id_lang = l.id_lang
                AND c.id_shop = s.id_shop
                AND c.id_lang = ' . $language_id . '
                ORDER BY id_shop ASC, id_cms ASC;';

        if (version_compare(_PS_VERSION_, '1.6', '<') === true) {// Prestashop 1.5
            $sql = 'SELECT c.*, l.name AS languageName, s.name AS shopName, s.id_shop
                    FROM ' . _DB_PREFIX_ . 'lang l, ' . _DB_PREFIX_ . 'cms_lang c, ' . _DB_PREFIX_ . 'shop s, ' . _DB_PREFIX_ . 'cms_shop cs
                    WHERE c.id_lang = l.id_lang
                    AND c.id_lang = ' . $language_id . '
                    AND cs.id_shop = s.id_shop
                    AND cs.id_cms = c.id_cms
                    ORDER BY s.id_shop ASC , id_cms ASC;';
        }

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

    public function getContextShop()
    {
        $shop = Shop::getContextShopID();
        $allShops = [];
        if ($shop == null) {// "Todas las tiendas"/"Default group" seleccionado
            foreach (Shop::getShops() as $shopElement) {
                $allShops[] = $shopElement["id_shop"];
            }
        } else {
            $allShops[] = $shop;
        }

        return $allShops;
    }
}