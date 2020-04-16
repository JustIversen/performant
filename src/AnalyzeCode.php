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
            && !$this->confirm("You are in production mode! Are you sure you want to proceed?\n
                                This tool was ment to be used in a development environment.\n
                                Proceed at your own risk. Running INSERT/DELETE/UPDATE queries could modify your data.")
        ) {
            return;
        }

        $this->answer = $this->ask("Please type PHP script to analyze.\n Examples:
        1) dispatch(new App\Jobs\Test(\'param\'));
        2) App::make(\App\Http\Controllers\Project\ComponentQuantitiesController::class)->index(135);");

        $this->time = $this->track(function () use (&$queries) {
            $this->getQueries(function () {
                eval($this->answer);
            });
        });

        $this->table(['id', 'query', 'time'], $this->table);

        $this->line("\nTotal queries found: " . count($this->queries));
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
                        'Shut down the analyzer.'
                    ]
                );

                if ($question === 'Show the full-length SQL query for a particular query ID.') {
                    $result = $this->displayQuery($this->queries);
                    $this->info('Query ID: ' . $result['id']);
                    $this->info('Query: ' . $result['query']);
                    $this->info('Execution Time: ' . $result['time'] . 'ms');
                } elseif ($question === 'Analyze the performance of a particular query ID.') {
                    $result = $this->analyzeQuery($this->queries);
                    $this->info("\n" . 'Result of our tests: ' . $result);
                } elseif ($question === 'Shut down the analyzer.') {
                    $this->info('Shutting down...');
                    $keepRunning = false;
                }
            }
        } else {
            $this->info('We found no queries in your inputted code.');
        }
    }

    /**
     * Asks for and takes a query ID which it analyses.
     * Returns the result of the Query analysis.
     *
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
        } while (!is_numeric($this->answer) || $this->answer > collect($queries)->count() - 1);

        $this->line("\nStarted analyzing your query. This might take a while depending on the query...\n");

        $query = collect($queries)->firstWhere('id', '=', $this->answer);

        $queryObject = new Query();
        $queryObject->query = $query['query'];
        $queryObject->explainCollection = collect(DB::select(DB::raw('Explain ' . $query['query'])));
        $queryObject->explainJsonCollection = collect(DB::select(DB::raw('Explain FORMAT=JSON ' . $query['query'])));
        $queryObject->flushCollection = $this->getFlushStatus($query['query']);

        return $queryObject->analyzeQuery();
    }

    /**
     *
     * Asks for and takes a Query ID which it uses to display the entire SQL query associated with the ID.
     *
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
        } while (!is_numeric($this->answer) || $this->answer > collect($queries)->count() - 1);

        return collect($queries)->firstWhere('id', '=', $this->answer);
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

    /**
     * Takes in a SQL query which it will try to get a Flush status on for further analyses.
     *
     * @param [type] $query
     * @return FLUSH collection
     */
    protected function getFlushStatus($query)
    {
        return DB::transaction(function () use ($query) {
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
