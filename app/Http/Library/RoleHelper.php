<?php

namespace App\Http\Library;

trait RoleHelper
{
    protected function isMasyarakat($user): bool
    {
        if (!empty($user)) {
            return $user->tokenCan('masyarakat');
        }
        return false;
    }
}
