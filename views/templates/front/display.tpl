{*
<div class="levels">

</div> *}
<div class="levels">
<div class="levels_whole animated fadeInRight">
    <div class="levels_top"></div>
    <div class="levels_mid">
        <div class="levels_container">
            <div class="levels_wrapper">
                <div class="levels_fill_wrap">
                    <div class="levels_fill  animated slideInUp" style="height:{100*($levels_module_total_orders / $levels_module_max_value)}%">
                    </div>
                </div>
                <div class="levels_hidden" style="height:{100*($levels_module_total_orders / $levels_module_max_value)}%">
                    <div class="animated fadeInLeft">
                        <p>До следующего уровня осталось: {$levels_module_until_next} Р</p>
                        <img src="{$arrow}"></img>
                    </div>
                </div>
                <div class="all_levels">
                {foreach from=$levels_module_all_levels item=item key=key name=name}
                <div class="lvl animated fadeInDownBig" style="animation-delay:{substr($key,3)/5}s;height:{$levels_module_percent[$key]}%">
                    <div class="lvl_key">{$key}</div>
                    <img class="lvl_pointer" src="{$arrow}">
                    <div class="lvl_amount">{$item}</div>
                </div>
                {/foreach}
                </div>
            </div>
        </div>
    </div>
    <div class="levels_bot"></div>

</div>
<div class="levels_menu animated fadeInLeft">
<div class="levels_menu_item">
<p>Текущий ЛВЛ</p> <div>{$levels_module_current_lvl}</div>
</div>
<div class="levels_menu_item">
 <p>Текущая скидка</p> <div>{$levels_module_discount}%</div>
 </div>
 <div class="levels_menu_item">
 <p>Всего заплачено</p> <div>{$levels_module_total_orders}</div>
 </div>
 </div>
<table class="table-bordered footab default footable-loaded footable animated fadeInDown">
    <thead>
        <tr>
            <th class="first_item footable-first-column" data-sort-ignore="true">ID</th>
            <th class="item footable-sortable">Всего оплачено
                <span class="footable-sort-indicator"></span>
            </th>
            <th class="item footable-sortable">Количество товаров
                <span class="footable-sort-indicator"></span>
            </th>
            <th class="item footable-sortable">Статус заказа
                <span class="footable-sort-indicator"></span>
            </th>
            <th class="item footable-sortable">Проверен ли платеж
                <span class="footable-sort-indicator"></span>
            </th>
        </tr>
    </thead>
    {foreach from=$order_history item=item key=key name=name}
    <tr>
        <td>{$item['id']}</td>
        <td>{$item['price']}</td>
        <td>{$item['quantity']}</td>
        <td>
            <span class="label levels_label" style="background-color:{$item['current_state_color']}; border-color:{$item['current_state_color']};">{$item['current_state']}</span>
        </td>
        <td>{($item['valid'] == 1)?"Да":"Нет"}</td>
    </tr>
    {/foreach}
</table>
<div class="levels_buttons animated fadeInDown">
    <a href="{$link->getPageLink('addresses')}" title="Addresses">
        <span>Мои адреса</span>
    </a>
        <a href="{$link->getPageLink('discount')}" title="Addresses">
        <span>Купоны</span>
    </a>
        <a href="{$link->getPageLink('history')}" title="Addresses">
        <span>Полная история</span>
    </a>
        <a href="{$link->getPageLink('identity')}" title="Addresses">
        <span>Личная информация</span>
    </a>
</div>
</div>