<?php

/**
 * A renderer produces the output as defined by a View
 * 
 * PHP version 5.3
 * 
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 Authors
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 */


/**
 * A renderer produces the output as defined by a View
 * 
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 Authors
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class AgaviMustacheRenderer extends AgaviRenderer implements AgaviIReusableRenderer
{
	/**
	 * @var		Mustache_Engine The template engine.
	 */
	protected $mustache = null;

	/**
	 * @var		string A string with the default template file extension,
	 *					including the dot.
	 */
	protected $defaultExtension = '.mustache';

	/**
	 * Pre-serialization callback.
	 *
	 * Excludes the Mustache instance to prevent excessive serialization load.
	 *
     * @return array
	 * @author	 Behrooz Shabani <everplays@gmail.com>
	 * @since	  1.0.8
	 */
	public function __sleep()
	{
		$keys = parent::__sleep();
		unset($keys[array_search('mustache', $keys)]);
		return $keys;
	}

	/**
	 * Initialize this Renderer.
	 *
	 * @param AgaviContext $context    The current application context.
	 * @param array		   $parameters An associative array of initialization parameters.
	 *
     * @return void
	 * @author	 Behrooz Shabani <everplays@gmail.com>
	 * @since	  1.0.8
	 */
	public function initialize(AgaviContext $context, array $parameters = array())
	{
		parent::initialize($context, $parameters);

		$this->setParameter(
            'options', 
            array_merge(
                array(
                    'loader' => 'Mustache_Loader_HandlebarsLoader',
                    'partials_loader' => 'Mustache_Loader_HandlebarsLoader',
                    'cache' => AgaviConfig::get('core.debug') ? false : AgaviConfig::get('core.cache_dir') . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'mustache',
                    ),
                (array)$this->getParameter('options', array())
            )
        );
	}

	/**
	 * Load and create an instance of Mustache.
	 *
	 * @return	 Mustache_Engine
	 *
	 * @author	 Behrooz Shabani <everplays@gmail.com>
	 * @since	  1.0.8
	 */
	protected function createEngineInstance()
	{
		if (!class_exists('Mustache_Engine')) {
			if (!class_exists('Mustache_Autoloader')) {
				include_once 'Mustache/Autoloader.php';
			}
			Mustache_Autoloader::register();
		}

		$parameters = (array)$this->getParameter('options', array());
		unset($parameters['loader']);
		unset($parameters['partials_loader']);
		return new Mustache_Engine($parameters);
	}

	/**
	 * Grab an initialized Mustache instance.
	 *
	 * @return	 Mustache_Engine
	 *
	 * @author	 Behrooz Shabani <everplays@gmail.com>
	 * @since	  1.0.8
	 */
	public function getEngine()
	{
		if (!$this->mustache) {
			$this->mustache = $this->createEngineInstance();

			// assigns can be set as globals
			foreach ($this->assigns as $key => $getter) {
				$this->mustache->addHelper($key, $this->context->$getter());
			}
            // the tags should be like:
            // {{#_}}gettext.domain::text needs to be translated{{/_}}
            // or
            // {{#_}}text in default domain{{/_}}
            $tm = $this->getContext()->getTranslationManager();
            $this->mustache->addHelper(
                '_', 
                function($text) use ($tm)
                {
                    $domain = null;
                    if (preg_match('/^[a-zA-Z0-9\.]+::/', $text)) {
                        list($domain, $text) = explode('::', $text, 2);
                    }
                    return $tm->_($text, $domain);
                }
            );
            // use it like: {{#AgaviConfig::get}}core.app_name{{/AgaviConfig}}
            $this->mustache->addHelper(
                'AgaviConfig::get', 
                function($config){
                    return AgaviConfig::get($config);
                }
            );

            $template_dir = $this->getParameter('template_dir', AgaviConfig::get('core.template_dir'));

            // set loader
            $loader = $this->getParameter('loader', 'Mustache_Loader_HandlebarsLoader');
            if (class_exists($loader)) {
                $loader = new $loader($template_dir);
            }
            $this->mustache->setLoader($loader);

            // set partial loader
            $partialsLoader = $this->getParameter('partials_loader', 'Mustache_Loader_HandlebarsLoader');
            if (class_exists($partialsLoader)) {
                $partialsLoader = new $partialsLoader($template_dir);
            }
            $this->mustache->setPartialsLoader($partialsLoader);
		}

		return $this->mustache;
	}

	/**
	 * Render the presentation and return the result.
	 *
	 * @param AgaviTemplateLayer $layer        The template layer to render.
	 * @param array              &$attributes  The template variables.
	 * @param array			     &$slots       The slots.
	 * @param array			     &$moreAssigns Associative array of additional assigns.
	 *
	 * @return	 string A rendered result.
	 *
	 * @author	 Behrooz Shabani <everplays@gmail.com>
	 * @since	  1.0.8
	 */
	public function render(AgaviTemplateLayer $layer, array &$attributes = array(), array &$slots = array(), array &$moreAssigns = array())
	{
		$mustache = $this->getEngine();

        $template_dir = $this->getParameter('template_dir', AgaviConfig::get('core.template_dir'));

		// get realpath of file to avoid . and ..
		$path = realpath($layer->getResourceStreamIdentifier());
		// remove extension
		$path = preg_replace('/\.[^\/\\\\]+$/', '', $path);
		$path = substr($path, strlen($template_dir) + 1);
		$template = $mustache->loadTemplate($path);

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

		// dynamic assigns (global ones were set in getEngine())
		$finalMoreAssigns = self::buildMoreAssigns($moreAssigns, $this->moreAssignNames);
		foreach ($finalMoreAssigns as $key => $value) {
			$data[$key] = $value;
		}

		return $template->render($data);
	}
}
