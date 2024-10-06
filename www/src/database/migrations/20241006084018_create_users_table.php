<?php

// declare(strict_types=1);

// use Phinx\Migration\AbstractMigration;

// final class CreateUsersTable extends AbstractMigration
// {
//     public function up(): void
//     {
//         $table = $this->table('users', [
//             'collation' => 'utf8mb4_general_ci',
//         ]);

//         $table->addColumn('id', 'integer', ['identity' => true])
//               ->addColumn('username', 'string', ['limit' => 50]) 
//               ->addColumn('password', 'string', ['limit' => 255]) 
//               ->addIndex(['username'], ['unique' => true])
//               ->create(); 
//     }

//     public function down(): void
//     {
//         if ($this->hasTable('users')) {
//             $this->table('users')->drop()->save(); 
//         }
//     } 
// }