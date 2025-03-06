<?php

namespace App\Service;

use App\Http\Controllers\logs;
use App\Http\Controllers\logsErrosController;
use App\Http\Controllers\Security;
use App\Models\ConfigVeiculo;
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

class VeiculoService
{

    public function __construct(private ConfigVeiculo $veiculo)
    {

    }

    /**
     * @param array $options
     * @return array|LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $this->verifyPermition("list.ConfigVeiculos");
        try {

            $data = Session::all();

            if (!isset($data["ConfigVeiculos"]) || empty($data["ConfigVeiculos"])) {
                session(["ConfigVeiculos" => array("status" => "0", "orderBy" => array("column" => "created_at", "sorting" => "1"), "limit" => "10")]);
                $data = Session::all();
            }

            $Filtros = new Security;
            if ($request->input()) {

                $Limpar = false;
                if ($request->input("limparFiltros") == true) {
                    $Limpar = true;
                }

                $arrayFilter = $Filtros->TratamentoDeFiltros($request->input(), $Limpar, ["ConfigVeiculos"]);
                if ($arrayFilter) {
                    session(["ConfigVeiculos" => $arrayFilter]);
                    $data = Session::all();
                }
            }


            $columnsTable = DisabledColumns::whereRouteOfList("list.ConfigVeiculos")
                ->first()
                ?->columns;
                $ConfigVeiculos = $this->veiculo
                ->select(DB::raw("config_veiculos.*, DATE_FORMAT(config_veiculos.created_at, '%d/%m/%Y - %H:%i:%s') as data_final

                "));

                if (!empty($data["ConfigVeiculos"]["orderBy"]) && isset($data["ConfigVeiculos"]["orderBy"]["column"])) {
                    $Coluna = $data["ConfigVeiculos"]["orderBy"]["column"];
                    $Sorting = strtolower($data["ConfigVeiculos"]["orderBy"]["sorting"] ?? "asc");


                    if (!in_array($Sorting, ["asc", "desc"])) {
                        $Sorting = "asc"; // Valor padrão para evitar erro
                    }

                    $ConfigVeiculos = $ConfigVeiculos->orderBy("config_veiculos.$Coluna", $Sorting);
                } else {
                    $ConfigVeiculos = $ConfigVeiculos->orderBy("config_veiculos.created_at", "desc");
                }

            //MODELO DE FILTRO PARA VOCE COLOCAR AQUI, PARA CADA COLUNA DO BANCO DE DADOS DEVERÁ TER UM IF PARA APLICAR O FILTRO, EXCLUIR O FILTRO DE ID, DELETED E UPDATED_AT

            $filtros = [
                "tipo" => "like",
                "marca" => "like",
                "modelo" => "like",
                "ano_fabricacao" => "=",
                "ano_modelo" => "=",
                "placa" => "like",
                "renavam" => "like",
                "chassi" => "like",
                "cor" => "like",
                "quilometragem" => "=",
                "categoria" => "like",
                "combustivel" => "like",
                "valor_venda" => "=",
                "valor_diaria" => "=",
                "disponivel_venda" => "=",
                "disponivel_locacao" => "=",
                "status" => "=",
            ];


            foreach ($filtros as $campo => $operador) {

                if (isset($data["ConfigClientes"][$campo])) {
                    $AplicaFiltro = $data["ConfigVeiculos"][$campo];

                    $ConfigVeiculos = $ConfigVeiculos->where("config_clientes.$campo", $operador, $operador === "like" ? "%$AplicaFiltro%" : $AplicaFiltro);
                }
            }


            $ConfigVeiculos = $ConfigVeiculos->where("config_veiculos.deleted", "0");

            $ConfigVeiculos = $ConfigVeiculos->paginate(($data["ConfigVeiculos"]["limit"] ?: 10))
                ->appends(["page", "orderBy", "searchBy", "limit"]);

            $this->registerLog(1,"Acessou a listagem do Módulo de ConfigVeiculos", 0);
            $Registros = $this->Registros();

            // dd(Inertia::render("ConfigVeiculos/List", [
            //     "columnsTable" => $columnsTable,
            //     "ConfigVeiculos" => $ConfigVeiculos,

            //     "Filtros" => $data["ConfigVeiculos"],
            //     "Registros" => $Registros,

            // ]));
            return Inertia::render("ConfigVeiculos/List", [
                "columnsTable" => $columnsTable,
                "ConfigVeiculos" => $ConfigVeiculos,

                "Filtros" => $data["ConfigVeiculos"],
                "Registros" => $Registros,

            ]);
        } catch (Exception $e){

            $this->errorHandler($e);
        }
    }

    private function Registros()
    {

        $mes = date("m");
        $Total = $this->veiculo
            ->where("config_veiculos.deleted", "0")
            ->count();

        $Ativos = $this->veiculo
            ->where("config_veiculos.deleted", "0")
            ->where("config_veiculos.status", "0")
            ->count();

        $Inativos = $this->veiculo
            ->where("config_veiculos.deleted", "0")
            ->where("config_veiculos.status", "1")
            ->count();

        $EsseMes = $this->veiculo
            ->where("config_veiculos.deleted", "0")
            ->whereMonth("config_veiculos.created_at", $mes)
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

        $this->verifyPermition("create.ConfigVeiculos");

        try {
            $data['token'] = md5(date("Y-m-d H:i:s") . rand(0, 999999999));
            $data['deleted'] = 0;
            $this->veiculo->create($data);

            $lastId = DB::getPdo()->lastInsertId();
            $this->registerLog(2, "Inseriu um Novo Registro no Módulo de ConfigVeiculos", $lastId);

            return redirect()->route("list.ConfigVeiculos");
        } catch (Exception $e) {
            $this->errorHandler($e);
        }

        return redirect()->route("list.ConfigVeiculos");
    }

    private function verifyPermition($permicao){

        $permUser = Auth::user()->hasPermissionTo($permicao);

        if (!$permUser) {
            return redirect()->route("list.Dashboard", ["id" => "1"]);
        }
    }

    private function errorHandler($e){

        $modulo = "ConfigVeiculos";

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

            $modulo = "ConfigVeiculos";
            $logs = new logs;
            $logs->RegistraLog($tipo, $modulo, $acao, $id);



    }

    public function edit($idConfigVeiculos)
    {

        $this->verifyPermition("edit.ConfigVeiculos");
        try {

            $configVeiculos = $this->veiculo->where("token", $idConfigVeiculos)->first();

            $acaoID = $this->return_id($idConfigVeiculos);
            $this->registerLog(1, "Abriu a Tela de Edição do Módulo de ConfigVeiculos", $acaoID);

            return Inertia::render("ConfigVeiculos/Edit", [
                "ConfigVeiculos" => $configVeiculos,

            ]);
        } catch (Exception $e) {
           $this->errorHandler($e);
        }
    }

    private function return_id($id)
    {

        $ConfigVeiculos = DB::table("config_veiculos");
        $ConfigVeiculos = $ConfigVeiculos->where("deleted", "0");
        $ConfigVeiculos = $ConfigVeiculos->where("token", $id)->first();

        return $ConfigVeiculos->id;
    }

    /**
     * @param array $data
     * @param string $id
     */
    public function update(array $data, string $id)
    {
        $this->verifyPermition('edit.ConfigVeiculos');

        try {

            $acaoId = $this->return_id($id);

            $veiculo = $this->veiculo->where("token", $id)->first();

            $veiculo->update($data);

            $this->registerLog(3,"Editou um registro no Módulo de ConfigVeiculos", $acaoId);

            return redirect()->route("list.ConfigVeiculos");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
   }

    /**
     * @return bool|string|array
     */
    public function destroy($id)
    {
        $this->verifyPermition('delete.ConfigVeiculos');

        try {
        $AcaoID = $this->return_id($id);

        $veiculo = $this->veiculo->where("token", $id)->first();

        if ($veiculo) {
            $veiculo->update(["deleted" => "1"]);
        }

          $this->registerLog(4, "Excluiu um registro no Módulo de ConfigVeiculos", $AcaoID);

           return redirect()->route("list.ConfigVeiculos");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
    }

    public function deleteSelected($id)
    {
        $this->verifyPermition('delete.ConfigVeiculos');

        try {

            $idsRecebido = explode(",", $id);


            foreach ($idsRecebido as $id) {
                $acaoId = $this->return_id($id);

                $veiculo = $this->veiculo->where("token", $id)->first();

                if ($veiculo) {
                    $veiculo->update(["deleted" => "1"]);
                    $this->registerLog(4, "Excluiu um registro no Módulo de ConfigVeiculos", $acaoId);
                }
            }


            return redirect()->route("list.ConfigVeiculos");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
    }


    public function deletarTodos()
    {
        $this->verifyPermition('delete.ConfigVeiculos');

        try{

             $this->veiculo->query()->update(['deleted' => 1]);

            $this->registerLog(4, "Excluiu TODOS os registros no Módulo de ConfigVeiculos", 0);

            return redirect()->route("list.ConfigVeiculos");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
    }


    public function restaurarTodos()
    {
        $this->verifyPermition('edit.ConfigVeiculos');

        try{

             $this->veiculo->query()->update(['deleted' => 0]);

            $this->registerLog(3, "Restaurou TODOS os registros no Módulo de ConfigVeiculos", 0);

            return redirect()->route("list.ConfigVeiculos");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
    }

    public function getVeiculos()
    {   
        try{
            $veiculos = $this->veiculo->select('id', 'placa')->where('deleted', 0)->get();
            return response()->json($veiculos);

        }
        catch (\Exception $e) {
            return $e;
        }
    }


}
