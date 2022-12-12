<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\UserRule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function check(string $module)
    {
        return UserRule::join('rules', 'user_rules.rule_id', 'rules.id')
            ->where('user_rules.user_id', auth()->id())
            ->where('key',$module)
            ->where("rules.active", true)
            ->count() > 0;
    }
}
