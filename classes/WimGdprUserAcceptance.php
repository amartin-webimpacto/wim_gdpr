<?php


class WimGdprUserAcceptance extends ObjectModel
{
    public $id_customer;
    public $id_gdpr_cms_version;
    public $date_add;
    public $ip_address;
    public $user_agent;
    public $user_browser;
    public $user_platform;
    public $url_on_acceptance;

    public static $ddbb_table = 'wim_gdpr_user_acceptance';
    public static $ddbb_field_id_customer = 'id_customer';
    public static $ddbb_field_id_gdpr_cms_version = 'id_gdpr_cms_version';
    public static $ddbb_field_date_add = 'date_add';
    public static $ddbb_field_ip_address = 'ip_address';
    public static $ddbb_field_user_agent = 'user_agent';
    public static $ddbb_field_user_browser = 'user_browser';
    public static $ddbb_field_user_platform = 'user_platform';
    public static $ddbb_field_url_on_acceptance = 'url_on_acceptance';


    public function __construct($id_customer = null, $id_gdpr_cms_version = null, $date_add = null,
                                $ip_address = null, $user_agent = null, $user_browser = null, $user_platform = null, $url_on_acceptance = null)
    {
        if ($id_customer == null) {
            $id_customer = GdprTools::getCurrentCustomer();
        }
        $this->id_customer = $id_customer;

        $this->id_gdpr_cms_version = $id_gdpr_cms_version;

        if ($date_add == null) {
            $date_add = date('Y-m-d H:i:s');
        }
        $this->date_add = $date_add;

        $this->ip_address =                ($ip_address != null                ? $ip_address               : GdprTools::getUserIpAdress() );
        $this->user_agent =                ($user_agent != null                ? $user_agent               : GdprTools::getUserAgent() );
        $this->user_browser =              ($user_browser != null              ? $user_browser             : GdprTools::getUserBrowser() );
        $this->user_platform =             ($user_platform != null             ? $user_platform            : GdprTools::getUserPlatform() );
        $this->url_on_acceptance =         ($url_on_acceptance != null         ? $url_on_acceptance        : GdprTools::getURLOnAcceptance() );
    }

    public function save()
    {
        $wim_gdpr_user_acceptance = array(
            WimGdprUserAcceptance::$ddbb_field_id_customer => pSQL($this->id_customer),
            WimGdprUserAcceptance::$ddbb_field_id_gdpr_cms_version => pSQL($this->id_gdpr_cms_version),
            WimGdprUserAcceptance::$ddbb_field_date_add => pSQL($this->date_add),
            WimGdprUserAcceptance::$ddbb_field_ip_address => pSQL($this->ip_address),
            WimGdprUserAcceptance::$ddbb_field_user_agent => pSQL($this->user_agent),
            WimGdprUserAcceptance::$ddbb_field_user_browser => pSQL($this->user_browser),
            WimGdprUserAcceptance::$ddbb_field_user_platform => pSQL($this->user_platform),
            WimGdprUserAcceptance::$ddbb_field_url_on_acceptance => pSQL($this->url_on_acceptance)
        );
        return (Db::getInstance()->insert(WimGdprUserAcceptance::$ddbb_table, $wim_gdpr_user_acceptance));
    }

    public function get()
    {

    }

    public function getAll()
    {

    }

}