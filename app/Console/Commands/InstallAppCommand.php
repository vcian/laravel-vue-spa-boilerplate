<?php

namespace App\Console\Commands;

use DB;
use File;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Question\Question;

class InstallAppCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installation of Laravel-Vue-Spa-Boilerplate: Laravel setup';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     *
     * @internal param Filesystem $filesystem
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('------------------');
        $this->line('Welcome to Laravel-Vue-Spa-Boilerplate.');
        $this->line('------------------');

        $extensions = get_loaded_extensions();
        $require_extensions = ['mbstring', 'openssl', 'curl', 'exif', 'fileinfo', 'tokenizer'];
        foreach (array_diff($require_extensions, $extensions) as $missing_extension) {
            $this->error('Missing '.ucfirst($missing_extension).' extension');
        }

        if (!file_exists('.env')) {
            File::copy('.env.example', '.env');
        }

        // Set database credentials in .env and migrate
        $this->setDatabaseInfo();
        $this->line('------------------');

        //Key Generate
        Artisan::call('key:generate');
        $this->line('Key generated in .env file!');
    }

    /**
     * Set Database info in .env file.
     *
     * @throws Exception
     *
     * @return void
     *
     * @author Sang Nguyen
     */
    protected function setDatabaseInfo()
    {
        $this->info('Setting up database (please make sure you have created database for this site)...!');

        $this->port = env('DB_PORT');
        $this->database = env('DB_DATABASE');
        $this->username = env('DB_USERNAME');
        $this->password = env('DB_PASSWORD');

        while (!check_database_connection()) {
            // Ask for database details
            $this->port = $this->ask('Enter a database port?', 3306);
            $this->database = $this->ask('Enter a database name', $this->guessDatabaseName());

            $this->username = $this->ask('What is your MySQL username?', 'root');

            $question = new Question('What is your MySQL password?', '<none>');
            $question->setHidden(true)->setHiddenFallback(true);
            $this->password = (new SymfonyQuestionHelper())->ask($this->input, $this->output, $question);
            if ($this->password === '<none>') {
                $this->password = '';
            }

            // Update DB credentials in .env file.
            $contents = $this->getKeyFile();
            $contents = preg_replace('/('.preg_quote('DB_PORT=').')(.*)/', 'DB_PORT='.$this->port, $contents);
            $contents = preg_replace('/('.preg_quote('DB_DATABASE=').')(.*)/', 'DB_DATABASE='.$this->database, $contents);
            $contents = preg_replace('/('.preg_quote('DB_USERNAME=').')(.*)/', 'DB_USERNAME='.$this->username, $contents);
            $contents = preg_replace('/('.preg_quote('DB_PASSWORD=').')(.*)/', 'DB_PASSWORD='.$this->password, $contents);

            if (!$contents) {
                throw new Exception('Error while writing credentials to .env file.');
            }

            // Write to .env
            $this->files->put('.env', $contents);

            // Set DB username and password in config
            $this->laravel['config']['database.connections.mysql.username'] = $this->username;
            $this->laravel['config']['database.connections.mysql.password'] = $this->password;

            // Clear DB name in config
            unset($this->laravel['config']['database.connections.mysql.database']);

            if (!check_database_connection()) {
                $this->error('Can not connect to database, please try again!');
            } else {
                $this->info('Connect to database successfully!');
            }
        }

        if ($this->confirm('You want to dump database sql ?')) {
            if (!empty($this->database)) {
                // Force the new login to be used
                DB::purge();

                // Switch to use {$this->database}
                DB::unprepared('USE `'.$this->database.'`');
                DB::connection()->setDatabaseName($this->database);

                $dumpDB = DB::unprepared(file_get_contents(base_path().'/database/dump/laravel_vue_spa_boilerplate.sql'));

                if ($dumpDB) {
                    $this->info('Import default database successfully!');
                }
            }
        } else {
            if ($this->confirm('You want to migrate tables?')) {
                // Switch to use {$this->database}
                DB::unprepared('USE `'.$this->database.'`');
                //DB::connection()->setDatabaseName($this->database);
                Artisan::call('migrate');
                $this->info('Migration successfully done!');
            }
        }
    }

    /**
     * Guess database name from app folder.
     *
     * @return string
     *
     * @author Sang Nguyen
     */
    protected function guessDatabaseName()
    {
        try {
            $segments = array_reverse(explode(DIRECTORY_SEPARATOR, app_path()));
            $name = explode('.', $segments[1])[0];

            return str_slug($name);
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Get the key file and return its content.
     *
     * @return string
     *
     * @author Sang Nguyen
     */
    protected function getKeyFile()
    {
        return $this->files->exists('.env') ? $this->files->get('.env') : $this->files->get('.env.example');
    }
}
