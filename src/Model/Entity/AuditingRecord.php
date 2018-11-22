<?php

namespace JeffersonSimaoGoncalves\Auditing\Model\Entity;

use Cake\ORM\Entity;

/**
 * AuditingRegistro Entity
 *
 * @property int $id
 * @property string $model_table
 * @property int $model_pk
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $deleted
 * @property int $created_by
 * @property int $updated_by
 *
 * @property \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingLog[] $auditing_logs
 */
class AuditingRecord extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'model_table'   => true,
        'model_pk'      => true,
        'created'       => true,
        'deleted'       => true,
        'created_by'    => true,
        'updated_by'    => true,
        'auditing_logs' => true,
    ];
}
