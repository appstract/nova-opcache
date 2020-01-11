<?php

namespace Appstract\NovaOpcache\Http\Controllers;

use Appstract\Opcache\OpcacheFacade as OPcache;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class OpcacheController.
 */
class NovaOpcacheController extends BaseController
{
    /**
     * Clear the OPcache.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear()
    {
        return response()->json(OPcache::clear());
    }

    /**
     * Get config values.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function config()
    {
        return response()->json(OPcache::getConfig());
    }

    /**
     * Get status info.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function status()
    {
        return response()->json(OPcache::getStatus());
    }

    /**
     * Compile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function compile()
    {
        return response()->json(OPcache::compile(true));
    }
}
