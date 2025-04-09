<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Region\Region;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //

   public function managementDashboard(){
        $data = $this->loadRegions();
        return Inertia::render('ManagementDashboard' ,['data' => $data]);
    }

    public function adminDashboard(){
        $data = $this->loadRegions();
        return Inertia::render('AdminDashboard', ['data' => $data]);
    }

    public function loginRegion(Request $request){
        loadActiveRegion($request->region);
        // return Inertia::render('Dashboard');
        return redirect()->intended(route('dashboard', absolute: false));
    }
    public function logOutRegion(){
        setRegionPid();
        setRegionName();

        if (superAdmin()) {
            // The user has the 'writer' role
            return redirect()->route('admin.dashboard')->with('success','Welcome back');
        }

        return redirect()->intended(route('management.dashboard', absolute: false));
    }

    private function loadRegions(){
        try {
            // setRegionPid() ;
            return Region::select('*',DB::raw('(select count(i.id)  from installations i where i.region_pid = regions.pid ) as total'))->get();
        } catch (\Throwable $e) {
            logError($e);
            return [];
        }
    }

}
