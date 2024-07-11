<?php

namespace Anthonygore\Courses\Listeners;

use Statamic\Events\UserRegistered;
use Statamic\Facades\User;

class UserRegistrationListener
{
    public function handle(UserRegistered $event)
    {
        $user = User::find($event->user->id);
        $user->addToGroup('students');
        $user->save();
    }
}
