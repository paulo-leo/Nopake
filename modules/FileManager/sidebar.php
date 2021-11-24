<?php
hello(open_menu('Estoque|inventory','store','np-deep-orange'));
/*Menu para filiasi*/
$menu_sidebar1 = array(
   'inventory/products|Produtos|list|admin,dev,editor',
   'inventory/groups|Grupo de produtos|list|admin,dev,editor',
   'inventory/products/create|Adicionar|add|admin,dev,editor'
);

hello(dropdown_menu('inventory/products|Produtos|store|admin,dev,editor,author,collaborator',
['items'=>items_menu($menu_sidebar1)]));

$menu_sidebar2 = array(
   'inventory/requests/create|Adicionar|add|admin,dev,editor'
);

hello(dropdown_menu('inventory/requests|Requisições|help|admin,dev,editor,author,collaborator',
['items'=>items_menu($menu_sidebar2)]));


$menu_sidebar3 = array(
   'inventory/product/create|Adicionar ordem|add|admin,dev,editor'
);

hello(dropdown_menu('inventory/stock|Ordens|list|admin,dev,editor,author,collaborator',
['items'=>items_menu($menu_sidebar3)]));




hello(close_menu());
?>
