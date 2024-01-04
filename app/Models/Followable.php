<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait Followable
{
    // following relationship.
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'user_id', 'following_user_id')->withTimestamps();
    }

    // followers relationship.
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_user_id', 'user_id')->withTimestamps();
    }

    // make auth user follow other users.
    public function follow(User $user): Model
    {
        return $this->following()->save($user);
    }

    public function unfollow(User $user): int
    {
        return $this->following()->detach($user);
    }

    // check if the auth user is following the passed user or not.
    public function isFollowing(User $user): bool
    {
        // this will make a load on the performance if the 'following' collection has a lot of records.
        // return $this->following->contains($user);

        return $this->following()
            ->where('following_user_id', $user->id)
            ->exists();
    }

    // get all friends of the auth user.
    public function follows()
    {
        return currentUser()->followers->merge(currentUser()->following);
    }
}
