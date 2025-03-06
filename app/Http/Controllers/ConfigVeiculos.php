<?php

namespace App\Http\Controllers;

use App\Http\Requests\CriarVeiculoRequest;
use App\Http\Requests\updateVeiculoRequest;
use Exception;
use App\Models\DisabledColumns;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Office;
use App\Service\VeiculoService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use stdClass;

class ConfigVeiculos extends Controller
{

    protected $veiculoService;

    public function __construct(VeiculoService $veiculoService)
    {
        $this->veiculoService = $veiculoService;
    }

    public function index(Request $request)
    {
       return $this->veiculoService->index($request);
    }

    public function create()
    {
        $Modulo = "ConfigVeiculos";
        $permUser = Auth::user()->hasPermissionTo("create.ConfigVeiculos");

        if (!$permUser) {
            return redirect()->route("list.Dashboard", ["id" => "1"]);
        }
        try {



            $Acao = "Abriu a Tela de Cadastro do Módulo de ConfigVeiculos";
            $Logs = new logs;
            $Registra = $Logs->RegistraLog(1, $Modulo, $Acao);

            return Inertia::render("ConfigVeiculos/Create", []);
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

    public function store(CriarVeiculoRequest $request)
    {
        $validatedData = $request->validated();
        return $this->veiculoService->store($validatedData);
    }




    public function edit($idConfigVeiculos)
    {
        return $this->veiculoService->edit($idConfigVeiculos);
    }


    public function update(updateVeiculoRequest $request, $id)
    {
       $validatedData = $request->validated();
       return $this->veiculoService->update($validatedData, $id);
    }

    public function delete($IDConfigVeiculos)
    {
       return $this->veiculoService->destroy($IDConfigVeiculos);
    }

    public function deleteSelected($IDConfigVeiculos = null)
    {
      return $this->veiculoService->deleteSelected($IDConfigVeiculos);
    }



    public function deletarTodos()
    {
       return $this->veiculoService->deletarTodos();
    }

    public function RestaurarTodos()
    {
       return $this->veiculoService->restaurarTodos();
    }

    public function DadosRelatorio()
    {
        $data = Session::all();

        $ConfigVeiculos = DB::table("config_veiculos")

            ->select(DB::raw("config_veiculos.*, DATE_FORMAT(config_veiculos.created_at, '%d/%m/%Y - %H:%i:%s') as data_final

            "))
            ->where("config_veiculos.deleted", "0");

        //MODELO DE FILTRO PARA VOCE COLOCAR AQUI, PARA CADA COLUNA DO BANCO DE DADOS DEVERÁ TER UM IF PARA APLICAR O FILTRO, EXCLUIR O FILTRO DE ID, DELETED E UPDATED_AT


        if (isset($data["ConfigVeiculos"]["nome"])) {
            $AplicaFiltro = $data["ConfigVeiculos"]["nome"];
            $ConfigVeiculos = $ConfigVeiculos->Where("config_veiculos.nome",  "like", "%" . $AplicaFiltro . "%");
        }


        if (isset($data["ConfigVeiculos"]["cpf"])) {
            $AplicaFiltro = $data["ConfigVeiculos"]["cpf"];
            $ConfigVeiculos = $ConfigVeiculos->where("config_veiculos.cpf", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigVeiculos"]["data_nascimento"])) {
            $AplicaFiltro = $data["ConfigVeiculos"]["data_nascimento"];
            $ConfigVeiculos = $ConfigVeiculos->whereDate("config_veiculos.data_nascimento", $AplicaFiltro);
        }

        if (isset($data["ConfigVeiculos"]["telefone"])) {
            $AplicaFiltro = $data["ConfigVeiculos"]["telefone"];
            $ConfigVeiculos = $ConfigVeiculos->where("config_veiculos.telefone", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigVeiculos"]["email"])) {
            $AplicaFiltro = $data["ConfigVeiculos"]["email"];
            $ConfigVeiculos = $ConfigVeiculos->where("config_veiculos.email", "like", "%" . $AplicaFiltro . "%");
        }

        // Campos de endereço
        if (isset($data["ConfigVeiculos"]["logradouro"])) {
            $AplicaFiltro = $data["ConfigVeiculos"]["logradouro"];
            $ConfigVeiculos = $ConfigVeiculos->where("config_veiculos.logradouro", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigVeiculos"]["numero"])) {
            $AplicaFiltro = $data["ConfigVeiculos"]["numero"];
            $ConfigVeiculos = $ConfigVeiculos->where("config_veiculos.numero", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigVeiculos"]["complemento"])) {
            $AplicaFiltro = $data["ConfigVeiculos"]["complemento"];
            $ConfigVeiculos = $ConfigVeiculos->where("config_veiculos.complemento", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigVeiculos"]["bairro"])) {
            $AplicaFiltro = $data["ConfigVeiculos"]["bairro"];
            $ConfigVeiculos = $ConfigVeiculos->where("config_veiculos.bairro", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigVeiculos"]["cep"])) {
            $AplicaFiltro = $data["ConfigVeiculos"]["cep"];
            $ConfigVeiculos = $ConfigVeiculos->where("config_veiculos.cep", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigVeiculos"]["cidade"])) {
            $AplicaFiltro = $data["ConfigVeiculos"]["cidade"];
            $ConfigVeiculos = $ConfigVeiculos->where("config_veiculos.cidade", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigVeiculos"]["estado"])) {
            $AplicaFiltro = $data["ConfigVeiculos"]["estado"];
            $ConfigVeiculos = $ConfigVeiculos->where("config_veiculos.estado", $AplicaFiltro);
        }



        $ConfigVeiculos = $ConfigVeiculos->get();

        $Dadosconfig_veiculos = [];
        foreach ($ConfigVeiculos as $config_veiculoss) {
            if ($config_veiculoss->status == "0") {
                $config_veiculoss->status = "Ativo";
            }
            if ($config_veiculoss->status == "1") {
                $config_veiculoss->status = "Inativo";
            }
            $Dadosconfig_veiculos[] = [
                //MODELO DE CA,MPO PARA VOCE COLOCAR AQUI, PARA CADA COLUNA DO BANCO DE DADOS DEVERÁ TER UM, EXCLUIR O ID, DELETED E UPDATED_AT
                'nome' => $config_veiculoss->nome,

            ];
        }
        return $Dadosconfig_veiculos;
    }

    public function exportarRelatorioExcel()
    {

        $permUser = Auth::user()->hasPermissionTo("create.ConfigVeiculos");

        if (!$permUser) {
            return redirect()->route("list.Dashboard", ["id" => "1"]);
        }


        $filePath = "Relatorio_ConfigVeiculos.xlsx";

        if (Storage::disk("public")->exists($filePath)) {
            Storage::disk("public")->delete($filePath);
            // Arquivo foi deletado com sucesso
        }

        $cabecalhoAba1 = array('nome', 'placa', 'modelo', 'ano', 'cor', 'valor_compra', 'observacao', 'status', 'Data de Cadastro');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $config_veiculos = $this->DadosRelatorio();

        // Define o título da primeira aba
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle("ConfigVeiculos");

        // Adiciona os cabeçalhos da tabela na primeira aba
        $spreadsheet->getActiveSheet()->fromArray($cabecalhoAba1, null, "A1");

        // Adiciona os dados da tabela na primeira aba
        $spreadsheet->getActiveSheet()->fromArray($config_veiculos, null, "A2");

        // Definindo a largura automática das colunas na primeira aba
        foreach ($spreadsheet->getActiveSheet()->getColumnDimensions() as $col) {
            $col->setAutoSize(true);
        }

        // Habilita a funcionalidade de filtro para as células da primeira aba
        $spreadsheet->getActiveSheet()->setAutoFilter($spreadsheet->getActiveSheet()->calculateWorksheetDimension());


        // Define o nome do arquivo
        $nomeArquivo = "Relatorio_ConfigVeiculos.xlsx";
        // Cria o arquivo
        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($nomeArquivo);
        $barra = "'/'";
        $barra = str_replace("'", "", $barra);
        $writer->save(storage_path("app" . $barra . "relatorio" . $barra . $nomeArquivo));

        return redirect()->route("download2.files", ["path" => $nomeArquivo]);
    }


    public function getVeiculos()
    {
        return $this->veiculoService->getVeiculos();
    }
}

