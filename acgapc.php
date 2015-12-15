<?php   
if (!defined('_PS_VERSION_'))
  exit;

class Acgapc extends Module
{
	public function __construct() {
	    $this->name = 'acgapc';
	    $this->tab = 'administration';
	    $this->version = '1.0.0';
	    $this->author = 'ICE Soft';
	    $this->need_instance = 0;
	    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
	    $this->bootstrap = true;
	 
	    parent::__construct();
	 
	    $this->displayName = $this->l('Auto Customer Group Assign based on Postal Code');
	    $this->description = $this->l('New customers will be automatically assigned to a customer group based on a set of given postal codes');
	 
	    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	}

	public function install() {
		if (!parent::install() || !$this->registerHook('actionCustomerAccountAdd') || 
    		!Configuration::updateValue('acgapc_pcs', serialize(array('08400','08440','08450','08459','08458','08530','08445','08430'))) ||
    		!Configuration::updateValue('acgapc_ugid', 13))
			return false;
		return true;
	}

	public function uninstall() {
	  if (!parent::uninstall() ||
		    !Configuration::deleteByName('acgapc_pcs') ||
		    !Configuration::deleteByName('acgapc_ugid'))
	    return false;
	  return true;
	}

	public function hookActionCustomerAccountAdd($params) {
		$customer = $params['newCustomer'];
		$postcode = Tools::getValue('postcode');
		$cp_array = unserialize(Configuration::get('acgapc_pcs'));
		if (in_array($postcode, $cp_array)) {
			$customer->addGroups(array((int)Configuration::get('acgapc_ugid')));
		}
		return true;
	}
}