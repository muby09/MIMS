<?php 

use Illuminate\Support\Facades\DB;



    function getUserRegion($pid){
        try {
            $region = DB::table('user_details as d')->join('regions as r','r.pid', 'd.region_pid')->where('user_pid', $pid)->first(['d.region_pid', 'r.region']);
                setRegionPid($region->region_pid);
                setRegionName($region->region);
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return false;
        }
    }

    

    function getInstallerSupervisor($pid){
        try {
            return DB::table('team_members as m')->join('teams as t','t.pid', 'm.team_pid')->where('m.region_pid',getRegionPid())->where('m.user_pid', $pid)->first(['m.team_pid', 't.supervisor']);
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return false;
        }
    }


    function getEmailByPid($pid){
        try {
            return DB::table('users')->where('pid', $pid)->pluck('email')->first();
        } catch (\Throwable $e) {
            logError($e->getMessage());
            return false;
        }
    }

    