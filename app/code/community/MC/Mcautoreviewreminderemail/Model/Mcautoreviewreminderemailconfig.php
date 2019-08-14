<?php

/*
 * @category    Community
 * @package     MC_Mcautoreviewreminderemail
 * @Document    Mcautoreviewreminderemailconfig.php
 * @Created on  April 11, 2012, 7:05 PM
 * @copyright   Copyright (c) 2012 Magento Complete
 */

class MC_Mcautoreviewreminderemail_Model_Mcautoreviewreminderemailconfig extends Mage_Core_Model_Config_Data
{
    
    protected function _beforeSave()
    {
		$license_approval = 0;
		$config_all_fields_value = $this->getFieldset_data();
		if($config_all_fields_value['mcautoreviewreminderemail_enable'] == 1){
			$license_key = $this->getValue();
			$arr = parse_url(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
			$domain = $arr['host'];
			$store_path = $arr['host'].$arr['path'];
			$product = "Auto_Review_Reminder_Emailer";
			$domain= substr($domain, 4, strlen($domain) - 1);
			if (empty($license_key)) {
			
				throw new Exception('Please Enter Valid License Key');
			}
			
			$license_approval = file_get_contents("http://irzoo.com/Extensions-Data/getinfo.php?storepath=".$store_path."&license=".$license_key."&domain=".$domain."&product=".$product);
	
			if (!empty($license_key) && (int)$license_approval != 1) {
				throw new Exception('Please Enter Valid License Key');
			}

			
			return $this;
		}
    }
	
	
}
