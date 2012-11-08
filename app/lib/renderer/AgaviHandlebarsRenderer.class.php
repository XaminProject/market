<?php

/**
 * A renderer produces the output as defined by a View
 * 
 * PHP version 5.3
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
 * A renderer produces the output as defined by a View
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 © ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class AgaviHandlebarsRenderer extends AgaviRenderer implements AgaviIReusableRenderer
{
    /**
     * @var    Handlebars_Engine The template engine.
     */
    protected $handlebar = null;

    /**
     * @var    string A string with the default template file extension,
     *          including the dot.
     */
    protected $defaultExtension = '.handlebars';

    /**
     * Pre-serialization callback.
     *
     * Excludes the Handlebars instance to prevent excessive serialization load.
     *
     * @return array
     * @author    fzerorubigd <fzerorubigd@gmail.com>
     * @since    1.0.8
     */
    public function __sleep()
    {
        $keys = parent::__sleep();
        unset($keys[array_search('handlebars', $keys)]);
        return $keys;
    }

    /**
     * Initialize this Renderer.
     *
     * @param AgaviContext $context    The current application context.
     * @param array        $parameters An associative array of initialization parameters.
     *
     * @return void
     * @author    fzerorubigd <fzerorubigd@gmail.com>
     * @since    1.0.8
     */
    public function initialize(AgaviContext $context, array $parameters = array())
    {
        parent::initialize($context, $parameters);

        $this->setParameter(
            'options', 
            array_merge(
                array(
                    'loader' => 'Handlebars_Loader_FilesystemLoader',
                    'partials_loader' => 'Handlebars_Loader_FilesystemLoader',
                    //TODO : Create filesystem cache for handlebars
                    //'cache' => AgaviConfig::get('core.debug') ? false : AgaviConfig::get('core.cache_dir') . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'mustache',
                    ),
                (array)$this->getParameter('options', array())
            )
        );
    }

    /**
     * Load and create an instance of Handlebars.
     *
     * @return   Handlebars_Engine
     *
     * @author    fzerorubigd <fzerorubigd@gmail.com>
     * @since    1.0.8
     */
    protected function createEngineInstance()
    {
        if (!class_exists('Handlebars_Engine')) {
            if (!class_exists('Handlebars_Autoloader')) {
                include_once 'Handlebars/Autoloader.php';
            }
            Handlebars_Autoloader::register();
        }

        $parameters = (array)$this->getParameter('options', array());
        unset($parameters['loader']);
        unset($parameters['partials_loader']);
        return new Handlebars_Engine($parameters);
    }

    /**
     * Grab an initialized Handlebars instance.
     *
     * @return   Handlebars_Engine
     *
     * @author    fzerorubigd <fzerorubigd@gmail.com>
     * @since    1.0.8
     */
    public function getEngine()
    {
        if (!$this->handlebar) {
            $this->handlebar = $this->createEngineInstance();

            // the tags should be like:
            // {{#_ gettext.domain}}text needs to be translated{{/_}}
            // or
            // {{#_}}text in default domain{{/_}}
            $tm = $this->getContext()->getTranslationManager();
            $this->handlebar->addHelper(
                '_', 
                function ($template, $context, $domain, $text) use ($tm) {
                    if (!$domain) {
                        $domain = null; //Set the default domain
                    }                        
                    return $tm->_($text, $domain);
                }
            );
            // use it like: {{#AgaviConfig::get core.app_name}}
            $this->handlebar->addHelper(
                'AgaviConfig::get', 
                function ($template, $context, $value) {
                    return AgaviConfig::get($value);
                }
            );

            $template_dir = $this->getParameter('template_dir', AgaviConfig::get('core.template_dir'));

            // set loader
            $loader = $this->getParameter('loader', 'Handlebars_Loader_FilesystemLoader');
            if (class_exists($loader)) {
                $loader = new $loader($template_dir);
            }
            $this->handlebar->setLoader($loader);

            // set partial loader
            $partialsLoader = $this->getParameter('partials_loader', 'Handlebars_Loader_FilesystemLoader');
            if (class_exists($partialsLoader)) {
                $partialsLoader = new $partialsLoader($template_dir);
            }
            $this->handlebar->setPartialsLoader($partialsLoader);
        }

        return $this->handlebar;
    }

    /**
     * Render the presentation and return the result.
     *
     * @param AgaviTemplateLayer $layer        The template layer to render.
     * @param array              &$attributes  The template variables.
     * @param array              &$slots       The slots.
     * @param array              &$moreAssigns Associative array of additional assigns.
     *
     * @return   string A rendered result.
     *
     * @author    fzerorubigd <fzerorubigd@gmail.com>
     * @since    1.0.8
     */
    public function render(AgaviTemplateLayer $layer, array &$attributes = array(), array &$slots = array(), array &$moreAssigns = array())
    {
        $handlebar = $this->getEngine();

        $template_dir = $this->getParameter('template_dir', AgaviConfig::get('core.template_dir'));

        // get realpath of file to avoid . and ..
        $path = realpath($layer->getResourceStreamIdentifier());
        // remove extension
        $path = preg_replace('/\.[^\/\\\\]+$/', '', $path);
        $path = substr($path, strlen($template_dir) + 1);
        $template = $handlebar->loadTemplate($path);

        $data = array();

        // template vars
        if ($this->extractVars) {
            foreach ($attributes as $name => $value) {
                $data[$name] = $value;
            }
        } else {
            $data[$this->varName] = $attributes;
        }

        // slots
        $data[$this->slotsVarName] = $slots;

        // assigns can be set as globals
        foreach ($this->assigns as $key => $getter) {
            $data[$key] = $this->context->$getter();
        }

        // dynamic assigns (global ones were set in getEngine())
        $finalMoreAssigns = self::buildMoreAssigns($moreAssigns, $this->moreAssignNames);
        foreach ($finalMoreAssigns as $key => $value) {
            $data[$key] = $value;
        }

        return $template->render($data);
    }
}
