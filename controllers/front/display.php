<?php
class levelsmoduledisplayModuleFrontController extends ModuleFrontController
{
  private $LVL_MIN = 3;
  public function initContent()
  {
    $this->max_lvl = Configuration::get("levels_module_count");
    $this->display_column_left = FALSE;
    $this->display_column_right = FALSE;
    $this->addCSS($this->module->getPathUri().'/css/wtf.css');
    $this->addJS($this->module->getPathUri().'/js/wtf.js');
    $levels_limits = $this->getLevelsLimits();
    $max_lvl_limit = $levels_limits['lvl'.$this->max_lvl];
    $link = new Link();
    parent::initContent();
    $this->context->smarty->assign(
      array(
        'levels_module_all_levels' =>$levels_limits,
        'levels_module_percent' => $this->getTotalOrdersRelativePositions($levels_limits),
        'levels_module_max_value' => $max_lvl_limit,
        'arrow' => $link->getBaseLink() . "/modules/levelsmodule/" . 'arrow.png',
        'order_history' => $this->getOrdersHistory(10),
        'link' => $link
      )
      );
    $this->setTemplate('display.tpl');
  }
  private function getOrdersHistory($count){
    $customer = $this->context->customer;
    $orders =  Order::getCustomerOrders($customer->id);
    $buying_history = $customer->getBoughtProducts();
    $buying = array();
    $i = 0;
    foreach ($orders as $value) {
        if($i > $count)break;
        $buying_table = array();
        $buying_table['price'] = $value['total_paid'];
        $buying_table['quantity'] = $value['nb_products'];
        $buying_table['id'] = $value['id_order'];
        $buying_table['valid'] = $value['valid'];
        $buying_table['current_state'] = $value['order_state'];
        $buying_table['current_state_color'] = $value['order_state_color'];
        array_push($buying,$buying_table);
        $i++;
    }
    return $buying;
  }
  private function getTotalOrdersRelativePositions($res){
    $arr = array();
    $max = $res['lvl'.$this->max_lvl];
    $previous = 0;
    for($i = $this->LVL_MIN; $i <= $this->max_lvl;$i++){
      $value = $res['lvl'.$i];
      $arr['lvl'.$i] = 100*(($value / $max) - $previous);
      $previous = $value / $max;
    }
    return $arr;
  }
  private function getLevelsLimits(){
  $res = array();
  for($i = $this->max_lvl; $i >= $this->LVL_MIN;$i--){
      $lvl_money = Configuration::get('levels_module_lvl'.$i);
      $res['lvl'.$i] = $lvl_money;
      }
      return $res;
  }
  
  }