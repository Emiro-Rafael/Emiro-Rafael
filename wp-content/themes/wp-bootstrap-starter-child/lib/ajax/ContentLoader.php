<?php
class ContentLoader
{
    private static $instance = null;
    private $partial_path;

    public function setPartialPath($path)
    {
        $this->partial_path = $path;
    }

    public function getContent()
    {
        ob_start();

        include( $this->partial_path );
        
        $content = ob_get_contents();

        ob_end_clean();
        
        return $content;
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new ContentLoader();
        }

        return self::$instance;
    }
}

ContentLoader::getInstance();