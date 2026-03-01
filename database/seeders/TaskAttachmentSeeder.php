<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskAttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $uploadedBy = $adminUser ? $adminUser->id : 1;

        $tasks = Task::all();

        if ($tasks->isEmpty()) {
            $this->command->warn('لا توجد مهام!');
            return;
        }

        $fileTypes = ['pdf', 'docx', 'xlsx', 'jpg', 'png', 'zip'];
        $fileNames = [
            'report.pdf',
            'document.docx',
            'data.xlsx',
            'image.jpg',
            'screenshot.png',
            'archive.zip',
            'presentation.pptx',
            'spreadsheet.xls',
        ];

        foreach ($tasks as $task) {
            // 30% من المهام لديها مرفقات
            if (rand(1, 100) > 30) {
                continue;
            }

            $numAttachments = rand(1, 3);

            for ($i = 0; $i < $numAttachments; $i++) {
                $fileType = $fileTypes[array_rand($fileTypes)];
                $fileName = $fileNames[array_rand($fileNames)];
                $filePath = "tasks/{$task->id}/" . uniqid() . ".{$fileType}";
                $fileSize = rand(100000, 5000000); // 100KB - 5MB

                TaskAttachment::create([
                    'task_id' => $task->id,
                    'file_path' => $filePath,
                    'file_name' => $fileName,
                    'file_type' => $fileType,
                    'file_size' => $fileSize,
                    'description' => "مرفق للمهمة: {$task->title}",
                    'uploaded_by' => $uploadedBy,
                ]);
            }
        }

        $totalAttachments = TaskAttachment::count();
        $this->command->info("✅ تم إنشاء $totalAttachments مرفق مهمة");
    }
}
