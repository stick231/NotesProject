<?php

namespace Controllers;

use Repository\MigrationRepository;

class MigrationController{
    private $migrationRepository;

    public function __construct(MigrationRepository $migrationRepository) {
        $this->migrationRepository = $migrationRepository;
    }   
    
    public function selectMigration()
    {
        return $this->migrationRepository->selectMigration();
    }

    public function actionUp($migrationId){
        return $this->migrationRepository->actionUp($migrationId);
    }

    public function actionDown($migrationId)
    {
        return $this->migrationRepository->actionDown($migrationId);
    }
}