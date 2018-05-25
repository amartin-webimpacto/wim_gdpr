<?php

class WimGdprActionAcceptance extends ObjectModel
{
    public $id_gdpr_action_acceptance;
    public $id_guest;
    public $id_customer;
    public $id_gdpr_cms_version;
    public $id_cms;
    public $id_shop;
    public $id_lang;
    public $date_add;
    public $ip_address;
    public $user_agent;
    public $user_browser;
    public $user_platform;
    public $url_on_acceptance;

    public static $ddbb_table = 'wim_gdpr_action_acceptance';
    public static $ddbb_primary_key_field = 'id_gdpr_action_acceptance';
    public static $ddbb_field_id_gdpr_action_acceptance = 'id_gdpr_action_acceptance';
    public static $ddbb_field_id_guest = 'id_guest';
    public static $ddbb_field_id_customer = 'id_customer';
    public static $ddbb_field_id_gdpr_cms_version = 'id_gdpr_cms_version';
    public static $ddbb_field_id_cms = 'id_cms';
    public static $ddbb_field_id_shop = 'id_shop';
    public static $ddbb_field_id_lang = 'id_lang';
    public static $ddbb_field_date_add = 'date_add';
    public static $ddbb_field_ip_address = 'ip_address';
    public static $ddbb_field_user_agent = 'user_agent';
    public static $ddbb_field_user_browser = 'user_browser';
    public static $ddbb_field_user_platform = 'user_platform';
    public static $ddbb_field_url_on_acceptance = 'url_on_acceptance';


    public function __construct($id_gdpr_cms_version = null, $id_cms = null, $id_gdpr_action_acceptance = null, $id_guest = null, $id_customer = null, $date_add = null,
                                $ip_address = null, $user_agent = null, $user_browser = null, $user_platform = null, $url_on_acceptance = null)
    {
        $this->id_gdpr_action_acceptance = ($id_gdpr_action_acceptance != null ? id_gdpr_action_acceptance : 0 );
        $this->id_gdpr_cms_version =       ($id_gdpr_cms_version != null       ? $id_gdpr_cms_version      : 0 );
        $this->id_cms =                    ($id_cms != null                    ? $id_cms                   : 0 );
        $this->id_guest =                  ($id_guest != null                  ? $id_guest                 : Context::getContext()->customer->id_guest );
        $this->id_customer =               ($id_customer != null               ? $id_customer              : GdprTools::getCurrentCustomer() );
        $this->date_add =                  ($date_add != null                  ? $date_add                 : date('Y-m-d H:i:s') );
        $this->ip_address =                ($ip_address != null                ? $ip_address               : GdprTools::getUserIpAdress() );
        $this->user_agent =                ($user_agent != null                ? $user_agent               : GdprTools::getUserAgent() );
        $this->user_browser =              ($user_browser != null              ? $user_browser             : GdprTools::getUserBrowser() );
        $this->user_platform =             ($user_platform != null             ? $user_platform            : GdprTools::getUserPlatform() );
        $this->url_on_acceptance =         ($url_on_acceptance != null         ? $url_on_acceptance        : GdprTools::getURLOnAcceptance() );
        $this->id_shop = Shop::getContextShopID();
        $this->id_lang = Context::getContext()->language->id;
    }

    public function save()
    {
        $wim_gdpr_action_acceptance = array(
            WimGdprActionAcceptance::$ddbb_field_id_gdpr_action_acceptance => $this->id_gdpr_action_acceptance,
            WimGdprActionAcceptance::$ddbb_field_id_guest => $this->id_guest,
            WimGdprActionAcceptance::$ddbb_field_id_customer => $this->id_customer,
            WimGdprActionAcceptance::$ddbb_field_id_gdpr_cms_version => $this->id_gdpr_cms_version,
            WimGdprActionAcceptance::$ddbb_field_id_cms => $this->id_cms,
            WimGdprActionAcceptance::$ddbb_field_id_shop => $this->id_shop,
            WimGdprActionAcceptance::$ddbb_field_id_lang => $this->id_lang,
            WimGdprActionAcceptance::$ddbb_field_date_add => pSQL($this->date_add),
            WimGdprActionAcceptance::$ddbb_field_ip_address => pSQL($this->ip_address),
            WimGdprActionAcceptance::$ddbb_field_user_agent => pSQL($this->user_agent),
            WimGdprActionAcceptance::$ddbb_field_user_browser => pSQL($this->user_browser),
            WimGdprActionAcceptance::$ddbb_field_user_platform => pSQL($this->user_platform),
            WimGdprActionAcceptance::$ddbb_field_url_on_acceptance => pSQL($this->url_on_acceptance)
        );
        return (Db::getInstance()->insert(WimGdprActionAcceptance::$ddbb_table, $wim_gdpr_action_acceptance));
    }

   /* public function add($id_gdpr_cms_version)
    {
        $tools = new GdprTools();

        $wim_gdpr_action_acceptance = array(
            WimGdprActionAcceptance::$ddbb_field_id_gdpr_action_acceptance => 0,
            WimGdprActionAcceptance::$ddbb_field_id_guest => int(Context::getContext()->customer->id_guest),
            WimGdprActionAcceptance::$ddbb_field_id_customer => int($tools->getCurrentCustomer()),
            WimGdprActionAcceptance::$ddbb_field_id_gdpr_cms_version => pSQL($id_gdpr_cms_version),
            WimGdprActionAcceptance::$ddbb_field_date_add => date('Y-m-d H:i:s'),
            WimGdprActionAcceptance::$ddbb_field_ip_address => pSQL($tools->getUserIpAdress()),
            WimGdprActionAcceptance::$ddbb_field_user_agent => pSQL($tools->getUserAgent()),
            WimGdprActionAcceptance::$ddbb_field_user_browser => pSQL($tools->getUserBrowser()),
            WimGdprActionAcceptance::$ddbb_field_user_platform => pSQL($tools->getUserPlatform()),
            WimGdprActionAcceptance::$ddbb_field_url_on_acceptance => pSQL($tools->getURLOnAcceptance())
        );
        return (Db::getInstance()->insert(WimGdprActionAcceptance::$ddbb_table, $wim_gdpr_action_acceptance));
    }
*/

}