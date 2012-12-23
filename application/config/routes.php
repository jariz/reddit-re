<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    $route['404_override'] = '';

    if(php_sapi_name() != "cli")
    switch($_SERVER['HTTP_HOST']) {
        case "modlog.reddit.re":
            $route['default_controller'] = "page";
            $route["r/(:any)"] = "page/filter";
            $route["r"] = "page/filter";
            $route["u/(:any)"] = "page/filter";
            $route["u"] = "page/filter";
            $route["t/(:any)"] = "page/filter";
            $route["t"] = "page/filter";
            $route["b/(:any)"] = "page/filter";
            $route["b"] = "page/filter";
            $route["(:any)"] = "page/modlog/$1";
            break;
        case "bot.reddit.re":
            $route['default_controller'] = "page";
            $route["(:any)"] = "page/bot/$1";
        default:
            $route['default_controller'] = "page";
            $route["modbot/(:num)"] = "page/modbot/$1";
            $route["(:any)"] = "page/go/$1";
            break;
    } else {
        $route['default_controller'] = "page";
        $route["(:any)"] = "page/modlog/$1";
    }



    /* End of file routes.php */
    /* Location: ./application/config/routes.php */