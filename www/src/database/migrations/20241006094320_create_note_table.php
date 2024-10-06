<?php
// declare(strict_types=1);

// use Phinx\Migration\AbstractMigration;

// final class CreateNoteTable extends AbstractMigration
// {
//     public function up(): void
//     {
//         if (!$this->hasTable('users')) {
//             throw new \RuntimeException('Таблица users не существует.');
//         }
        
//         if ($this->hasTable('note')) {
//             return;
//         }

//         $table = $this->table('note', [
//             'collation' => 'utf8mb4_general_ci',
//         ]);

//         $table->addColumn('id_note', 'integer', ['identity' => true]) 
//             ->addColumn('title', 'string', ['limit' => 255])
//             ->addColumn('content', 'text') 
//             ->addColumn('time', 'datetime') 
//             ->addColumn('reminder_time', 'datetime', ['null' => true]) 
//             ->addColumn('last_update', 'datetime', ['null' => true])
//             ->addColumn('expired', 'boolean', ['default' => false])
//             ->addColumn('user_id', 'integer') 
//             ->addIndex(['user_id'], ['name' => 'fk_user']) 
//             ->addForeignKey('user_id', 'users', 'id', [ 
//                 'delete'=> 'CASCADE',
//                 'update' => 'CASCADE'
//             ]);

//         try {
//             $table->create(); 
//         } catch (\Exception $e) {
//             echo 'Ошибка: ' . $e->getMessage();
//         }
//     }

//     public function down(): void
//     {
//         if ($this->hasTable('note')) {
//             $this->table('note')->drop()->save(); 
//         }
//     }
// }