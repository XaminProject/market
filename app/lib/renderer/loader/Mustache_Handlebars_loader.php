<?php

/**
 * A loder to convert Handlebars to mustache
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
 * View class
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */

class Mustache_Loader_HandlebarsLoader extends  Mustache_Loader_FilesystemLoader
{
    /**
     * Helper function for loading a Mustache file by name.
     *
     * @param string $name file name to load
     *
     * @return string Mustache Template source
     * @throws InvalidArgumentException if a template file is not found.
     */
    protected function loadFile($name)
    {
        return $this->convert(parent::loadFile($name));
    }

    /**
     * Convert handlebars to mustache, nasty way :)
     *
     * @param string $hb handlebars string
     *
     * @return string mustache data
     */
    protected function convert($hb)
    {
        $hb = chr(5) . $hb;
        $start = chr(6); //Ok its a funny trik but work very fast!
        $stop = chr(7);

        //This is a bug in strtok i think
        $hb = str_replace('}}{{', '}}' . chr(5) . '{{', $hb);
        $hb = str_replace('{{', $start, $hb);
        $hb = str_replace('}}', $stop, $hb);

        $stack = array();
        $result = '';
        $str = strtok($hb, $start);
        while ($str) {
            $result .= $str; //This is pre $strat
            $token = strtok($stop);
            if ($token == '' ) { //There is problem with empty tags
				break;
			}
            $found = false;
            $similars = array ('with', 'each', 'if', 'unless');
            foreach ($similars as $key) {
                if (!$found && preg_match('/^#' . $key . '.*/', $token)) {
                    $t = $key;
                    $s = preg_replace('/^#' . $key . '[ ]*/', '', $token);
                    array_push($stack, array($t, $s));
                    $tagStart = ($key == 'unless') ? '^' : '#';
                    $result .= '{{' . $tagStart . $s . '}}';
                    $found = true;
                    break;
                } elseif (!$found && preg_match('/^\/' . $key . '$/', $token)) {
                    //Check stack, last one must be a with
                    $top = array_pop($stack);
                    if ($top[0] == $key) {
                        $result .= '{{/' . $top[1] . '}}';
                    } else {
                        throw new Exception("{{/{$top[0]}}} required but {{/$key}} is here");
                    } 
                    $found = true;
                    break;
                }                   
            } 
            if (!$found && preg_match('/^bindAttr.*/', $token)) {
                $token = preg_replace('/^bindAttr[ ]*/', '', $token);
                //Now token is in attr="value" mode, split it
                $data = explode('=', $token);
                if (count($data) != 2) {
                    throw new Exception("Invalid bindAttr parameter $token ");
                }
                $data[1] = trim($data[1], "\"' ");
                $result .= '{{#' . $data[1] . '}}' . $data[0] . '="{{.}}"{{/' . $data[1] . '}}';
            } elseif (!$found) {
                //pass it as is
                $result .= '{{' . $token . '}}';
            }                
            $str = strtok($start);
        }
        //If stack is not empty let mustache take care of that part.
        return str_replace(chr(5), '', $result);           
    }        
}    