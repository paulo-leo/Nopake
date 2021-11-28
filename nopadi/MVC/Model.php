<?php
/*
*Classe responsável por criar um modelo a partir do nome da propria classe
*/

namespace Nopadi\MVC;

use Nopadi\Base\DB;

class Model extends DB
{
	protected $table = null;
	protected $prefix = null;
	protected $connect = null;
	protected $primary = 'id';
	private $data;
	/*page os valores das propriedas publicas não existentes*/
	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}

	/*salva ou atualiza no banco de dados um registro*/
	/*OBS: a atualização acontece quando é informado um id no parametro ou no objeto da classe*/
	public function save($id = null)
	{
		if (array_key_exists($this->primary, $this->data) || !is_null($id)) {

			$id = !is_null($id) ? $id : $this->primary;
			$values = $this->data;
			unset($values[$this->primary]);
			return $this->update($values, $id);
		} else {
			$values = $this->data;
			return $this->insert($values);
		}
	}

	public function __construct()
	{

		if (!is_null($this->prefix) && $this->prefix != false) {
			if (substr($this->prefix, -1) != "_") {
				$this->prefix .= "_";
			}
		}

		if (is_null($this->table)) {
			$this->table = strtolower(get_class($this));
			if (substr($this->table, -5) == "model") {
				$this->table = str_ireplace("model", "", $this->table);
			}
			if (substr($this->table, -1) != "s") {
				$this->table .= "s";
			}
		}

		$this->table = $this->gerateTable($this->table);

		$this->tableName($this->table);
		$this->connectName($this->connect);
		$this->primaryName($this->primary);
	}
	//Gera o nome da tabela com prefixo
	private function gerateTable($table)
	{
		$table = explode('\\', $table);
		$count = count($table);
		$table = $table[$count - 1];


		$table = $this->prefix . $table;

		return strtolower($table);
	}
}
