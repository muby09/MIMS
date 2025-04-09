<?php

namespace App\Http\Controllers\Inventory;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Inventory\Item;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Inventory\DamagedItem;
use App\Models\Inventory\DamagedItemDetail;
use App\Models\Inventory\ItemQuantity;
// use App\Models\Inventory\ItemsQuantity;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    //


    public function loadItemNames()
    {

        try {
            $data = Item::get();
            return pushData($data, 'Item name Loaded');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // load region inventory items 
    public function inventoryList()
    {
        try {
            if (superAdmin()) {
                $lists = ItemQuantity::with('item')->with('region')->paginate(20);
            } else {
                $lists = ItemQuantity::where('region_pid', getRegionPid())->with('item')->with('region')->paginate(20);
            }
            return Inertia::render('Inventory/InventoryList', ['lists' => $lists]);
        } catch (\Throwable $e) {
            logError($e);
            return pushData([], ERR_EMT);
        }
    }
    // load region inventory items 
    public function damagedItems()
    {
        try {
            if (superAdmin()) {
                $lists = ItemQuantity::with('item')->with('region')->paginate(20);
            } else {
                $lists = DamagedItem::where('region_pid', getRegionPid())->with('item')->with('region')->paginate(20);
            }

            // $lists = DamagedItem::with('item')->with('region')->paginate(20);
            return Inertia::render('Inventory/DamagedItem', ['lists' => $lists]);
        } catch (\Throwable $e) {
            logError($e);
            return pushData([], ERR_EMT);
        }
    }
    // load region inventory items 
    public function damagedItemDetail()
    {
        try {
            if (superAdmin()) {
                $lists = DamagedItemDetail::with('item')->with('region')->paginate(20);
            } else {
                $lists = DamagedItemDetail::where('region_pid', getRegionPid())->with('item')->with('region')->paginate(20);
            }
            return pushData($lists,'Data loaded');
        } catch (\Throwable $e) {
            logError($e);
            return pushData([], ERR_EMT);
        }
    }



    public function createItemName(Request $request){

        $validator =  Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'unit' => 'required|string',
        ]);



        if (!$validator->fails()) {
            try {
                $data  = [
                    'name' => $request->name ,
                    'description' => $request->description ,
                    'unit' => $request->unit ,
                    'pid' => $request->pid ?? public_id(),
                    'creator' => getUserPid(),
                ];
                $result = $this->addOrEditItemName($data);
                return pushResponse($result, $request->pid ? 'Item name updated' :"Item name added");
            } catch (\Throwable $e) {
                logError($e);
                return responseMessage(status: 204, data: [], msg: STS_500);
            }
        }
        return responseMessage(data: $validator->errors()->toArray(), status: 422, msg: STS_422);

    }

    public function addInventoryItem(Request $request){

        $validator =  Validator::make($request->all(), [
            'items.*.quantity' => 'required|numeric',
            'items.*.item_pid' => 'required|string',
        ]);



        if (!$validator->fails()) {
            try {
                $result = false;
                $count = count($request->items);
                if ($count > 0) {
                    for ($i = 0; $i < $count; $i++) {
                        $data  = [
                            'item_pid' => $request->items[$i]['item_pid'],
                            'quantity' => $request->items[$i]['quantity'],
                            'pid' => public_id(),
                            'creator' => getUserPid(),
                            'region_pid' => getRegionPid(),
                        ];
                        $result = $this->addOrEditQnt($data);
                    }
                }
                return pushResponse($result,"Item name added");
            } catch (\Throwable $e) {
                logError($e);
                return responseMessage(status: 204, data: [], msg: STS_500);
            }
        }
        return responseMessage(data: $validator->errors()->toArray(), status: 422, msg: STS_422);

    }

    public function removeDamageItem(Request $request){
        $validator =  Validator::make($request->all(), [
            'quantity' => 'required|numeric|gt:0|lte:qnt',
            'item_pid' => 'required|string',
            'cause' => 'required|string',
        ]);

        if (!$validator->fails()) {
            try {
                $data  = [
                    'item_pid' => $request->item_pid,
                    'quantity' => $request->quantity,
                    // 'pid' => public_id(),
                    'creator' => getUserPid(),
                    'region_pid' => getRegionPid(),
                ];
                DB::beginTransaction();
                $qnt = ItemQuantity::where(['item_pid' => $data['item_pid'], 'region_pid' => $data['region_pid']])->first();
                $qnt->quantity -= $data['quantity'];
                $save = $qnt->save();
                if ($save) {
                    $qnt = DamagedItem::where(['item_pid' => $data['item_pid'], 'region_pid' => $data['region_pid']])->first();
                    if ($qnt) {
                        $qnt->quantity += $data['quantity'];
                        $result = $qnt->save();
                    }
                    $result = DamagedItem::create($data);

                
                }
                if($result){
                    $detail = [
                            'region_pid'=> getRegionPid() , 
                            'item_pid' => $request->item_pid , 
                            'date' => $request->date , 
                            'cause' => $request->cause , 
                            'creator' => getUserPid() , 
                            'quantity' => $request->quantity 
                        ];
                    $result = DamagedItemDetail::create($detail);
                }
                if ($result) {
                    DB::commit();
                } else {
                    DB::rollBack();
                }
                return pushResponse($result, "Item moved to Damaged.");
            } catch (\Throwable $e) {
                logError($e);
                DB::rollBack();
                return responseMessage(status: 204, data: [], msg: STS_500);
            }
        }
        return responseMessage(data: $validator->errors()->toArray(), status: 422, msg: STS_422);

    }

    private function addOrEditItemName(array $data){
        try {
            return Item::updateOrCreate(['pid' => $data['pid']],$data);
        } catch (\Throwable $e) {
            logError($e);
            return false;
        }
    }


    private function addOrEditQnt(array $data){
        try {
            $qnt = ItemQuantity::where([
                'item_pid' => $data['item_pid'],
                'region_pid' => $data['region_pid']])->first();
            if($qnt){
                $qnt->quantity += $data['quantity'];
             return   $qnt->save();
            }
            return ItemQuantity::create($data);
        } catch (\Throwable $e) {
            logError($e);
            return false;
        }
    }

    private function removeDamagedItem(array $data){
        try {
            $qnt = ItemQuantity::where(['item_pid' => $data['item_pid'],'region_pid' => $data['region_pid']])->first();
            $qnt->quantity -= $data['quantity'];
            $save = $qnt->save();
            if($save){
                $qnt = DamagedItem::where([ 'item_pid' => $data['item_pid'] , 'region_pid' => $data['region_pid'] ])->first();
                if ($qnt) {
                    $qnt->quantity += $data['quantity'];
                    $result = $qnt->save();
                }
                $result = DamagedItem::create($data);
              
                return $result;
            }
        } catch (\Throwable $e) {
            logError($e);
            return false;
        }
    }
}
