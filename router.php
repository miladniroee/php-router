<?php

class Route
{
    private static string $Url;
    private static array $Parameters = [];

    public static function get($Url, $Params)
    {
        // Add Url to class
        self::$Url = $Url;
        // Check request to be GET
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && self::CheckURL()) {
            self::InitController($Params);
        }
    }

    public static function post($Url, $Params)
    {
        // Add Url to class
        self::$Url = $Url;
        // Check request to be POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && self::CheckURL()) {
            self::InitController($Params);
        }
    }

    private static function InitController($Params)
    {
        $Params = explode('@', $Params);
        $Controller = $Params[0];
        $Method = $Params[1];

        require_once __DIR__ . "/controller/{$Controller}.php";
        $ClassInit = new $Controller();
        $ClassInit->$Method();
        if (property_exists($ClassInit, 'Scripts')):
            $_SESSION['Scripts'] = $ClassInit->Scripts;
        else:
            $_SESSION['Scripts'] = [];
        endif;
    }

    private static function CheckURL(): bool
    {
        // uri that programmer define
        $Uri = array_values(array_filter(explode('/', self::$Url)));

        //client url
        $Url = array_filter(explode('/', $_GET['param']));

        /**
         * Url and Uri are not at same size.
         * so, not this one :))
         * @return false
         */
        if (count($Uri) !== count($Url)):
            return false;
        endif;

        /**
         * check parameter by every section
         * if there is variable in uri, it will add to $Parameter array
         */
        foreach ($Uri as $Key => $Params):
            
            //if url has {*} accept whatever in it
            if (preg_match("/\{(.*?)\}/", $Params)):
                $Param = str_replace(['{', '}'], '', $Params);
                self::$Parameters[$Param] = $Url[$Key];
                continue;
            else:
                if ($Params !== $Url[$Key]):
                    return false;
                endif;
            endif;
        endforeach;

        // client url truly found
        return true;
    }

}
