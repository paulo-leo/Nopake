<?php

namespace Nopadi\Base;

use Nopadi\Http\URI;
use PDO;
//Autor: Paulo Leonardo da Silva Cassimiro
//Classe para criar um objeto único de instancia PDO
class Connection
{

    private static $Host;
    private static $DBname;
    private static $User;
    private static $Password;
    private static $Driver;
    //@var PDO
    private static $Connect = null;

    private static function Connect($conn = null)
    {

        self::jsonConfig();

        if (!is_null($conn) && is_array($conn)) {

            if (isset($conn['host'])) {
                self::$Host = $conn['host'];
            }
            if (isset($conn['base'])) {
                self::$DBname = $conn['base'];
            }
            if (isset($conn['user'])) {
                self::$User = $conn['user'];
            }
            if (isset($conn['pass'])) {
                self::$Password = $conn['pass'];
            }
            if (isset($conn['sgbd'])) {
                self::$Driver = $conn['sgbd'];
            }
        }

        $DBname = self::$DBname;

        try {
            if (self::$Connect == null) :
                switch (strtolower(self::$Driver)) {
                    case "mysql":
                        $dsn = 'mysql:host=' . self::$Host . ';dbname=' . $DBname;
                        break;
                    case "sqlite":
                        $dsn = 'sqlite:' . self::$Host . ':' . $DBname;
                        break;
                }
                $options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'];
                self::$Connect = new PDO($dsn, self::$User, self::$Password, $options);
            endif;
        } catch (PDOExeption $e) {
            PHPErro($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            die;
        }
        self::$Connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return self::$Connect;
    }

    public static function getConn($dbName = null)
    {
        return self::Connect($dbName);
    }
    /*Metodo para acessar o arquivo de configuração*/
    private static function jsonConfig()
    {
        self::$Host = NP_DB_HOST;
        self::$DBname = NP_DB_NAME;
        self::$User = NP_DB_USER;
        self::$Password = NP_DB_PASS;
        self::$Driver = NP_DB_SGBD;
    }
}
