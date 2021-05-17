<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class GenerateTestingData extends Command
{
    use ConfirmableTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate test data for the api!';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        if (!$this->confirmToProceed()) {
            return 1;
        }


        User::query()->delete();
        Article::query()->delete();
        Category::query()->delete();

       $user= User::factory()->hasArticles(1)->create([
            'name'=>'leandro',
            'email'=> 'leandrofernando739@gmail.com'
        ]);

        $articles = Article::factory()->count(14)->create();

        $this->line("");
        $this->info('User UUID');
        $this->line($user->id);
        $this->line("");
        $this->info('Token');
        $this->line($user->createToken('jorge')->plainTextToken);
        $this->line("");
        $this->info("Articles Id");
        $this->line($user->articles->first()->slug);
        $this->line("");
        $this->info("Category Id");

        $this->line($articles->first()->category->id);



    }
}
