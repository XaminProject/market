<?php

/**
 * Module description
 * 
 * PHP versions 5.2
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 */


/**
 * Module class description
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Cli_EmberCodeModel extends MarketCliBaseModel
{
    /**
     * Retrieves the genRoutes attribute.
     *
     * @param string $context context name to use routes
     *
     * @return       mixed the value for genRoutes
     */
    public function genRoutes($context)
    {
        $cfg = AgaviConfig::get('core.config_dir') . '/routing.xml';
        if (is_readable($cfg)) {
            //We need web routes.
            $roles = unserialize(file_get_contents(AgaviConfigCache::checkConfig($cfg, $context)));
        } else {
            throw new Exception($cfg . ' is not readable');
        }

        $routes = array('child' => false, 'childs' => array()); 
        foreach ($roles as $name => $data) {
            if ($name{0} == '_') {
                continue;
            }
            
            $current = explode('.', $name);

            $route = '';
            $routePath = '';
            $routeTemplate = '';
            $parent = array();
            $croute = &$routes['childs'];
            while ($r = array_shift($current)) {
                if (isset($roles[implode('.', $parent)])) {
                    $route = $roles[implode('.', $parent)]['opt']['reverseStr'];
                    $routePath .= $route;
                }
                $parent[] = $r;
                $routeTemplate .= ucfirst($r);
                if (count($current) == 0) {
                    $route = $data['opt']['reverseStr'];
                    $routePath .= $route;
                }                    
                if (!isset($croute[$r])) {
                    $croute[$r] = array (
                        'route'  => $route, 
                        'path'   => $routePath, 
                        'temp'   => $routeTemplate, 
                        'child'  => isset($data['opt']['action']) && $data['opt']['action'] , 
                        'childs' => array()
                    );
                }          
                $croute = &$croute[$r]['childs'];
            }
        }   
        return $this->getRouteCode('root', $routes);
    }

    /**
     * Generate route code for ember
     *
     * @param string  $key   key name
     * @param array   $data  routing data
     * @param integer $level data structure level
     *
     * @return string;
     */
    protected function getRouteCode($key ,array $data, $level = 1) 
    {
        $s = AgaviConfig::get('core.debug') ? PHP_EOL : ' '; 
        $t = AgaviConfig::get('core.debug') ? "\t" : ''; 
        $buffer = '';
        if (isset($data['child']) && $data['child']) {
            $buffer .= str_repeat($t, $level) . $key . ': Market.Route.extend({' . $s;
        } else {
            $buffer .= str_repeat($t, $level) . $key . ': Ember.Route.extend({' . $s;
        }                

        if (isset($data['temp'])) {
            $buffer .= str_repeat($t, $level + 1) . "routeTemplate: '{$data['temp']}'," . $s;
        }
        if (isset($data['path'])) {
            $buffer .= str_repeat($t, $level + 1) . "routePath: '{$data['path']}'," . $s;
        }                
        
        foreach ($data['childs'] as $cKey => $cData) {
            $buffer .= $this->getRouteCode($cKey, $cData, $level + 1) . ',' . $s;
        }            
        if (isset($data['route'])) {
            $buffer .= str_repeat($t, $level + 1) . "route: '{$data['route']}'" . $s;
        } else {
            $buffer .= str_repeat($t, $level + 1) . "dummy: ''" . $s;
        }            
        $buffer .= str_repeat($t, $level) . '})';
        return $buffer;
    }        
}
