{* <div class="levels">

</div> *}
<div class="levels_whole">
<div class="levels_top">Верх бака</div>
<div class="levels_mid">
<div class="levels_container">
<div class="levels_wrapper">
<div class="levels_fill" style="height:{100*($levels_module_total_orders / $levels_module_max_value)}%">
<div>
<p>До следующего уровня осталось: {$levels_module_until_next} Р</p>
<img src="{$arrow}"></img>
</div>
</div>
{foreach from=$levels_module_all_levels item=item key=key name=name}
<div class="lvl" style="height:{$levels_module_percent[$key]}%">
<div class="lvl_key">{$key}</div>
<img class="lvl_pointer" src="{$arrow}">
<div class="lvl_amount">{$item}</div>
</div>
{/foreach}
</div>
</div>
</div>
<div class="levels_bot">Низ бака</div>

</div>
Текущий ЛВЛ {$levels_module_current_lvl}<br>
Текущая скидка {$levels_module_discount}%<br>
Всего заплачено {$levels_module_total_orders}