<?php

namespace App\Service;

use App\Http\Controllers\logs;
use App\Http\Controllers\logsErrosController;
use App\Http\Controllers\Security;
use App\Models\ConfigFuncionario;
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

class FuncionarioService
{

    public function __construct(private ConfigFuncionario $cliente)
    {

    }

    /**
     * @param array $options
     * @return array|LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $this->verifyPermition("list.ConfigFuncionarios");
        try {


            $data = Session::all();

            if (!isset($data["ConfigFuncionarios"]) || empty($data["ConfigFuncionarios"])) {
                session(["ConfigFuncionarios" => array("status" => "0", "orderBy" => array("column" => "created_at", "sorting" => "1"), "limit" => "10")]);
                $data = Session::all();
            }

            $Filtros = new Security;
            if ($request->input()) {

                $Limpar = false;
                if ($request->input("limparFiltros") == true) {
                    $Limpar = true;
                }

                $arrayFilter = $Filtros->TratamentoDeFiltros($request->input(), $Limpar, ["ConfigFuncionarios"]);
                if ($arrayFilter) {
                    session(["ConfigFuncionarios" => $arrayFilter]);
                    $data = Session::all();
                }
            }


            $columnsTable = DisabledColumns::whereRouteOfList("list.ConfigFuncionarios")
                ->first()
                ?->columns;
                $ConfigFuncionarios = $this->cliente
                ->select(DB::raw("config_funcionarios.*, DATE_FORMAT(config_funcionarios.created_at, '%d/%m/%Y - %H:%i:%s') as data_final

                "));

                if (!empty($data["ConfigFuncionarios"]["orderBy"]) && isset($data["ConfigFuncionarios"]["orderBy"]["column"])) {
                    $Coluna = $data["ConfigFuncionarios"]["orderBy"]["column"];
                    $Sorting = strtolower($data["ConfigFuncionarios"]["orderBy"]["sorting"] ?? "asc");


                    if (!in_array($Sorting, ["asc", "desc"])) {
                        $Sorting = "asc"; // Valor padrão para evitar erro
                    }

                    $ConfigFuncionarios = $ConfigFuncionarios->orderBy("config_funcionarios.$Coluna", $Sorting);
                } else {
                    $ConfigFuncionarios = $ConfigFuncionarios->orderBy("config_funcionarios.created_at", "desc");
                }

            //MODELO DE FILTRO PARA VOCE COLOCAR AQUI, PARA CADA COLUNA DO BANCO DE DADOS DEVERÁ TER UM IF PARA APLICAR O FILTRO, EXCLUIR O FILTRO DE ID, DELETED E UPDATED_AT

            $filtros = [
                'nome' => "like",
                'cpf' => "like",
                'salario' => "like",
                'cargo' => "like",
                'status' => "like",
            ];

            foreach ($filtros as $campo => $operador) {
                if (isset($data["ConfigFuncionarios"][$campo])) {
                    $AplicaFiltro = $data["ConfigFuncionarios"][$campo];
                    $ConfigFuncionarios = $ConfigFuncionarios->where("config_funcionarios.$campo", $operador, $operador === "like" ? "%$AplicaFiltro%" : $AplicaFiltro);
                }
            }

            $ConfigFuncionarios = $ConfigFuncionarios->where("config_funcionarios.deleted", "0");

            $ConfigFuncionarios = $ConfigFuncionarios->paginate(($data["ConfigFuncionarios"]["limit"] ?: 10))
                ->appends(["page", "orderBy", "searchBy", "limit"]);


            $this->registerLog(1,"Acessou a listagem do Módulo de ConfigFuncionarios", 0);
            $Registros = $this->Registros();



            return Inertia::render("ConfigFuncionarios/List", [
                "columnsTable" => $columnsTable,
                "ConfigFuncionarios" => $ConfigFuncionarios,

                "Filtros" => $data["ConfigFuncionarios"],
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
            ->where("config_funcionarios.deleted", "0")
            ->count();

        $Ativos = $this->cliente
            ->where("config_funcionarios.deleted", "0")
            ->where("config_funcionarios.status", "0")
            ->count();

        $Inativos = $this->cliente
            ->where("config_funcionarios.deleted", "0")
            ->where("config_funcionarios.status", "1")
            ->count();

        $EsseMes = $this->cliente
            ->where("config_funcionarios.deleted", "0")
            ->whereMonth("config_funcionarios.created_at", $mes)
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
        $this->verifyPermition("create.ConfigFuncionarios");

        try {
            $data['token'] = md5(date("Y-m-d H:i:s") . rand(0, 999999999));
            $data['deleted'] = 0;
            $this->cliente->create($data);

            $lastId = DB::getPdo()->lastInsertId();
            $this->registerLog(2, "Inseriu um Novo Registro no Módulo de ConfigFuncionarios", $lastId);

            return redirect()->route("list.ConfigFuncionarios");
        } catch (Exception $e) {
            dd($e);
            $this->errorHandler($e);
        }

        return redirect()->route("list.ConfigFuncionarios");
    }

    private function verifyPermition($permicao){

        $permUser = Auth::user()->hasPermissionTo($permicao);

        if (!$permUser) {
            return redirect()->route("list.Dashboard", ["id" => "1"]);
        }
    }

    private function errorHandler($e){

        $modulo = "ConfigFuncionarios";

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

            $modulo = "ConfigFuncionarios";
            $logs = new logs;
            $logs->RegistraLog($tipo, $modulo, $acao, $id);



    }

    public function edit($idConfigFuncionarios)
    {

        $this->verifyPermition("edit.ConfigFuncionarios");
        try {

            $ConfigFuncionarios = $this->cliente->where("token", $idConfigFuncionarios)->first();

            $acaoID = $this->return_id($idConfigFuncionarios);
            $this->registerLog(1, "Abriu a Tela de Edição do Módulo de ConfigFuncionarios", $acaoID);

            return Inertia::render("ConfigFuncionarios/Edit", [
                "ConfigFuncionarios" => $ConfigFuncionarios,

            ]);
        } catch (Exception $e) {
           $this->errorHandler($e);
        }
    }

    private function return_id($id)
    {

        $ConfigFuncionarios = DB::table("config_funcionarios");
        $ConfigFuncionarios = $ConfigFuncionarios->where("deleted", "0");
        $ConfigFuncionarios = $ConfigFuncionarios->where("token", $id)->first();

        return $ConfigFuncionarios->id;
    }

    /**
     * @param array $data
     * @param string $id
     */
    public function update(array $data, string $id)
    {
        $this->verifyPermition('edit.ConfigFuncionarios');

        try {

            $acaoId = $this->return_id($id);

            $cliente = $this->cliente->where("token", $id)->first();

            $cliente->update($data);

            $this->registerLog(3,"Editou um registro no Módulo de ConfigFuncionarios", $acaoId);

            return redirect()->route("list.ConfigFuncionarios");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
   }

    /**
     * @return bool|string|array
     */
    public function destroy($id)
    {
        $this->verifyPermition('delete.ConfigFuncionarios');

        try {
        $AcaoID = $this->return_id($id);

        $cliente = $this->cliente->where("token", $id)->first();

        if ($cliente) {
            $cliente->update(["deleted" => "1"]);
        }

          $this->registerLog(4, "Excluiu um registro no Módulo de ConfigFuncionarios", $AcaoID);

           return redirect()->route("list.ConfigFuncionarios");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
    }

    public function deleteSelected($id)
    {
        $this->verifyPermition('delete.ConfigFuncionarios');

        try {

            $idsRecebido = explode(",", $id);


            foreach ($idsRecebido as $id) {
                $acaoId = $this->return_id($id);

                $cliente = $this->cliente->where("token", $id)->first();

                if ($cliente) {
                    $cliente->update(["deleted" => "1"]);
                    $this->registerLog(4, "Excluiu um registro no Módulo de ConfigFuncionarios", $acaoId);
                }
            }


            return redirect()->route("list.ConfigFuncionarios");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
    }


    public function deletarTodos()
    {
        $this->verifyPermition('delete.ConfigFuncionarios');

        try{

             $this->cliente->query()->update(['deleted' => 1]);

            $this->registerLog(4, "Excluiu TODOS os registros no Módulo de ConfigFuncionarios", 0);

            return redirect()->route("list.ConfigFuncionarios");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
    }


    public function restaurarTodos()
    {
        $this->verifyPermition('edit.ConfigFuncionarios');

        try{

             $this->cliente->query()->update(['deleted' => 0]);

            $this->registerLog(3, "Restaurou TODOS os registros no Módulo de ConfigFuncionarios", 0);

            return redirect()->route("list.ConfigFuncionarios");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
    }

    public function getClientes()
    {
        try{
            $cliente = $this->cliente->select('id', 'nome')->where('deleted', 0)->get();
            return response()->json($cliente);

        }
        catch (\Exception $e) {
            return $e;
        }
    }


}
