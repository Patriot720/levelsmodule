<?php
class levelsmoduledisplayModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
  //   $this->display_column_left = FALSE;
  //   $this->addJS($this->module->getPathUri().'/js/wtf.js');
  //   parent::initContent();
  //   var_dump($this->context->customer->getStats());
  //   var_dump($this->context->customer->id_default_group);
  //   $this->context->customer->id_default_group = '5'; // Settings default group
  //   $this->context->customer->update(); // Saving to sql
  //   var_dump($this->context->customer->id_default_group);
  //   $group = new Group(null,PS_LANG_DEFAULT);
  //   $group->name = 'Cool_group';
  //   $group->reduction = '95.00';
  //   $group->date_add = date('Y-m-d H:i:s');
  //   $group->date_upd = date('Y-m-d H:i:s');
  //   $group->price_display_method = 0;
  //   $group->add();
  //   var_dump(Group::getGroups(1));
  //  // $this->addJS($this->module->_path.'/views/js/wtf.js');
   
  //   $this->setTemplate('vassa.tpl');
    $this->display_column_left = FALSE;
    $this->addCSS($this->module->getPathUri().'/css/wtf.css');
    $this->addJS($this->module->getPathUri().'/js/wtf.js');
    $maximum = Configuration::get("levels_module_count");
    $all = $this->getAll();
    $max = $all['lvl'.$maximum];
    $link = new Link();
    parent::initContent();
    $this->context->smarty->assign(
      array(
        'levels_module_all_levels' =>$all,
        'levels_module_percent' => $this->getHeights($all),
        'levels_module_max_value' => $max,
        'arrow' => $link->getBaseLink() . "/modules/levelsmodule/" . 'arrow.png',
      )
      );
    $this->setTemplate('display.tpl');
    



  }
  private function getHeights($res){
    $arr = array();
    $maximum = Configuration::get("levels_module_count");
    $max = $res['lvl'.$maximum];
    $previous = 0;
    for($i = 3; $i <= $maximum;$i++){
      $value = $res['lvl'.$i];
      $arr['lvl'.$i] = 100*(($value / $max) - $previous);
      $previous = $value / $max;
    }
    return $arr;
  }
  private function getAll(){
  $res = array();
  $count = Configuration::get('levels_module_count');
  for($i = $count; $i >= 3;$i--){
      $lvl_money = Configuration::get('levels_module_lvl'.$i);
      $res['lvl'.$i] = $lvl_money;
      }
      return $res;
  }
  
  }