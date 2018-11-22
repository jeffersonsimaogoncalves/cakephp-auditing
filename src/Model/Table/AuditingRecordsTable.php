<?php

namespace JeffersonSimaoGoncalves\Auditing\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AuditingRecords Model
 *
 * @property \JeffersonSimaoGoncalves\Auditing\Model\Table\AuditingLogsTable|\Cake\ORM\Association\HasMany $AuditingLogs
 *
 * @method \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingRecord get($primaryKey, $options = [])
 * @method \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingRecord newEntity($data = null, array $options = [])
 * @method \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingRecord[] newEntities(array $data, array $options = [])
 * @method \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingRecord|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingRecord|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingRecord patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingRecord[] patchEntities($entities, array $data, array $options = [])
 * @method \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingRecord findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AuditingRecordsTable extends Table
{

    /**
     * set connection name
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        $connection = Configure::read('JeffersonSimaoGoncalves/Auditing.connection');
        if (!empty($connection)) {
            return $connection;
        };

        return parent::defaultConnectionName();
    }

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     *
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('auditing_records');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('AuditingLogs', [
            'foreignKey' => 'auditing_record_id',
            'className'  => 'JeffersonSimaoGoncalves/Auditing.AuditingLogs',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     *
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('model_table')
            ->maxLength('model_table', 100)
            ->requirePresence('model_table', 'create')
            ->notEmpty('model_table');

        $validator
            ->scalar('model_pk')
            ->maxLength('model_pk', 50)
            ->requirePresence('model_pk', 'create')
            ->notEmpty('model_pk');

        $validator
            ->dateTime('deleted')
            ->allowEmpty('deleted');

        $validator
            ->integer('created_by')
            ->allowEmpty('created_by');

        $validator
            ->integer('updated_by')
            ->allowEmpty('updated_by');

        return $validator;
    }
}
