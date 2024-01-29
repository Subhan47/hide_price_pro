<?php

namespace App\Jobs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Osiset\ShopifyApp\Actions\CancelCurrentPlan;
use Osiset\ShopifyApp\Contracts\Commands\Shop as IShopCommand;
use Osiset\ShopifyApp\Contracts\Queries\Shop as IShopQuery;




class AppUninstalledJob extends \Osiset\ShopifyApp\Messaging\Jobs\AppUninstalledJob
{
    public function handle(
        IShopCommand $shopCommand,
        IShopQuery $shopQuery,
        CancelCurrentPlan $cancelCurrentPlanAction
    ): bool {
        $result = parent::handle($shopCommand, $shopQuery, $cancelCurrentPlanAction);
        $shop = $shopQuery->getByDomain($this->domain);

        Log::info('AppUninstalledJob executed successfully.', [
            'result' =>  $this->data,
        ]);

        $data = $this->data;
        dispatch(new SendUninstallEmail([
            'name' => $data->name,
            'email' => $data->email,
        ]));

        return $result;
    }
}
