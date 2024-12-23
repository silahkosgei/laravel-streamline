<?php

namespace Iankibet\Streamline\Features\Streamline;

use App\Http\Controllers\Controller;
use Iankibet\Streamline\Attributes\Permission;
use Iankibet\Streamline\Attributes\Validate;
use Iankibet\Streamline\Component;
use Iankibet\Streamline\Features\Support\StreamlineSupport;
use Iankibet\Streamline\Stream;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Str;

class HandleStreamlineRequest extends Controller implements HasMiddleware
{

    protected $classReflection;

    public function handleRequest(Request $request)
    {
        $middleware = config('streamline.middleware', []);
        $this->middleware($middleware);
        $this->validateRequest($request);

        $class = StreamlineSupport::convertStreamToClass($request->input('stream'));

        $guestClasses = config('streamline.guest_classes', []);
        if (in_array($class, $guestClasses)) {
            $this->middleware('guest');
        }

        if (!class_exists($class)) {
            $error = 'Stream class not found';
            if (app()->environment('local')) {
                $error .= ' - ' . $class;
            }
            abort(404, $error);
        }

        $action = $request->input('action','onMounted');
        $params = $request->input('params', []);
        $constructorParams = [];
        if(!$action || $action == 'onMounted'){
            $constructorParams = $params;
        }
        $instance = new $class(...$constructorParams);
        $instance->setAction($action);
        $requestData = $request->all();
        // remove action and params from request data
        unset($requestData['action']);
        unset($requestData['params']);
        $instance->setRequestData($requestData);
        if (!method_exists($instance, $action)) {
            abort(404, 'Action not found');
        }
        $this->classReflection = new \ReflectionClass($instance);
        return $this->invokeAction($instance, $action, $params);
    }

    protected function validateRequest(Request $request)
    {
        $request->validate([
            'stream' => 'required|string',
            'action' => 'nullable|string',
            'params' => ''// this is optional,
        ]);
        if ($request->has('params')) {
            $params = $request->input('params');
            if (!is_array($params)) {
                $params = explode(',', $params);
                $request->merge(['params' => $params]);
            }
        }
    }

    protected function invokeAction($instance, string $action, array $params)
    {
        // Check if the required parameters are provided, Reflection is slow so only do this in local environment for debugging
        $reflection = new \ReflectionMethod($instance, $action);
        $requiredParams = $reflection->getNumberOfRequiredParameters();
        if (count($params) < $requiredParams) {
            $missingParams = array_diff(
                array_map(fn($param) => $param->getName(), $reflection->getParameters()),
                array_keys($params)
            );

            abort(400, 'Missing required parameters: ' . implode(', ', $missingParams));
        }
        // check if instance implements StreamlineComponent
        if (!$instance instanceof Component && !$instance instanceof Stream) {
            abort(404, 'Streamline class must extend Iankibet\Streamline\Stream');
        }
        // check if action has Validate attribute

        $reflectionClass = $this->classReflection;
        $classAttributes = $reflectionClass->getAttributes(Permission::class);
        // check attributes for permission on the action
        $attributes = $reflection->getAttributes(Permission::class);
        $attributes = array_merge($attributes, $classAttributes);
        if (count($attributes) > 0) {
            foreach ($attributes as $attribute) {
                $permissionInstance = $attribute->newInstance();
                $permissionSlugs = $permissionInstance->getPermissions();
                foreach ($permissionSlugs as $permissionSlug) {
                    $user = \request()->user();
                    if (!$user || !$user->can($permissionSlug)) {
                        abort(403, 'Unauthorized: ' . $permissionSlug);
                    }
                }
            }
        }
        $validateAttributes = $reflection->getAttributes(Validate::class);
        if (count($validateAttributes) > 0) {
            $instance->validate();
        }
        return $instance->$action(...array_values($params));
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        $stream = \request('stream');
        $guestStream = config('streamline.guest_streams', []);
        if (in_array($stream, $guestStream)) {
            return ['guest'];
        }
        $middleware = config('streamline.middleware', []);
        return $middleware;
    }
}
