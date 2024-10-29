<?php
class SCModel
{
    protected static $servername;
    protected static $username;
    protected static $password;
    protected static $dbname;

    /**
     * Connects to the database and returns the connection
     *
     * @return mixed
     */
    public static function getSnackCrateDB()
    {
        self::$servername = $_ENV['cr_db_host'];
        self::$username = $_ENV['cr_db_username'];
        self::$password = $_ENV['cr_db_password'];
        self::$dbname = $_ENV['cr_db_dbname'];

        static $dbh = null;

        if ($dbh === null) {
            try {
                $dbh = new PDO('mysql:dbname=' . self::$dbname . ';host=' . self::$servername . ';charset=utf8', self::$username, self::$password);

                //Throw exception when error occurs
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        return $dbh;
    }

    public static function getDirectus()
    {
        self::$servername = $_ENV['cr_db_host'];
        self::$username = $_ENV['cr_db_username'];
        self::$password = $_ENV['cr_db_password'];
        self::$dbname = 'snackcratedb';

        static $dbh = null;

        if ($dbh === null) {
            try {
                $dbh = new PDO('mysql:dbname=' . self::$dbname . ';host=' . self::$servername . ';charset=utf8', self::$username, self::$password);

                //Throw exception when error occurs
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        return $dbh;
    }

    /**
     * Connects to the database and returns the custom connection
     *
     * @return mixed
     */
    public static function getDBC($database_name = 'db_fi_name', $uname = 'db_fi_user', $pass = 'db_fi_password', $server = 'db_fi_name')
    {
        self::$servername = $_ENV[$server];
        self::$username = $_ENV[$uname];
        self::$password = $_ENV[$pass];
        self::$dbname = $_ENV[$database_name];

        static $dbh = null;

        if ($dbh === null) {

            try {
                $dbh = new PDO('mysql:dbname=' . self::$dbname . ';host=' . self::$servername . ';charset=utf8', self::$username, self::$password);

                //Throw exception when error occurs
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        return $dbh;
    }

    public static function getDomain()
    {
        $host = $_SERVER['HTTP_HOST'];
        preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
        return $matches[0];
    }
}