<?php

namespace JeffersonSimaoGoncalves\Auditing\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Class LoggerBehavior
 *
 * @author Jefferson Simão Gonçalves <gerson.simao.92@gmail.com>
 *
 * @package JeffersonSimaoGoncalves\Auditing\Model\Behavior
 */
class LoggerBehavior extends Behavior
{
    /**
     * @param array $config
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
    }

    /**
     * @param \Cake\Event\Event $event
     * @param \Cake\ORM\Entity $entity
     * @param \ArrayObject $options
     */
    public function afterSave(Event $event, Entity $entity, \ArrayObject $options)
    {
        $class = get_class($entity);

        if (!in_array($class, ['JeffersonSimaoGoncalves\\Auditing\\Model\\Entity\\AuditingRecord', 'JeffersonSimaoGoncalves\\Auditing\\Entity\\Table\\AuditingLog'])) {
            $recordTable = TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/Auditing.AuditingRecords');
            $logTable = TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/Auditing.AuditingLogs');
            $log = $logTable->newEntity();


            $data = Router::getRequest()->getData();
            unset($data['_save']);
            if (empty($data)) {
                $data = $entity->toArray();
            }

            $diff = $entity->extractOriginalChanged(array_keys($data));

            $query = $recordTable->find('all')
                ->where(['model_pk' => $entity->id, 'model_table' => $class]);

            $record = $query->first();


            if ($entity->isNew() || is_null($record)) {
                $record = $recordTable->newEntity();

                $record->model_table = $class;
                $record->model_pk = $entity->id;
                $record->created = date('Y-m-d H:i:s');

            }

            if ($entity->isNew()) {
                $log->action_type = 'INSERT';
            } else {
                $log->old_data = json_encode($diff);
                $log->action_type = 'UPDATE';
            }

            $log->created = date('Y-m-d H:i:s');

            if ($recordTable->save($record)) {
                $record_id = $record->id;
                $log->auditing_record_id = $record_id;
                $logTable->save($log);
            }
        }
    }

    /**
     * @param \Cake\Event\Event $event
     * @param \Cake\ORM\Entity $entity
     * @param \ArrayObject $options
     */
    public function afterDelete(Event $event, Entity $entity, \ArrayObject $options)
    {
        $class = get_class($entity);

        if (!in_array($class, ['Auditotia\\Model\\Entity\\AuditingRecord', 'Auditotia\\Model\\Entity\\AuditingLog'])) {
            $recordTable = TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/Auditing.AuditingRecords');
            $logTable = TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/Auditing.AuditingLogs');
            $log = $logTable->newEntity();

            $query = $recordTable->find('all')
                ->where(['model_pk' => $entity->id, 'model_table' => $class]);

            $record = $query->first();

            $log->action_type = 'DELETE';
            $log->created = date('Y-m-d H:i:s');
            $log->auditing_record_id = $record->id;

            $logTable->save($log);
        }
    }

    /**
     * @param $aArray1
     * @param $aArray2
     *
     * @return array
     */
    private function arrayRecursiveDiff($aArray1, $aArray2)
    {
        $aReturn = [];

        foreach ($aArray1 as $mKey => $mValue) {
            if (array_key_exists($mKey, $aArray2)) {
                if (is_array($mValue)) {
                    $aRecursiveDiff = $this->arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                    if (count($aRecursiveDiff)) {
                        $aReturn[$mKey] = $aRecursiveDiff;
                    }
                } else {
                    if ($mValue != $aArray2[$mKey]) {
                        $aReturn[$mKey] = $mValue;
                    }
                }
            } else {
                $aReturn[$mKey] = $mValue;
            }
        }

        return $aReturn;
    }
}
