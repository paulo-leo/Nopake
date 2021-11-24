<?php
/*
*Classe responsável por fazer o upload de uma imagem já com seu tamnho compactado. 
*Author: Paulo Leonardo da Silva Cassimiro
*
*/

namespace Nopadi\FS;

class UploadImage
{
        private $file_name;
        private $file_height;
        private $file_width;
        private $file_folder;
        private $file_new_name;
        private $file_error_code;
 
        public function __construct($arr='userfile')
        {
            $file_name =   is_array($arr) ? $arr['name'] : $arr;
            $file_new_name = isset($arr['new_name']) ? $arr['new_name'] : false;
			$file_height = isset($arr['height']) ? $arr['height'] : 50;
			$file_width  = isset($arr['width']) ? $arr['width'] : 50;
            $file_folder = isset($arr['folder']) ? $arr['folder'] : "uploads/images/";

            
			$this->file_new_name   = $file_new_name;
            $this->file_name = isset($_FILES[$file_name]) ? $_FILES[$file_name] : null;
            $this->file_height  = $file_height;
            $this->file_width = $file_width;
            $this->file_folder   = $file_folder;

            if(empty($_FILES)) $this->file_error_code = 9;
        }

		//retorna a extensao da imagem	
         private function getExt()
         {
			$tmp = explode('.', $this->file_name['name']);
			$file_extension = strtolower(end($tmp));
			return $file_extension;
		} 
         
        private function ehImagem($extensao)
        {
            $extensoes = array('png','jpg','jpeg','gif');     // extensoes permitidas
            if (in_array($extensao, $extensoes))
                return true;    
        }
         
        //file_width, file_height, tipo, localizacao da imagem original
        private function redimensionar($imgLarg, $imgAlt, $tipo, $img_localizacao)
        {
            //descobrir novo tamanho sem perder a proporcao
            if ( $imgLarg > $imgAlt ){
                $novaLarg = $this->file_width;
                $novaAlt = round( ($novaLarg / $imgLarg) * $imgAlt );
            }
            elseif ( $imgAlt > $imgLarg ){
                $novaAlt = $this->file_height;
                $novaLarg = round( ($novaAlt / $imgAlt) * $imgLarg );
            }
            else{
				$novafile_height = $novafile_width = max($this->file_width, $this->file_height);
				$novaAlt = $this->file_height;
				$novaLarg = $this->file_width;
			} // file_height == file_width
                
             
            //redimencionar a imagem
             
            //cria uma nova imagem com o novo tamanho   
            $novaimagem = imagecreatetruecolor($novaLarg, $novaAlt);
             
			
            switch ($tipo){
                case 1: // gif
                    $origem = imagecreatefromgif($img_localizacao);
                    imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0,
                    $novaLarg, $novaAlt, $imgLarg, $imgAlt);
                    imagegif($novaimagem, $img_localizacao);
                    break;
                case 2: // jpg
                    $origem = imagecreatefromjpeg($img_localizacao);
                    imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0,
                    $novaLarg, $novaAlt, $imgLarg, $imgAlt);
                    imagejpeg($novaimagem, $img_localizacao);
                    break;
                case 3: // png
                    $origem = imagecreatefrompng($img_localizacao);
                    imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0,
                    $novaLarg, $novaAlt, $imgLarg, $imgAlt);
                    imagepng($novaimagem, $img_localizacao);
                    break;
            }
             
            //destroi as imagens criadas
			if($tipo < 4){
				imagedestroy($novaimagem);
                imagedestroy($origem);
			}
            
        }
         
        public function save(){  
            
        if($this->file_error_code != 9){

            $extensao = $this->getExt();

            /*Cria um diretório caso não exista*/
            if(!is_dir($this->file_folder)) mkdir($this->file_folder,0755,true);
             
            //gera um nome unico para a imagem em funcao do tempo
             if($this->file_new_name){
                 $new_name = md5($this->file_new_name).'.'.$extensao;
             }else{
                $new_name = md5(uniqid(rand(), true)).'.'.$extensao;
             }

            //localizacao do file_name 
            $destino = $this->file_folder . "IMG_".$new_name;
             
        
            if(!$this->ehImagem($extensao)){
                $destino = false;
                $this->file_error_code = 10;
            }

             //move o file_name
             if($destino){
             if (! move_uploaded_file($this->file_name['tmp_name'], $destino)){
              
                $this->file_error_code = $this->file_name['error'];
            
                $destino = false;
            }
              }
                 
            if ($this->ehImagem($extensao) && $destino){                                             
                //pega a file_width, file_height, tipo e atributo da imagem
				
                list($file_width, $file_height, $tipo, $atributo) = getimagesize($destino);
               
                // testa se é preciso redimensionar a imagem
                if(($file_width > $this->file_width) || ($file_height > $this->file_height))
                    $this->redimensionar($file_width, $file_height, $tipo, $destino);
            }
            return $destino;
        }else return false;
           
        } 
        public function getMessage($code=false){

        if(!$code){
            switch($this->file_error_code){
                case 0 : $msg = 'UPLOAD_IMAGE_OK'; break;
                //Valor: 0; não houve erro, o upload foi bem sucedido. 
                case 1 : $msg = 'UPLOAD_ERR_INI_SIZE'; break;
                //Valor 1; O arquivo enviado excede o limite definido na diretiva upload_max_filesizedo php.ini. 
                case 2 : $msg = 'UPLOAD_ERR_FORM_SIZE'; break;
                //Valor: 2; O arquivo excede o limite definido em MAX_FILE_SIZE no formulário HTML. 
                case 3 : $msg = 'UPLOAD_ERR_PARTIAL'; break;
                //Valor: 3; O upload do arquivo foi feito parcialmente. 
                case 4 : $msg = 'UPLOAD_ERR_NO_FILE'; break;
                //Valor: 4; Nenhum arquivo foi enviado. 
                case 6 : $msg = 'UPLOAD_ERR_NO_TMP_DIR'; break;
                //Valor: 6; Pasta temporária ausênte.
                case 7 : $msg = 'UPLOAD_ERR_CANT_WRITE'; break;
                //Valor: 7; Falha em escrever o arquivo em disco. 
                case 8 : $msg = 'UPLOAD_ERR_EXTENSION'; break;
                //Valor: 8; Uma extensão do PHP interrompeu o upload do arquivo.
                case 9 : $msg = 'UPLOAD_ERR_SELECT_FILE'; break;
                //Valor: 9; Nenhum arquivo foi selecionado pelo usuário.
                case 10 : $msg = 'UPLOAD_ERR_EXT'; break;
                //Valor: 10; Extensão do arquivo é invalida.
              } return $msg;
            }else return $this->file_error_code;
        }                      
    }
    