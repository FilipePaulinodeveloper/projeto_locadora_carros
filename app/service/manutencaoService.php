<?php

namespace App\Service;

use App\Http\Controllers\logs;
use App\Http\Controllers\logsErrosController;
use App\Http\Controllers\Security;
use App\Models\ConfigManutencao;
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

class ManutencaoService
{

    public function __construct(private ConfigManutencao $manutencao)
    {

    }

    /**
     * @param array $options
     * @return array|LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $this->verifyPermition("list.ConfigManutencoes");
        try {

            $data = Session::all();

            if (!isset($data["ConfigManutencoes"]) || empty($data["ConfigManutencoes"])) {
                session(["ConfigManutencoes" => array("status" => "0", "orderBy" => array("column" => "created_at", "sorting" => "1"), "limit" => "10")]);
                $data = Session::all();
            }

            $Filtros = new Security;
            if ($request->input()) {

                $Limpar = false;
                if ($request->input("limparFiltros") == true) {
                    $Limpar = true;
                }

                $arrayFilter = $Filtros->TratamentoDeFiltros($request->input(), $Limpar, ["ConfigManutencoes"]);
                if ($arrayFilter) {
                    session(["ConfigManutencoes" => $arrayFilter]);
                    $data = Session::all();
                }
            }


            $columnsTable = DisabledColumns::whereRouteOfList("list.ConfigManutencoes")
                ->first()
                ?->columns;
                $ConfigManutencoes = $this->manutencao
                ->select(DB::raw("config_manutencoes.*, DATE_FORMAT(config_manutencoes.created_at, '%d/%m/%Y - %H:%i:%s') as data_final

                "));

                if (!empty($data["ConfigManutencoes"]["orderBy"]) && isset($data["ConfigManutencoes"]["orderBy"]["column"])) {
                    $Coluna = $data["ConfigManutencoes"]["orderBy"]["column"];
                    $Sorting = strtolower($data["ConfigManutencoes"]["orderBy"]["sorting"] ?? "asc");


                    if (!in_array($Sorting, ["asc", "desc"])) {
                        $Sorting = "asc"; // Valor padrão para evitar erro
                    }

                    $ConfigManutencoes = $ConfigManutencoes->orderBy("config_manutencoes.$Coluna", $Sorting);
                } else {
                    $ConfigManutencoes = $ConfigManutencoes->orderBy("config_manutencoes.created_at", "desc");
                }

            //MODELO DE FILTRO PARA VOCE COLOCAR AQUI, PARA CADA COLUNA DO BANCO DE DADOS DEVERÁ TER UM IF PARA APLICAR O FILTRO, EXCLUIR O FILTRO DE ID, DELETED E UPDATED_AT

            $filtros = [
                "tipo" => "like",
                "data_manutencao" => "like",
                "descricao" => "like",
                "valor" => "like",
                "tipo" => "like",
                "token" => "like",
                "status" => "like"
            ];


            foreach ($filtros as $campo => $operador) {

                if (isset($data["ConfigClientes"][$campo])) {
                    $AplicaFiltro = $data["ConfigManutencoes"][$campo];

                    $ConfigManutencoes = $ConfigManutencoes->where("config_clientes.$campo", $operador, $operador === "like" ? "%$AplicaFiltro%" : $AplicaFiltro);
                }
            }


            $ConfigManutencoes = $ConfigManutencoes->where("config_manutencoes.deleted", "0");

            $ConfigManutencoes = $ConfigManutencoes->paginate(($data["ConfigManutencoes"]["limit"] ?: 10))
                ->appends(["page", "orderBy", "searchBy", "limit"]);

            $this->registerLog(1,"Acessou a listagem do Módulo de ConfigManutencoes", 0);
            $Registros = $this->Registros();

            // dd(Inertia::render("ConfigManutencoes/List", [
            //     "columnsTable" => $columnsTable,
            //     "ConfigManutencoes" => $ConfigManutencoes,

            //     "Filtros" => $data["ConfigManutencoes"],
            //     "Registros" => $Registros,

            // ]));
            return Inertia::render("ConfigManutencoes/List", [
                "columnsTable" => $columnsTable,
                "ConfigManutencoes" => $ConfigManutencoes,

                "Filtros" => $data["ConfigManutencoes"],
                "Registros" => $Registros,

            ]);
        } catch (Exception $e){

            $this->errorHandler($e);
        }
    }

    private function Registros()
    {

        $mes = date("m");
        $Total = $this->manutencao
            ->where("config_manutencoes.deleted", "0")
            ->count();

        $Ativos = $this->manutencao
            ->where("config_manutencoes.deleted", "0")
            ->where("config_manutencoes.status", "0")
            ->count();

        $Inativos = $this->manutencao
            ->where("config_manutencoes.deleted", "0")
            ->where("config_manutencoes.status", "1")
            ->count();

        $EsseMes = $this->manutencao
            ->where("config_manutencoes.deleted", "0")
            ->whereMonth("config_manutencoes.created_at", $mes)
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

        $this->verifyPermition("create.ConfigManutencoes");

        try {
            $data['token'] = md5(date("Y-m-d H:i:s") . rand(0, 999999999));
            $data['deleted'] = 0;

            
            $this->manutencao->create($data);

            $lastId = DB::getPdo()->lastInsertId();
            $this->registerLog(2, "Inseriu um Novo Registro no Módulo de ConfigManutencoes", $lastId);

            return redirect()->route("list.ConfigManutencoes");
        } catch (Exception $e) {
            $this->errorHandler($e);
        }

        return redirect()->route("list.ConfigManutencoes");
    }

    private function verifyPermition($permicao){

        $permUser = Auth::user()->hasPermissionTo($permicao);

        if (!$permUser) {
            return redirect()->route("list.Dashboard", ["id" => "1"]);
        }
    }

    private function errorHandler($e){

        $modulo = "ConfigManutencoes";

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

            $modulo = "ConfigManutencoes";
            $logs = new logs;
            $logs->RegistraLog($tipo, $modulo, $acao, $id);



    }

    public function edit($idConfigManutencoes)
    {

        $this->verifyPermition("edit.ConfigManutencoes");
        try {

            $configManutencoes = $this->manutencao->where("token", $idConfigManutencoes)->first();

            $acaoID = $this->return_id($idConfigManutencoes);
            $this->registerLog(1, "Abriu a Tela de Edição do Módulo de ConfigManutencoes", $acaoID);

            return Inertia::render("ConfigManutencoes/Edit", [
                "ConfigManutencoes" => $configManutencoes,

            ]);
        } catch (Exception $e) {
           $this->errorHandler($e);
        }
    }

    private function return_id($id)
    {

        $ConfigManutencoes = DB::table("config_manutencoes");
        $ConfigManutencoes = $ConfigManutencoes->where("deleted", "0");
        $ConfigManutencoes = $ConfigManutencoes->where("token", $id)->first();

        return $ConfigManutencoes->id;
    }

    /**
     * @param array $data
     * @param string $id
     */
    public function update(array $data, string $id)
    {
        
        $this->verifyPermition('edit.ConfigManutencoes');

        try {

            $acaoId = $this->return_id($id);

            $manutencao = $this->manutencao->where("token", $id)->first();

            $manutencao->update($data);

            $this->registerLog(3,"Editou um registro no Módulo de ConfigManutencoes", $acaoId);

            return redirect()->route("list.ConfigManutencoes");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
   }

    /**
     * @return bool|string|array
     */
    public function destroy($id)
    {
        
        $this->verifyPermition('delete.ConfigManutencoes');

        try {
        $AcaoID = $this->return_id($id);

        $manutencao = $this->manutencao->where("token", $id)->first();
        
        if ($manutencao) {
           $teste =  $manutencao->update(["deleted" => "1"]);
        }

          $this->registerLog(4, "Excluiu um registro no Módulo de ConfigManutencoes", $AcaoID);

           return redirect()->route("list.ConfigManutencoes");

        } catch (\Exception $e) {
            dd($e);
            $this->errorHandler($e);
        }
    }

    public function deleteSelected($id)
    {
        $this->verifyPermition('delete.ConfigManutencoes');

        try {

            $idsRecebido = explode(",", $id);


            foreach ($idsRecebido as $id) {
                $acaoId = $this->return_id($id);

                $manutencao = $this->manutencao->where("token", $id)->first();

                if ($manutencao) {
                    $manutencao->update(["deleted" => "1"]);
                    $this->registerLog(4, "Excluiu um registro no Módulo de ConfigManutencoes", $acaoId);
                }
            }


            return redirect()->route("list.ConfigManutencoes");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
    }


    public function deletarTodos()
    {
        $this->verifyPermition('delete.ConfigManutencoes');

        try{

             $this->manutencao->query()->update(['deleted' => 1]);

            $this->registerLog(4, "Excluiu TODOS os registros no Módulo de ConfigManutencoes", 0);

            return redirect()->route("list.ConfigManutencoes");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
    }


    public function restaurarTodos()
    {
        $this->verifyPermition('edit.ConfigManutencoes');

        try{

             $this->manutencao->query()->update(['deleted' => 0]);

            $this->registerLog(3, "Restaurou TODOS os registros no Módulo de ConfigManutencoes", 0);

            return redirect()->route("list.ConfigManutencoes");

        } catch (\Exception $e) {
            $this->errorHandler($e);
        }
    }


}
