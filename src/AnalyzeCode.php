<?php

namespace JustIversen\Performant;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class AnalyzeCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analyze:code';

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Analyze code execution';

    /**
     * An empty table for display performance analytics
     *
     * @var array
     */
    protected $table = [];

    /**
     * @var null
     */
    protected $answer = null;

    /**
     * @var array
     */
    protected $queries = [];

    /**
     * @var null
     */
    protected $time = null;

    /**
     * Create a new command instance
     *
     * AnalyzeCode constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command
     *
     * @return mixed
     */
    public function handle()
    {
        if (
            app()->environment('production')
            && !$this->confirm('You are in production mode! Are you sure you want to proceed?')
        ) {
            return;
        }

        $this->answer = $this->ask("Please type PHP script to analyze. Examples:
        1) dispatch(new App\Jobs\Test(\'param\'));
        2) App::make(\App\Http\Controllers\Project\ComponentQuantitiesController::class)->index(135);");

        $this->time = $this->track(function () use (&$queries) {
            $this->getQueries(function () {
                eval($this->answer);
            });
        });

        $this->table(['id', 'query', 'time'], $this->table);

        $this->line("\nTotal queries: " . count($this->queries));
        $this->line('Total time: ' . $this->time . ' sec');

        $this->line("\nTop 10 worst performing queries");
        $this->table(['id', 'query', 'time (ms)'], collect($this->table)->sortByDesc('time')->slice(0, 10));

        $keepRunning = true;
        if (!empty($this->table)) {
            while ($keepRunning) {
                $question = $this->choice(
                    'Select one of the following options:',
                    [
                        'Show the full-length SQL query for a particular query ID.',
                        'Analyze the performance of a particular query ID.',
                        'Shut down our analyzer.'
                    ]
                );

                if ($question === 'Show the full-length SQL query for a particular query ID.') {
                    $this->displayQuery($this->queries);
                } elseif ($question === 'Analyze the performance of a particular query ID.') {
                    $this->analyzeQuery($this->queries);
                } elseif ($question === 'Shut down our analyzer.') {
                    $keepRunning = false;
                }
            }
        }
    }

    /**
     * @param $queries
     */
    protected function analyzeQuery($queries)
    {
        $counter = 0;
        do {
            if ($counter === 0) {
                $this->answer = $this->ask('Enter the ID of the Query you want to analyze');
            } else {
                $this->answer = $this->ask('Your input didn\'t match a query ID. Please try again:');
            }
            $counter++;
        } while (!is_numeric($this->answer) && $this->answer < 1 && $this->answer > 10);

        $this->line("\nStarted analyzing your query. This might take a while depending on the query...");

        $this->line("\nStarted analyzing your query. This might take a while depending on the query...");

        $query = collect($queries)->firstWhere('id', '=', $this->answer);

        $queryObject = new Query();
        $queryObject->query = 'Explain ' . $query['query'];
        $queryObject->explainCollection = collect(DB::select(DB::raw('Explain ' . $query['query'])));
        $queryObject->flushCollection = $this->getFlushStatus($query['query']);

        //var_dump($queryObject->analyzeQuery());
        var_dump($queryObject->flushCollection);

        // Vi kunne lave noget Hvis explain collection !== null, så analyser følgende. Ellers, måske fejl?

        /*
        $first = $query[0];
        if ($query['query']->starts === '29' && $first->Action === '1') {
            $last = end($array);
            if ($last->positionId === '29' && $last->Action === '0' {
                // Stuff
            }
        }*/
    }

    /**
     * @param $queries
     */
    protected function displayQuery($queries)
    {
        $counter = 0;
        do {
            if ($counter === 0) {
                $this->answer = $this->ask('Enter the ID of the Query you want to see in full length: ');
            } else {
                $this->answer = $this->ask('Your input didn\'t match a query ID. Please try again:');
            }
            $counter++;
        } while (!is_numeric($this->answer) && $this->answer < 1 && $this->answer > 10);

        $query = collect($queries)->firstWhere('id', '=', $this->answer);
        $this->info('Query ID: ' . $query['id']);
        $this->info('Query: ' . $query['query']);
        $this->info('Time: ' . $query['time'] . 'ms');
    }

    /**
     * @param callable $callback
     */
    protected function getQueries(callable $callback)
    {
        $this->queries = [];

        DB::enableQueryLog();
        $callback();
        $logs = DB::getQueryLog();
        DB::disableQueryLog();

        $queries = [];

        foreach ($logs as $key => $log) {
            $data = [
                'id' => $key,
                'query' => vsprintf(str_replace('?', '\'%s\'', $log['query']), $log['bindings']),
                'time' => $log['time']
            ];

            $this->queries[] = $data;
            $this->table[] = [
                'id' => $data['id'],
                'query' => substr($data['query'], 0, 120) . ' (...)',
                'time' => $data['time']
            ];
        }
    }

    protected function getFlushStatus($query)
    {
        return DB::transaction(function () use ($query) {
            DB::select(DB::raw('FLUSH STATUS;'));
            DB::select(DB::raw($query));
            return DB::select(DB::raw('SHOW STATUS;'));
        });
    }

    protected function getFlushStatus($query)
    {
        return DB::transaction(function() use ($query) {
        DB::select(DB::raw('FLUSH STATUS;'));
        DB::select(DB::raw($query));
        return DB::select(DB::raw('SHOW STATUS;'));
        });
    }

    /**
     * @param callable $callback
     * @return float|string
     */
    protected function track(callable $callback)
    {
        $start = microtime(true);
        $callback();

        return (microtime(true) - $start);
    }
}
