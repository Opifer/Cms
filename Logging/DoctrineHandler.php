<?php

namespace Opifer\CmsBundle\Logging;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This Monolog Handler is currently disabled.
 * To enable it;
 *  - Uncomment the service ID inside CmsBundle/Resources/config/services.yml
 *  - Uncomment the doctrine handler in the monolog config.
 */
class DoctrineHandler extends AbstractProcessingHandler
{
    /** @var bool */
    private $initialized = false;

    /** @var ContainerInterface */
    private $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param int                $level
     * @param bool               $bubble
     */
    public function __construct(ContainerInterface $container, $level = Logger::DEBUG, $bubble = true)
    {
        $this->container = $container;
        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        // Ensure the doctrine channel is ignored (unless its greater than a warning error),
        // otherwise you will create an infinite loop, as doctrine like to log.. a lot..
        if ('doctrine' == $record['channel']) {
            if ((int) $record['level'] >= Logger::WARNING) {
                error_log($record['message']);
            }

            return;
        }

        // Make sure to only log from a certain log level
        if ((int) $record['level'] >= Logger::WARNING) {
            try {
                $em = $this->container->get('doctrine')->getManager();
                $conn = $em->getConnection();

                $created = date('Y-m-d H:i:s');

                $query = $conn->prepare(
                    'INSERT INTO log(channel, level, message, created_at) '.
                    'VALUES('.$conn->quote($record['channel']).", '".$record['level']."', ".$conn->quote($record['message']).", '".$created."');"
                );
                $query->execute();
            } catch (\Exception $e) {
                // Fallback to just writing to php error logs if something really bad happens
                error_log($record['message']);
                error_log($e->getMessage());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    private function initialize()
    {
        $this->initialized = true;
    }
}
