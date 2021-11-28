<?php

namespace Nopadi\Http;

use Nopadi\Http\URI;

class Middleware
{
  private $data;

  public function execute()
  {
    $this->handle($x);
  }

  protected function handle($x)
  {
    /*Aqui ficará toda a logica do Middleware*/
  }
  protected function automatic()
  {
    /*Aqui ficará toda a logica do Middleware*/
  }

  protected function redirect($to = null)
  {

    $base = new URI();
    $base = $base->base();
    $to = ($to == '/') ? null : $to;
    $base = $base . $to;
    header('HTTP/1.1 302 Redirect');
    header('Location:' . $base);
  }
  /*Middleware que será executado para todas as rotas do sistema*/
  public function set($key, $params = array())
  {
    $this->data[$key] = $params;
  }

  public function all()
  {

    foreach ($this->data as $key => $params) {

      $count = explode('\\', $key);
      $namespace = (count($count) == 1) ? 'App\Middlewares\\' . $key : $key;

      call_user_func_array(array(new $namespace, 'handle'), array_values($params));
    }
  }
}
