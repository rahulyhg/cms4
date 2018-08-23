<?php

namespace Botble\Base\Traits;

use Auth;
use Botble\ACL\Models\User;
use Botble\ACL\Repositories\Interfaces\RoleInterface;
use Botble\ACL\Repositories\Interfaces\UserInterface;

trait PrepareTestDataTrait
{
    /**
     * @var User
     */
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = $this->prepareAuthorize();
    }

    /**
     * @return mixed
     */
    protected function prepareAuthorize()
    {
        $user = app(UserInterface::class)
            ->getModel()
            ->firstOrCreate(
                ['email' => 'john.smith@gmail.com'],
                [
                    'email' => 'john.smith@gmail.com',
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'username' => 'john.smith',
                    'password' => bcrypt('123456789'),
                    'super_user' => 1,
                    'manage_supers' => 1,
                ]
            );

        acl_activate_user($user);

        app(RoleInterface::class)
            ->getModel()
            ->firstOrCreate(
                [
                    'slug' => 'administrators',
                ],
                [
                    'name' => 'Administrators',
                    'slug' => 'administrators',
                    'description' => 'Highest role in the system',
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                    'is_default' => 1,
                ]
            );

        return $user;
    }
}