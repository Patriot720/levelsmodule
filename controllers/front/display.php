<?php
class levelsmoduledisplayModuleFrontController extends ModuleFrontController
{
  private $LVL_MIN = 3;
  public function initContent()
  {
    $this->max_lvl = Configuration::get("levels_module_count");
    $this->display_column_left = FALSE;
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
        'order_history' => $this->getOrdersHistory()
      )
      );
    $this->setTemplate('display.tpl');
  }
  private function getOrdersHistory(){
    $customer = $this->context->customer;
    $buying_history = $customer->getBoughtProducts();
    $buying = array();
    foreach ($buying_history as $value) {
        $buying_table = array();
        $buying_table['name'] = $value['product_name'];
        $buying_table['price'] = $value['total_price_tax_incl'];
        $buying_table['quantity'] = $value['product_quantity'];
        $buying_table['id'] = $value['product_id'];
        $buying_table['valid'] = $value['valid'];
        $l = new Order($value['id_order'],1);
        $buying_table['current_state'] = $l->getCurrentStateFull(1)['name'];
        // $buying_table['current_state'] = $value->getCurrentStateFull();
        array_push($buying,$buying_table);
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