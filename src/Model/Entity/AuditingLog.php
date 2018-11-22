<?php

namespace JeffersonSimaoGoncalves\Auditing\Model\Entity;

use Cake\ORM\Entity;

/**
 * AuditingLog Entity
 *
 * @property int $id
 * @property int $auditing_record_id
 * @property string $action_type
 * @property \Cake\I18n\FrozenTime $created
 * @property int $created_by
 * @property string $old_data
 *
 * @property \JeffersonSimaoGoncalves\Auditing\Model\Entity\AuditingRecord $auditing_record
 */
class AuditingLog extends Entity
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
        'auditing_record_id' => true,
        'action_type'        => true,
        'created'            => true,
        'created_by'         => true,
        'old_data'           => true,
        'auditing_record'    => true,
    ];
}
