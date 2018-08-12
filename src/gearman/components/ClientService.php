<?php

//namespace modules\import\components;

/**
 * Class ClientService
 * @property string $performer
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
    /** @var GearmanClient */
    protected $client;
    /** @var CLogger */
    protected $logger;

    /**
     * @param string $performer
     * @param string $data json format
     * @param string $priority normal|high|low
     * @return string
     */
    public function send($data, $performer = null, $priority = self::PRIORITY_NORMAL)
    {
        if ($performer !== null) {
            $this->performer = $performer;
        }
        if ($priority === self::PRIORITY_HIGH) {
            return $this->client->doHighBackground($this->performer, $data);
        }
        if ($priority === self::PRIORITY_LOW) {
            return $this->client->doLowBackground($this->performer, $data);
        }
        return $this->client->doBackground($this->performer, $data);
    }

    public function isSuccess()
    {
        return $this->client->returnCode() === GEARMAN_SUCCESS;
    }

    public function setPerformer($performer)
    {
        $this->performer = $performer;
    }

    public function getPerformer()
    {
        return $this->performer;
    }

    public function getClient()
    {
        if (!$this->client instanceof GearmanClient) {
            $this->client = new GearmanClient();
            $this->client->addServer($this->host, $this->port);
        }

        return $this->client;
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
