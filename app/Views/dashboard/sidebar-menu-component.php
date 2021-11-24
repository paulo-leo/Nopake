<?php
/*Menu para arquivos*/
$menu_sidebar_files = array(
   'folder/create|Criar pasta|create_new_folder|admin,dev,editor',
   'images/create|Enviar arquivo|file_upload|admin,dev,editor'
);

$menu_sidebar_posts = array(
   'posts?type=page|:pages|import_contacts|admin,dev,editor',
   'posts?type=doc|:docs|library_books|admin,dev,editor,author,collaborator',
   'posts?type=trash|:trash|delete_sweep|admin,dev,editor'
);

$menu_sidebar_posts_create = array(
    'posts.create?type=page|:add.page|add|admin,dev,editor',
    'posts.create?type=doc|:add.doc|add|admin,dev,editor,author,collaborator'
    );

$menu_sidebar_system = array(
'users.create|:user.create|person_add',
'settings|:settings|settings'
);


//Menu para arquivos
hello(dropdown_menu('folder|Arquivos|folder|admin,dev,editor,author,collaborator',
['items'=>items_menu($menu_sidebar_files)]));

//Menu para listar posts
hello(dropdown_menu('posts|:posts|edit|admin,dev,editor,author,collaborator',
['items'=>items_menu($menu_sidebar_posts)]));

//Menu para criar posts
hello(dropdown_menu('posts.create|:post.create|note_add|admin,dev,editor,author,collaborator',
['items'=>items_menu($menu_sidebar_posts_create)]));

//Menu de categorias
hello(dropdown_menu('cats|:cats|list|admin,dev,editor,author,collaborator'));
hello(dropdown_menu('cats.create|:cat.create|playlist_add|admin,dev,editor'));

//Menu de usuários do sistema
hello(dropdown_menu('users|:users|person|admin',
['items'=>items_menu(['users.create|:user.create|person_add|admin'])]));

//Acesso somente para Ads e Devs

//Menu de configurações do sistema
$menu_sidebar_yeba = array(
'yeba/cmd|Mensagens|message',
'yeba/cmd|Pagamentos|attach_money'

);
hello(dropdown_menu('yeba|YebaTour|airplanemode_active|admin,dev',
['items'=>items_menu($menu_sidebar_yeba)]));


//Menu de configurações do sistema
$menu_sidebar_settings = array(
'settings/nwc|NWC|code'
);

hello(dropdown_menu('settings|:settings|settings|admin,dev',
['items'=>items_menu($menu_sidebar_settings)]));

?>
