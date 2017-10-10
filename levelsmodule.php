<?php 
if (!defined('_PS_VERSION_')) {
    exit;
}
class LevelsModule extends Module
{
    public function __construct()
    {
        $this->name = 'levelsmodule';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Firstname Lastname';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('My module');
        $this->description = $this->l('Description of my module.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->l('No name provided');
        }
    }
    public function install()
    {
        parent::install();
                $this->registerHook('leftColumn');
                $this->registerHook('header');
                $this->registerHook("top");
                Configuration::updateValue('levels_module_count', 12);
                for($lvl = 1; $lvl <= 12;$lvl++){
                    Configuration::updateValue('levels_module_lvl'.$lvl,2000*$lvl);
                    $group = new Group(null,PS_LANG_DEFAULT);
                    $group->name = 'lvl'.$lvl;
                    if($lvl == 1)
                        $group->reduction = '5.00';
                    elseif ($lvl == 2) {
                        $group->reduction = '15.00';
                    }
                    else {
                        $group->reduction = (string)(15 + $lvl - 2);    
                    }
                    $group->price_display_method = 0;
                    $group->add();
                }
        return true;
    }
    public function uninstall(){
            parent::uninstall();
            $groups = Group::getGroups(1);
            foreach($groups as $g){
                if(strpos($g['name'],'lvl') !== false){
                    $group = new Group($g['id_group']);
                    $group->delete();
                }
            }
            return true;
    }
     public function getContent(){
         $output = null;
         if (Tools::isSubmit('submit' . $this->name)){
            $count = Configuration::get('levels_module_count');
            for($i = 3; $i <= $count;$i++){
                $lvl = Tools::getValue('levels_module_lvl'.$i);
                if(!$lvl ||
                    empty($lvl)
                    || !is_numeric($lvl)){
                        $output.= $this->displayError($this->l('Invalid Configuration value lvl' . $i));
                    }
                else{
                    Configuration::updateValue("levels_module_lvl".$i,$lvl);
                    if(!$output)
                        $output = $this->displayConfirmation($this->l('Settings updated'));
                }
            }
         }
         return $output . $this->displayForm();
     }
     public function displayForm(){
        $helper = new HelperForm();
        
       // Module, Token and currentIndex
       $helper->module = $this;
       $helper->name_controller = $this->name;
       $helper->token = Tools::getAdminTokenLite('AdminModules');
       $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        
       // Language
       $helper->default_form_language = $default_lang;
       $helper->allow_employee_form_lang = $default_lang;
        
       // title and Toolbar
       $helper->title = $this->displayName;
       $helper->show_toolbar = true;        // false -> remove toolbar
       $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
       $helper->submit_action = 'submit'.$this->name;
       $helper->toolbar_btn = array(
           'save' =>
           array(
               'desc' => $this->l('Save'),
               'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
               '&token='.Tools::getAdminTokenLite('AdminModules'),
           ),
           'back' => array(
               'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
               'desc' => $this->l('Back to list')
          )
       );
       $fields_form[0]['form'] = array(
           'legend' => array(
               'title' => $this->l('Settings'),
               'description' => $this->l('Top border values')
           ),
           'input' => $this->generateLevels(),
           'submit' => array(
               'title' => $this->l('Save'),
               'class' => 'btn btn-default pull-right'
           )
           );
        
       // Load current value
       $count = Configuration::get('levels_module_count');
       for($i = 3; $i <= $count;$i++){
           $helper->fields_value['levels_module_lvl'.$i] = Configuration::get('levels_module_lvl'.$i);
       };
       $helper->fields_value['MYMODULE_NAME'] = Configuration::get('MYMODULE_NAME');
        
       return $helper->generateForm($fields_form);
     }
     private function generateLevels(){
        $count = Configuration::get('levels_module_count');
        $levels = array();
        for($i = 3; $i <= $count;$i++){
            array_push($levels,array(
                'type' => 'text',
                'label' => $this->l("Level ".$i),
                'name' => 'levels_module_lvl'.$i,
                'size' => 20,
                'required' => true,
            ));
        };
        return $levels;
     }
    // public function getContent()
    // {
    //     $output = null;

    //     if (Tools::isSubmit('submit' . $this->name))
    //         {
    //         $my_module_name = strval(Tools::getValue('MYMODULE_NAME'));
    //         if (!$my_module_name
    //             || empty($my_module_name)
    //             || !Validate::isGenericName($my_module_name))
    //             $output .= $this->displayError($this->l('Invalid Configuration value'));
    //         else
    //             {
    //             Configuration::updateValue('MYMODULE_NAME', $my_module_name);
    //             $output .= $this->displayConfirmation($this->l('Settings updated'));
    //         }
    //     }
    //     return $output . $this->displayForm();
    // }
    // public function displayForm()
    // {
    //   // Get default language
    //     $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
    //     $helper = $this->generateList();
    //     return $helper->generateList('',$this->fields_form);
    // }
    public function hookDisplayRightColumn($params)
    {
        return $this->hookDisplayLeftColumn($params);
    }
    private function setLvl($lvl){
        $group = Group::searchByName('lvl'.$lvl);
        $this->context->customer->id_default_group = $group['id_group'];
        $this->context->customer->update();
        return $lvl;
    }
    public function hookDisplayTop($params){
        $link = new Link();
        $login_url = $link->getBaseLink() . 'login';
        $module_url = $link->getModuleLink('levelsmodule','display');
        $stats = $this->context->customer->getStats();
        $isGuest = !$this->context->customer->isLogged();
        $total_orders = (int)$stats['total_orders'];
        $isBuyer = $total_orders ? true : false;
        $discount = 0;
        if($isBuyer){
            $lvl = $this->setLvl(2);
            $discount = 15;
        }
        elseif(!$isGuest){
            $lvl = $this->setLvl(1);
        }
        $count = Configuration::get('levels_module_count');
        for($i = $count; $i >= 3;$i--){
            $lvl_money = Configuration::get('levels_module_lvl'.$i);
            if($total_orders > $lvl_money){
                $lvl = $this->setLvl($i);
                $discount = (int)Group::searchByName('lvl'.$lvl)['reduction'];
                $difference = Configuration::get('levels_module_lvl'.($lvl+1)) - $total_orders;
                break;
            }
        }
        $this->context->smarty->assign(
            array(
                'levels_module_login_url' => $login_url,
                'levels_module_module_url' => $module_url,
                'levels_module_is_guest' => $isGuest,
                'levels_module_total_orders' => $total_orders,
                'levels_module_is_buyer' => $isBuyer,
                'levels_module_discount' => $discount,
                'levels_module_current_lvl' => $lvl,
                'levels_module_until_next' => $difference
            )
            );
        return $this->display(__FILE__, 'levelsmodule.tpl');
    }
    public function hookDisplayLeftColumn($params)
    {
        $this->context->smarty->assign(
            array(
                'my_module_name' => Configuration::get('MYMODULE_NAME'),
                'my_module_link' => $this->context->link->getModuleLink('levelsmodule', 'display')
            )
        );
        return $this->display(__FILE__, 'levelsmodule.tpl');
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . 'css/levelsmodule.css', 'all');
    }
}
