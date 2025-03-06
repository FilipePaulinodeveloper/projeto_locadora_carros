<?php

use App\Http\Controllers\Company;
use App\Http\Controllers\Login;
use App\Http\Controllers\Permissions;
use App\Http\Controllers\ProtectedDownloads;
use App\Http\Controllers\Userlist;
use App\Http\Controllers\Utils;
use App\Http\Controllers\logs;
use App\Http\Controllers\logsErrosController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\SMTP;

use App\Http\Controllers\ConfigCarros;
use App\Http\Controllers\ConfigClientes;
use App\Http\Controllers\ConfigManutencoes;
use App\Http\Controllers\ConfigVeiculos;
use App\Http\Controllers\ConfigVendas;
// ALTERAHEAD


use App\Models\Benefit;
use App\Models\Office as ModelsOffice;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

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


Route::middleware(['auth', 'has.temp.password'])->group(function () {
    Route::get('/usuarios', [Userlist::class, 'index'])->name('list.users');

    Route::get('usuarios/criar', [Userlist::class, 'create'])
        ->name('form.store.user');

    Route::post('usuarios/criar', [Userlist::class, 'store'])
        ->name('store.user');

    Route::get('usuarios/editar/{user_id}', [Userlist::class, 'edit'])
        ->name('form.update.user');

    Route::post('usuarios/editar/{user_id}', [Userlist::class, 'update'])
    ->name('update.user');

    Route::get('Profile', [Userlist::class, 'editProfile'])
    ->name('form.update.profile');

    Route::post('Profile', [Userlist::class, 'updateProfile'])
        ->name('update.userProfile');


    Route::post('usuarios/{user_id}', [Userlist::class, 'delete'])
        ->name('form.delete.user');

    Route::get('usuarios/recuperar-senha-interno/{user_id}', [Userlist::class, 'resendPassword'])
        ->name('resend.password.user');


    Route::get('empresas', [Company::class, 'index'])->name('list.company');
    Route::get('empresas/criar', [Company::class, 'create'])->name('form.store.company');
    Route::post('empresas/criar', [Company::class, 'store'])->name('store.company');
    Route::get('empresas/editar/{id}', [Company::class, 'edit'])->name('form.update.company');
    Route::post('empresas/editar/{id}', [Company::class, 'update'])->name('update.company');
    Route::post('empresas/deletar/{id}', [Company::class, 'delete'])->name('delete.company');


    Route::get('permissoes', [Permissions::class, 'render'])
        ->name('list.permission');

    Route::get('permissoes/criar', [Permissions::class, 'create'])
        ->name('form.store.permission');

    Route::post('permissoes/criar', [Permissions::class, 'store'])
        ->name('store.permission');

    Route::get('permissoes/editar/{permission_id}', [Permissions::class, 'edit'])
        ->name('form.update.permission');

    Route::post('permissoes/editar/{permission_id}', [Permissions::class, 'update'])
        ->name('update.permission');

    Route::post('permissoes/{permission_id}', [Permissions::class, 'delete'])
        ->name('form.delete.permission');

    Route::get('get-files/{filename?}', [ProtectedDownloads::class, 'showJobImage'])
        ->name('get.files');

    Route::get('download-files/{path}', [ProtectedDownloads::class, 'download'])->name('download.files');
    Route::get('download2-files/{path}', [ProtectedDownloads::class, 'download2'])->name('download2.files');





    Route::get('/index', function () {  return redirect()->route('list.Dashboard');   })->name('home');
    Route::get('/', function () {  return redirect()->route('list.Dashboard');   });




    Route::get('logs', [logs::class, 'index'])->name('list.logs');
    Route::get('logs/criar', [logs::class, 'create'])->name('form.store.logs');
    Route::post('logs/criar', [logs::class, 'store'])->name('store.logs');
    Route::get('logs/editar/{id}', [logs::class, 'edit'])->name('form.update.logs');
    Route::post('logs/editar/{id}', [logs::class, 'update'])->name('update.logs');
    Route::post('logs/deletar/{id}', [logs::class, 'delete'])->name('delete.logs');



	Route::get('logsErros', [logsErrosController::class, 'index'])->name('list.logsErros');
    Route::get('logsErros/criar', [logsErrosController::class, 'create'])->name('form.store.logsErros');
    Route::post('logsErros/criar', [logsErrosController::class, 'store'])->name('store.logsErros');
    Route::get('logsErros/editar/{id}', [logsErrosController::class, 'edit'])->name('form.update.logsErros');
    Route::post('logsErros/editar/{id}', [logsErrosController::class, 'update'])->name('update.logsErros');
    Route::post('logsErros/deletar/{id}', [logsErrosController::class, 'delete'])->name('delete.logsErros');




Route::get('logsUsuario', [logs::class, 'index'])->name('list.logsUsuario');
    Route::get('logsUsuario/criar', [logs::class, 'create'])->name('form.store.logsUsuario');
    Route::post('logsUsuario/criar', [logs::class, 'store'])->name('store.logsUsuario');
	Route::post('logsUsuario/criarAjax', [logs::class, 'storeAjax'])->name('storeAjax.logsUsuario');
    Route::get('logsUsuario/editar/{id}', [logs::class, 'edit'])->name('form.update.logsUsuario');
    Route::post('logsUsuario/editar/{id}', [logs::class, 'update'])->name('update.logsUsuario');
	Route::post('logsUsuario/editar/{id}', [logs::class, 'updateAjax'])->name('updateAjax.logsUsuario');
    Route::post('logsUsuario/deletar/{id}', [logs::class, 'delete'])->name('delete.logsUsuario');
	Route::post('logsUsuario/deletar/{id}', [logs::class, 'deleteAjax'])->name('deleteAjax.logsUsuario');


    Route::get('SMTP/editar', [SMTP::class, 'edit'])->name('list.SMTP');
    Route::post('SMTP/editar/{id}', [SMTP::class, 'update'])->name('update.SMTP');



Route::get('ConfigCarros', [ConfigCarros::class, 'index'])->name('list.ConfigCarros');
Route::post('ConfigCarros', [ConfigCarros::class, 'index'])->name('listP.ConfigCarros');
Route::get('ConfigCarros/criar', [ConfigCarros::class, 'create'])->name('form.store.ConfigCarros');
Route::post('ConfigCarros/criar', [ConfigCarros::class, 'store'])->name('store.ConfigCarros');
Route::get('ConfigCarros/editar/{id}', [ConfigCarros::class, 'edit'])->name('form.update.ConfigCarros');
Route::post('ConfigCarros/editar/{id}', [ConfigCarros::class, 'update'])->name('update.ConfigCarros');
Route::post('ConfigCarros/deletar/{id}', [ConfigCarros::class, 'delete'])->name('delete.ConfigCarros');
Route::post('ConfigCarros/deletarSelecionados/{id?}', [ConfigCarros::class, 'deleteSelected'])->name('deleteSelected.ConfigCarros');
Route::post('ConfigCarros/deletarTodos', [ConfigCarros::class, 'deletarTodos'])->name('deletarTodos.ConfigCarros');
Route::post('ConfigCarros/RestaurarTodos', [ConfigCarros::class, 'RestaurarTodos'])->name('RestaurarTodos.ConfigCarros');
Route::get('ConfigCarros/RelatorioExcel', [ConfigCarros::class, 'exportarRelatorioExcel'])->name('get.Excel.ConfigCarros');



Route::get('ConfigClientes', [ConfigClientes::class, 'index'])->name('list.ConfigClientes');
	Route::post('ConfigClientes', [ConfigClientes::class, 'index'])->name('listP.ConfigClientes');
    Route::get('ConfigClientes/criar', [ConfigClientes::class, 'create'])->name('form.store.ConfigClientes');
    Route::post('ConfigClientes/criar', [ConfigClientes::class, 'store'])->name('store.ConfigClientes');
    Route::get('ConfigClientes/editar/{id}', [ConfigClientes::class, 'edit'])->name('form.update.ConfigClientes');
    Route::post('ConfigClientes/editar/{id}', [ConfigClientes::class, 'update'])->name('update.ConfigClientes');
    Route::post('ConfigClientes/deletar/{id}', [ConfigClientes::class, 'delete'])->name('delete.ConfigClientes');
	Route::post('ConfigClientes/deletarSelecionados/{id?}', [ConfigClientes::class, 'deleteSelected'])->name('deleteSelected.ConfigClientes');
	Route::post('ConfigClientes/deletarTodos', [ConfigClientes::class, 'deletarTodos'])->name('deletarTodos.ConfigClientes');
	Route::post('ConfigClientes/RestaurarTodos', [ConfigClientes::class, 'RestaurarTodos'])->name('RestaurarTodos.ConfigClientes');
	Route::get('ConfigClientes/RelatorioExcel', [ConfigClientes::class, 'exportarRelatorioExcel'])->name('get.Excel.ConfigClientes');
    Route::get('ConfigClientes/nome', [ConfigClientes::class, 'getClientes'])->name('get.nome.ConfigCliente');

    Route::get('ConfigVeiculos', [ConfigVeiculos::class, 'index'])->name('list.ConfigVeiculos');
	Route::post('ConfigVeiculos', [ConfigVeiculos::class, 'index'])->name('listP.ConfigVeiculos');
    Route::get('ConfigVeiculos/criar', [ConfigVeiculos::class, 'create'])->name('form.store.ConfigVeiculos');
    Route::post('ConfigVeiculos/criar', [ConfigVeiculos::class, 'store'])->name('store.ConfigVeiculos');
    Route::get('ConfigVeiculos/editar/{id}', [ConfigVeiculos::class, 'edit'])->name('form.update.ConfigVeiculos');
    Route::post('ConfigVeiculos/editar/{id}', [ConfigVeiculos::class, 'update'])->name('update.ConfigVeiculos');
    Route::post('ConfigVeiculos/deletar/{id}', [ConfigVeiculos::class, 'delete'])->name('delete.ConfigVeiculos');
	Route::post('ConfigVeiculos/deletarSelecionados/{id?}', [ConfigVeiculos::class, 'deleteSelected'])->name('deleteSelected.ConfigVeiculos');
	Route::post('ConfigVeiculos/deletarTodos', [ConfigVeiculos::class, 'deletarTodos'])->name('deletarTodos.ConfigVeiculos');
	Route::post('ConfigVeiculos/RestaurarTodos', [ConfigVeiculos::class, 'RestaurarTodos'])->name('RestaurarTodos.ConfigVeiculos');
	Route::get('ConfigVeiculos/RelatorioExcel', [ConfigVeiculos::class, 'exportarRelatorioExcel'])->name('get.Excel.ConfigVeiculos');
    Route::get('ConfigVeiculos/nome', [ConfigVeiculos::class, 'getVeiculos'])->name('get.nome.ConfigVeiculo');



    Route::get('ConfigManutencoes', [ConfigManutencoes::class, 'index'])->name('list.ConfigManutencoes');
	Route::post('ConfigManutencoes', [ConfigManutencoes::class, 'index'])->name('listP.ConfigManutencoes');
    Route::get('ConfigManutencoes/criar', [ConfigManutencoes::class, 'create'])->name('form.store.ConfigManutencoes');
    Route::post('ConfigManutencoes/criar', [ConfigManutencoes::class, 'store'])->name('store.ConfigManutencoes');
    Route::get('ConfigManutencoes/editar/{id}', [ConfigManutencoes::class, 'edit'])->name('form.update.ConfigManutencoes');
    Route::post('ConfigManutencoes/editar/{id}', [ConfigManutencoes::class, 'update'])->name('update.ConfigManutencoes');
    Route::post('ConfigManutencoes/deletar/{id}', [ConfigManutencoes::class, 'delete'])->name('delete.ConfigManutencoes');
	Route::post('ConfigManutencoes/deletarSelecionados/{id?}', [ConfigManutencoes::class, 'deleteSelected'])->name('deleteSelected.ConfigManutencoes');
	Route::post('ConfigManutencoes/deletarTodos', [ConfigManutencoes::class, 'deletarTodos'])->name('deletarTodos.ConfigManutencoes');
	Route::post('ConfigManutencoes/RestaurarTodos', [ConfigManutencoes::class, 'RestaurarTodos'])->name('RestaurarTodos.ConfigManutencoes');
	Route::get('ConfigManutencoes/RelatorioExcel', [ConfigManutencoes::class, 'exportarRelatorioExcel'])->name('get.Excel.ConfigManutencoes');

    Route::get('ConfigVendas', [ConfigVendas::class, 'index'])->name('list.ConfigVendas');
	Route::post('ConfigVendas', [ConfigVendas::class, 'index'])->name('listP.ConfigVendas');
    Route::get('ConfigVendas/criar', [ConfigVendas::class, 'create'])->name('form.store.ConfigVendas');
    Route::post('ConfigVendas/criar', [ConfigVendas::class, 'store'])->name('store.ConfigVendas');
    Route::get('ConfigVendas/editar/{id}', [ConfigVendas::class, 'edit'])->name('form.update.ConfigVendas');
    Route::post('ConfigVendas/editar/{id}', [ConfigVendas::class, 'update'])->name('update.ConfigVendas');
    Route::post('ConfigVendas/deletar/{id}', [ConfigVendas::class, 'delete'])->name('delete.ConfigVendas');
	Route::post('ConfigVendas/deletarSelecionados/{id?}', [ConfigVendas::class, 'deleteSelected'])->name('deleteSelected.ConfigVendas');
	Route::post('ConfigVendas/deletarTodos', [ConfigVendas::class, 'deletarTodos'])->name('deletarTodos.ConfigVendas');
	Route::post('ConfigVendas/RestaurarTodos', [ConfigVendas::class, 'RestaurarTodos'])->name('RestaurarTodos.ConfigVendas');
	Route::get('ConfigVendas/RelatorioExcel', [ConfigVendas::class, 'exportarRelatorioExcel'])->name('get.Excel.ConfigVendas');

    Route::get('Dashboard/Calendario', [Dashboard::class, 'Calendario'])->name('list.DashboardCalendario');
    Route::get('Dashboard/{id?}', [Dashboard::class, 'index'])->name('list.Dashboard');


    Route::get('cep/{cep}', [Utils::class, 'getAddressViaCep'])->name('get.address.viacep');



    Route::post('toggle-column-table/', [Utils::class, 'toggleColumnsTables'])
        ->name('toggle.columns.tables');

    Route::post('/logout', [Login::class, 'logout'])->name('logout');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/nova-senha', [Login::class, 'replaceTempPasswordView'])->name('temp.password');
    Route::post('/nova-senha', [Login::class, 'replaceTempPassword'])->name('send.temp.password');
});



Route::get('/login', [Login::class, 'index'])->name('login');

Route::post('/login', [Login::class, 'login'])->name('action.login');

Route::get('/esqueci-minha-senha', [Login::class, 'forgotPassword'])->name('forgot.password');

Route::post('/esqueci-minha-senha', [Login::class, 'recoveryPasswordSend'])->name('forgot.password.send');

Route::get('/recuperar-minha-senha', [Login::class, 'recoveryPassword'])->name('recovery.password');

Route::get('/recuperar-minha-senha/{code}', [Login::class, 'recoveryPassword'])->name('recovery.password');

Route::post('/recuperar-minha-senha/{code}', [Login::class, 'recoveryPasswordSend'])->name('recovery.password.send');
