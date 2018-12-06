<?php

namespace JeffersonSimaoGoncalves\Auditing\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AuditingLogs Model
 *
 * @property \JeffersonSimaoGoncalves\Auditing\Model\Table\AuditingRecordsTable|\Cake\ORM\Association\BelongsTo $AuditingRecords
 *
 * @method \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingLog get($primaryKey, $options = [])
 * @method \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingLog newEntity($data = null, array $options = [])
 * @method \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingLog[] newEntities(array $data, array $options = [])
 * @method \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingLog|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingLog|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingLog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingLog[] patchEntities($entities, array $data, array $options = [])
 * @method \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingLog findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AuditingLogsTable extends Table
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
        }

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

        $this->setTable('auditing_logs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('AuditingRecords', [
            'foreignKey' => 'auditing_record_id',
            'joinType'   => 'INNER',
            'className'  => 'JeffersonSimaoGoncalves/Auditing.AuditingRecords',
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
            ->scalar('action_type')
            ->maxLength('action_type', 20)
            ->requirePresence('action_type', 'create')
            ->notEmpty('action_type');

        $validator
            ->integer('created_by')
            ->allowEmpty('created_by');

        $validator
            ->scalar('old_data')
            ->allowEmpty('old_data');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     *
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['auditing_record_id'], 'AuditingRecords'));

        return $rules;
    }
}
