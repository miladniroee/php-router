<?php

class Route
{
    private static string $Url;
    private static string $Uri;

    public static function get($Url, $Params)
    {
        self::$Url = $Url;
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && self::CheckURL()) {
            self::InitController($Params);
        }
    }

    public static function post($Url, $Params)
    {
        self::$Url = $Url;

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

    private static function CheckURL():bool
    {
        $Uri = array_values(array_filter(explode('/', self::$Url)));
        $Url = array_filter(explode('/', $_GET['param']));
        foreach ($Uri as $Key => $Params):
            if (preg_match("/\{(.*?)\}/", $Params)):
                continue;
            else:
                if ($Params !== $Url[$Key]):
                    return false;
                endif;
            endif;
        endforeach;
        return true;
    }

}
