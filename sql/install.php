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

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wim_gdpr_cms_versions` (
  `id_gdpr_cms_version` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cms` INT(10) UNSIGNED NULL,
  `id_shop` INT(11) UNSIGNED NULL,
  `id_lang` INT(10) UNSIGNED NULL,
  `id_employee` INT(10) UNSIGNED NULL,
  `old_meta_title` VARCHAR(128) NULL,
  `old_meta_description` VARCHAR(255) NULL,
  `old_meta_keywords` VARCHAR(255) NULL,
  `old_content` LONGTEXT NULL,
  `old_link_rewrite` VARCHAR(128) NULL,
  `new_meta_title` VARCHAR(128) NULL,
  `new_meta_description` VARCHAR(255) NULL,
  `new_meta_keywords` VARCHAR(255) NULL,
  `new_content` LONGTEXT NULL,
  `new_link_rewrite` VARCHAR(128) NULL,
  `modification_reason_for_a_new` TEXT NULL,
  `show_to_users` TINYINT UNSIGNED NULL,
  `date_add` DATETIME NULL,
  PRIMARY KEY (`id_gdpr_cms_version`),
  INDEX `fk_id_cms_idx` (`id_cms` ASC),
  INDEX `fk_id_shop_idx` (`id_shop` ASC),
  INDEX `fk_id_lang_idx` (`id_lang` ASC),
  INDEX `fk_id_employee_idx` (`id_employee` ASC))
 ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wim_gdpr_user_acceptance` (
  `id_customer` INT(10) UNSIGNED NOT NULL,
  `id_gdpr_cms_version` INT UNSIGNED NOT NULL,
  `date_add` DATETIME NOT NULL,
  PRIMARY KEY (`id_customer`, `id_gdpr_cms_version`),
  INDEX `fk_id_gdpr_cms_version_idx` (`id_gdpr_cms_version` ASC))
  ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';



$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wim_gdpr_action_acceptance` (
  `id_gdpr_action_acceptance` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_guest` INT(10) UNSIGNED,
  `id_customer` INT(10) UNSIGNED,
  `id_gdpr_cms_version` INT UNSIGNED NOT NULL,
  `date_add` DATETIME NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `user_agent` VARCHAR(255) NOT NULL,
  `user_browser` VARCHAR(150) NOT NULL,
  `user_platform` VARCHAR(150) NOT NULL,
  `url_on_acceptance` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_gdpr_action_acceptance`),
  INDEX `fk_id_gdpr_cms_version_idx` (`id_gdpr_cms_version` ASC))
  ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';


foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
// Crear variable de configuracion
if (!Configuration::hasKey("WIM_GDPR_CMS_LIST")) {
    Configuration::updateGlobalValue('WIM_GDPR_CMS_LIST', "");
}
