<?php

namespace App\Http\Controllers;

use App\Http\Requests\CriarManutencaoRequest;
use App\Http\Requests\CriarVeiculoRequest;
use App\Http\Requests\updateManutencaoRequest;
use Exception;
use App\Models\DisabledColumns;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Office;
use App\Service\ManutencaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use stdClass;

class ConfigManutencoes extends Controller
{

    protected $ManutencaoService;

    public function __construct(ManutencaoService $ManutencaoService)
    {
        $this->ManutencaoService = $ManutencaoService;
    }

    public function index(Request $request)
    {
       return $this->ManutencaoService->index($request);
    }

    public function create()
    {
        $Modulo = "ConfigManutencoes";
        $permUser = Auth::user()->hasPermissionTo("create.ConfigManutencoes");

        if (!$permUser) {
            return redirect()->route("list.Dashboard", ["id" => "1"]);
        }
        try {



            $Acao = "Abriu a Tela de Cadastro do Módulo de ConfigManutencoes";
            $Logs = new logs;
            $Registra = $Logs->RegistraLog(1, $Modulo, $Acao);

            return Inertia::render("ConfigManutencoes/Create", []);
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

    public function store(CriarManutencaoRequest $request)
    {
        $validatedData = $request->validated();
        return $this->ManutencaoService->store($validatedData);
    }




    public function edit($idConfigManutencoes)
    {
        return $this->ManutencaoService->edit($idConfigManutencoes);
    }


    public function update(updateManutencaoRequest $request, $id)
    {       
       $validatedData = $request->validated();
       return $this->ManutencaoService->update($validatedData, $id);
    }

    public function delete($IDConfigManutencoes)
    {
       return $this->ManutencaoService->destroy($IDConfigManutencoes);
    }

    public function deleteSelected($IDConfigManutencoes = null)
    {
      return $this->ManutencaoService->deleteSelected($IDConfigManutencoes);
    }



    public function deletarTodos()
    {
       return $this->ManutencaoService->deletarTodos();
    }

    public function RestaurarTodos()
    {
       return $this->ManutencaoService->restaurarTodos();
    }

    public function DadosRelatorio()
    {
        $data = Session::all();

        $ConfigManutencoes = DB::table("config_veiculos")

            ->select(DB::raw("config_veiculos.*, DATE_FORMAT(config_veiculos.created_at, '%d/%m/%Y - %H:%i:%s') as data_final

            "))
            ->where("config_veiculos.deleted", "0");

        //MODELO DE FILTRO PARA VOCE COLOCAR AQUI, PARA CADA COLUNA DO BANCO DE DADOS DEVERÁ TER UM IF PARA APLICAR O FILTRO, EXCLUIR O FILTRO DE ID, DELETED E UPDATED_AT


        if (isset($data["ConfigManutencoes"]["nome"])) {
            $AplicaFiltro = $data["ConfigManutencoes"]["nome"];
            $ConfigManutencoes = $ConfigManutencoes->Where("config_veiculos.nome",  "like", "%" . $AplicaFiltro . "%");
        }


        if (isset($data["ConfigManutencoes"]["cpf"])) {
            $AplicaFiltro = $data["ConfigManutencoes"]["cpf"];
            $ConfigManutencoes = $ConfigManutencoes->where("config_veiculos.cpf", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigManutencoes"]["data_nascimento"])) {
            $AplicaFiltro = $data["ConfigManutencoes"]["data_nascimento"];
            $ConfigManutencoes = $ConfigManutencoes->whereDate("config_veiculos.data_nascimento", $AplicaFiltro);
        }

        if (isset($data["ConfigManutencoes"]["telefone"])) {
            $AplicaFiltro = $data["ConfigManutencoes"]["telefone"];
            $ConfigManutencoes = $ConfigManutencoes->where("config_veiculos.telefone", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigManutencoes"]["email"])) {
            $AplicaFiltro = $data["ConfigManutencoes"]["email"];
            $ConfigManutencoes = $ConfigManutencoes->where("config_veiculos.email", "like", "%" . $AplicaFiltro . "%");
        }

        // Campos de endereço
        if (isset($data["ConfigManutencoes"]["logradouro"])) {
            $AplicaFiltro = $data["ConfigManutencoes"]["logradouro"];
            $ConfigManutencoes = $ConfigManutencoes->where("config_veiculos.logradouro", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigManutencoes"]["numero"])) {
            $AplicaFiltro = $data["ConfigManutencoes"]["numero"];
            $ConfigManutencoes = $ConfigManutencoes->where("config_veiculos.numero", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigManutencoes"]["complemento"])) {
            $AplicaFiltro = $data["ConfigManutencoes"]["complemento"];
            $ConfigManutencoes = $ConfigManutencoes->where("config_veiculos.complemento", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigManutencoes"]["bairro"])) {
            $AplicaFiltro = $data["ConfigManutencoes"]["bairro"];
            $ConfigManutencoes = $ConfigManutencoes->where("config_veiculos.bairro", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigManutencoes"]["cep"])) {
            $AplicaFiltro = $data["ConfigManutencoes"]["cep"];
            $ConfigManutencoes = $ConfigManutencoes->where("config_veiculos.cep", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigManutencoes"]["cidade"])) {
            $AplicaFiltro = $data["ConfigManutencoes"]["cidade"];
            $ConfigManutencoes = $ConfigManutencoes->where("config_veiculos.cidade", "like", "%" . $AplicaFiltro . "%");
        }

        if (isset($data["ConfigManutencoes"]["estado"])) {
            $AplicaFiltro = $data["ConfigManutencoes"]["estado"];
            $ConfigManutencoes = $ConfigManutencoes->where("config_veiculos.estado", $AplicaFiltro);
        }



        $ConfigManutencoes = $ConfigManutencoes->get();

        $Dadosconfig_veiculos = [];
        foreach ($ConfigManutencoes as $config_veiculoss) {
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

        $permUser = Auth::user()->hasPermissionTo("create.ConfigManutencoes");

        if (!$permUser) {
            return redirect()->route("list.Dashboard", ["id" => "1"]);
        }


        $filePath = "Relatorio_ConfigManutencoes.xlsx";

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
        $spreadsheet->getActiveSheet()->setTitle("ConfigManutencoes");

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
        $nomeArquivo = "Relatorio_ConfigManutencoes.xlsx";
        // Cria o arquivo
        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($nomeArquivo);
        $barra = "'/'";
        $barra = str_replace("'", "", $barra);
        $writer->save(storage_path("app" . $barra . "relatorio" . $barra . $nomeArquivo));

        return redirect()->route("download2.files", ["path" => $nomeArquivo]);
    }
}

