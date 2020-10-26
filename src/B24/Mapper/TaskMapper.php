<?php
namespace conta\B24\Mapper;

use conta\B24\Domain\Domain;
use conta\B24\Domain\Task\Task;

class TaskMapper extends Mapper
{
    const CREATE = 'tasks.task.add';

    public function create(Domain $domain): void
    {
        $params = $this->composeCreateParams($domain);
        $answer = $this->b24->call(static::CREATE, $params);
        $domain->setId($answer['result']['task']['id']);
    }

    public function update(Domain $object): void
    {
        throw new \Exception('Should implement update method');
    }

    protected function doCreateObject(array $fields): Domain
    {
        throw new \Exception('Should implement doCreateObject method');
    }

    protected function composeParams(array $ids): array
    {
        // TODO: Implement composeParams() method.
    }

    private function composeCreateParams(Task $task): array
    {
        $fields = [
            Task::TITLE => $task->getTitle(),
            Task::DESCRIPTION => $task->getDescription(),
            Task::RESPONSIBLE_ID => $task->getResponsibleId(),
            Task::CREATED_BY => $task->getCreatedById(),
            Task::DEADLINE => $task->getDeadline(),
            Task::GROUP_ID => $task->getGroupId(),
            Task::UF_CRM_TASK => $task->getCrmSettings(),
        ];
        return ['fields' => $fields];
    }
}