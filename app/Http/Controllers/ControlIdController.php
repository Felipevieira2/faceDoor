<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ControlIdJobService;
use App\Repositories\Interfaces\ControlIdJobRepositoryInterface;

class ControlIdController extends Controller
{
    private ControlIdJobRepositoryInterface $jobRepository;

    public function __construct(ControlIdJobRepositoryInterface $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }
 
    public function handlePush(Request $request)
    {
        Log::info('handlePush');
        Log::info($request->all());

        $deviceId = $request->input('deviceId');
        $uuid = $request->input('uuid');
        $controlidService = new ControlIdJobService($this->jobRepository);
        $response = $controlidService->processPush($deviceId, $uuid);
     
        return response()->json($response, 200);
    }

    public function handleResult(Request $request)
    {
        $controlidService = new ControlIdJobService($this->jobRepository);
        $response = $controlidService->processResult($request);
   
        Log::info('handlePush response');
        Log::info($response);
        
        return response()->json($response, 200);
    }
}
