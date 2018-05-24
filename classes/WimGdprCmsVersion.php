<?php


class WimGdprCmsVersion extends ObjectModel
{
    public $id_gdpr_cms_version;
    public $id_cms;
    public $id_shop;
    public $id_lang;
    public $id_employee;
    public $old_meta_title;
    public $old_meta_description;
    public $old_meta_keywords;
    public $old_content;
    public $old_link_rewrite;
    public $new_meta_title;
    public $new_meta_description;
    public $new_meta_keywords;
    public $new_content;
    public $new_link_rewrite;
    public $modification_reason_for_a_new;
    public $show_to_users;
    public $date_add;

    public static $ddbb_table = 'wim_gdpr_cms_versions';
    public static $ddbb_primary_key_field = 'id_gdpr_cms_version';
    public static $ddbb_field_id_gdpr_cms_version = 'id_gdpr_cms_version';
    public static $ddbb_field_id_cms = 'id_cms';
    public static $ddbb_field_id_shop = 'id_shop';
    public static $ddbb_field_id_lang = 'id_lang';
    public static $ddbb_field_id_employee = 'id_employee';
    public static $ddbb_field_old_meta_title = 'old_meta_title';
    public static $ddbb_field_old_meta_description = 'old_meta_description';
    public static $ddbb_field_old_meta_keywords = 'old_meta_keywords';
    public static $ddbb_field_old_content = 'old_content';
    public static $ddbb_field_old_link_rewrite = 'old_link_rewrite';
    public static $ddbb_field_new_meta_title = 'new_meta_title';
    public static $ddbb_field_new_meta_description = 'new_meta_description';
    public static $ddbb_field_new_meta_keywords = 'new_meta_keywords';
    public static $ddbb_field_new_content = 'new_content';
    public static $ddbb_field_new_link_rewrite = 'new_link_rewrite';
    public static $ddbb_field_modification_reason_for_a_new = 'modification_reason_for_a_new';
    public static $ddbb_field_show_to_users = 'show_to_users';
    public static $ddbb_field_date_add = 'date_add';

    /*public function __construct($id_gdpr_cms_version = null, $id_cms = null, $id_shop = null, $id_lang = null, $id_employee = null,
                                $old_meta_title = null, $old_meta_description = null, $old_meta_keywords = null, $old_content = null, $old_link_rewrite = null,
                                $new_meta_title = null, $new_meta_description = null, $new_meta_keywords = null, $new_content = null, $new_link_rewrite = null,
                                $modification_reason_for_a_new = null, $show_to_users = null, $date_add = null){
        
    }*/

    public function save()
    {

    }

    public function add($newCms)
    {
        $id_cms = $newCms["id_cms"];
        $oldCms = GdprTools::getCms($id_cms, $newCms["id_lang"]);

        if (version_compare(_PS_VERSION_, '1.6', '<') === true) { // Prestashop <= 1.5
            $newCms['old_meta_title'] = $oldCms["meta_title"];
            $newCms['old_meta_description'] = $oldCms["meta_description"];
            $newCms['old_meta_keywords'] = $oldCms["meta_keywords"];
            $newCms['old_content'] = $oldCms["content"];
            $newCms['old_link_rewrite'] = $oldCms["link_rewrite"];
            if (WimGdprCmsVersion::exists($newCms)) { // Evitar insertar duplicados
                return true;
            }
        }

        $win_gdpr_cms_version = array(WimGdprCmsVersion::$ddbb_field_id_gdpr_cms_version => 0,
            WimGdprCmsVersion::$ddbb_field_id_cms => pSQL($id_cms),
            WimGdprCmsVersion::$ddbb_field_id_shop => pSQL($newCms["id_shop"]),
            WimGdprCmsVersion::$ddbb_field_id_lang => pSQL($newCms["id_lang"]),
            WimGdprCmsVersion::$ddbb_field_id_employee => pSQL(Context::getContext()->employee->id),
            WimGdprCmsVersion::$ddbb_field_old_meta_title => pSQL($oldCms["meta_title"]),
            WimGdprCmsVersion::$ddbb_field_old_meta_description => pSQL($oldCms["meta_description"]),
            WimGdprCmsVersion::$ddbb_field_old_meta_keywords => pSQL($oldCms["meta_keywords"]),
            WimGdprCmsVersion::$ddbb_field_old_content => pSQL($oldCms["content"]),
            WimGdprCmsVersion::$ddbb_field_old_link_rewrite => pSQL($oldCms["link_rewrite"]),
            WimGdprCmsVersion::$ddbb_field_new_meta_title => pSQL($newCms["new_meta_title"]),
            WimGdprCmsVersion::$ddbb_field_new_meta_description => pSQL($newCms["new_meta_description"]),
            WimGdprCmsVersion::$ddbb_field_new_meta_keywords => pSQL($newCms["new_meta_keywords"]),
            WimGdprCmsVersion::$ddbb_field_new_content => pSQL($newCms["new_content"]),
            WimGdprCmsVersion::$ddbb_field_new_link_rewrite => pSQL($newCms["new_link_rewrite"]),
            WimGdprCmsVersion::$ddbb_field_modification_reason_for_a_new => Db::getInstance()->escape($newCms["modification_reason_for_a_new"],true),
            WimGdprCmsVersion::$ddbb_field_show_to_users => pSQL($newCms["show_to_users"]),
            WimGdprCmsVersion::$ddbb_field_date_add => date('Y-m-d H:i:s')
        );

        return (Db::getInstance()->insert(WimGdprCmsVersion::$ddbb_table, $win_gdpr_cms_version));
    }

    public function getAll()
    {

    }

    /**
     * A partir de un listado de cms protegidos, se obtiene la última actualización en la tabla "wim_gdpr_cms_versions"
     * para cada cms y comprueba si se deben mostrar o no al usuario:
     * if "wim_gdpr_cms_versions.show_to_users" == 1 || "wim_gdpr_cms_versions.show_to_users" == 2
     */
    public function getCmsToShowToUser()
    {
        $data = array();
        $protectedCmsList = GdprTools::getProtectedCmsList();
        $shop_id = (int)Context::getContext()->shop->id;
        if (!Context::getContext()->cookie->logged) {
            return $data;
        }
        $sql = 'SELECT v.*
                FROM ' . _DB_PREFIX_ . 'wim_gdpr_cms_versions v, ' . _DB_PREFIX_ . 'cms_shop s
                WHERE v.' . WimGdprCmsVersion::$ddbb_field_id_cms . ' = s.id_cms
                AND s.id_shop = "' . $shop_id . '"
                AND v.' . WimGdprCmsVersion::$ddbb_field_id_gdpr_cms_version . ' IN(
                    SELECT max(' . WimGdprCmsVersion::$ddbb_field_id_gdpr_cms_version . ')
                    FROM ' . _DB_PREFIX_ . 'wim_gdpr_cms_versions
                    WHERE ' . WimGdprCmsVersion::$ddbb_field_id_cms . ' IN (' . implode(",", $protectedCmsList) . ')
                    AND ' . WimGdprCmsVersion::$ddbb_field_id_lang . ' = ' . Context::getContext()->language->id . '
                    AND ' . WimGdprCmsVersion::$ddbb_field_id_shop . ' = ' . $shop_id . '
                    GROUP BY ' . WimGdprCmsVersion::$ddbb_field_id_cms . '
                )
                AND v.' . WimGdprCmsVersion::$ddbb_field_show_to_users . ' IN (1)
                AND v.' . WimGdprCmsVersion::$ddbb_field_id_gdpr_cms_version . ' NOT IN (
                    SELECT ' . WimGdprUserAcceptance::$ddbb_field_id_gdpr_cms_version . '
                    FROM ' . _DB_PREFIX_ . 'wim_gdpr_user_acceptance
                    WHERE ' . WimGdprUserAcceptance::$ddbb_field_id_customer . ' = ' . GdprTools::getCurrentCustomer() . '
                );';
        if ($rows = Db::getInstance()->ExecuteS($sql)) {
            foreach ($rows as $row) {
                $data[] = array(
                    "id_gdpr_cms_version" => $row[WimGdprCmsVersion::$ddbb_field_id_gdpr_cms_version],
                    "id_cms" => $row[WimGdprCmsVersion::$ddbb_field_id_cms],
                    "show_to_users" => $row[WimGdprCmsVersion::$ddbb_field_show_to_users],
                    "content" => $row[WimGdprCmsVersion::$ddbb_field_new_content],
                    "title" => $row[WimGdprCmsVersion::$ddbb_field_new_meta_title],
                    "modification_reason_for_a_new" => nl2br($row[WimGdprCmsVersion::$ddbb_field_modification_reason_for_a_new])
                );
            }
        }
        return $data;
    }

    /**
     * @param $id_cms
     * @return int
     * A partir de un "id_cms", devuelve el útlimo valor "show_to_users" asociado a este
     */
    public function getCmsShowToUserValue($id_cms, $id_shop)
    {
        $sql = 'SELECT ' . WimGdprCmsVersion::$ddbb_field_show_to_users . '
                FROM ' . _DB_PREFIX_ . 'wim_gdpr_cms_versions
                WHERE ' . WimGdprCmsVersion::$ddbb_field_id_cms . ' = ' . $id_cms . '
                AND ' . WimGdprCmsVersion::$ddbb_field_id_shop . ' IN(' . implode(",", $id_shop) . ')
                ORDER BY ' . WimGdprCmsVersion::$ddbb_field_id_gdpr_cms_version . ' DESC';

        if ($row = Db::getInstance()->getRow($sql)) {
            return $row[WimGdprCmsVersion::$ddbb_field_show_to_users];
        }
        return 0;
    }

    public function exists($obj)
    {
        $sql = 'SELECT *
                FROM ' . _DB_PREFIX_ . 'wim_gdpr_cms_versions
                WHERE ' . WimGdprCmsVersion::$ddbb_field_id_cms . ' = "' . pSQL($obj["id_cms"]) . '"
                AND ' . WimGdprCmsVersion::$ddbb_field_id_shop . ' = "' . pSQL($obj["id_shop"]) . '"
                AND ' . WimGdprCmsVersion::$ddbb_field_id_lang . ' = "' . pSQL($obj["id_lang"]) . '"
                AND ' . WimGdprCmsVersion::$ddbb_field_id_employee . ' = "' . pSQL(Context::getContext()->employee->id) . '"
                AND ' . WimGdprCmsVersion::$ddbb_field_modification_reason_for_a_new . ' = "' . pSQL($obj["modification_reason_for_a_new"]) . '"
                AND ' . WimGdprCmsVersion::$ddbb_field_show_to_users . ' = "' . pSQL($obj["show_to_users"]) . '"
                AND ' . WimGdprCmsVersion::$ddbb_field_date_add . ' = "' . date('Y-m-d H:i:s') . '"';

        return Db::getInstance()->getRow($sql);
    }

    /**
     * Devuelve un registro de la tabla 'wim_gdpr_cms_versions' a partir de un id recibido
     * @param $id_gdpr_cms_version
     * @return array|bool|null|object
     */
    public function get($id_gdpr_cms_version)
    {
        $sql = '
			SELECT *
			FROM `' . _DB_PREFIX_ . 'wim_gdpr_cms_versions`
			WHERE `' . WimGdprCmsVersion::$ddbb_field_id_gdpr_cms_version . '` = ' . (int)$id_gdpr_cms_version;

        return Db::getInstance()->getRow($sql);
    }

    /**
     * @param $id_cms
     * @param $id_lang
     * @param $id_shop
     * @return array|bool|null|object
     * Devuelve el último registro insertado a partir de un id_cms, id_lang e id_shop
     */
    public function getLast($id_cms, $id_lang, $id_shop)
    {
        $sql = '
			SELECT *
			FROM `' . _DB_PREFIX_ . 'wim_gdpr_cms_versions`
			WHERE `' . WimGdprCmsVersion::$ddbb_field_id_cms . '` = ' . (int)$id_cms . '
			AND `' . WimGdprCmsVersion::$ddbb_field_id_shop . '` = ' . (int)$id_shop . '
			AND `' . WimGdprCmsVersion::$ddbb_field_id_lang . '` = ' . (int)$id_lang . '
			ORDER BY ' . WimGdprCmsVersion::$ddbb_field_id_gdpr_cms_version . ' DESC
			';

        return Db::getInstance()->getRow($sql);
    }

    /**
     * Devuelve un historico de versiones de un CMS
     * @param $cms_id
     * @throws PrestaShopDatabaseException
     */
    public function getHistory($cms_id)
    {
        $lang_id = Context::getContext()->language->id;
        $shop_id = Context::getContext()->shop->id;

        $sql = 'SELECT *
        FROM `' . _DB_PREFIX_ . 'wim_gdpr_cms_versions`
        WHERE `' . WimGdprCmsVersion::$ddbb_field_show_to_users . '` in(1,2)
        AND `' . WimGdprCmsVersion::$ddbb_field_id_cms . '` = ' . (int)$cms_id . '
        AND `' . WimGdprCmsVersion::$ddbb_field_id_shop . '` = ' . (int)$shop_id . '
        AND `' . WimGdprCmsVersion::$ddbb_field_id_lang . '` = ' . (int)$lang_id . '
        ORDER BY `' . WimGdprCmsVersion::$ddbb_field_date_add . '`, `' . WimGdprCmsVersion::$ddbb_field_id_gdpr_cms_version . '`;';

        return Db::getInstance()->ExecuteS($sql);
    }
}