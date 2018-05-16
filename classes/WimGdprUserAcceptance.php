<?php


class WimGdprUserAcceptance extends ObjectModel
{
    public $id_customer;
    public $id_gdpr_cms_version;
    public $date_add;

    public static $ddbb_table = 'wim_gdpr_user_acceptance';
    public static $ddbb_field_id_customer = 'id_customer';
    public static $ddbb_field_id_gdpr_cms_version = 'id_gdpr_cms_version';
    public static $ddbb_field_date_add = 'date_add';


    public function __construct($id_customer = null, $id_gdpr_cms_version = null, $date_add = null)
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
    }

    public function save()
    {
        $wim_gdpr_user_acceptance = array(
            WimGdprUserAcceptance::$ddbb_field_id_customer => pSQL($this->id_customer),
            WimGdprUserAcceptance::$ddbb_field_id_gdpr_cms_version => pSQL($this->id_gdpr_cms_version),
            WimGdprUserAcceptance::$ddbb_field_date_add => pSQL($this->date_add)
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