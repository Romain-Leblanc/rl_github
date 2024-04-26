<?php
 
if (!defined('_PS_VERSION_')) {
    exit;
}

class Rl_Github extends Module {
    public function __construct()
    {
        $this->name = 'rl_github';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'LEBLANC Romain';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Github module');
        $this->description = $this->l('Showing github link in leftColumn');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module ?');

        if (!Configuration::get('GITHUB_USERNAME')) {
            $this->warning = $this->l('No name provided');
        }
    }

    public function install()
    {
        // Installation multi boutique
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
     
        if (!parent::install() ||
            !$this->registerHook('header') ||
            !$this->registerHook('leftColumn') ||
            !Configuration::updateValue('GITHUB_USERNAME', null)
        ) {
            return false;
        }
     
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !Configuration::deleteByName('GITHUB_USERNAME')
        ) {
            return false;
        }
     
        return true;
    }

    public function getContent()
    {
        $output = null;
        
        if (Tools::isSubmit('btnSubmit')) {
            $pageName = strval(Tools::getValue('GITHUB_USERNAME'));
        
            if (!$pageName || empty($pageName)) {
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            } else {
                Configuration::updateValue('GITHUB_USERNAME', $pageName);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }
    
        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Github username'),
                        'name' => 'GITHUB_USERNAME',
                        'size' => 20,
                        'required' => true
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name'  => 'btnSubmit'
                )
            ),
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $defaultLang;

        $helper->fields_value['GITHUB_USERNAME'] = Configuration::get('GITHUB_USERNAME');

        return $helper->generateForm(array($form));
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->registerStylesheet(
            'rl_github',
            $this->_path.'views/css/rl_github.css',
            ['server' => 'remote', 'position' => 'head', 'priority' => 150]
        );
    }

    public function hookDisplayLeftColumn($params)
    {
        $this->context->smarty->assign([
            'github_username' => Configuration::get('GITHUB_USERNAME'),
        ]);

        return $this->display(__FILE__, 'rl_github.tpl');
    }
}