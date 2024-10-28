<?php

namespace Controllers;

use Entities\Migration;
use Repository\MigrationRepository;

class MigrationController{
    private $migrationRepository;

    static function getActionMethodsMigration()
    {
        return 
            ['migrationRead' => 'selectMigration',
            'data-migration-up' => 'actionUp',
            'data-migration-down' => 'actionDown',
            'data-migration-delete' => 'deleteMigration',
            'data-migration-edit' => 'editForm',
            'data-migration-create' => 'createMigration',
            'data-migration-update' => 'updateMigration'
        ];
    }

    public function __construct(MigrationRepository $migrationRepository) {
        $this->migrationRepository = $migrationRepository;
    }   

    public function selectMigration()
    {
        return $this->migrationRepository->selectMigration();
    }

    public function editForm(Migration $migration)
    {
        return $this->migrationRepository->editForm($migration);
    }

    public function createMigration(Migration $migration)
    {
        return $this->migrationRepository->createMigration($migration);
    }

    public function deleteMigration(Migration $migrationId)
    {
        return $this->migrationRepository->deleteMigration($migrationId);
    }

    public function updateMigration(Migration $migration)
    {
        return $this->migrationRepository->updateMigration($migration);
    }

    public function actionUp(Migration $migration){
        return $this->migrationRepository->actionUp($migration);
    }

    public function actionDown(Migration $migration)
    {
        return $this->migrationRepository->actionDown($migration);
    }
}