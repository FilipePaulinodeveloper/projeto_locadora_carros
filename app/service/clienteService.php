<?php

namespace App\Service;

use App\Http\Controllers\logs;
use App\Http\Controllers\logsErrosController;
use App\Http\Controllers\Security;
use App\Models\ConfigCliente;
use App\Models\User;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\DisabledColumns;
use stdClass;

class ClienteService
{

    public function __construct(private ConfigCliente $cliente)
    {

    }

    /**
     * @param array $options
     * @return array|LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $this->verifyPermition("list.ConfigClientes");
        try {


            $data = Session::all();

            if (!isset($data["ConfigClientes"]) || empty($data["ConfigClientes"])) {
                session(["ConfigClientes" => array("status" => "0", "orderBy" => array("column" => "created_at", "sorting" => "1"), "limit" => "10")]);
                $data = Session::all();
            }

            $Filtros = new Security;
            if ($request->input()) {

                $Limpar = false;
                if ($request->input("limparFiltros") == true) {
                    $Limpar = true;
                }

                $arrayFilter = $Filtros->TratamentoDeFiltros($request->input(), $Limpar, ["ConfigClientes"]);
                if ($arrayFilter) {
                    session(["ConfigClientes" => $arrayFilter]);
                    $data = Session::all();
                }
            }


            $columnsTable = DisabledColumns::whereRouteOfList("list.ConfigClientes")
                ->first()
                ?->columns;
                $ConfigClientes = $this->cliente
                ->select(DB::raw("config_clientes.*, DATE_FORMAT(config_clientes.created_at, '%d/%m/%Y - %H:%i:%s') as data_final

                "));

                if (!empty($data["ConfigClientes"]["orderBy"]) && isset($data["ConfigClientes"]["orderBy"]["column"])) {
                    $Coluna = $data["ConfigClientes"]["orderBy"]["column"];
                    $Sorting = strtolower($data["ConfigClientes"]["orderBy"]["sorting"] ?? "asc");


                    if (!in_array($Sorting, ["asc", "desc"])) {
                        $Sorting = "asc"; // Valor padrão para evitar erro
                    }

                    $ConfigClientes = $ConfigClientes->orderBy("config_clientes.$Coluna", $Sorting);
                } else {
                    $ConfigClientes = $ConfigClientes->orderBy("config_clientes.created_at", "desc");
                }

            //MODELO DE FILTRO PARA VOCE COLOCAR AQUI, PARA CADA COLUNA DO BANCO DE DADOS DEVERÁ TER UM IF PARA APLICAR O FILTRO, EXCLUIR O FILTRO DE ID, DELETED E UPDATED_AT

            $filtros = [
                "nome" => "like",
                "cpf" => "like",
                "telefone" => "like",
                "email" => "like",
                "logradouro" => "like",
                "numero" => "like",
                "complemento" => "like",
                "bairro" => "like",
                "cep" => "like",
                "cidade" => "like",
                "estado" => "=",
            ];

            foreach ($filtros as $campo => $operador) {
                if (isset($data["ConfigClientes"][$campo])) {
                    $AplicaFiltro = $data["ConfigClientes"][$campo];
                    $ConfigClientes = $ConfigClientes->where("config_clientes.$campo", $operador, $operador === "like" ? "%$AplicaFiltro%" : $AplicaFiltro);
                }
            }

            if (isset($data["ConfigClientes"]["data_nascimento"])) {
                $AplicaFiltro = $data["ConfigClientes"]["data_nascimento"];
                $ConfigClientes = $ConfigClientes->whereDate("config_clientes.data_nascimento", $AplicaFiltro);
            }


            $ConfigClientes = $ConfigClientes->where("config_clientes.deleted", "0");

            $ConfigClientes = $ConfigClientes->paginate(($data["ConfigClientes"]["limit"] ?: 10))
                ->appends(["page", "orderBy", "searchBy", "limit"]);

            $this->registerLog(1,"Acessou a listagem do Módulo de ConfigClientes", 0);
            $Registros = $this->Registros();

            return Inertia::render("ConfigClientes/List", [
                "columnsTable" => $columnsTable,
                "ConfigClientes" => $ConfigClientes,

                "Filtros" => $data["ConfigClientes"],
                "Registros" => $Registros,

            ]);
        } catch (Exception $e){
            dd($e);
            $this->errorHandler($e);
        }
    }

    private function Registros()
    {

        $mes = date("m");
        $Total = $this->cliente
            ->where("config_clientes.deleted", "0")
            ->count();

        $Ativos = $this->cliente
            ->where("config_clientes.deleted", "0")
            ->where("config_clientes.status", "0")
            ->count();

        $Inativos = $this->cliente
            ->where("config_clientes.deleted", "0")
            ->where("config_clientes.status", "1")
            ->count();

        $EsseMes = $this->cliente
            ->where("config_clientes.deleted", "0")
            ->whereMonth("config_clientes.created_at", $mes)
            ->count();


        $data = new stdClass;
        $data->total = number_format($Total, 0, ",", ".");
        $data->ativo = number_format($Ativos, 0, ",", ".");
        $data->inativo = number_format($Inativos, 0, ",", ".");
        $data->mes = number_format($EsseMes, 0, ",", ".");
        return $data;
    }

    /**
     * @param string $id
     * @return object
     */
    public function show(string $id)
    {
         return $this->cliente->with('phones')->find($id);
    }

    /**
     * @param array $payload
     */
    public function store($data)
    {
        $this->verifyPermition("create.ConfigClientes");

        try {
            $data['token'] = md5(date("Y-m-d H:i:s") . rand(0, 999999999));
            $data['deleted'] = 0;
            $this->cliente->create($data);

            $lastId = DB::getPdo()->lastInsertId();
            $this->registerLog(2, "Inseriu um Novo Registro no Módulo de ConfigClientes", $lastId);

            return redirect()->route("list.ConfigClientes");
        } catch (Exception $e) {
            $this->errorHandler($e);
        }

        return redirect()->route("list.ConfigClientes");
    }

    private function verifyPermition($permicao){

        $permUser = Auth::user()->hasPermissionTo($permicao);

        if (!$permUser) {
            return redirect()->route("list.Dashboard", ["id" => "1"]);
        }
    }

    private function errorHandler($e){

        $modulo = "ConfigClientes";

        $Error = $e->getMessage();
        $Error = explode("MESSAGE:", $Error);


        $Pagina = $_SERVER["REQUEST_URI"];

        $Erro = $Error[0];
        $Erro_Completo = $e->getMessage();
        $LogsErrors = new logsErrosController;
        $Registra = $LogsErrors->RegistraErro($Pagina, $modulo, $Erro, $Erro_Completo);
        abort(403, "Erro localizado e enviado ao LOG de Erros");
    }

    private function registerLog($tipo, $acao, $id)
    {

            $modulo = "ConfigClientes";
            $logs = new logs;
            $logs->RegistraLog($tipo, $modulo, $acao, $id);



    }

    public function edit($idConfigClientes)
    {

        $this->verifyPermition("edit.ConfigClientes");
        try {

            $configClientes = $this->cliente->where("token", $idConfigClientes)->first();

            $acaoID = $this->return_id($idConfigClientes);
            $this->registerLog(1, "Abriu a Tela de Edição do Módulo de ConfigClientes", $acaoID);

            return Inertia::render("ConfigClientes/Edit", [
                "ConfigClientes" => $configClientes,

            ]);
        } catch (Exception $e) {
           $this->errorHandler($e);
        }
    }

    private function return_id($id)
    {

        $ConfigClientes = DB::table("config_clientes");
        $ConfigClientes = $ConfigClientes->where("deleted", "0");
        $ConfigClientes = $ConfigClientes->where("token", $id)->first();

        return $ConfigClientes->id;
    }

    /**
     * @param array $data
     * @param string $id
     */
    public function update(array $data, string $id)
    {
        $this->verifyPermition('edit.ConfigClientes');

        try {

            $acaoId = $this->return_id($id);

            $cliente = $this->cliente->where("token", $id)->first();

            $cliente->update($data);

            $this->registerLog(3,"Editou um registro no Módulo de ConfigClientes", $acaoId);

            return redirect()->route("list.ConfigClientes");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
   }

    /**
     * @return bool|string|array
     */
    public function destroy($id)
    {
        $this->verifyPermition('delete.ConfigClientes');

        try {
        $AcaoID = $this->return_id($id);

        $cliente = $this->cliente->where("token", $id)->first();

        if ($cliente) {
            $cliente->update(["deleted" => "1"]);
        }

          $this->registerLog(4, "Excluiu um registro no Módulo de ConfigClientes", $AcaoID);

           return redirect()->route("list.ConfigClientes");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
    }



}
