<?php
//Yii::setPathOfAlias('gearman', 'studxxx.yii1-gearman.src.gearman');

/**
 * Фоновый обработчик задач
 * Задачи: ImportPrice|ImportToDb|ReadContent
 * ReadImages|ImportImages|ImportImagesToDb
 * Class ContentBuilderCommand
 */
class WorkerCommand extends CConsoleCommand
{
    public $host = '127.0.0.1';
    public $port = 4730;
    public $performer = 'yiiworker';

    /** @var ServerService */
    private $serverService;

    public function init()
    {
        Yii::import('studxxx.yii1-gearman.src.gearman.components.ServerService');

        $this->serverService = new ServerService();
        $this->serverService->setHost($this->host);
        $this->serverService->setPort($this->port);
        $this->serverService->setPerformer($this->performer);
    }

    /**
     * Start worker
     * @param string $performer - name worker
     */
    public function run($performer = null)
    {
        if ($performer) {
            $this->serverService->setPerformer($performer);
        }
        $this->serverService->run();
    }
}
