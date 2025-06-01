<?php

declare(strict_types=1);

namespace App\Actions\v1\LoadOrder;

use App\Models\LoadOrder;

final class DeleteLoadOrder
{
    public function execute(LoadOrder $loadOrder): void
    {
        $loadOrder->delete();
    }
}
