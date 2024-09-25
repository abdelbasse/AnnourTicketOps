<?php

use App\Http\Controllers\AdminTicketManagementController;
use App\Http\Controllers\AdminUserManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketManagementController;
use Illuminate\Support\Facades\Route;
use App\Exports\TableExport;
use App\Http\Controllers\AccuileController;
use App\Http\Controllers\FileManagementController;
use App\Http\Controllers\TicketExporterController;
use App\Http\Controllers\UserInfoAdminUserManagamentController;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/upload.image', [ImageUploadController::class, 'upload'])->name('upload.image');
Route::get('/download/{filename?}', function ($filename) {
    if (is_null($filename)) {
        return response()->json(['success' => false, 'message' => 'Filename is required.'], 400);
    }
    $filePath = 'public/' . $filename;

    if (Storage::exists($filePath)) {
        return Storage::download($filePath);
    } else {
        abort(404, 'File not found.');
    }
})->name('download.file');

Route::get('/login', [AuthController::class, 'index'])->name('login-form');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/login', [AuthController::class, 'login'])->name('login-submit');

Route::middleware('login')->group(function () {

    Route::get('/', [AccuileController::class,'index'])->name('home');
    Route::get('/json', [AccuileController::class,'fetch'])->name('home.ajax');

    // route for all profile update ect
    Route::get('/profile', [ProfileController::class, 'index'])->name('personal-profile');
    // route profile change personal informatin
    Route::post('/profile', [ProfileController::class, 'change'])->name('personal-profile-changeInfo');

    // Ticket management
    Route::get('/tickets', [AdminTicketManagementController::class, 'index'])->name('ticket.index');

    // Ticket management json RealTime updates
    Route::get('/tickets/fetch', [AdminTicketManagementController::class, 'fetchTickets'])->name('ticket.fetch');

    // any one user or admin can do this
    Route::post('/Tickets/items/Ticket/Add', [AdminTicketManagementController::class, 'newTicket'])->name('ticket.Add.Ticket');
    Route::post('/Tickets/transform', [AdminTicketManagementController::class, 'tranformTicket'])->name('ticket.transform');
    Route::post('/Tickets/transform/mine', [AdminTicketManagementController::class, 'tranformTicketToMe'])->name('ticket.transform.toMe');
    Route::post('/Tickets/transform/respond', [AdminTicketManagementController::class, 'tranformTicketRespond'])->name('ticket.transform.Respondes');

    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // ||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||

    // Access to the ticket page
    Route::get('/{id}/ticket', [TicketManagementController::class, 'index'])->name('ticket.homepage');

    // Ticket management json RealTime updates
    Route::get('/{id}/ticket/json', [TicketManagementController::class, 'fetchJSONRealTimeData'])->name('ticket.homepage.json');

    Route::post('/ticket/addLog', [TicketManagementController::class, 'AddTicketLog'])->name('ticket.Log.Add');
    Route::post('/ticket/cloture', [TicketManagementController::class, 'clotureTicket'])->name('ticket.cloture');
    Route::post('/ticket/validation', [TicketManagementController::class, 'ValidationTicket'])->name('ticket.validation');
    Route::post('/ticket/add/comment', [TicketManagementController::class, 'addComment'])->name('ticket.add.commet.validation');
    Route::post('/ticket/recoveryRapport/add/comment', [TicketManagementController::class, 'addCommentRapport'])->name('ticket.add.commet.recovery.repport');
    Route::post('/ticket/setParent', [TicketManagementController::class, 'setParent'])->name('ticket.set.parent');

    Route::get('/ticket/Rapport/{id}', [TicketManagementController::class, 'getRapport'])->name('ticket.pdf.rapport');
    // abcd
    Route::post('/export-table', [TicketExporterController::class, 'exportTable'])->name('export.tickets.list');

    // Routes only for admins
    Route::middleware('Admin')->group(function () {
        Route::get('/Users', [AdminUserManagementController::class, 'index'])->name('admin.allUsersList');
        // Add new user
        Route::post('/Users', [AdminUserManagementController::class, 'submit'])->name('admin.allUsersList.form');
        // get user data every duration of time for real time data
        Route::get('/Users/json', [AdminUserManagementController::class, 'getUsersJson'])->name('admin.allUsersList.json');

        // Show user personal informations
        Route::get('/user/{id}/info',[UserInfoAdminUserManagamentController::class,'index'])->name('admin.user.info');

        Route::get('/user/{id}/info/json',[UserInfoAdminUserManagamentController::class,'fetch'])->name('admin.user.info.json');

        Route::post('/user/{id}/info',[UserInfoAdminUserManagamentController::class,'change'])->name('admin.user.info.form');

        // ticket managemnt for admin
        Route::post('/Tickets/items/Delete', [AdminTicketManagementController::class, 'delete'])->name('admin.ticket.delete');
        Route::post('/Tickets/items/Edit', [AdminTicketManagementController::class, 'edit'])->name('admin.ticket.Edit');
        Route::post('/Tickets/items/Add', [AdminTicketManagementController::class, 'addNew'])->name('admin.ticket.Add');
    });

    // |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    // |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||

    Route::get('/files/json',[FileManagementController::class,'fetch'])->name('fileM.json.fetch');

    Route::get('/files',[FileManagementController::class,'index'])->name('fileM.index');
    Route::post('/files',[FileManagementController::class,'submit'])->name('fileM.submit');
    Route::post('/files/order',[FileManagementController::class,'OrderSubmit'])->name('fileM.submit.newFilesOrder');

    // Routes only for Supervisorrs
    // Route::middleware('Supervisor')->group(function () {
    // });

    // // Routes only for norml users
    // Route::middleware('User')->group(function () {
    //     // ticket managemnt for normal user's
    //     // Route::post('/Tickets/Edit',[AdminTicketManagementController::class,'edit'])->name('user.ticket.Edit');
    //     // Route::post('/Tickets/Add',[AdminTicketManagementController::class,'addNew'])->name('user.ticket.Add');
    // });
});
