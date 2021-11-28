<?php
hello(open_menu('Início|dashboard','home','np-deep-orange'));
/*Menu para filiasi*/
$menu_settings = array(
   'settings/environment|Gerais',
   'settings/theme|Tema',
   'settings/sectors|Setorização',
   'settings/smtp|SMTP|email',
   'settings/db|Banco de dados',
   'settings/key-api|Chave de API',
   'settings/modules|Módulos'
);


$menu_apps = get_menu_modules(true);

$menu_users = array(
   'users|Usuários',
   'settings/roles|Funções',
   'users-x|Permissões',
   'users/create|Adicionar usuário'
);

hello(dropdown_menu('users|Acesso|lock|admin,dev,editor,author,collaborator',
['items'=>items_menu($menu_users)]));

hello(dropdown_menu('apps|Aplicativos|widgets|admin,dev,editor,author,collaborator',
['items'=>items_menu($menu_apps)]));

hello(dropdown_menu('settings|Configurações|settings|admin,dev,editor,author,collaborator',
['items'=>items_menu($menu_settings )]));




hello(close_menu());
?>
