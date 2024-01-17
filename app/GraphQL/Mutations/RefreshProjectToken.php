<?php

namespace App\GraphQL\Mutations;

use App\Contracts\Crm;

final class RefreshProjectToken
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        app(Crm::class)->updateTokens([$args['id']]);

        return app(\App\Repositories\Project::class)->find($args['id']);
    }
}
