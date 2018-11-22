<?php

namespace JeffersonSimaoGoncalves\Auditing\Model\Behavior;

use Cake\Event\Event;
use Cake\I18n\Time;
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
            TableRegistry::getTableLocator()->remove('JeffersonSimaoGoncalves/Auditing.AuditingRecords');
            TableRegistry::getTableLocator()->remove('JeffersonSimaoGoncalves/Auditing.AuditingLogs');
            /** @var \JeffersonSimaoGoncalves\Auditing\Model\Table\AuditingRecordsTable $recordTable */
            $recordTable = TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/Auditing.AuditingRecords');
            /** @var \JeffersonSimaoGoncalves\Auditing\Model\Table\AuditingLogsTable $logTable */
            $logTable = TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/Auditing.AuditingLogs');
            $log = $logTable->newEntity();

            $data = Router::getRequest(true)->getData();
            unset($data['_save']);
            if (empty($data)) {
                $data = $entity->toArray();
            }

            $configLog = $recordTable->getConnection()->config();
            $configEntity = TableRegistry::getTableLocator()->get($entity->getSource())->getConnection()->config();

            $database = isset($configEntity['database']) ? $configEntity['database'] : $configLog['database'];

            $diff = $entity->extractOriginalChanged(array_keys($data));

            $query = $recordTable->find('all')->where(['model_pk' => $entity->id, 'model_database' => $database, 'model_table' => $class]);

            $record = $query->first();

            if ($entity->isNew() || is_null($record)) {
                $record = $recordTable->newEntity();
                $record->model_table = $class;
                $record->model_database = $database;
                $record->model_pk = $entity->id;
                $record->created = date('Y-m-d H:i:s');
            }

            if ($entity->isNew()) {
                $log->action_type = 'INSERT';
            } else {
                $log->old_data = $diff;
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

        if (!in_array($class, ['JeffersonSimaoGoncalves\\Auditing\\Model\\Entity\\AuditingRecord', 'JeffersonSimaoGoncalves\\Auditing\\Model\\Entity\\AuditingLog'])) {
            TableRegistry::getTableLocator()->remove('JeffersonSimaoGoncalves/Auditing.AuditingRecords');
            TableRegistry::getTableLocator()->remove('JeffersonSimaoGoncalves/Auditing.AuditingLogs');
            /** @var \JeffersonSimaoGoncalves\Auditing\Model\Table\AuditingRecordsTable $recordTable */
            $recordTable = TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/Auditing.AuditingRecords');
            /** @var \JeffersonSimaoGoncalves\Auditing\Model\Table\AuditingLogsTable $logTable */
            $logTable = TableRegistry::getTableLocator()->get('JeffersonSimaoGoncalves/Auditing.AuditingLogs');
            $log = $logTable->newEntity();

            $configLog = $recordTable->getConnection()->config();
            $configEntity = TableRegistry::getTableLocator()->get($entity->getSource())->getConnection()->config();

            $database = isset($configEntity['database']) ? $configEntity['database'] : $configLog['database'];

            $query = $recordTable->find('all')->where(['model_pk' => $entity->id, 'model_database' => $database, 'model_table' => $class]);

            $record = $query->first();

            $log->action_type = 'DELETE';
            $log->created_by = Time::now();
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
