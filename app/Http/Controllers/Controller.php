<?php

namespace App\Http\Controllers;

use App\Jobs\ExampleJob;
use App\Models\ExampleModel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function webIndex()
    {
        return view('welcome');
    }

    public function webPhpInfo()
    {
        phpinfo();
    }

    public function webOpcache()
    {
        dd(opcache_get_status());
    }

    public function webConfig()
    {
        dd(config());
    }

    public function webWorker()
    {
        ExampleJob::dispatch();
        return 'queued';
    }

    public function webModels()
    {
        return view('models', [
            'models' => ExampleModel::latest()->paginate(15)
        ]);
    }

    public function apiIndex()
    {
        return response()->json([
            'build' => env('BUILD_VERSION', '-'),
        ]);
    }

    public function apiPost(Request $request)
    {
        $request->validate([
            'title' => 'bail|required|max:255',
        ]);

        return response()->json([
            'title' => $request->input('title'),
        ]);
    }
}
