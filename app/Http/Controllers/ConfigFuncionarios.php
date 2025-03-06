<?php

namespace App\Http\Controllers;

use App\Http\Requests\CriarFuncionarioRequest;
use App\Http\Requests\updateFuncionarioRequest;
use Exception;
use App\Models\DisabledColumns;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Office;
use App\Service\funcionarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use stdClass;

class ConfigFuncionarios extends Controller
{

    protected $funcionarioService;

    public function __construct(funcionarioService $funcionarioService)
    {
        $this->funcionarioService = $funcionarioService;
    }

    public function index(Request $request)
    {
       return $this->funcionarioService->index($request);
    }

    public function create()
    {
        $Modulo = "ConfigFuncionarios";
        $permUser = Auth::user()->hasPermissionTo("create.ConfigFuncionarios");

        if (!$permUser) {
            return redirect()->route("list.Dashboard", ["id" => "1"]);
        }
        try {



            $Acao = "Abriu a Tela de Cadastro do Módulo de ConfigFuncionarios";
            $Logs = new logs;
            $Registra = $Logs->RegistraLog(1, $Modulo, $Acao);

            return Inertia::render("ConfigFuncionarios/Create", []);
        } catch (Exception $e) {

            $Error = $e->getMessage();
            $Error = explode("MESSAGE:", $Error);


            $Pagina = $_SERVER["REQUEST_URI"];

            $Erro = $Error[0];
            $Erro_Completo = $e->getMessage();
            $LogsErrors = new logsErrosController;
            $Registra = $LogsErrors->RegistraErro($Pagina, $Modulo, $Erro, $Erro_Completo);
            abort(403, "Erro localizado e enviado ao LOG de Erros");
        }
    }

    public function store(CriarFuncionarioRequest $request)
    {
        $validatedData = $request->validated();
        return $this->funcionarioService->store($validatedData);
    }




    public function edit($idConfigFuncionarios)
    {
        return $this->funcionarioService->edit($idConfigFuncionarios);
    }


    public function update(updateFuncionarioRequest $request, $id)
    {
       $validatedData = $request->validated();
       return $this->funcionarioService->update($validatedData, $id);
    }

    public function delete($IDConfigFuncionarios)
    {
       return $this->funcionarioService->destroy($IDConfigFuncionarios);
    }

    public function deleteSelected($IDConfigFuncionarios = null)
    {
      return $this->funcionarioService->deleteSelected($IDConfigFuncionarios);
    }



    public function deletarTodos()
    {
       return $this->funcionarioService->deletarTodos();
    }

    public function RestaurarTodos()
    {
       return $this->funcionarioService->restaurarTodos();
    }

    public function DadosRelatorio()
    {
        $data = Session::all();

        $ConfigFuncionarios = DB::table("config_clientes")

            ->select(DB::raw("config_clientes.*, DATE_FORMAT(config_clientes.created_at, '%d/%m/%Y - %H:%i:%s') as data_final

            "))
            ->where("config_clientes.deleted", "0");

        //MODELO DE FILTRO PARA VOCE COLOCAR AQUI, PARA CADA COLUNA DO BANCO DE DADOS DEVERÁ TER UM IF PARA APLICAR O FILTRO, EXCLUIR O FILTRO DE ID, DELETED E UPDATED_AT


        if (isset($data["ConfigFuncionarios"]["nome"])) {
            $AplicaFiltro = $data["ConfigFuncionarios"]["nome"];
            $ConfigFuncionarios = $ConfigFuncionarios->Where("config_clientes.nome",  "like", "%" . $AplicaFiltro . "%");
        }


        if (isset($data["ConfigFuncionarios"]["cpf"])) {
            $AplicaFiltro = $data["ConfigFuncionarios"]["cpf"];
            $ConfigFuncionarios = $ConfigFuncionarios->where("config_clientes.cpf", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigFuncionarios"]["data_nascimento"])) {
            $AplicaFiltro = $data["ConfigFuncionarios"]["data_nascimento"];
            $ConfigFuncionarios = $ConfigFuncionarios->whereDate("config_clientes.data_nascimento", $AplicaFiltro);
        }

        if (isset($data["ConfigFuncionarios"]["telefone"])) {
            $AplicaFiltro = $data["ConfigFuncionarios"]["telefone"];
            $ConfigFuncionarios = $ConfigFuncionarios->where("config_clientes.telefone", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigFuncionarios"]["email"])) {
            $AplicaFiltro = $data["ConfigFuncionarios"]["email"];
            $ConfigFuncionarios = $ConfigFuncionarios->where("config_clientes.email", "like", "%" . $AplicaFiltro . "%");
        }

        // Campos de endereço
        if (isset($data["ConfigFuncionarios"]["logradouro"])) {
            $AplicaFiltro = $data["ConfigFuncionarios"]["logradouro"];
            $ConfigFuncionarios = $ConfigFuncionarios->where("config_clientes.logradouro", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigFuncionarios"]["numero"])) {
            $AplicaFiltro = $data["ConfigFuncionarios"]["numero"];
            $ConfigFuncionarios = $ConfigFuncionarios->where("config_clientes.numero", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigFuncionarios"]["complemento"])) {
            $AplicaFiltro = $data["ConfigFuncionarios"]["complemento"];
            $ConfigFuncionarios = $ConfigFuncionarios->where("config_clientes.complemento", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigFuncionarios"]["bairro"])) {
            $AplicaFiltro = $data["ConfigFuncionarios"]["bairro"];
            $ConfigFuncionarios = $ConfigFuncionarios->where("config_clientes.bairro", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigFuncionarios"]["cep"])) {
            $AplicaFiltro = $data["ConfigFuncionarios"]["cep"];
            $ConfigFuncionarios = $ConfigFuncionarios->where("config_clientes.cep", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigFuncionarios"]["cidade"])) {
            $AplicaFiltro = $data["ConfigFuncionarios"]["cidade"];
            $ConfigFuncionarios = $ConfigFuncionarios->where("config_clientes.cidade", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigFuncionarios"]["estado"])) {
            $AplicaFiltro = $data["ConfigFuncionarios"]["estado"];
            $ConfigFuncionarios = $ConfigFuncionarios->where("config_clientes.estado", $AplicaFiltro);
        }



        $ConfigFuncionarios = $ConfigFuncionarios->get();

        $Dadosconfig_clientes = [];
        foreach ($ConfigFuncionarios as $config_clientess) {
            if ($config_clientess->status == "0") {
                $config_clientess->status = "Ativo";
            }
            if ($config_clientess->status == "1") {
                $config_clientess->status = "Inativo";
            }
            $Dadosconfig_clientes[] = [
                //MODELO DE CA,MPO PARA VOCE COLOCAR AQUI, PARA CADA COLUNA DO BANCO DE DADOS DEVERÁ TER UM, EXCLUIR O ID, DELETED E UPDATED_AT
                'nome' => $config_clientess->nome,

            ];
        }
        return $Dadosconfig_clientes;
    }

    public function exportarRelatorioExcel()
    {

        $permUser = Auth::user()->hasPermissionTo("create.ConfigFuncionarios");

        if (!$permUser) {
            return redirect()->route("list.Dashboard", ["id" => "1"]);
        }


        $filePath = "Relatorio_ConfigFuncionarios.xlsx";

        if (Storage::disk("public")->exists($filePath)) {
            Storage::disk("public")->delete($filePath);
            // Arquivo foi deletado com sucesso
        }

        $cabecalhoAba1 = array('nome', 'placa', 'modelo', 'ano', 'cor', 'valor_compra', 'observacao', 'status', 'Data de Cadastro');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $config_clientes = $this->DadosRelatorio();

        // Define o título da primeira aba
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle("ConfigFuncionarios");

        // Adiciona os cabeçalhos da tabela na primeira aba
        $spreadsheet->getActiveSheet()->fromArray($cabecalhoAba1, null, "A1");

        // Adiciona os dados da tabela na primeira aba
        $spreadsheet->getActiveSheet()->fromArray($config_clientes, null, "A2");

        // Definindo a largura automática das colunas na primeira aba
        foreach ($spreadsheet->getActiveSheet()->getColumnDimensions() as $col) {
            $col->setAutoSize(true);
        }

        // Habilita a funcionalidade de filtro para as células da primeira aba
        $spreadsheet->getActiveSheet()->setAutoFilter($spreadsheet->getActiveSheet()->calculateWorksheetDimension());


        // Define o nome do arquivo
        $nomeArquivo = "Relatorio_ConfigFuncionarios.xlsx";
        // Cria o arquivo
        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($nomeArquivo);
        $barra = "'/'";
        $barra = str_replace("'", "", $barra);
        $writer->save(storage_path("app" . $barra . "relatorio" . $barra . $nomeArquivo));

        return redirect()->route("download2.files", ["path" => $nomeArquivo]);
    }


    public function getClientes()
    {
        return $this->funcionarioService->getClientes();
    }
}

