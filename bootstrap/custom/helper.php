<?php

use App\Mail\AuthMail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;


function logVar($var)
{
    Log::info(json_encode($var));
}

function logError(\Exception $e)
{
    try {
        Log::error($e, [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'user' => getUserPid()
        ]);
    } catch (\Throwable $th) {
        Log::error([$e,$th]);
    }
}
function public_id()
{
    return strtoupper(str_shuffle(date('YMDHism') . time()));
}


function responseMessage($status, $data = [], $msg = null, $code = 200)
{
    return response([
        'status' => $status,
        'data' => $data,
        'message' => $msg
    ], $code ?? $status);
}

function pushResponse($status,$msg,$code=201){
    if ($status) {

        return responseMessage(status:$code , data: $status, msg: $msg);
    }
    return responseMessage(status: 204, data: [], msg: 'Operation Failed...');
}

function pushData($data,$msg = 'Data Loaded'){
    if ($data->isNotEmpty()) {
        return responseMessage(status: 200, data: $data, msg: $msg);
    }
    return responseMessage(status: 204, msg: ERR_EMT);
}

function randomNumber($len = 7)
{
    return substr(random_int(1, 99999999999999999), 1, $len);
}


function uniqueId()
{
    return strtoupper(date('yMd'));
}

function base64Encode($var)
{
    return base64_encode(base64_encode($var));
}
function base64Decode($var)
{
    return base64_decode(base64_decode($var));
}

function getUserPid()
{
    if (auth()->user()) {
        return auth()->user()['pid'];
    }
}

function getRegionPid()
{

   return session('regionPid');
}


function setRegionPid($pid = null)
{
    session(['regionPid' => $pid]); //get  Type
}
function setRegionName($name = null)
{
    session(['regionName' => $name]); //get  Type
}
function getRegionName()
{

    return session('regionName');
}


function getUsername()
{
    if (auth()->user()) {
        return auth()->user()['useranme'] ?? auth()->user()['email'];
    }
}

function superAdmin()
{
    return auth()->user()->hasRole('super admin') ;
}

function matchStatus($role)
{
    $role =  match ($role) {
        'pending' => 0,
        'resolved' => 3,
        'unresolved' => 4,
        'replaced' => 5,
        default => 0
    };
    return $role;
}

function removeThis($str, $chr = ',')
{
    return rtrim($str, $chr);
}
function getInitials($string = null)
{
    $string = preg_split("/[\s,_-]+/", $string);
    $ret = '';
    foreach ($string as $word)
        $ret .= $word[0];
    return $ret;
}
function dateToAge($date)
{
    return Carbon::parse($date)->age;
    // $date = new DateTime($date);
    // return $date->diff(Carbon::now())
    // ->format('%y years, %m months and %d days');
}


function daysFromNow($d = '+ 1')
{
    return date("Y-m-d", strtotime(" {$d} day"));
}


function confrimYear($year = 18)
{
    // $dt = new Carbon();
    // $before = $dt->subYears($year)->format('Y-m-d');
    return now()->subYears($year)->toDateString();
}

function date_diff_weekdays($from, $to)
{
    if ($from === null || $to === null)
        return null;

    $date_from = new DateTime($from);
    $date_to = new DateTime($to);

    // calculate number of weekdays from start of week - start date
    $from_day = intval($date_from->format('w')); // 0 (for Sunday) through 6 (for Saturday)
    if ($from_day == 0)
        $from_day = 7;
    $from_wdays = $from_day > 5 ? 5 : $from_day;

    // calculate number of weekdays from start of week - end date
    $to_day = intval($date_to->format('w'));
    if ($to_day == 0)
        $to_day = 7;
    $to_wdays = $to_day > 5 ? 5 : $to_day;

    // calculate number of days between the two dates
    $interval = $date_from->diff($date_to);
    $days = intval($interval->format('%R%a')); // shows negative values too

    // calculate number of full weeks between the two dates
    $weeks_between = floor($days / 7);
    if ($to_day >= $from_day)
        $weeks_between -= 1;

    // complete calculation of number of working days between
    $diff_wd = 5 * ($weeks_between) + (5 - $from_wdays) + $to_wdays;

    return $diff_wd;
}

function isWeekend($date)
{
    $date = strtotime($date);
    $date = date("l", $date);
    $date = strtolower($date);
    return ($date == "saturday" || $date == "sunday") ;
    
}

 

function sprintNumber($num, $pre = 3)
{
    return trim(sprintf("%0{$pre}d\n", $num));
}

function justDate()
{
    return date('Y-m-d');
}

function ampm()
{
    return date('h:i A');
}

function formatDate($date)
{
    if(empty($date)){
        return ;
    }
    return date('d M, Y', strtotime($date));
}

function formatDateTime($date)
{
    if(empty($date)){
        return ;
    }
    return date('d M, Y h:i A', strtotime($date));
}

function shortTime($date)
{
    return date('Y-M-d', strtotime($date));
}
function fullDate()
{
    return date('Y-m-d H:i:s');
}

function timeNow()
{
    return date('H:i');
}

function sendMail($param)
{
    try {
        return Mail::to($param['email'])->send(new AuthMail($param));
    } catch (\Throwable $e) {
        logError($e);
        return false;
    }
}

// function saveImg($image, $path = 'images', $name = null)
// {
//     $name = str_replace('/', '-', $name . ' idr' . '.png' /*$image->extension()*/);
//     $destinationPath = public_path("/files/" . $path . '/');

//     $img = Image::make($image->path());
//     $img->resize(150, 150, function ($constraint) {
//         $constraint->aspectRatio();
//     })->save($destinationPath . $name);

//     return $destinationPath.'/'.$name;
// }

function saveFile($file, $name = null, $path = 'documents')
{
    try {
        // $name = str_replace('/', '-', $name);
        $fileName = uniqueId() . time() . '-' . str_replace('/', '-', $name) . '.' . $file->extension();
        $filePath = "files/{$path}";
        $file->move(public_path($filePath), $fileName);
        return $filePath . '/' . $fileName;
    } catch (\Throwable $e) {
        logError($e);
        return false;
    }
}
function saveBase64File($file, $name = 'optimal', $path = 'documents/')
{
    try {
        $ext = explode('/', mime_content_type($file))[1];
        $path = "files/{$path}";
        File::exists($path) ?: File::makeDirectory($path, 0777);
        $filename = $path. $name.'-' . public_id(). '.' . $ext;
        list(, $file_data) = explode(';', $file);
        list(, $file_data) = explode(',', $file_data);
        file_put_contents($filename, base64_decode($file_data));
        return $filename;
    } catch (\Throwable $e) {
        logError($e);
        return false;
    }
}

function saveImg($image, $path, $name = null)
{
    try {
        $ext = explode('/', mime_content_type($image))[1];
        list(, $file_data) = explode(';', $image);
        list(, $file_data) = explode(',', $file_data);
        $path = str_replace('files/', '', $path); // find n replace files/  in case of existing path that already conatained since we prepend files/ before returning to avoid files/files
        $destinationPath = public_path("/files/" . $path); // prepend  /files and public path
        File::exists($destinationPath) ?: File::makeDirectory($destinationPath, true);
        $name = $name ?? 'TL' . date('YmdHis.').$ext;
        // uploadImage($image,$name, $destinationPath);
        // file_put_contents($destinationPath, $image);
        if (is_string($image)) {
            $image = base64_decode($file_data);
            file_put_contents($destinationPath . '/' . $name, $image);
        } else {
            $image->move($destinationPath, $name);
        }
        return 'files/' . $path . $name;  // return file path+ file name and prepend files
    } catch (\Throwable $e) {
        logError($e);
        return false;
    }
}
