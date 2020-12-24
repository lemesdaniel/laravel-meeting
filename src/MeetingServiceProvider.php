<?php

namespace Nncodes\Meeting;

use Illuminate\Support\ServiceProvider;
use Nncodes\Meeting\Commands\MeetingCommand;

class MeetingServiceProvider extends ServiceProvider
{
    /**
     * Undocumented function
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/meeting.php' => config_path('meeting.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../resources/views' => base_path('resources/views/vendor/meeting'),
            ], 'views');

            $migrationFileName = 'create_meetings_table.php';
            if (! $this->migrationFileExists($migrationFileName)) {
                $this->publishes([
                    __DIR__ . "/../database/migrations/{$migrationFileName}.stub" => database_path('migrations/' . date('Y_m_d_His', time()) . '_' . $migrationFileName),
                ], 'migrations');
            }

            $this->commands([
                MeetingCommand::class,
            ]);
        }

        //binds
        foreach(config('meeting.providers', []) as $key => $target){
            $this->app->bind('laravel-meeting:' .$key, $target);
        }
        
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'meeting');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/meeting.php', 'meeting');
    }

    /**
     * Undocumented function
     *
     * @param string $migrationFileName
     * @return bool
     */
    public static function migrationFileExists(string $migrationFileName): bool
    {
        $len = strlen($migrationFileName);
        foreach (glob(database_path("migrations/*.php")) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName)) {
                return true;
            }
        }

        return false;
    }
}
