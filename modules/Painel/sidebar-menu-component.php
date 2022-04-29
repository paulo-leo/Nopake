<?php
/*Menu para arquivos*/
$menu_sidebar_files = array(
   'folder/create|Criar pasta|create_new_folder|admin,dev,editor',
   'images/create|Enviar arquivo|file_upload|admin,dev,editor'
);

$menu_sidebar_system = array(
'users.create|:user.create|person_add',
'settings|:settings|settings'
);


//Menu para arquivos
hello(dropdown_menu('folder|Arquivos|folder|admin,dev,editor,author,collaborator',
['items'=>items_menu($menu_sidebar_files)]));

//Menu de usuários do sistema
hello(dropdown_menu('users|:users|person|admin',
['items'=>items_menu(['users.create|:user.create|person_add|admin'])]));

//Acesso somente para Ads e Devs

//Menu de configurações do sistema
$menu_sidebar_settings = array(
'settings/nwc|NWC|code'
);

hello(dropdown_menu('settings|:settings|settings|admin,dev',
['items'=>items_menu($menu_sidebar_settings)]));

?>
