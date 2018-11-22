<?php

use Migrations\AbstractMigration;

class Initial extends AbstractMigration
{
    public function up()
    {
        $this->table('auditing_logs')
            ->addColumn('auditing_record_id', 'integer', [
                'default' => null,
                'limit'   => 11,
                'null'    => false,
            ])
            ->addColumn('action_type', 'string', [
                'default' => null,
                'limit'   => 20,
                'null'    => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit'   => null,
                'null'    => false,
            ])
            ->addColumn('created_by', 'integer', [
                'default' => null,
                'limit'   => 11,
                'null'    => true,
            ])
            ->addColumn('old_data', 'text', [
                'default' => null,
                'limit'   => null,
                'null'    => true,
            ])
            ->addIndex(
                [
                    'auditing_record_id',
                ]
            )
            ->create();

        $this->table('auditing_records')
            ->addColumn('model_table', 'string', [
                'default' => null,
                'limit'   => 100,
                'null'    => false,
            ])
            ->addColumn('model_pk', 'string', [
                'default' => null,
                'limit'   => 50,
                'null'    => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit'   => null,
                'null'    => false,
            ])
            ->addColumn('deleted', 'datetime', [
                'default' => null,
                'limit'   => null,
                'null'    => true,
            ])
            ->addColumn('created_by', 'integer', [
                'default' => null,
                'limit'   => 11,
                'null'    => true,
            ])
            ->addColumn('updated_by', 'integer', [
                'default' => null,
                'limit'   => 11,
                'null'    => true,
            ])
            ->create();

        $this->table('auditing_logs')
            ->addForeignKey(
                'auditing_record_id',
                'auditing_records',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT',
                ]
            )
            ->update();
    }

    public function down()
    {
        $this->table('auditing_logs')
            ->dropForeignKey(
                'auditing_record_id'
            )->save();

        $this->table('auditing_logs')->drop()->save();
        $this->table('auditing_records')->drop()->save();
    }
}
