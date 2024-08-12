<?php

use App\Http\Controllers\AirplaneController;
use App\Http\Controllers\AirportController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\PassportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\EmployeesPDFController;
use App\Http\Controllers\CustomerSegmentationController;
use App\Http\Controllers\EmployeesChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/register-verify/{email}', [AuthenticationController::class, 'checkRegisterVerification']);
Route::post('/forget-password', [AuthenticationController::class, 'forgetPassword']);
Route::post('password-verification/{email}', [AuthenticationController::class, 'checkVerification']);
Route::post('/reset-password', [AuthenticationController::class, 'resetPassword'])->middleware('reset-password');
Route::post('/login', [AuthenticationController::class, 'login']);

Route::middleware('check-auth')->prefix('/')->group(function () {
    Route::get('/', [AuthenticationController::class, 'profile']);
    Route::post('/', [AuthenticationController::class, 'updateProfile']);
    Route::post('/logout', [AuthenticationController::class, 'logout']);

    Route::prefix('passports')->group(function () {
        Route::post('/', [PassportController::class, 'addPassport']);
        Route::post('/{passport}', [PassportController::class, 'updatePassport']);
        Route::delete('/{passport}', [PassportController::class, 'deletePassport']);
        Route::get('/', [PAssportController::class, 'getPassports']);
        Route::get('/all', [PassportController::class, 'getAllPassports']);
        Route::get('/{passport}', [PassportController::class, 'getPassportInformation']);
    });

    Route::prefix('cities')->group(function () {
        Route::get('/', [CityController::class, 'getCities']);
        Route::get('/{city}', [CityController::class, 'getCityInformation']);
    });

    Route::prefix('airplanes')->group(function () {
        Route::middleware('manage-airplane')->group(function () {
            Route::post('/', [AirplaneController::class, 'addAirplane']);
            Route::put('/{airplane}', [AirplaneController::class, 'editAirplane']);
            Route::delete('/{airplane}', [AirplaneController::class, 'deleteAirplane']);
        });
        Route::middleware('read-airplane')->group(function () {
            Route::get('/', [AirplaneController::class, 'getAirplanes']);
            Route::get('/{airplane}', [AirplaneController::class, 'getAirplaneInformation']);
        });
    });

    Route::prefix('airports')->group(function () {
        Route::middleware('manage-airport')->group(function () {
            Route::post('/', [AirportController::class, 'addAirport']);
            Route::put('/{airport}', [AirportController::class, 'editAirport']);
            Route::delete('/{airport}', [AirportController::class, 'deleteAirport']);
        });
        Route::middleware('read-airport')->group(function () {
            Route::get('/', [AirportController::class, 'getAirports']);
            Route::get('/{airport}', [AirportController::class, 'getAirportInformation']);
        });
    });

    Route::prefix('flights')->group(function () {
        Route::middleware('manage-flight')->group(function () {
            Route::post('/', [FlightController::class, 'createFlight']);
            Route::put('/{flight}', [FlightController::class, 'updateFlight']);
            Route::delete('/{flight}', [FlightController::class, 'deleteFlight']);
        });
        Route::middleware('read-flight')->group(function () {
            Route::get('/', [FlightController::class, 'getFlights']);
            Route::get('/{flight}', [FlightController::class, 'getFlightInformation']);
        });
    });

    Route::middleware('manage-flight')->prefix('schedules')->group(function (){
        Route::prefix('days')->group(function (){
            
        });
    });

    Route::prefix('employees')->group(function () {
        Route::middleware('manage-employee')->group(function () {
            Route::post('/', [EmployeeController::class, 'addEmployee']);
            Route::put('/{employee}', [EmployeeController::class, 'updateEmployee']);
            Route::delete('/{employee}', [EmployeeController::class, 'deleteEmployee']);
            Route::post('/update-email/{employee}/{email}', [EmployeeController::class, 'updateEmail']);
        });
        Route::middleware('read-employee')->group(function () {
            Route::get('/', [EmployeeController::class, 'getEmployees']);
            Route::get('/{employee}', [EmployeeController::class, 'getEmployeeInformation']);
        });
        Route::middleware('manage-employee')->prefix('roles')->group(function () {
            Route::delete('/{employee}', [EmployeeController::class, 'deleteRoles']);
            Route::post('/{employee}', [EmployeeController::class, 'addRoles']);
        });
    });

    Route::middleware('manage-employee')->prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'getRoles']);
        Route::get('/{role}', [RoleController::class, 'getRoleInformation']);
    });

    Route::middleware('manage-airplane')->prefix('classes')->group(function () {
        Route::post('/{airplane}', [ClassController::class, 'addClass']);
        Route::put('/{classM}', [ClassController::class, 'editClass']);
        Route::delete('/{classM}', [ClassController::class, 'deleteClass']);
    });

    Route::get('/run-segmentation', [CustomerSegmentationController::class, 'runSegmentation']);
    Route::get('/segmentation-results', [CustomerSegmentationController::class, 'getLatestResults']);


    Route::post('/ingest-pdf', [EmployeesPDFController::class, 'employeeIngestPDF']);
    Route::get('/pdfs', [EmployeesPDFController::class, 'getPDFs']);
    Route::delete('/pdfs/{id}', [EmployeesPDFController::class, 'deletePDF']);

    Route::post('/send-message', [ChatController::class, 'sendMessage']);
    Route::get('chat-history/{thread_id}', [ChatController::class, 'getChatHistory']);

    // Route to list all threads
    Route::get('/threads', [ChatController::class, 'listThreads']);

    // Route to create a new thread
    Route::post('/create-thread', [ChatController::class, 'createThread']);

    /****************************** Need Editing ******************************/
    // Route::prefix('reservations')->group(function () {
    //     Route::middleware('manage-reservation')->prefix('/')->group(function () {
    //         Route::post('/', [ReservationController::class, 'createReservation']);
    //         Route::put('/{reservation}', [ReservationController::class, 'updateReservation']);
    //     });
    //     Route::middleware('read-reservation')->prefix('/')->group(function () {
    //         Route::get('/', [ReservationController::class, 'getReservations']);
    //         Route::get('/{reservation}', [ReservationController::class, 'getReservationInformation']);
    //     });
    // });
    /****************************** End ******************************/
});
