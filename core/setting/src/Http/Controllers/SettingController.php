<?php

namespace Botble\Setting\Http\Controllers;

use Assets;
use Auth;
use Botble\Setting\Http\Requests\EmailTemplateRequest;
use Botble\Setting\Repositories\Interfaces\SettingInterface;
use EmailHandler;
use Exception;
use Illuminate\Support\Facades\File;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Illuminate\Http\Request;

class SettingController extends BaseController
{
    /**
     * @var SettingInterface
     */
    protected $settingRepository;

    /**
     * SettingController constructor.
     * @param SettingInterface $settingRepository
     */
    public function __construct(SettingInterface $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author Sang Nguyen
     */
    public function getOptions()
    {
        page_title()->setTitle(trans('core.setting::setting.title'));

        return view('core.setting::index');
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function postEdit(Request $request, BaseHttpResponse $response)
    {

        foreach ($request->except(['_token']) as $setting_key => $setting_value) {
            setting()->set($setting_key, $setting_value);
        }

        setting()->save();

        return $response
            ->setPreviousUrl(route('settings.options'))
            ->setMessage(trans('core.base::notices.update_success_message'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getEmailConfig()
    {
        if (!config('core.base.general.allow_config_mail_server_from_admin') && !app()->runningInConsole()) {
            abort(404);
        }
        page_title()->setTitle(trans('core.setting::setting.email_setting_title'));
        Assets::addAppModule(['setting']);

        return view('core.setting::email');
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postEditEmailConfig(Request $request, BaseHttpResponse $response)
    {
        if (!config('core.base.general.allow_config_mail_server_from_admin')) {
            abort(404);
        }

        foreach ($request->except(['_token']) as $setting_key => $setting_value) {
            setting()->set($setting_key, $setting_value);
        }

        setting()->save();

        return $response
            ->setPreviousUrl(route('settings.email'))
            ->setMessage(trans('core.base::notices.update_success_message'));
    }

    /**
     * @param $type
     * @param $name
     * @param $template_file
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getEditEmailTemplate($type, $name, $template_file)
    {
        Assets::addStylesheetsDirectly([
                'vendor/core/packages/codemirror/lib/codemirror.css',
                'vendor/core/packages/codemirror/addon/hint/show-hint.css',
                'vendor/core/css/custom-css.css',
            ])
            ->addJavascriptDirectly([
                'vendor/core/packages/codemirror/lib/codemirror.js',
                'vendor/core/packages/codemirror/lib/css.js',
                'vendor/core/packages/codemirror/addon/hint/show-hint.js',
                'vendor/core/packages/codemirror/addon/hint/anyword-hint.js',
                'vendor/core/packages/codemirror/addon/hint/css-hint.js',
            ])
            ->addAppModule(['setting']);


        $email_content = get_setting_email_template_content($type, $name, $template_file);
        $email_subject = get_setting_email_subject($type, $name, $template_file);
        $plugin_data = [
            'type' => $type,
            'name' => $name,
            'template_file' => $template_file,
        ];

        page_title()->setTitle(trans(config($type . '.' . $name . '.email.templates.' . $template_file . '.title', '')));
        return view('core.setting::email-template-edit', compact('email_subject', 'email_content', 'plugin_data'));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postStoreEmailTemplate(EmailTemplateRequest $request, BaseHttpResponse $response)
    {
        if ($request->has('email_subject_key')) {
            setting()->set($request->input('email_subject_key'), $request->input('email_subject'));
            setting()->save();
        }

        save_file_data($request->input('template_path'), $request->input('email_content'), false);
        return $response->setMessage(trans('core.base::notices.update_success_message'));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postResetToDefault(Request $request, BaseHttpResponse $response)
    {
        $this->settingRepository->deleteBy(['key' => $request->input('email_subject_key')]);
        File::delete($request->input('template_path'));
        return $response->setMessage(trans('core.setting::setting.email.reset_success'));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postChangeEmailStatus(Request $request, BaseHttpResponse $response)
    {
        setting()->set($request->input('key'), $request->input('value'));
        setting()->save();
        return $response->setMessage(trans('core.base::notices.update_success_message'));
    }

    /**
     * @param BaseHttpResponse $response
     * @throws \Throwable
     */
    public function postSendTestEmail(BaseHttpResponse $response)
    {
        try {
            EmailHandler::send('Test send mail', 'Subject test email', ['to' => setting('admin_email'), 'name' => Auth::user()->getFullName()]);
            return $response->setMessage('Send email successfully!');
        } catch (Exception $exception) {
            return $response->setError(true)
                ->setMessage($exception->getMessage());
        }
    }
}
