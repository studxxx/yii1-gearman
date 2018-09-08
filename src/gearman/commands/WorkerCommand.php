<?php

/**
 * Background task handler
 * Tasks: ImportPrice|ImportToDb|ReadContent
 * ReadImages|ImportImages|ImportImagesToDb
 * Class ContentBuilderCommand
 */
class WorkerCommand extends CConsoleCommand
{
    public $host = '127.0.0.1';
    public $port = 4730;
    public $consumer = 'yiiconsumer';

    /** @var ServerService */
    private $serverService;

    public function init()
    {
        Yii::import('vendor.studxxx.yii1-gearman.src.gearman.components.ServerService');
        Yii::import('vendor.studxxx.yii1-gearman.src.gearman.helpers.StringHelper');

        $this->serverService = new ServerService();
        $this->serverService->setHost($this->host);
        $this->serverService->setPort($this->port);
        $this->serverService->setConsumer($this->consumer);
    }

    /**
     * Start worker
     * @param string $consumer - name consumer
     */
    public function run($consumer = null)
    {
        if ($consumer) {
            $this->serverService->setConsumer($consumer);
        }
        $this->serverService->run();
    }
}
