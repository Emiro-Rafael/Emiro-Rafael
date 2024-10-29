<?php

class Session
{
    private static $instance = null;
    private static $SESSION_LENGTH = 3600;
    private static $session_table = 'candybar_cart_session';

    public function __construct()
    {
        if(!session_id() && !headers_sent())
        {
            session_start();
        }

        if(empty($_SESSION['csrf_token']))
        {
            $this->set( 'csrf_token', bin2hex(random_bytes(32)) );
        }

        $this->dbh = SCModel::getSnackCrateDB();

        global $user_data;
        $this->user_data = $user_data;
    }

    private function _removeExpiredIdempotency()
    {
        if( !empty($_SESSION['idempotency_tokens']) )
        {
            foreach($_SESSION['idempotency_tokens'] as $key => $token)
            {
                if( time() > $token->expires)
                {
                    unset($_SESSION['idempotency_tokens'][$key]);
                }
            }
        }
    }

    public function checkCartTokenExists()
    {
        if( empty( $_SESSION['cart_token'] ) )
        {
            $this->set( 'cart_token', bin2hex(random_bytes(32)) );
            $this->addSession();
            return false;
        }
        return true;
    }

    public function getCartToken()
    {
        $this->checkCartTokenExists();
        return $_SESSION['cart_token'];
    }

    public function set( $key, $value )
    {
        $_SESSION[$key] = $value;
    }

    public function addSession()
    {
        $items = serialize( $_SESSION['cart'] );
        $expires = date("Y-m-d H:i:s", strtotime("+1 hours"));
        
        $this->set( 'cart_expires', $expires );

        $user_id = empty($this->user_data) ? NULL : $this->user_data->id;
        try
        {
            $stmt = $this->dbh->prepare("INSERT INTO " . self::$session_table . " (id, user_id, token, items, expires) VALUES (NULL, :user_id, :token, :items, :expires)");
            $stmt->bindParam(":token", $_SESSION['cart_token']);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":items", $items);
            $stmt->bindParam(":expires", $expires);
            $stmt->execute();
        }
        catch(Exception $e)
        {
            // We tried to insert a token twice, just ignore
        }
        $stmt = null;
    }

    public function destroySession()
    {
        if( empty($_SESSION['cart_token']) )
        {
            unset( $_SESSION['cart'], $_SESSION['cart_expires'] );
            return false;
        }

        $stmt = $this->dbh->prepare("DELETE FROM " . self::$session_table . " WHERE token = :token");
        $stmt->bindParam(":token", $_SESSION['cart_token']);
        $stmt->execute();
        $stmt = null;

        unset( $_SESSION['cart'], $_SESSION['cart_token'], $_SESSION['cart_expires'] );
    }

    public function updateSession()
    {
        $items = serialize( $_SESSION['cart'] );
        $expires = date("Y-m-d H:i:s", strtotime("+1 hours"));

        $this->set( 'cart_expires', $expires );

        $user_id = empty($this->user_data) ? NULL : $this->user_data->id;
        $stmt = $this->dbh->prepare("UPDATE " . self::$session_table . " SET user_id = :user_id, items = :items, expires = :expires WHERE token = :token");
        $stmt->bindParam(":items", $items);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":expires", $expires);
        $stmt->bindParam(":token", $_SESSION['cart_token']);
        $stmt->execute();
        
        if( $stmt->rowCount() == 0 )
        {
            $this->addSession();
        }
        $stmt = null;
    }

    public function checkCartExpiry()
    {
        $time = date('Y-m-d H:i:s');
        if( !empty($_SESSION['cart_expires']) && $time > $_SESSION['cart_expires'] )
        {
            $this->destroySession();
        }
        else
        {
            $_SESSION['cart_expires'] = date("Y-m-d H:i:s", strtotime("+1 hours"));
        }
        $this->_removeExpiredIdempotency();
    }

    public function handleCartSessionUpdate()
    {
        if( $this->checkCartTokenExists() )
        {
            $this->updateSession();
        }
    }

    public function fetchUserSession($user_id)
    {
        $stmt = $this->dbh->prepare("SELECT items FROM " . self::$session_table . " WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        $serialized_items = $stmt->fetch(PDO::FETCH_COLUMN);
        $stmt = null;
        return $serialized_items;
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Session();
        }

        return self::$instance;
    }
}