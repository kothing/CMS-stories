<?php

namespace Botble\ACL\Events;

use Botble\ACL\Models\Role;
use Botble\ACL\Models\User;
use Botble\Base\Events\Event;
use Illuminate\Queue\SerializesModels;

class RoleAssignmentEvent extends Event
{
    use SerializesModels;

    public Role $role;

    public User $user;

    public function __construct(Role $role, User $user)
    {
        $this->role = $role;
        $this->user = $user;
    }
}
