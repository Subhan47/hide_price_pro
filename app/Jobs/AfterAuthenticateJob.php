<?php

namespace App\Jobs;

use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AfterAuthenticateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param $shopDomain
     */
    protected  $shopDomain;
    public function __construct($shopDomain)
    {
        $this->shopDomain = $shopDomain;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $shopifyApi = $this->shopDomain->api()->rest('GET', '/admin/shop.json');
        $domain = $shopifyApi['body']['shop']['domain'];
        $name = $shopifyApi['body']['shop']['name'];
        $email = $shopifyApi['body']['shop']['email'];

        if (!$this->hasEmailAlreadySent($domain)) {
            dispatch(new SendWelcomeEmail([
                'name' => $name, 'email' => $email
            ]));

            $this->markEmailAsSent($domain);
        }
    }

    public function hasEmailAlreadySent($domain){
        return User::where('name', $domain)->where('email_sent', true)->exists();
    }

    public function markEmailAsSent($domain){
        User::where('name', $domain)->update(['email_sent' => true]);
    }
}
