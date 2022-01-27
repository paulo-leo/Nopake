<?php
/*
*Classe para criar recursos padronizados para as rotas.
*Autor: Paulo Leonardo da Silva Cassimiro
*/
namespace Nopadi\MVC;

class Controller
{
	/*Método será executado automaticamente caso um recurso sejá invocado e não tenha sido sobreecrito. Esse método também pode ser sobreecrito*/
    protected function objectWriteNot($m)
    {
        $class = get_class($this);
        hello(style());
        hello(':controller.error', 'danger');
        hello($m, 'success');
        hello($class, 'info');
    }
	
    /* Recurso para exibir a página principal */

    public function index()
    {
        $this->objectWriteNot("index");
    }

    /* Recurso para exibir o formulário de tarefas create */

    public function create()
    {
        $this->objectWriteNot("create");
    }

    /* Recurso para mostrar um única tarefa */

    public function show()
    {
        $this->objectWriteNot("show");
    }

    /* Recurso para editar uma única tarefa */

    public function edit()
    {
        $this->objectWriteNot("edit");
    }

    /* Recurso para criação via metodo post */

    public function store()
    {
        $this->objectWriteNot("store");
    }

    /* Recurso para deletar */

    public function destroy()
    {
        $this->objectWriteNot("destroy");
    }
	
	/* Recurso para pesquisar */

    public function search()
    {
        $this->objectWriteNot("search");
    }

    /* Recurso para atualizar */

    public function update()
    {
        $this->objectWriteNot("update");
    }

    /* Recurso para atualizar upload de arquivos */

    public function upload()
    {
        $this->objectWriteNot("upload");
    }

    /* Recurso para ajuda */

    public function help()
    {
        $this->objectWriteNot("help");
    }
	
	/* Recurso para atualizar pelo id */

    public function up()
    {
        $this->objectWriteNot("up");
    }
	
	/* Recurso para deletar pelo id */
    public function del()
    {
        $this->objectWriteNot("del");
    }
	
	/* Recurso para pagar pelo id */

    public function down()
    {
        $this->objectWriteNot("down");
    }

    /* Recurso para pegar o id do recurso atual via parametro*/
    public function id()
    {
        $resource = $_SERVER['REQUEST_URI'];

        $resource = explode('/', $resource);
        $total = count($resource) - 1;
        $last = $resource[$total];

        if ($last == 'edit') {
            $last = isset($resource[$total - 1]) ? $resource[$total - 1] : $last;
        }

        return trim(htmlspecialchars($last, ENT_QUOTES));
    }
	public static function sid(){
		$x = new Controller;
		return $x->id();
	}
	
	public function total()
    {
        $this->objectWriteNot("total");
    }
	
	public function home()
    {
        $this->objectWriteNot("home");
    }
	
	public function filter()
    {
        $this->objectWriteNot("filter");
    }
	public function record(){}
	public function records(){}
}
