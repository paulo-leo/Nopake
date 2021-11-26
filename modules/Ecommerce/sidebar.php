<?php
hello(open_menu('Russel|russel','store','np-deep-orange'));
/*Menu para filiasi*/
$menu_sidebar1 = array(
   'fin/dre|DRE|list',
);

hello(dropdown_menu('fin/dre|DRE|list',
['items'=>items_menu($menu_sidebar1)]));

hello(close_menu());
?>
