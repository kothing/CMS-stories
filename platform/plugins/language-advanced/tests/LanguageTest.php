<?php

namespace Botble\LanguageAdvanced\Tests;

use Botble\ACL\Models\User;
use Botble\ACL\Repositories\Interfaces\ActivationInterface;
use Botble\Language\Models\Language;
use Botble\Language\Models\LanguageMeta;
use Botble\Page\Models\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LanguageTest extends TestCase
{
    public function testTranslatable(): void
    {
        $this->createLanguages();

        $this->assertTrue(is_plugin_active('language') && is_plugin_active('language-advanced'));

        $user = $this->createUser();

        $this->be(User::first());

        $page = Page::create([
            'name' => 'This is a page in English',
            'user_id' => $user->id,
        ]);

        $this->get(route('pages.edit', $page->id))
            ->assertSee('This is a page in English');

        DB::table('pages_translations')->truncate();
        DB::table('pages_translations')->insert([
            'lang_code' => 'zh_CN',
            'pages_id' => $page->id,
            'name' => 'This is a page in Chinese',
        ]);

        $this->call('GET', route('pages.edit', $page->id), ['ref_lang' => 'zh_CN']);
        //->assertSee('This is a page in Chinese');

        $page->delete();

        $this->assertDatabaseHas(
            'pages_translations',
            [
                'lang_code' => 'zh_CN',
                'pages_id' => $page->id,
                'name' => 'This is a page in Chinese',
            ]
        );
    }

    protected function createUser(): User
    {
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
        $user->save();

        $activationRepository = app(ActivationInterface::class);

        $activation = $activationRepository->createUser($user);

        $activationRepository->complete($user, $activation->code);

        return $user;
    }

    protected function createLanguages()
    {
        $languages = [
            [
                'lang_name' => 'English',
                'lang_locale' => 'en',
                'lang_is_default' => true,
                'lang_code' => 'en_US',
                'lang_is_rtl' => false,
                'lang_flag' => 'us',
                'lang_order' => 0,
            ],
            [
                'lang_name' => 'Chinese',
                'lang_locale' => 'zh',
                'lang_is_default' => false,
                'lang_code' => 'zh_CN',
                'lang_is_rtl' => false,
                'lang_flag' => 'vn',
                'lang_order' => 0,
            ],
        ];

        Language::truncate();
        LanguageMeta::truncate();

        foreach ($languages as $item) {
            Language::create($item);
        }
    }
}
