<?php

/**
 * Class ServerService
 * @property CLogger $logger
 * @property GearmanWorker $service
 */
class ServerService extends CComponent
{
    /** @var GearmanWorker */
    protected $worker;
    /** @var string */
    protected $host = '127.0.0.1';
    /** @var int  */
    protected $port = 4730;
    /** @var string  */
    protected $consumer = 'yiiconsumer';

    public function run()
    {
        if (PHP_SAPI === 'cli') {
            $this->getLogger()->autoFlush = 1;
            $this->getLogger()->autoDump = true;
        }

        while (1) {
            print "Waiting for job..." . PHP_EOL;

            try {
                $this->service->work();

                if (!$this->isSuccess()) {
//                    break;
                    // @todo set timeout
                    $this->logger->log($this->service->getErrno(), CLogger::LEVEL_ERROR, 'console');
                    $this->logger->log($this->service->error(), CLogger::LEVEL_ERROR, 'console');
                }
            } catch (GearmanException $e) {
                $this->logger->log($e->getMessage(), CLogger::LEVEL_ERROR, 'console');
            } catch (Exception $e) {
                $this->logger->log($e->getMessage(), CLogger::LEVEL_ERROR, 'console');
            }
        }
    }

    public function isSuccess()
    {
        return $this->service->returnCode() === GEARMAN_SUCCESS;
    }

    public function process(GearmanJob $job)
    {
        $workload = unserialize($job->workload());
        // @todo echo gettype($workload) . PHP_EOL;
        //$handle = $this->job->handle();

        try {
            if (empty($workload['performer'])) {
                throw new CException('Consumer must be set in message');
            }
            $this->attachBehavior($workload['performer'], $workload['performer']);

            if (!$this->{$workload['performer']} instanceof WorkerJobInterface) {
                throw new CException($workload['performer'] . ' not instance of WorkerJobInterface');
            }
            $this->{$workload['performer']}->perform($workload['data']);

            $this->detachBehavior($workload['performer']);
        } catch (CException $e) {
            $this->logger->log($e->getMessage(), CLogger::LEVEL_ERROR, 'console');
            $job->sendFail();
        }
    }

    public function getLogger()
    {
        return Yii::getLogger();
    }

    public function getService()
    {
        if (!$this->worker instanceof GearmanWorker) {
            $this->worker = new GearmanWorker();
            $this->worker->addServer($this->host, $this->port);
            $this->worker->addFunction($this->consumer, [$this, 'process']);
        }
        return $this->worker;
    }

    /**
     * @return string
     */
    public function getConsumer()
    {
        return $this->consumer;
    }

    /**
     * @param string $consumer
     */
    public function setConsumer($consumer)
    {
        $this->consumer = $consumer;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }
}
