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
        if (app()->environment('production') && !
            $this->confirm('You are in a production mode! Are you sure you want to proceed?')) {
            return;
        }

        $this->answer = $this->ask("Please type PHP script to analyze. Examples:
        1) dispatch(new App\Jobs\Test(\'param\'));
        2) App::make(\App\Http\Controllers\Project\ComponentQuantitiesController::class)->index(135);");

        $this->time = $this->track(function () use (&$queries){
            $this->getQueries(function () {
                eval($this->answer);
            });
        });

        $this->table(['id', 'query', 'time'], $this->table);

        $this->line("\nTotal queries: " . count($this->queries));
        $this->line('Total time: ' . $this->time . ' sec');

        $this->line("\nTop 10 worst performing queries");
        $this->table(['id','query', 'time (ms)'], collect($this->table)->sortByDesc('time')->slice(0,10));

        if($id = $this->ask('Enter query ID to see full query')) {
            $query = collect($this->queries)->firstWhere('id', '=', $id);

            $this->line('Time: ' . $query['time']);
            $this->line('Query: ' . $query['query']);
        }
    }

    protected function getQueries(Callable $callback){
        $this->queries = [];

        DB::enableQueryLog();
        $callback();
        $logs = DB::getQueryLog();
        DB::disableQueryLog();

         $queries = [];

         foreach($logs as $key => $log){
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
     * Track execution time
     */
    protected function track(Callable $callback){
        $start = microtime(true);
        $callback();

        return (microtime(true) - $start);
    }
}
