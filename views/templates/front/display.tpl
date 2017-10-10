{* <div class="levels">

</div> *}
<div class="levels_whole">
<div class="levels_top">Верх бака</div>
<div class="levels_mid">
{foreach from=$levels_module_all_levels item=item key=key name=name}
<div class="lvl">
<div class="lvl_key">{$key}</div>
<div class="lvl_amount">{$item}</div>
</div>
{/foreach}
</div>
<div class="levels_bot">Низ бака</div>

</div>
Текущий ЛВЛ {$levels_module_current_lvl}<br>
Текущая скидка {$levels_module_discount}%