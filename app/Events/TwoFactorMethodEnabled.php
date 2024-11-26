<?php

namespace App\Events;

use App\Models\TwoFactorMethod;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TwoFactorMethodEnabled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public TwoFactorMethod $method, public Model $model) {}
}
