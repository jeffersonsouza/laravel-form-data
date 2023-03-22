<?php

namespace AliReaza\Laravel\Request\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Request;
use AliReaza\Component\HttpFoundation\Request\FormData as FormDataHandler;
use Illuminate\Http\UploadedFile;

class FormData
{
    private array $disallowMethods = [
        Request::METHOD_GET,
        Request::METHOD_HEAD,
        Request::METHOD_POST,
    ];

    public function handle($request, Closure $next)
    {
        if ($request instanceof Request) {
            if (!in_array($request->getRealMethod(), $this->disallowMethods)) {
                $headers = $request->headers;
                $contentType = $headers->get('content-type');

                if (preg_match('/multipart\/form-data/', $contentType)) {
                    $content = $request->getContent();

                    $static = new FormDataHandler($content);

                    $request->request->add($static->inputs);

                    foreach ($static->files as $key => $file) {
                        $file = new UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['error'], true);
                        $request->files->set($key, $file);
                    }
                }
            }
        }

        return $next($request);
    }
}
