<?php

namespace App\Console\Commands;

use App\Services\Ai\TaskParser;
use Illuminate\Console\Command;

class AiTask extends Command
{
    protected $signature = 'ai:task {prompt* : e.g. "create task to create mobile app, delivery date is 29 aug 2026"}
                                    {--refine= : Follow-up instruction, replays the reasoning from the first call}
                                    {--json : Print the raw JSON object only}';

    protected $description = 'Turn a sentence into {title, start_date, end_date, description, client}';

    public function handle(TaskParser $parser): int
    {
        $prompt = implode(' ', $this->argument('prompt'));

        $task = $parser->parse($prompt);

        if ($refine = $this->option('refine')) {
            $task = $parser->refine($task, $refine);
        }

        $fields = [
            'title'       => $task['title'],
            'start_date'  => $task['start_date'],
            'end_date'    => $task['end_date'],
            'description' => $task['description'],
            'client'      => $task['client'],
        ];

        if ($this->option('json')) {
            $this->line(json_encode($fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        }

        $this->newLine();
        foreach ($fields as $key => $value) {
            $this->components->twoColumnDetail('<fg=gray>' . $key . '</>', (string) ($value ?? '—'));
        }
        $this->newLine();
        $this->components->twoColumnDetail('source', $task['source'] . ($task['model'] ? ' · ' . $task['model'] : ''));

        if (! empty($task['error'])) {
            $this->components->warn($task['error']);
        }
        if (! empty($task['refine_error'])) {
            $this->components->warn($task['refine_error']);
        }

        return self::SUCCESS;
    }
}
