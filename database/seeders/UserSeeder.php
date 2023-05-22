<?php

namespace Database\Seeders;

use Botble\ACL\Models\User;
use Botble\ACL\Repositories\Interfaces\ActivationInterface;
use Botble\Base\Supports\BaseSeeder;
use MetaBox;
use Schema;

class UserSeeder extends BaseSeeder
{
    public function run(): void
    {
        $files = $this->uploadFiles('users');

        Schema::disableForeignKeyConstraints();

        User::truncate();

        $user = new User();
        $user->first_name = 'Mr';
        $user->last_name = 'Admnistrator';
        $user->email = 'admin@domain.com';
        $user->username = 'admin';
        $user->password = bcrypt('123456');
        $user->super_user = 1;
        $user->manage_supers = 1;
        $user->avatar_id = ! empty($files) ? $files[0]['data']['id'] : null;
        $user->save();

        $activationRepository = app(ActivationInterface::class);

        $activation = $activationRepository->createUser($user);

        MetaBox::saveMetaBoxData($user, 'bio', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi autem blanditiis deleniti inventore porro quidem rem suscipit voluptatibus! Aut illum libero, praesentium quis quod rerum sint? Ducimus iure nulla totam!');

        $activationRepository->complete($user, $activation->code);
    }
}
