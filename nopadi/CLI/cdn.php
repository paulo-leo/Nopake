<?php

$file = @file_get_contents('config/app/cdn.json',true);
if($file){ $file = json_decode($file,true); } 



$path = 'public/cdn';



if(!is_dir($path))
{
     mkdir($path, 0777,true); 
}

if(!is_dir($path.'/css'))
{
     mkdir($path.'/css', 0777,true); 
}

if(!is_dir($path.'/js'))
{
     mkdir($path.'/js', 0777,true); 
}

if(!is_dir($path.'/img'))
{
     mkdir($path.'/img', 0777,true); 
}


foreach($file as $name=>$url)
{
	$ext = substr($url,-3,3);
	$ext2 = substr($url,-4,4);
	$local = null;
	
	$exe = true;

    if($ext == '.js')
	{
		$local = $path.'/js/'.$name.'.js';
	}
	
	elseif($ext == 'css')
	{
		$local = $path.'/css/'.$name.'.css';
	}
	
	elseif($ext2 == '.png')
	{
		$local = $path.'/img/'.$name.'.png';
	}
	
	elseif($ext2 == '.jpg')
	{
		$local = $path.'/img/'.$name.'.jpg';
	}
	
	elseif($ext2 == '.gif')
	{
		$local = $path.'/img/'.$name.'.gif';
	}else{
		
		$exe = false;
	}
	
	
	
	if($exe){
	echo "O seguinte CDN foi lido e copiado com sucesso para dentro do seu projeto.";
	echo "\n";
	echo $url;
	echo "\n";
	$content = file_get_contents($url,true);
	file_put_contents($local,$content);
	
	}else{
		
		echo "Dependências não localizada e copiada.";
	    echo "\n";
		
	}
	
	
}
echo "\n";
echo "Dependências do CDN instalados com sucesso.";

echo "\n";
echo $path;

?>