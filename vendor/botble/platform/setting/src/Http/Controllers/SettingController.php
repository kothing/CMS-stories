<?php

namespace Botble\Setting\Http\Controllers;

use Assets;
use BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Core;
use Botble\Base\Supports\Language;
use Botble\Media\Repositories\Interfaces\MediaFileInterface;
use Botble\Setting\Http\Requests\EmailTemplateRequest;
use Botble\Setting\Http\Requests\LicenseSettingRequest;
use Botble\Setting\Http\Requests\MediaSettingRequest;
use Botble\Setting\Http\Requests\ResetEmailTemplateRequest;
use Botble\Setting\Http\Requests\SendTestEmailRequest;
use Botble\Setting\Http\Requests\SettingRequest;
use Botble\Setting\Repositories\Interfaces\SettingInterface;
use Carbon\Carbon;
use EmailHandler;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use RvMedia;
use Throwable;

class SettingController extends BaseController
{
    protected SettingInterface $settingRepository;

    public function __construct(SettingInterface $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    public function getOptions()
    {
        page_title()->setTitle(trans('core/setting::setting.title'));

        Assets::addScriptsDirectly('vendor/core/core/setting/js/setting.js')
            ->addStylesDirectly('vendor/core/core/setting/css/setting.css')
            ->usingVueJS();

        return view('core/setting::index');
    }

    public function postEdit(SettingRequest $request, BaseHttpResponse $response)
    {
        $this->saveSettings(
            $request->except([
                '_token',
                'locale',
                'default_admin_theme',
                'admin_locale_direction',
            ])
        );

        $locale = $request->input('locale');
        if ($locale && array_key_exists($locale, Language::getAvailableLocales())) {
            session()->put('site-locale', $locale);
        }

        $isDemoModeEnabled = app()->environment('demo');

        if (! $isDemoModeEnabled) {
            setting()->set('locale', $locale)->save();
        }

        $adminTheme = $request->input('default_admin_theme');
        if ($adminTheme != setting('default_admin_theme')) {
            session()->put('admin-theme', $adminTheme);
        }

        if (! $isDemoModeEnabled) {
            setting()->set('default_admin_theme', $adminTheme)->save();
        }

        $adminLocalDirection = $request->input('admin_locale_direction');
        if ($adminLocalDirection != setting('admin_locale_direction')) {
            session()->put('admin_locale_direction', $adminLocalDirection);
        }

        if (! $isDemoModeEnabled) {
            setting()->set('admin_locale_direction', $adminLocalDirection)->save();
        }

        return $response
            ->setPreviousUrl(route('settings.options'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    protected function saveSettings(array $data): void
    {
        foreach ($data as $settingKey => $settingValue) {
            if (is_array($settingValue)) {
                $settingValue = json_encode(array_filter($settingValue));
            }

            setting()->set($settingKey, (string)$settingValue);
        }

        setting()->save();
    }

    public function getEmailConfig()
    {
        page_title()->setTitle(trans('core/base::layouts.setting_email'));

        Assets::addScriptsDirectly('vendor/core/core/setting/js/setting.js')
            ->addStylesDirectly('vendor/core/core/setting/css/setting.css');

        return view('core/setting::email');
    }

    public function postEditEmailConfig(SettingRequest $request, BaseHttpResponse $response)
    {
        $this->saveSettings($request->except(['_token']));

        return $response
            ->setPreviousUrl(route('settings.email'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function getEditEmailTemplate(string $type, string $module, string $template)
    {
        page_title()->setTitle(trans(config($type . '.' . $module . '.email.templates.' . $template . '.title', '')));

        Assets::addStylesDirectly([
            'vendor/core/core/base/libraries/codemirror/lib/codemirror.css',
            'vendor/core/core/base/libraries/codemirror/addon/hint/show-hint.css',
            'vendor/core/core/setting/css/setting.css',
        ])
            ->addScriptsDirectly([
                'vendor/core/core/base/libraries/codemirror/lib/codemirror.js',
                'vendor/core/core/base/libraries/codemirror/lib/css.js',
                'vendor/core/core/base/libraries/codemirror/addon/hint/show-hint.js',
                'vendor/core/core/base/libraries/codemirror/addon/hint/anyword-hint.js',
                'vendor/core/core/base/libraries/codemirror/addon/hint/css-hint.js',
                'vendor/core/core/setting/js/setting.js',
            ]);

        $emailContent = get_setting_email_template_content($type, $module, $template);
        $emailSubject = get_setting_email_subject($type, $module, $template);
        $pluginData = [
            'type' => $type,
            'name' => $module,
            'template_file' => $template,
        ];

        return view('core/setting::email-template-edit', compact('emailContent', 'emailSubject', 'pluginData'));
    }

    public function postStoreEmailTemplate(EmailTemplateRequest $request, BaseHttpResponse $response)
    {
        if ($request->has('email_subject_key')) {
            setting()
                ->set($request->input('email_subject_key'), $request->input('email_subject'))
                ->save();
        }

        $templatePath = get_setting_email_template_path($request->input('module'), $request->input('template_file'));

        BaseHelper::saveFileData($templatePath, $request->input('email_content'), false);

        return $response->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function postResetToDefault(ResetEmailTemplateRequest $request, BaseHttpResponse $response)
    {
        $this->settingRepository->deleteBy(['key' => $request->input('email_subject_key')]);

        $templatePath = get_setting_email_template_path($request->input('module'), $request->input('template_file'));

        File::delete($templatePath);

        return $response->setMessage(trans('core/setting::setting.email.reset_success'));
    }

    public function postChangeEmailStatus(Request $request, BaseHttpResponse $response)
    {
        setting()
            ->set($request->input('key'), $request->input('value'))
            ->save();

        return $response->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function postSendTestEmail(BaseHttpResponse $response, SendTestEmailRequest $request)
    {
        try {
            EmailHandler::send(
                file_get_contents(core_path('setting/resources/email-templates/test.tpl')),
                'Test',
                $request->input('email'),
                [],
                true
            );

            return $response->setMessage(trans('core/setting::setting.test_email_send_success'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getMediaSetting()
    {
        page_title()->setTitle(trans('core/setting::setting.media.title'));

        Assets::addScriptsDirectly('vendor/core/core/setting/js/setting.js')
            ->addStylesDirectly('vendor/core/core/setting/css/setting.css');

        return view('core/setting::media');
    }

    public function postEditMediaSetting(MediaSettingRequest $request, BaseHttpResponse $response)
    {
        $this->saveSettings($request->except(['_token']));

        return $response
            ->setPreviousUrl(route('settings.media'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function getVerifyLicense(Core $coreApi, BaseHttpResponse $response)
    {
        if (! File::exists(storage_path('.license'))) {
            return $response->setError()->setMessage('Your license is invalid. Please activate your license!');
        }

        try {
            $result = $coreApi->verifyLicense(true);

            if (! $result['status']) {
                return $response->setError()->setMessage($result['message']);
            }

            $activatedAt = Carbon::createFromTimestamp(filectime($coreApi->getLicenseFilePath()));
        } catch (Throwable $exception) {
            $activatedAt = Carbon::now();
            $result = ['message' => $exception->getMessage()];
        }

        $data = [
            'activated_at' => $activatedAt->format('M d Y'),
            'licensed_to' => setting('licensed_to'),
        ];

        return $response->setMessage($result['message'])->setData($data);
    }

    public function activateLicense(LicenseSettingRequest $request, BaseHttpResponse $response, Core $coreApi)
    {
        $buyer = $request->input('buyer');
        if (filter_var($buyer, FILTER_VALIDATE_URL)) {
            $buyer = explode('/', $buyer);
            $username = end($buyer);

            return $response
                ->setError()
                ->setMessage('Envato username must not a URL. Please try with username "' . $username . '"!');
        }

        try {
            $purchaseCode = $request->input('purchase_code');

            $result = $coreApi->activateLicense($purchaseCode, $buyer);

            $resetLicense = false;
            if (! $result['status']) {
                if (str_contains($result['message'], 'License is already active')) {
                    $coreApi->deactivateLicense($purchaseCode, $buyer);

                    $result = $coreApi->activateLicense($purchaseCode, $buyer);

                    if (! $result['status']) {
                        return $response->setError()->setMessage($result['message']);
                    }

                    $resetLicense = true;
                } else {
                    return $response->setError()->setMessage($result['message']);
                }
            }

            setting()
                ->set(['licensed_to' => $request->input('buyer')])
                ->save();

            $activatedAt = Carbon::createFromTimestamp(filectime($coreApi->getLicenseFilePath()));

            $data = [
                'activated_at' => $activatedAt->format('M d Y'),
                'licensed_to' => $request->input('buyer'),
            ];

            if ($resetLicense) {
                return $response->setMessage(
                    $result['message'] . ' Your license on the previous domain has been revoked!'
                )->setData($data);
            }

            return $response->setMessage($result['message'])->setData($data);
        } catch (Throwable $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function deactivateLicense(BaseHttpResponse $response, Core $coreApi)
    {
        try {
            $result = $coreApi->deactivateLicense();

            if (! $result['status']) {
                return $response->setError()->setMessage($result['message']);
            }

            $this->settingRepository->deleteBy(['key' => 'licensed_to']);

            return $response->setMessage($result['message']);
        } catch (Throwable $exception) {
            return $response->setError()->setMessage($exception->getMessage());
        }
    }

    public function resetLicense(LicenseSettingRequest $request, BaseHttpResponse $response, Core $coreApi)
    {
        try {
            $result = $coreApi->deactivateLicense($request->input('purchase_code'), $request->input('buyer'));

            if (! $result['status']) {
                return $response->setError()->setMessage($result['message']);
            }

            $this->settingRepository->deleteBy(['key' => 'licensed_to']);

            return $response->setMessage($result['message']);
        } catch (Throwable $exception) {
            return $response->setError()->setMessage($exception->getMessage());
        }
    }

    public function generateThumbnails(MediaFileInterface $fileRepository, BaseHttpResponse $response)
    {
        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        $files = $fileRepository->allBy([], [], ['url', 'mime_type', 'folder_id']);

        $errors = [];

        foreach ($files as $file) {
            try {
                RvMedia::generateThumbnails($file);
            } catch (Exception) {
                $errors[] = $file->url;
            }
        }

        $errors = array_unique($errors);

        $errors = array_map(function ($item) {
            return [$item];
        }, $errors);

        if ($errors) {
            return $response
                ->setError()
                ->setMessage(trans('core/setting::setting.generate_thumbnails_error', ['count' => count($errors)]));
        }

        return $response->setMessage(trans('core/setting::setting.generate_thumbnails_success', ['count' => count($files)]));
    }

    public function previewEmailTemplate(Request $request, string $type, string $module, string $template)
    {
        $emailHandler = EmailHandler::setModule($module)
            ->setType($type)
            ->setTemplate($template);

        $variables = $emailHandler->getVariables($type, $module, $template);

        $coreVariables = $emailHandler->getCoreVariables();

        Arr::forget($variables, array_keys($coreVariables));

        $inputData = $request->only(array_keys($variables));

        if (! empty($inputData)) {
            foreach ($inputData as $key => $value) {
                $inputData[BaseHelper::stringify($key)] = BaseHelper::clean(BaseHelper::stringify($value));
            }
        }

        $routeParams = [$type, $module, $template];

        $backUrl = route('setting.email.template.edit', $routeParams);

        $iframeUrl = route('setting.email.preview.iframe', $routeParams);

        return view(
            'core/setting::preview-email',
            compact('variables', 'inputData', 'backUrl', 'iframeUrl')
        );
    }

    public function previewEmailTemplateIframe(Request $request, string $type, string $module, string $template)
    {
        $emailHandler = EmailHandler::setModule($module)
            ->setType($type)
            ->setTemplate($template);

        $variables = $emailHandler->getVariables($type, $module, $template);

        $coreVariables = $emailHandler->getCoreVariables();

        Arr::forget($variables, array_keys($coreVariables));

        $inputData = $request->only(array_keys($variables));

        foreach ($variables as $key => $variable) {
            if (! isset($inputData[$key])) {
                $inputData[$key] = '{{ ' . $key . ' }}';
            } else {
                $inputData[$key] = BaseHelper::clean(BaseHelper::stringify($inputData[$key]));
            }
        }

        $emailHandler->setVariableValues($inputData);

        $content = get_setting_email_template_content($type, $module, $template);

        $content = $emailHandler->prepareData($content);

        return BaseHelper::clean($content);
    }
}
