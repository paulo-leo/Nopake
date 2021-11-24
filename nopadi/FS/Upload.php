<?php 
/*
*Classe responsável por fazer o upload de arquivos. 
*Author: Paulo Leonardo da Silva Cassimiro
*
*/

namespace Nopadi\FS;

  class Upload
    {
      private $uploadDir = 'uploads/';//pasta para onde os arquivos serão enviados
      private $allowedExtensions = array();//extensões permitidas para upload
      private $maxSize='2';//2mb  tamanho máximo do arquivo que pode ser enviado
      private $randomName=false;//define a opção se o arquivo irá receber um nome aleatório ou continuar com o nome original
      
      //modifica a variavel $uploadDir informando um novo local(pasta,diretório) para onde o arquivo será enviado
      public function setUploadDir($dir)
      {
        $this->uploadDir = $dir;
      }
      /**
       * Define quais extensões ou tipo de arquivos serão permitidos enviar
       * exemplo do parâmetro que você pode passar ao chamar esta função:array('png','jpg','gif');
       */
      public function setAllowedExtensions($ext)
      {
        if(is_array($ext))
        {
         $this->allowedExtensions = $ext;
        }
        else
        {
         $this->allowedExtensions = array($ext);
        }
      }
      /**
       * define o tamanho máximo que o arquivo pode ter para ser enviado em MB
       */
      public function setMaxSize($size)
      {
        $this->size = $size;
      }
      /**
       * obtém o tamanho do arquivo enviado em mb
       */
      private function getFileSize($bytes)
      {
        $size = $bytes/1024/1024;
        return $size;
      }
      /**
       * define se o arquivo enviado receberá um nome aleatório ou continuará com o nome original
       * o parâmetro para informar nesta função é true ou false
       */
      public function setRandomName(bool $val)
      {
        $this->randomName = $val;
      }
      /**
       * função que faz o upload do arquivo
       * a parâmetro $postFile é o arquivo enviado no formulário
       * ex: uploadFile($_FILES['file']);
      */
      public function uploadFile($postFile)
      {
        //verifica se existe algum erro ao enviar o arquivo. 
        //Se existir, exibe o erro e encerra a função
        if($postFile['error'])
        {
          echo $this->getErrorMessage($postFile['error']);
          return false;
        }
        //verifica se o tamanho do arquivo é maior que o permitido. 
        //Se for maior exibe uma mensagem com essa informação e encerra a função
        if($this->getFileSize($postFile['size'])>$this->maxSize)
        {
          echo 'O tamanho do arquivo enviado ('.number_format($this->getFileSize($postFile['size']),2).'mb) é maior do que o permitido(até '.$this->maxSize.'mb)';
          return false;
        }
        //verifica se o tipo de arquivo enviado está na lista de arquivos permitidos
        //se for um tipo de arquivo não permitido, mostra uma mensagem informando que é um arquivo não permitido
        //e exibe a lista de arquivos permitidos, depois encerra a função
        $fileExt = $this->getFileExt($postFile['name']);//obtém o tipo de arquivo
        if(!in_array($fileExt, $this->allowedExtensions))
        {
          echo 'Tipo de arquivo inválido => '.$fileExt;
          echo '<br>São aceitos apenas aquivos do tipo => '. implode(', ', $this->allowedExtensions);
          return false;
        }
        //verifica se a pasta que o arquivo será enviado existe.
        //Se não existir encerra a função e informa que a pasta não existe
          if(!$this->hasUploadDir($this->uploadDir))
          {
            echo 'Diretório de upload não existe';
            return false;
          }
          //a linha abaixo atribui um nome ao arquivo
          //se for definido para gerar um nome aleatório ele gera um nome aleatório, senão, ele mantém o nome original do arquivo
          $fileName = $this->randomName ? date('dmyhis').rand().'.'.$fileExt : $postFile['name'];
          
          //tenta mover o arquivo enviado através do formulário html para a pasta de upload informada
          if(move_uploaded_file($postFile['tmp_name'], $this->uploadDir.'/'.$fileName))
          {
            return $fileName;
          }
          else
          {
           return false;
          }
        
      }
      
      //obtém a mensagem de erro do upload através do código de erro informado
       private function getErrorMessage($code) 
        { 
            switch ($code) { 
                case UPLOAD_ERR_INI_SIZE: 
                  $message = 'O arquivo enviado excede o tamanho máximo de envio da diretiva upload_max_filesize do php.ini';
                    break; 
                case UPLOAD_ERR_FORM_SIZE: 
                  $message = 'O arquivo enviado excede a directive MAX_FILE_SIZE que foi especificada o formulário HTML';
                    break; 
                case UPLOAD_ERR_PARTIAL: 
                  $message = 'O arquivo enviado foi enviado parcialmente';
                    break; 
                case UPLOAD_ERR_NO_FILE: 
                    $message = 'Nenhum arquivo foi enviado'; 
                    break; 
                case UPLOAD_ERR_NO_TMP_DIR: 
                    $message = 'Não é possível encontrar a pasta temporária'; 
                    break; 
                case UPLOAD_ERR_CANT_WRITE: 
                    $message = 'Falha ao escrever o arquivo no disco'; 
                    break; 
                case UPLOAD_ERR_EXTENSION: 
                  $message = 'Uma extensão do PHP interompeu o envio do arquivo';
                    break; 
                default: 
                    $message = 'Erro de envio desconhecido'; 
                    break; 
            } 
            return $message; 
        }
        //obtém o tipo de arquivo
        private function getFileExt($fileName)
        {
          preg_match("/\.[a-zA-Z]{2,4}$/", $fileName,$matches);
          $fileExt = strtolower(str_replace('.', '',$matches[0]));
          return $fileExt;
        }
        /** 
         * verifica se a pasta de upload existe
         * se existir retorna true(ou seja, verdadeiro) senão retorna false(falso)
         */
        private function hasUploadDir($dir)
        {
          if(file_exists($dir))
          {
            return true;
          }
          return false;
        }
    } 
