<?php

namespace App\Providers;

use Illuminate\Mail\Mailer;
use Illuminate\Support\ServiceProvider;

class MailServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Register the mailer instance
        $this->app->singleton('mailer', function ($app) {
            $this->registerSwiftMailer($app);

            // Create a new mailer instance
            $mailer = new Mailer(
                $app['view'], $app['swift.mailer'], $app['events']
            );

            // Set additional mailer configurations here if needed

            return $mailer;
        });
    }

    /**
     * Register the Swift Mailer instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function registerSwiftMailer($app)
    {
        $app->singleton('swift.mailer', function ($app) {
            return new \Swift_Mailer($app['swift.transport']->driver());
        });
    }
}
