<?php
/**
 * Mailer model
 *
 * PHP version 5.2
 *
 * @category Xamin
 * @package  Market
 * @author   fzerorubigd <fzerorubigd@gmail.com>
 * @license  Custom <http://xamin.ir>
 * @link     http://xamin.ir
 */

/**
 * Mailer model (Agavi model)
 * 
 * @category Xamin
 * @package  Market
 * @author   fzerorubigd <fzerorubigd@gmail.com>
 * @license  Custom <http://xamin.ir>
 * @link     http://xamin.ir
 */

class Mailer_MainModel extends MarketMailerBaseModel
{
    /**
     * Mailer model parameters
     * 
     * @var array
     */
    protected $parameters;

    /**
     * Logger status
     * @var bool
     */
    protected $logEnable;

    /**
     * Logger class if enabled
     * @var Swift_Plugin_Logger_Array
     */
    protected $logger;
	
	/**
	 * Mailer 
	 * @var Swift_Mailer
	 */
    protected $mailer;

	/**
	 * Initialize model 
	 * 
	 * @param AgaviContext $context    Agavi context
	 * @param array        $parameters parameters
	 * 
	 * @return void 
	 * 
	 * @author   fzerorubigd <fzerorubigd@gmail.com>
	 */
	public function initialize(AgaviContext $context, array $parameters = array()) 
    {
		parent::initialize($context, $parameters);
		$this->_initParameters($parameters);
	}

	/**
	 * Initialize parameter for mailer
	 * 
	 * @param array $parameters parameters
	 * 
	 * @return void
	 * 
	 * @author   fzerorubigd <fzerorubigd@gmail.com>
	 */
    private function _initParameters($parameters) 
    {
        $this->parameters = array();
        if (isset($parameters['sender'])) {
            $this->parameters['sender'] = $parameters['sender'];
        } else {
            $this->parameters['sender'] = AgaviConfig::get('mailer.sender', array('name' => 'Xamin', 'email' => 'nreply@xamin.ir'));
        }

        if (isset($parameters['transport'])) {
            $this->parameters['transport'] = $parameters['transport'];
        } else {
            $this->parameters['transport'] = AgaviConfig::get('mailer.transport', array('type' => 'MailTransport'));
        }
        
        $this->logEnable = (isset($parameters['log'])) ? $parameters['log'] : AgaviConfig::get('mailer.log', false);
        $this->mailer = null;
    }

	/**
	 * Create new transport for mailer
	 * 
	 * @return Swift_Mailer_Transport
	 * 
	 * @author fzerorubigd <fzerorubigd@gmail.com>
	 */
    protected function createTransport() 
    {
        $args = $this->parameters['transport'];
        
        $type = 'Swift_' . $args['type']; 
        unset($args['type']);
        $transport = $type::newInstance();
        foreach ($args as $key => $value) {
            if (is_callable(array($transport, 'set' . ucfirst(strtolower($key))))) {
                call_user_func_array(array($transport, 'set' . ucfirst(strtolower($key))), array($value));
            }
        }
        
        return $transport;
    }

    /**
     * Create mailer instance
     * 
     * @return Swift_Mailer
     * @access protected
     */
    protected function createMailer() 
    {
        if (!$this->mailer) {
            $transport = $this->createTransport();
            $this->mailer = Swift_Mailer::newInstance($transport);

            if ($this->logEnable) {
                $this->logger = new Swift_Plugins_Loggers_ArrayLogger();
                $this->mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($this->logger));
            }
        }
        return $this->mailer;
    }

    /**
     * create Message
     * 
     * @param string $subject subject
     *
     * @return Swift_Message
     * @access protected
     */
    protected function createMessage($subject)
    {
        $sender = $this->parameters['sender'];
        $message = Swift_Message::newInstance($subject)
            ->setFrom(array($sender['email'] => $sender['name']));

        return $message;
    }

    /**
     * Send message
     * 
     * @param array  $to       Array of email addresses
     * @param string $subject  message subject
     * @param string $htmlBody Html body
     *
     * @return bool
     * @access public 
     */
    public function send(array $to, $subject, $htmlBody ) 
    {
        $mailer = $this->createMailer();
        $message = $this->createMessage($subject);

        $message->setTo($to);

        $body = strip_tags($htmlBody);
        $message->setBody($body)
            ->addPart($htmlBody, 'text/html');

        $result = $mailer->send($message);
        if ($this->logEnable && $this->logger) {
            $agaviLogger = $this->getContext()->getLoggerManager();
            $agaviLogger->log($this->logger->dump());
        }
        return $result;
    }
}
