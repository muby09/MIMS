<?php

use App\Http\Controllers\DashboardController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\DropDownController;
use App\Http\Controllers\DependencyController;
use App\Http\Controllers\Region\TeamController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Region\FeederController;
use App\Http\Controllers\Region\RegionController;
use App\Http\Controllers\Inventory\ItemController;
use App\Http\Controllers\Inventory\MeterController;
use App\Http\Controllers\Inventory\RequestController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth'])->group(function(){
    Route::get('/meter-summary', [MeterController::class, 'meterSummary']);//->name('admin.dashboard');
    Route::get('/meter-installation', [MeterController::class, 'meterInstallation']);//->name('admin.dashboard');

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    //management
    Route::middleware(['role:management'])->group(function () {
    });
    //management
    Route::middleware(['role:super admin|management'])->group(function () {
        Route::get('/admin-dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
        Route::get('/management-dashboard', [DashboardController::class, 'managementDashboard'])->name('management.dashboard');
        Route::get('/login-region', [DashboardController::class, 'loginRegion'])->name('login.region');
    });
    Route::get('/logout-region', [DashboardController::class, 'logOutRegion'])->name('logout.region');

    // super admin roles 
    Route::middleware(['role:super admin|region admin'])->group(function(){
        Route::get('/dependency', [DependencyController::class, 'index'])->name('dependency');
        Route::get('/feeders', [FeederController::class, 'index'])->name('feeders');
        Route::get('/load-all-regions', [RegionController::class, 'index'])->name('load.regions');
        Route::post('/create-region', [RegionController::class, 'createRegion'])->name('create.region');

        Route::post('/create-trading-zone', [DependencyController::class, 'createTradingZone'])->name('create.zone');
        Route::get('/load-trading-zone', [DependencyController::class, 'loadTradingZone'])->name('load.zone');


        Route::post('/create-meter-type', [DependencyController::class, 'createMeterType'])->name('create.zone');
        Route::get('/load-meter-types', [DependencyController::class, 'loadMeterTypes'])->name('load.zone');

        Route::post('/create-meter-brand', [DependencyController::class, 'createMeterBrand'])->name('create.types');
        Route::get('/load-meter-brand', [DependencyController::class, 'loadMeterBrands'])->name('load.brands');

        Route::post('/create-33kv-feeder', [FeederController::class, 'create33kvFeeder'])->name('create.33kv.feeder');
        Route::get('/load-feeder-33', [FeederController::class, 'load33kvFeeder']);//->name('create.33kv.feeder');
        Route::post('/create-11kv-feeder', [FeederController::class, 'create11kvFeeder'])->name('create.11kv.feeder');
        Route::get('/load-feeder-11', [FeederController::class, 'load11kvFeeder']);//->name('create.33kv.feeder');
        
        // create store Item 
        Route::post('/create-item-name', [ItemController::class, 'createItemName']);
        Route::get('/item-names', [ItemController::class, 'loadItemNames']);
        
        
    });
    
    // region admin 
    Route::middleware(['role:region admin|super admin'])->group(function(){
        Route::get('/meter-list', [MeterController::class, 'index'])->name('meter.list');
        Route::inertia('/teams', 'Region/Team')->name('teams');
        Route::get('/load-teams', [TeamController::class, 'loadTeams'])->name('load.teams');
        Route::post('/create-team', [TeamController::class, 'createTeam'])->name('create.team');
        Route::post('/add-team-member', [TeamController::class, 'addTeamMember'])->name('add.team.member');
        Route::get('/load-team-member', [TeamController::class, 'loadTeamMembers'])->name('load.team.members');

        // users 
        Route::inertia('/staff', 'Staff/Staff')->name('create.staff');
        Route::post('/staff' , [StaffController::class, 'createStaff']);
        Route::get('/load-staff' , [StaffController::class, 'loadStaff']);
        Route::get('/load-admin-staff' , [StaffController::class, 'loadAdminStaff']);
        Route::get('/search-staff-list/{query}' , [StaffController::class, 'searchStaff']);
        
        
        //Schedule 
        Route::get('/schedules' , [DependencyController::class, 'schedules'])->name('schedules');
        Route::post('/schedules' , [DependencyController::class, 'uploadSchedule']);
        
        
    });
    
    //filed supervisor
    Route::middleware(['role:supervisor|region admin'])->group(function () {
        // request 
        Route::inertia('/request', 'Region/Request')->name('request');
        Route::get('/load-request', [RequestController::class, 'index']);
        Route::post('/request-item', [RequestController::class, 'itemRequest']);;
        
    });
    //filed supervisor
    Route::middleware(['role:supervisor|super admin|region admin'])->group(function () {
        Route::inertia('/team-assigned-meters', 'Region/TeamAssignedMeters')->name('assigned.meters');
        Route::post('/add-meter-number', [MeterController::class, 'addMeterNumber']);//->name('assigned.meters');
        Route::get('/load-team-assigned-meters', [MeterController::class, 'loadTeamAssignedMeters']);//->name('assigned.meters');
        Route::inertia('/team-members', 'Region/TeamMember')->name('team.members');
        Route::get('/load-team-member', [TeamController::class, 'loadMembers']);//->name('team.members');
        
    });
    // data staff
    Route::middleware(['role:data entry|super admin|region admin'])->group(function () {
        Route::inertia('/installations', 'Region/Installations')->name('installations');
        Route::post('/record-form', [MeterController::class, 'recordForm'])->name('record.form');
        Route::get('/schedule-list' , [DependencyController::class, 'scheduleList'])->name('schedule.list');
        Route::get('/search-schedule-list/{query}', [DependencyController::class, 'searchScheduleList']);//->name('schedule.list');
        Route::get('/installed-list', [MeterController::class, 'installedList']);//->name('schedule.list');

        Route::get('/search-installed-list/{query}', [MeterController::class, 'searchInstalledList']);//->name('schedule.list');
        Route::post('/filter-installed-list', [MeterController::class, 'filterInstalledList']);//->name('schedule.list');
        Route::inertia('/customer-complains', 'Region/Complains')->name('complains');
        Route::post('/add-customer-complain', [MeterController::class, 'addCustomerComplain']);//->name('complains');
        Route::get('/complain-list', [MeterController::class, 'complainList']);//->name('complains');
        Route::get('/search-complain-list/{query}', [MeterController::class, 'searchComplainList']);//->name('complains');


    });
    // staff
    Route::middleware(['role:staff,super admin'])->group(function () {});
    // installer 
    Route::middleware(['role:installer,super admin'])->group(function () {});

    // store manager 
    Route::middleware(['role:store|super admin|region admin'])->group(function () {
        Route::inertia('/request-list', 'Region/RequestList')->name('request.list');
        Route::get('/load-request-list', [RequestController::class, 'loadRequestList']);
        Route::inertia('/meter-list', 'Inventory/MeterList')->name('meter.list');
        Route::get('/load-meter-list',[MeterController::class,'index']);
        Route::get('/search-meter-list/{query}',[MeterController::class,'searchMeterList']);
        Route::post('/upload-meter-list',[MeterController::class,'addMeterList'])->name('upload.list');
        Route::get('/inventory-list',[ItemController::class,'inventoryList'])->name('inventory.list');
        Route::post('/add-inventory-item',[ItemController::class,'addInventoryItem'])->name('add.inventory.item');
        Route::post('/remove-damage-item',[ItemController::class,'removeDamageItem'])->name('remove.damage.item');
        Route::get('/damaged-items',[ItemController::class,'damagedItems'])->name('damage.item');
        Route::get('/damaged-item-details',[ItemController::class,'damagedItemDetail'])->name('damaged.item.detail');
        // approve request 
        Route::post('/approve-request', [RequestController::class, 'approveRequest']);

    });
    
    
    Route::post('/update-passord',[StaffController::class,'updatePassword']);//->name('damaged.item.detail');

});

Route::get('/drop-states', [DropDownController::class, 'loadStates'])->name('load.states');
Route::get('/drop-state-lga/{id}', [DropDownController::class, 'loadStateLga'])->name('load.state.lga');
Route::get('/drop-state-regions', [DropDownController::class, 'loadStateRegion']);//->name('load.regions');
Route::get('/drop-regions/{state}', [DropDownController::class, 'loadRegions']);//->name('load.regions');
Route::get('/drop-regions-admin', [DropDownController::class, 'loadRegionsAdmin']);//->name('load.regions');
Route::get('/drop-feeder-33/{region}', [DropDownController::class, 'loadRegion33KvFeeder']);//->name('load.regions');
Route::get('/drop-feeder-11/{feeder33}', [DropDownController::class, 'loadRegion11KvFeeder']);//->name('load.regions');
Route::get('/drop-item-names', [DropDownController::class, 'loadItemName']);
Route::get('/drop-item-quantity', [DropDownController::class, 'loadItemQuantity']);
Route::get('/drop-teams', [DropDownController::class, 'loadTeams']);
Route::get('/drop-roles', [DropDownController::class, 'dropDownRoles']);
Route::get('/drop-users', [DropDownController::class, 'dropDownUsers']);
Route::get('/drop-supervisors', [DropDownController::class, 'dropDownSupervisor']);
Route::get('/drop-installers', [DropDownController::class, 'dropDownInstallers']);
Route::get('/drop-zone-state', [DropDownController::class, 'dropDownZoneState']);
Route::get('/drop-zone/{state_id}', [DropDownController::class, 'dropDownZone']);
Route::get('/drop-meter-types', [DropDownController::class, 'dropDownMeterType']);
Route::get('/drop-meter-brands', [DropDownController::class, 'dropDownMeterBrand']);




require __DIR__.'/auth.php';
