<?php

namespace models;

class GeneralLogFilter extends \KZ\model\Filter
{
    public $threadId;

    public $serverId;

    public $commandType;

    public $argument;

    public $eventTime;

    public $userHost;

    public $sortBy = 'default';

    public $p = 1;

    protected $commandTypeOptions;

    public function rules()
    {
        return [
            'threadId' => [],
            'serverId' => [],
            'commandType' => [
                ['validateCommandType']
            ],
            'argument' => [],
            'eventTime' => [],
            'userHost' => [],
            'sortBy' => [
                ['validateSortBy']
            ],
            'p' => []
        ];
    }

    public function validateCommandType($attr)
    {
        if (!array_key_exists($this->commandType, $this->getCommandTypeOptions()))
            $this->addError($attr, 'Incorrect value');
    }

    public function getCommandTypeOptions(array $out = [])
    {
        if (!isset($this->commandTypeOptions)) {
            $table = new \tables\GeneralLog();
            $this->commandTypeOptions = $table->getCommandTypeOptions();
        }

        return array_merge($out, $this->commandTypeOptions);
    }

    public function validateSortBy($attr)
    {
        if (!array_key_exists($this->sortBy, $this->getSortByOptions()))
            $this->addError($attr, 'Incorrect value');
    }

    public function getSortByOptions(array $out = [])
    {
        return array_merge($out, [
            'default' => 'By event time desc, grouped by thread',
            'event_time_asc' => 'Event time ASC',
            'event_time_desc' => 'Event time DESC',
            'thread_id_asc' => 'Thread id ASC',
            'thread_id_desc' => 'Thread id DESC',
        ]);
    }
} 
