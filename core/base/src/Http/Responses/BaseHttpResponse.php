<?php

namespace Botble\Base\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use URL;

class BaseHttpResponse implements Responsable
{
    /**
     * @var bool
     */
    protected $error = false;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var null
     */
    protected $previous_url = null;

    /**
     * @var null
     */
    protected $next_url = null;

    /**
     * @var bool
     */
    protected $with_input = false;

    /**
     * @var int
     */
    protected $code = 200;

    /**
     * @param $error
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function setError($error): self
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @param $message
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function setMessage($message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param $data
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function setData($data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param null $previous_url
     * @return BaseHttpResponse
     */
    public function setPreviousUrl($previous_url): self
    {
        $this->previous_url = $previous_url;
        return $this;
    }

    /**
     * @param null $next_url
     * @return BaseHttpResponse
     */
    public function setNextUrl($next_url): self
    {
        $this->next_url = $next_url;
        return $this;
    }

    /**
     * @param bool $with_input
     * @return BaseHttpResponse
     */
    public function withInput(bool $with_input): self
    {
        $this->with_input = $with_input;
        return $this;
    }

    /**
     * @param int $code
     * @return BaseHttpResponse
     */
    public function setCode(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->error;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return BaseHttpResponse|\Illuminate\Http\RedirectResponse
     * @author Sang Nguyen
     */
    public function toResponse($request)
    {
        if ($request->expectsJson()) {
            return response()
                ->json([
                    'error' => $this->error,
                    'data' => $this->data,
                    'message' => $this->message,
                ]);
        }

        if ($request->input('submit') === 'save' && !empty($this->previous_url)) {
            return $this->responseRedirect($this->previous_url);
        } elseif (!empty($this->next_url)) {
            return $this->responseRedirect($this->next_url);
        }

        return $this->responseRedirect(URL::previous());
    }

    /**
     * @param $url
     * @return \Illuminate\Http\RedirectResponse
     * @author Sang Nguyen
     */
    protected function responseRedirect($url)
    {
        if ($this->with_input) {
            return redirect()
                ->to($url)
                ->with($this->error ? 'error_msg' : 'success_msg', $this->message)
                ->withInput();
        }

        return redirect()
            ->to($url)
            ->with($this->error ? 'error_msg' : 'success_msg', $this->message);
    }
}
