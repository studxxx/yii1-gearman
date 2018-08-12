<?php

/**
 * Class ServerService
 * @property CLogger $logger
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
    protected $performer = 'yiiworker';

    public function run()
    {
        if (PHP_SAPI === 'cli') {
            $this->getLogger()->autoFlush = 1;
            $this->getLogger()->autoDump = true;
        }

        while (1) {
            print "Waiting for job..." . PHP_EOL;

            try {
                $this->worker->work();

                if (!$this->isSuccess()) {
                    // @todo set timeout
                    break;
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
        return $this->worker->returnCode() === GEARMAN_SUCCESS;
    }

    public function process(GearmanJob $job)
    {
        $workload = unserialize($job->workload());
        // @todo echo gettype($workload) . PHP_EOL;
        //$handle = $this->job->handle();

        try {
            $this->attachBehavior($workload->behavior, $workload->behavior);

            if (!$this->{$workload->behavior} instanceof WorkerJobInterface) {
                throw new CException($workload->behavior . ' not instance of WorkerJobInterface');
            }
            $this->{$workload->behavior}->perform($workload->data);

            $this->detachBehavior($workload->behavior);
        } catch (CException $e) {
            $this->logger->log($e->getMessage(), CLogger::LEVEL_ERROR, 'console');
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
            $this->worker->addFunction($this->performer, [$this, 'process']);
        }
        return $this->worker;
    }

    /**
     * @return string
     */
    public function getPerformer()
    {
        return $this->performer;
    }

    /**
     * @param string $performer
     */
    public function setPerformer($performer)
    {
        $this->performer = $performer;
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
