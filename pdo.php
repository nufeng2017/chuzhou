<?php
class DB{
    private static $pdo;
    public static function getPdo ()
    {
//        var_dump(self::$pdo);
//        echo '';
        if ( self::$pdo == null )
        {
            $host = '127.0.0.1';
            $user = 'root';
            $pwd = 'root';
            $dbname = 'house';

            $dsn = "mysql:host=$host;dbname=$dbname;port=3306";
            $pdo = new PDO ( $dsn, $user, $pwd );
            $pdo->query('set names utf8;');
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo = $pdo;
        }
        return self::$pdo;
    }

    public static function getStmt ( $sql )
    {
        $pdo = self::getPdo ();
        return $pdo -> prepare( $sql );
    }

}
