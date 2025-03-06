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
use App\Service\vendaService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use stdClass;

class ConfigVendas extends Controller
{

    protected $vendaService;

    public function __construct(vendaService $vendaService)
    {
        $this->vendaService = $vendaService;
    }

    public function index(Request $request)
    {
       return $this->vendaService->index($request);
    }

    public function create()
    {
        $Modulo = "ConfigVendas";
        $permUser = Auth::user()->hasPermissionTo("create.ConfigVendas");

        if (!$permUser) {
            return redirect()->route("list.Dashboard", ["id" => "1"]);
        }
        try {



            $Acao = "Abriu a Tela de Cadastro do Módulo de ConfigVendas";
            $Logs = new logs;
            $Registra = $Logs->RegistraLog(1, $Modulo, $Acao);

            return Inertia::render("ConfigVendas/Create", []);
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
        return $this->vendaService->store($validatedData);
    }




    public function edit($idConfigVendas)
    {
        return $this->vendaService->edit($idConfigVendas);
    }


    public function update(updateClienteRequest $request, $id)
    {
       $validatedData = $request->validated();
       return $this->vendaService->update($validatedData, $id);
    }

    public function delete($IDConfigVendas)
    {
       return $this->vendaService->destroy($IDConfigVendas);
    }

    public function deleteSelected($IDConfigVendas = null)
    {
      return $this->vendaService->deleteSelected($IDConfigVendas);
    }



    public function deletarTodos()
    {
       return $this->vendaService->deletarTodos();
    }

    public function RestaurarTodos()
    {
       return $this->vendaService->restaurarTodos();
    }

    public function DadosRelatorio()
    {
        $data = Session::all();

        $ConfigVendas = DB::table("config_clientes")

            ->select(DB::raw("config_clientes.*, DATE_FORMAT(config_clientes.created_at, '%d/%m/%Y - %H:%i:%s') as data_final

            "))
            ->where("config_clientes.deleted", "0");

        //MODELO DE FILTRO PARA VOCE COLOCAR AQUI, PARA CADA COLUNA DO BANCO DE DADOS DEVERÁ TER UM IF PARA APLICAR O FILTRO, EXCLUIR O FILTRO DE ID, DELETED E UPDATED_AT


        if (isset($data["ConfigVendas"]["nome"])) {
            $AplicaFiltro = $data["ConfigVendas"]["nome"];
            $ConfigVendas = $ConfigVendas->Where("config_clientes.nome",  "like", "%" . $AplicaFiltro . "%");
        }


        if (isset($data["ConfigVendas"]["cpf"])) {
            $AplicaFiltro = $data["ConfigVendas"]["cpf"];
            $ConfigVendas = $ConfigVendas->where("config_clientes.cpf", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigVendas"]["data_nascimento"])) {
            $AplicaFiltro = $data["ConfigVendas"]["data_nascimento"];
            $ConfigVendas = $ConfigVendas->whereDate("config_clientes.data_nascimento", $AplicaFiltro);
        }

        if (isset($data["ConfigVendas"]["telefone"])) {
            $AplicaFiltro = $data["ConfigVendas"]["telefone"];
            $ConfigVendas = $ConfigVendas->where("config_clientes.telefone", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigVendas"]["email"])) {
            $AplicaFiltro = $data["ConfigVendas"]["email"];
            $ConfigVendas = $ConfigVendas->where("config_clientes.email", "like", "%" . $AplicaFiltro . "%");
        }

        // Campos de endereço
        if (isset($data["ConfigVendas"]["logradouro"])) {
            $AplicaFiltro = $data["ConfigVendas"]["logradouro"];
            $ConfigVendas = $ConfigVendas->where("config_clientes.logradouro", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigVendas"]["numero"])) {
            $AplicaFiltro = $data["ConfigVendas"]["numero"];
            $ConfigVendas = $ConfigVendas->where("config_clientes.numero", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigVendas"]["complemento"])) {
            $AplicaFiltro = $data["ConfigVendas"]["complemento"];
            $ConfigVendas = $ConfigVendas->where("config_clientes.complemento", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigVendas"]["bairro"])) {
            $AplicaFiltro = $data["ConfigVendas"]["bairro"];
            $ConfigVendas = $ConfigVendas->where("config_clientes.bairro", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigVendas"]["cep"])) {
            $AplicaFiltro = $data["ConfigVendas"]["cep"];
            $ConfigVendas = $ConfigVendas->where("config_clientes.cep", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigVendas"]["cidade"])) {
            $AplicaFiltro = $data["ConfigVendas"]["cidade"];
            $ConfigVendas = $ConfigVendas->where("config_clientes.cidade", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigVendas"]["estado"])) {
            $AplicaFiltro = $data["ConfigVendas"]["estado"];
            $ConfigVendas = $ConfigVendas->where("config_clientes.estado", $AplicaFiltro);
        }



        $ConfigVendas = $ConfigVendas->get();

        $Dadosconfig_clientes = [];
        foreach ($ConfigVendas as $config_clientess) {
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

        $permUser = Auth::user()->hasPermissionTo("create.ConfigVendas");

        if (!$permUser) {
            return redirect()->route("list.Dashboard", ["id" => "1"]);
        }


        $filePath = "Relatorio_ConfigVendas.xlsx";

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
        $spreadsheet->getActiveSheet()->setTitle("ConfigVendas");

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
        $nomeArquivo = "Relatorio_ConfigVendas.xlsx";
        // Cria o arquivo
        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($nomeArquivo);
        $barra = "'/'";
        $barra = str_replace("'", "", $barra);
        $writer->save(storage_path("app" . $barra . "relatorio" . $barra . $nomeArquivo));

        return redirect()->route("download2.files", ["path" => $nomeArquivo]);
    }
}

