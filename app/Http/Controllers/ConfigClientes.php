<?php

namespace App\Http\Controllers;

use App\Http\Requests\CriarClienteRequest;
use App\Http\Requests\updateClienteRequest;
use Exception;
use App\Models\DisabledColumns;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Office;
use App\Service\ClienteService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use stdClass;

class ConfigClientes extends Controller
{

    protected $clienteService;

    public function __construct(ClienteService $clienteService)
    {
        $this->clienteService = $clienteService;
    }

    public function index(Request $request)
    {
       return $this->clienteService->index($request);
    }

    public function create()
    {
        $Modulo = "ConfigClientes";
        $permUser = Auth::user()->hasPermissionTo("create.ConfigClientes");

        if (!$permUser) {
            return redirect()->route("list.Dashboard", ["id" => "1"]);
        }
        try {



            $Acao = "Abriu a Tela de Cadastro do Módulo de ConfigClientes";
            $Logs = new logs;
            $Registra = $Logs->RegistraLog(1, $Modulo, $Acao);

            return Inertia::render("ConfigClientes/Create", []);
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

    public function store(CriarClienteRequest $request)
    {
        $validatedData = $request->validated();
        return $this->clienteService->store($validatedData);
    }




    public function edit($idConfigClientes)
    {
        return $this->clienteService->edit($idConfigClientes);
    }


    public function update(updateClienteRequest $request, $id)
    {
       $validatedData = $request->validated();
       return $this->clienteService->update($validatedData, $id);
    }

    public function delete($IDConfigClientes)
    {
       return $this->clienteService->destroy($IDConfigClientes);
    }

    public function deleteSelected($IDConfigClientes = null)
    {
      return $this->clienteService->deleteSelected($IDConfigClientes);
    }



    public function deletarTodos()
    {
       return $this->clienteService->deletarTodos();
    }

    public function RestaurarTodos()
    {
       return $this->clienteService->restaurarTodos();
    }

    public function DadosRelatorio()
    {
        $data = Session::all();

        $ConfigClientes = DB::table("config_clientes")

            ->select(DB::raw("config_clientes.*, DATE_FORMAT(config_clientes.created_at, '%d/%m/%Y - %H:%i:%s') as data_final

            "))
            ->where("config_clientes.deleted", "0");

        //MODELO DE FILTRO PARA VOCE COLOCAR AQUI, PARA CADA COLUNA DO BANCO DE DADOS DEVERÁ TER UM IF PARA APLICAR O FILTRO, EXCLUIR O FILTRO DE ID, DELETED E UPDATED_AT


        if (isset($data["ConfigClientes"]["nome"])) {
            $AplicaFiltro = $data["ConfigClientes"]["nome"];
            $ConfigClientes = $ConfigClientes->Where("config_clientes.nome",  "like", "%" . $AplicaFiltro . "%");
        }


        if (isset($data["ConfigClientes"]["cpf"])) {
            $AplicaFiltro = $data["ConfigClientes"]["cpf"];
            $ConfigClientes = $ConfigClientes->where("config_clientes.cpf", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigClientes"]["data_nascimento"])) {
            $AplicaFiltro = $data["ConfigClientes"]["data_nascimento"];
            $ConfigClientes = $ConfigClientes->whereDate("config_clientes.data_nascimento", $AplicaFiltro);
        }

        if (isset($data["ConfigClientes"]["telefone"])) {
            $AplicaFiltro = $data["ConfigClientes"]["telefone"];
            $ConfigClientes = $ConfigClientes->where("config_clientes.telefone", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigClientes"]["email"])) {
            $AplicaFiltro = $data["ConfigClientes"]["email"];
            $ConfigClientes = $ConfigClientes->where("config_clientes.email", "like", "%" . $AplicaFiltro . "%");
        }

        // Campos de endereço
        if (isset($data["ConfigClientes"]["logradouro"])) {
            $AplicaFiltro = $data["ConfigClientes"]["logradouro"];
            $ConfigClientes = $ConfigClientes->where("config_clientes.logradouro", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigClientes"]["numero"])) {
            $AplicaFiltro = $data["ConfigClientes"]["numero"];
            $ConfigClientes = $ConfigClientes->where("config_clientes.numero", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigClientes"]["complemento"])) {
            $AplicaFiltro = $data["ConfigClientes"]["complemento"];
            $ConfigClientes = $ConfigClientes->where("config_clientes.complemento", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigClientes"]["bairro"])) {
            $AplicaFiltro = $data["ConfigClientes"]["bairro"];
            $ConfigClientes = $ConfigClientes->where("config_clientes.bairro", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigClientes"]["cep"])) {
            $AplicaFiltro = $data["ConfigClientes"]["cep"];
            $ConfigClientes = $ConfigClientes->where("config_clientes.cep", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigClientes"]["cidade"])) {
            $AplicaFiltro = $data["ConfigClientes"]["cidade"];
            $ConfigClientes = $ConfigClientes->where("config_clientes.cidade", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigClientes"]["estado"])) {
            $AplicaFiltro = $data["ConfigClientes"]["estado"];
            $ConfigClientes = $ConfigClientes->where("config_clientes.estado", $AplicaFiltro);
        }



        $ConfigClientes = $ConfigClientes->get();

        $Dadosconfig_clientes = [];
        foreach ($ConfigClientes as $config_clientess) {
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

        $permUser = Auth::user()->hasPermissionTo("create.ConfigClientes");

        if (!$permUser) {
            return redirect()->route("list.Dashboard", ["id" => "1"]);
        }


        $filePath = "Relatorio_ConfigClientes.xlsx";

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
        $spreadsheet->getActiveSheet()->setTitle("ConfigClientes");

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
        $nomeArquivo = "Relatorio_ConfigClientes.xlsx";
        // Cria o arquivo
        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($nomeArquivo);
        $barra = "'/'";
        $barra = str_replace("'", "", $barra);
        $writer->save(storage_path("app" . $barra . "relatorio" . $barra . $nomeArquivo));

        return redirect()->route("download2.files", ["path" => $nomeArquivo]);
    }
}

