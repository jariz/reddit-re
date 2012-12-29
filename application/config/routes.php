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
        case "snapshot.reddit.re":
            $route["default_controller"] = "snapshot";
            $route["small/(:any)"] = "snapshot/small";
            $route["(:any)"] = "snapshot/full/$1";
            break;
        case "template.reddit.re":
            $route["default_controller"] = "template";
            $route["api/(:any)"] = "template/api";
            $route["api/(:any)/(:any)"] = "template/api";
            $route["api/(:any)/(:any)/(:any)"] = "template/api";
            $route["snapshot/(:any)"] = "template/snapshot/$1";
            $route["(:any)"] = "template/go/$1";
            break;
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