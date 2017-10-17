<?php 
if (!defined('_PS_VERSION_')) {
    exit;
}
class LevelsModule extends Module
{
    private $RUN_TESTS = 0;
    public $LVL_MIN = 3;
    public function __construct()
    {
        $this->name = 'levelsmodule';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Andrew Serkin';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Levels Module');
        $this->description = $this->l('Loyalty plan module');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
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
                    $reduction = 15 + $lvl - 2;
                    if($lvl == 1){
                        $reduction = 5;
                        Configuration::updateValue('levels_module_lvl'.$lvl,0);
                    }
                    if($lvl == 2){
                        $reduction = 15;
                        Configuration::updateValue('levels_module_lvl'.$lvl,0);
                    }
                    $this->createGroup($lvl,$reduction);
                }
        return true;
    }
    private function createGroup($lvl,$reduction){
        $group = new Group(null,PS_LANG_DEFAULT);
        $group->name = 'lvl'.$lvl;
        $group->reduction = $reduction;
        $group->price_display_method = 0;
        $group->add();
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
            for($i = $this->LVL_MIN; $i <= $count;$i++){
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
       for($i = $this->LVL_MIN; $i <= $count;$i++){
           $helper->fields_value['levels_module_lvl'.$i] = Configuration::get('levels_module_lvl'.$i);
       };
       $helper->fields_value['MYMODULE_NAME'] = Configuration::get('MYMODULE_NAME');
        
       return $helper->generateForm($fields_form);
     }
     private function generateLevels(){
        $count = Configuration::get('levels_module_count');
        $levels = array();
        for($i = $this->LVL_MIN; $i <= $count;$i++){
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
        $this->runTests();
        $link = new Link();
        $customer = $this->context->customer;
        $stats = $customer->getStats();
        $isGuest = !$customer->isLogged();
        $total_orders = (int)$stats['total_orders'];
        $isBuyer = $total_orders ? true : false;
        $discount = 0;
        var_dump($buying_table);
        if($isBuyer){
            $lvl = $this->setLvl(2);
            $discount = 15;
        }
        elseif(!$isGuest){
            $lvl = $this->setLvl(1);
        }
        $count = Configuration::get('levels_module_count');
        for($i = $count; $i >= $this->LVL_MIN;$i--){
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
                'levels_module_login_url' => $link->getBaseLink() . 'login',
                'levels_module_module_url' => $link->getModuleLink('levelsmodule','display'),
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
    private $tests_count = 1;
    private function runTests(){
        if($this->RUN_TESTS){
            $this->setLvlShouldChangeLvl();
        }
    }
    private function assertTrue($assertion,$message = "Passed "){
        if($assertion){
            var_dump($message.$this->$test_count++);
        } else{
            var_dump("Not Passed".$this->tests_count++);
        }
    }
    private function assertEqual($first,$second,$message = "Passed "){
        if($first == $second){
            var_dump($message.$this->tests_count++);
        } else{
            var_dump($this->tests_count++." Expected ".$first." To be equal to ".$second." But it wasn't");
        }
    }
    private function setLvlShouldChangeLvl(){
        $this->setLvl(2);
        $lvl = $this->context->customer->id_default_group;
        $lvl = new Group($lvl,1);
        $this->assertEqual($lvl->name,"lvl2");
    }
    private function iterateLevels_should_do_func_with_args_on_each_iteration(){
        $this->iterateLevels(function(){

        });
    }
}
