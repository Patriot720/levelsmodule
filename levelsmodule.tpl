<!-- Block mymodule -->
{* <div id="mymodule_block_home" class="block">
  <h4>Welcome!</h4>
  <div class="block_content">
    <p>Hello,
       {if isset($my_module_name) && $my_module_name}
           {$my_module_name}
       {else}
           World
       {/if}
       !       
    </p>   
    <ul>
      <li><a href="{$my_module_link}" title="Click this link">Click me!</a></li>
    </ul>
  </div>
</div> *}
<div class="col-sm-4 clearfix">
<div class="shopping_cart levels_module">
{if isset($levels_module_total_orders) && $levels_module_total_orders !== null}
  <a href="{$levels_module_module_url}">Текущая скидка {$levels_module_discount}%</a>
{elseif isset($levels_module_is_buyer) && $levels_module_is_buyer}
  <a href="{$levels_module_module_url}">Купи ченить и стань богом</a>
{elseif isset($levels_module_is_guest) && $levels_module_is_guest}
  <a href="{$levels_module_login_url}">Зарегестируйся чтобы получить</a>
  {/if}
</div>
</div>
<!-- /Block mymodule -->