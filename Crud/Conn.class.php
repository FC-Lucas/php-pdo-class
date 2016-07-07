<?php

class Conn {
    private static $Host = HOST1;
    private static $User = USER1;
    private static $Pass = PASS1;
    private static $Dbsa = DBSA1;
    private static $Connect = Null;


    private static function Conectar() {
        try{
            if(self::$Connect == null){
                $destino = 'mysql:host=' . self::$Host . ';dbname=' . self::$Dbsa;
                $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8');
                self::$Connect = new PDO($destino, self::$User, self::$Pass, $options);
            }
        }catch (PDOException $e) {
            PHPErro($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
        }

        self::$Connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return self::$Connect;
    }

    public static function getConn() {
        return self::Conectar();
    }
 }