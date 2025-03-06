<?php

namespace App\Service;

use App\Http\Controllers\logs;
use App\Http\Controllers\logsErrosController;
use App\Http\Controllers\Security;
use App\Models\ConfigVenda;
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

class VendaService
{

    public function __construct(private ConfigVenda $venda)
    {

    }

    /**
     * @param array $options
     * @return array|LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $this->verifyPermition("list.ConfigVendas");
        try {

            $data = Session::all();

            if (!isset($data["ConfigVendas"]) || empty($data["ConfigVendas"])) {
                session(["ConfigVendas" => array("status" => "0", "orderBy" => array("column" => "created_at", "sorting" => "1"), "limit" => "10")]);
                $data = Session::all();
            }

            $Filtros = new Security;
            if ($request->input()) {

                $Limpar = false;
                if ($request->input("limparFiltros") == true) {
                    $Limpar = true;
                }

                $arrayFilter = $Filtros->TratamentoDeFiltros($request->input(), $Limpar, ["ConfigVendas"]);
                if ($arrayFilter) {
                    session(["ConfigVendas" => $arrayFilter]);
                    $data = Session::all();
                }
            }


            $columnsTable = DisabledColumns::whereRouteOfList("list.ConfigVendas")
                ->first()
                ?->columns;
                $ConfigVendas = $this->venda
                ->select(DB::raw("config_vendas.*, DATE_FORMAT(config_vendas.created_at, '%d/%m/%Y - %H:%i:%s') as data_final

                "));

                if (!empty($data["ConfigVendas"]["orderBy"]) && isset($data["ConfigVendas"]["orderBy"]["column"])) {
                    $Coluna = $data["ConfigVendas"]["orderBy"]["column"];
                    $Sorting = strtolower($data["ConfigVendas"]["orderBy"]["sorting"] ?? "asc");


                    if (!in_array($Sorting, ["asc", "desc"])) {
                        $Sorting = "asc"; // Valor padrão para evitar erro
                    }

                    $ConfigVendas = $ConfigVendas->orderBy("config_vendas.$Coluna", $Sorting);
                } else {
                    $ConfigVendas = $ConfigVendas->orderBy("config_vendas.created_at", "desc");
                }

            //MODELO DE FILTRO PARA VOCE COLOCAR AQUI, PARA CADA COLUNA DO BANCO DE DADOS DEVERÁ TER UM IF PARA APLICAR O FILTRO, EXCLUIR O FILTRO DE ID, DELETED E UPDATED_AT

            $filtros = [
                "id_venda" => "=",
                "id_cliente" => "=",
                "valor" => "=",
                "tipo" => "like",
                "token" => "like",
                "status" => "=",                
            ];


            foreach ($filtros as $campo => $operador) {

                if (isset($data["ConfigClientes"][$campo])) {
                    $AplicaFiltro = $data["ConfigVendas"][$campo];

                    $ConfigVendas = $ConfigVendas->where("config_clientes.$campo", $operador, $operador === "like" ? "%$AplicaFiltro%" : $AplicaFiltro);
                }
            }


            $ConfigVendas = $ConfigVendas->where("config_vendas.deleted", "0");

            $ConfigVendas = $ConfigVendas->paginate(($data["ConfigVendas"]["limit"] ?: 10))
                ->appends(["page", "orderBy", "searchBy", "limit"]);

            $this->registerLog(1,"Acessou a listagem do Módulo de ConfigVendas", 0);
            $Registros = $this->Registros();

            // dd(Inertia::render("ConfigVendas/List", [
            //     "columnsTable" => $columnsTable,
            //     "ConfigVendas" => $ConfigVendas,

            //     "Filtros" => $data["ConfigVendas"],
            //     "Registros" => $Registros,

            // ]));
            return Inertia::render("ConfigVendas/List", [
                "columnsTable" => $columnsTable,
                "ConfigVendas" => $ConfigVendas,

                "Filtros" => $data["ConfigVendas"],
                "Registros" => $Registros,

            ]);
        } catch (Exception $e){

            $this->errorHandler($e);
        }
    }

    private function Registros()
    {

        $mes = date("m");
        $Total = $this->venda
            ->where("config_vendas.deleted", "0")
            ->count();

        $Ativos = $this->venda
            ->where("config_vendas.deleted", "0")
            ->where("config_vendas.status", "0")
            ->count();

        $Inativos = $this->venda
            ->where("config_vendas.deleted", "0")
            ->where("config_vendas.status", "1")
            ->count();

        $EsseMes = $this->venda
            ->where("config_vendas.deleted", "0")
            ->whereMonth("config_vendas.created_at", $mes)
            ->count();


        $data = new stdClass;
        $data->total = number_format($Total, 0, ",", ".");
        $data->ativo = number_format($Ativos, 0, ",", ".");
        $data->inativo = number_format($Inativos, 0, ",", ".");
        $data->mes = number_format($EsseMes, 0, ",", ".");
        return $data;
    }



    /**
     * @param array $payload
     */
    public function store($data)
    {

        $this->verifyPermition("create.ConfigVendas");

        try {
            $data['token'] = md5(date("Y-m-d H:i:s") . rand(0, 999999999));
            $data['deleted'] = 0;
            $this->venda->create($data);

            $lastId = DB::getPdo()->lastInsertId();
            $this->registerLog(2, "Inseriu um Novo Registro no Módulo de ConfigVendas", $lastId);

            return redirect()->route("list.ConfigVendas");
        } catch (Exception $e) {
            $this->errorHandler($e);
        }

        return redirect()->route("list.ConfigVendas");
    }

    private function verifyPermition($permicao){

        $permUser = Auth::user()->hasPermissionTo($permicao);

        if (!$permUser) {
            return redirect()->route("list.Dashboard", ["id" => "1"]);
        }
    }

    private function errorHandler($e){

        $modulo = "ConfigVendas";

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

            $modulo = "ConfigVendas";
            $logs = new logs;
            $logs->RegistraLog($tipo, $modulo, $acao, $id);



    }

    public function edit($idConfigVendas)
    {

        $this->verifyPermition("edit.ConfigVendas");
        try {

            $ConfigVendas = $this->venda->where("token", $idConfigVendas)->first();

            $acaoID = $this->return_id($idConfigVendas);
            $this->registerLog(1, "Abriu a Tela de Edição do Módulo de ConfigVendas", $acaoID);

            return Inertia::render("ConfigVendas/Edit", [
                "ConfigVendas" => $ConfigVendas,

            ]);
        } catch (Exception $e) {
           $this->errorHandler($e);
        }
    }

    private function return_id($id)
    {

        $ConfigVendas = DB::table("config_vendas");
        $ConfigVendas = $ConfigVendas->where("deleted", "0");
        $ConfigVendas = $ConfigVendas->where("token", $id)->first();

        return $ConfigVendas->id;
    }

    /**
     * @param array $data
     * @param string $id
     */
    public function update(array $data, string $id)
    {
        $this->verifyPermition('edit.ConfigVendas');

        try {

            $acaoId = $this->return_id($id);

            $venda = $this->venda->where("token", $id)->first();

            $venda->update($data);

            $this->registerLog(3,"Editou um registro no Módulo de ConfigVendas", $acaoId);

            return redirect()->route("list.ConfigVendas");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
   }

    /**
     * @return bool|string|array
     */
    public function destroy($id)
    {
        $this->verifyPermition('delete.ConfigVendas');

        try {
        $AcaoID = $this->return_id($id);

        $venda = $this->venda->where("token", $id)->first();

        if ($venda) {
            $venda->update(["deleted" => "1"]);
        }

          $this->registerLog(4, "Excluiu um registro no Módulo de ConfigVendas", $AcaoID);

           return redirect()->route("list.ConfigVendas");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
    }

    public function deleteSelected($id)
    {
        $this->verifyPermition('delete.ConfigVendas');

        try {

            $idsRecebido = explode(",", $id);


            foreach ($idsRecebido as $id) {
                $acaoId = $this->return_id($id);

                $venda = $this->venda->where("token", $id)->first();

                if ($venda) {
                    $venda->update(["deleted" => "1"]);
                    $this->registerLog(4, "Excluiu um registro no Módulo de ConfigVendas", $acaoId);
                }
            }


            return redirect()->route("list.ConfigVendas");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
    }


    public function deletarTodos()
    {
        $this->verifyPermition('delete.ConfigVendas');

        try{

             $this->venda->query()->update(['deleted' => 1]);

            $this->registerLog(4, "Excluiu TODOS os registros no Módulo de ConfigVendas", 0);

            return redirect()->route("list.ConfigVendas");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
    }


    public function restaurarTodos()
    {
        $this->verifyPermition('edit.ConfigVendas');

        try{

             $this->venda->query()->update(['deleted' => 0]);

            $this->registerLog(3, "Restaurou TODOS os registros no Módulo de ConfigVendas", 0);

            return redirect()->route("list.ConfigVendas");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
    }

    public function getvendas()
    {   
        try{
            $vendas = $this->venda->select('id', 'placa')->where('deleted', 0)->get();
            return response()->json($vendas);

        }
        catch (\Exception $e) {
            return $e;
        }
    }


}
