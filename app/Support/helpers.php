<?php

function currentUser(): \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable|null
{
    return auth()->user();
}
