<?php

namespace API\AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\FlattenException as HttpFlattenException;
use Symfony\Component\Debug\Exception\FlattenException as DebugFlattenException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;

/**
 * Custom ExceptionController that uses the view layer and supports HTTP response status code mapping
 */
class ExceptionController extends ContainerAware
{

    /**
     * Converts an Exception to a Response.
     *
     * @param Request                                    $request
     * @param HttpFlattenException|DebugFlattenException $exception
     * @param DebugLoggerInterface                       $logger
     *
     * @return Response
     *
     * @throws \InvalidArgumentException
     */
    public function showAction(Request $request, $exception, DebugLoggerInterface $logger = null)
    {

        $format = $this->getFormat($request, $request->getRequestFormat());
        if (null === $format) {
            $message = 'Nenhum formato para resposta foi deternimado: ';
            $message .= $this->getExceptionMessage($exception);

            return new Response($message, Codes::HTTP_NOT_ACCEPTABLE, $exception->getHeaders());
        }

        $currentContent = $this->getAndCleanOutputBuffering();
        $code = $this->getStatusCode($exception);
        $viewHandler = $this->container->get('fos_rest.view_handler');
        $parameters = $this->getParameters($viewHandler, $currentContent, $code, $exception, $logger, $format);

        try {
            //Monta View para retorno
            $view = View::create($parameters, $code, $exception->getHeaders());
            $view->setFormat($format);

            if ($viewHandler->isFormatTemplating($format)) {
                $view->setTemplate($this->findTemplate($request, $format, $code, $this->container->get('kernel')->isDebug()));
            }
            $response = $viewHandler->handle($view);
        } catch (\Exception $e) {
            //Caso de algum erro não esperado
            $message = 'Ocorreu um erro inesperado no sistema.';
            $message .= $this->getExceptionMessage($exception);
            $response = new Response($message, Codes::HTTP_INTERNAL_SERVER_ERROR, $exception->getHeaders());
        }

        return $response;
    }

    /**
     * Gets and cleans any content that was already outputted.
     *
     * This code comes from Symfony and should be synchronized on a regular basis
     * see src/Symfony/Bundle/TwigBundle/Controller/ExceptionController.php
     *
     * @return string
     */
    protected function getAndCleanOutputBuffering()
    {
        $startObLevel = $this->container->get('request')->headers->get('X-Php-Ob-Level', -1);

        // ob_get_level() never returns 0 on some Windows configurations, so if
        // the level is the same two times in a row, the loop should be stopped.
        $previousObLevel = null;
        $currentContent = '';

        while (($obLevel = ob_get_level()) > $startObLevel && $obLevel !== $previousObLevel) {
            $previousObLevel = $obLevel;
            $currentContent .= ob_get_clean();
        }

        return $currentContent;
    }

    /**
     * Extracts the exception message.
     *
     * @param HttpFlattenException|DebugFlattenException $exception
     * @param array                                      $exceptionMap
     *
     * @return int|false
     */
    protected function isSubclassOf($exception, $exceptionMap)
    {
        $exceptionClass = $exception->getClass();
        $reflectionExceptionClass = new \ReflectionClass($exceptionClass);
        try {
            foreach ($exceptionMap as $exceptionMapClass => $value) {
                if ($value
                    && ($exceptionClass === $exceptionMapClass || $reflectionExceptionClass->isSubclassOf($exceptionMapClass))
                ) {
                    return $value;
                }
            }
        } catch (\ReflectionException $re) {
            return "FOSUserBundle: Invalid class in  fos_res.exception.messages: "
                    .$re->getMessage();
        }

        return false;
    }

    /**
     * Extracts the exception message.
     *
     * @param HttpFlattenException|DebugFlattenException $exception
     *
     * @return string Message
     */
    protected function getExceptionMessage($exception)
    {
        $exceptionMap = $this->container->getParameter('fos_rest.exception.messages');
        $showExceptionMessage = $this->isSubclassOf($exception, $exceptionMap);

        if ($showExceptionMessage || $this->container->get('kernel')->isDebug()) {
            return $exception->getMessage();
        }

        $statusCode = $this->getStatusCode($exception);

        return array_key_exists($statusCode, Response::$statusTexts) ? Response::$statusTexts[$statusCode] : 'error';
    }

    /**
     * Determines the status code to use for the response.
     *
     * @param HttpFlattenException|DebugFlattenException $exception
     *
     * @return int
     */
    protected function getStatusCode($exception)
    {
        $exceptionMap = $this->container->getParameter('fos_rest.exception.codes');
        $isExceptionMappedToStatusCode = $this->isSubclassOf($exception, $exceptionMap);

        return $isExceptionMappedToStatusCode ?: $exception->getStatusCode();
    }

    /**
     * Determines the format to use for the response.
     *
     * @param Request $request
     * @param string  $format
     *
     * @return string
     */
    protected function getFormat(Request $request, $format)
    {
        try {
            $formatNegotiator = $this->container->get('fos_rest.exception_format_negotiator');
            $format = $formatNegotiator->getBestFormat($request) ?: $format;
            $request->attributes->set('_format', $format);
        } catch (StopFormatListenerException $e) {
            $format = $request->getRequestFormat();
        }

        return $format;
    }

    /**
     * Determines the parameters to pass to the view layer.
     *
     * Overwrite it in a custom ExceptionController class to add additionally parameters
     * that should be passed to the view layer.
     *
     * @param ViewHandler                                $viewHandler
     * @param string                                     $currentContent
     * @param int                                        $code
     * @param HttpFlattenException|DebugFlattenException $exception
     * @param DebugLoggerInterface                       $logger
     * @param string                                     $format
     *
     * @return array
     */
    protected function getParameters(ViewHandler $viewHandler, $currentContent, $code, $exception, DebugLoggerInterface $logger = null, $format = 'html')
    {

        //Monta array para retorno customizado
        $parameters  = array(
            'status' => $code,
        );

        switch ($code) {
            case 412:
                $parameters['mensagem'] = "Dados do formulário inválidos";
                $parameters['detalhes'] = json_decode($exception->getMessage(), true);
                break;
            case 500:
                $parameters['mensagem'] = "Erro desconhecido.";
                break;
            default:
                $parameters['mensagem'] = $exception->getMessage();
        }

        //Verifica se está em debug mode para retornar stack trace
        if ($this->container->get('kernel')->isDebug()) {
            $parameters['exception'] = $exception;
            $parameters['currentContent'] = $currentContent;
            $parameters['status_text'] = array_key_exists($code, Response::$statusTexts) ? Response::$statusTexts[$code] : "error";
        }

        if ($viewHandler->isFormatTemplating($format)) {
            $parameters['logger'] = $logger;
        }

        return $parameters;
    }

    /**
     * Finds the template for the given format and status code.
     *
     * Note this method needs to be overridden in case another
     * engine than Twig should be supported;
     *
     * This code is inspired by TwigBundle and should be synchronized on a regular basis
     * see src/Symfony/Bundle/TwigBundle/Controller/ExceptionController.php
     *
     * @param Request $request
     * @param string  $format
     * @param int     $statusCode
     * @param bool    $debug
     *
     * @return TemplateReference
     */
    protected function findTemplate(Request $request, $format, $statusCode, $debug)
    {
        $name = $debug ? 'exception' : 'error';
        if ($debug && 'html' == $format) {
            $name = 'exception_full';
        }

        // when not in debug, try to find a template for the specific HTTP status code and format
        if (!$debug) {
            $template = new TemplateReference('TwigBundle', 'Exception', $name.$statusCode, $format, 'twig');
            if ($this->container->get('templating')->exists($template)) {
                return $template;
            }
        }

        // try to find a template for the given format
        $template = new TemplateReference('TwigBundle', 'Exception', $name, $format, 'twig');
        if ($this->container->get('templating')->exists($template)) {
            return $template;
        }

        // default to a generic HTML exception
        $request->setRequestFormat('html');

        return new TemplateReference('TwigBundle', 'Exception', $name, 'html', 'twig');
    }
}
