<?php

/**
 * Class ClientService
 * @property GearmanClient $client
 * @property string $consumer
 * @property string $host
 * @property integer $port
 */
class ClientService extends CComponent
{
    const PRIORITY_HIGH = 'high';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_LOW = 'low';

    /** @var string */
    protected $host = '127.0.0.1';
    /** @var int  */
    protected $port = 4730;

    /**
     * @param string|null $consumer
     * @param string $data json format
     * @param string $priority normal|high|low
     * @return string
     */
    public function send($data, $consumer = null, $priority = self::PRIORITY_NORMAL)
    {
        if ($consumer !== null) {
            $this->consumer = $consumer;
        }
        if ($priority === self::PRIORITY_HIGH) {
            return $this->client->doHighBackground($this->consumer, $data);
        }
        if ($priority === self::PRIORITY_LOW) {
            return $this->client->doLowBackground($this->consumer, $data);
        }
        return $this->client->doBackground($this->consumer, $data);
    }

    public function isSuccess()
    {
        return $this->client->returnCode() === GEARMAN_SUCCESS;
    }

    public function setConsumer($consumer)
    {
        $this->consumer = $consumer;
    }

    public function getConsumer()
    {
        return $this->consumer;
    }

    public function getClient()
    {
        $client = new GearmanClient();
        $client->addServer($this->host, $this->port);

        return $client;
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
