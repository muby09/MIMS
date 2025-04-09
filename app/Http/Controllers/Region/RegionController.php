<?php

namespace App\Http\Controllers\Region;

use App\Http\Controllers\Controller;
use App\Models\Region\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegionController extends Controller
{
    //
    public function index(){

        try {
            $data = Region::get();
            return pushData($data,'Regions loaded');
        } catch (\Throwable $e) {
            logError($e);
            return pushData([],'Empty Result');
        }

    }

    public function createRegion(Request $request){
        $validator =  Validator::make($request->all(),[
            'state' => 'required|string',
            'region' => 'required|string',
        ]);

        if(!$validator->fails()){
            try {
                $data = [
                    'state' => $request->state,
                    'region' => $request->region,
                    'pid' => $request->pid ?? public_id(),
                ];
                $result = $this->addOrEditRegion($data);
                return pushResponse($result,$request->pid ? 'Region Updated' :'Region Added');

            } catch (\Throwable $e) {
                logError($e);
                return responseMessage(status: 204, data: [], msg: STS_500);
            }
        }
        return responseMessage(data: $validator->errors()->toArray(), status: 422, msg: STS_422);

    }

    private function addOrEditRegion($data){
        try {
            return Region::updateOrCreate(['pid' => $data['pid']],$data);
        } catch (\Throwable $e) {
            logError($e);
            return false;
        }
    }
}
