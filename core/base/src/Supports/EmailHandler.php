<?php

namespace Botble\Base\Supports;

use Botble\Base\Events\SendMailEvent;
use Carbon\Carbon;
use Exception;
use MailVariable;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;
use URL;

class EmailHandler
{

    /**
     * @param $view
     * @author Sang Nguyen
     */
    public function setEmailTemplate($view)
    {
        config()->set('core.base.general.email_template', $view);
    }

    /**
     * @param $content
     * @param $title
     * @param $args
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function send($content, $title, $args = [])
    {
        try {

            $args['to'] = array_get($args, 'to', setting('email_from_address', setting('admin_email')));
            $args['name'] = array_get($args, 'to', setting('email_from_name'));

            $content = MailVariable::prepareData($content);
            $title = MailVariable::prepareData($title);

            event(new SendMailEvent($content, $title, $args));
        } catch (Exception $ex) {
            info($ex->getMessage());
            $this->sendErrorException($ex);
        }
    }

    /**
     * Sends an email to the developer about the exception.
     *
     * @param  \Exception $exception
     * @return void
     * @author Sang Nguyen
     * @throws \Throwable
     */
    public function sendErrorException(Exception $exception)
    {
        try {
            $ex = FlattenException::create($exception);

            $handler = new SymfonyExceptionHandler();

            $url = URL::full();
            $error = $handler->getContent($ex);

            EmailHandler::send(
                view('core.base::emails.error-reporting', compact('url', 'ex', 'error'))->render(),
                $exception->getFile(),
                [
                    'to' => !empty(config('core.base.general.error_reporting.to')) ? config('core.base.general.error_reporting.to') : setting('admin_email'),
                    'name' => setting('site_title'),
                ]
            );
        } catch (Exception $ex) {
            info($ex->getMessage());
        }
    }
}
