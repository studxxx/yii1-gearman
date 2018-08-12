<?php

/**
 * @author studxxx
 */
class WorkerAction extends CAction
{
    /** @var string */
    public $host = '127.0.0.1';
    /** @var int */
    public $port = 4730;
    /** @var string */
    public $behavior;
    /** @var array */
    public $data;
    /** @var string */
    public $performer = 'yiiworker';
    /** @var array */
    public $message = [
        'error' => 0,
        'message' => 'Start job',
    ];
    public $priority = ClientService::PRIORITY_NORMAL;

    public function run()
    {
        Yii::import('studxxx.yii1-gearman.src.gearman.components.ClientService');

        if (!Yii::app()->request->isAjaxRequest) {
            $this->setErrorMessage('No current request');
        }

        if (empty($_REQUEST)) {
            $this->setErrorMessage('No request data');
        }
        $this->data = $_REQUEST;

        $gearman = new ClientService();
        $gearman->setHost($this->host);
        $gearman->setPort($this->port);
        $gearman->send(
            CJSON::encode(['behavior' => $this->behavior, 'data' => $this->data]),
            $this->performer,
            $this->priority
        );

        $this->setSuccessMessage('Start job');
    }

    protected function setErrorMessage($message) {
        $this->message['error'] = 1;
        $this->message['message'] = $message;
        throw new CHttpException(404, CJSON::encode($this->message));
    }

    protected function setSuccessMessage($message)
    {
        $this->message['message'] = $message;
        echo CJSON::encode($this->message);
    }
}
