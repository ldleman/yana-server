<?php
require_once(dirname(__DIR__).DIRECTORY_SEPARATOR.'constant.php');
/**
 * PDO Connector for database connexion.
 *
 * @author v.carruesco
 *
 * @category Core
 *
 * @license copyright
 */
class Database
{
    public $connection = null;
    public static $instance = null;
    private function __construct()
    {
        $this->connect();
    }

    /**
     * Methode de recuperation unique de l'instance.
     *
     * @author Valentin CARRUESCO
     *
     * @category Singleton
     *
     * @param <Aucun>
     *
     * @return <pdo> $instance
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance->connection;
    }

    public function connect()
    {
        try {
         $base = BASE_SGBD;
         require_once(__ROOT__.'connector/'.$base.'.class.php');
         $connectionString = str_replace(
             array('{{ROOT}}','{{BASE_HOST}}','{{BASE_NAME}}','{{BASE_LOGIN}}','{{BASE_PASSWORD}}'),
             array(__ROOT__,BASE_HOST,BASE_NAME,BASE_LOGIN,BASE_PASSWORD),
             $base::connection);
         $this->connection = new PDO($connectionString, BASE_LOGIN, BASE_PASSWORD);
         $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     } catch (Exception $e) {
        echo 'Connection à la base impossible : ', $e->getMessage();
        die();
    }
}
}
