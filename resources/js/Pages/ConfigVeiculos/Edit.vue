<template>
    <PageTitle title="Carros" />
    <Nav class="mt-4 text-sm text-gray-400 list-none bg-white p-3 px-10 rounded-sm flex space-x-10 shadow-sm">
        <button class="font-bold" :class="{ 'text-primary': currentNav == 1 }" @click="currentNav = 1">
            Informações Gerais
        </button>
    </Nav>
    <form @submit.prevent="submit">
        <section class="mt-6 bg-white rounded-sm p-10 shadow-sm" v-if="currentNav == 1">
            <div class="flex flex-col space-y-1">
                <SectionTitle class="text-xs text-gray-600 font-bold uppercase">INFORMAÇÕES GERAIS</SectionTitle>
                <SectionTitle class="text-xs text-gray-600">Informações essenciais para inserção de Carros no sistema.
                </SectionTitle>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-6 max-md:grid-cols-1">


                <div>
                    <span class="p-float-label">
                        <Dropdown class="w-full" v-model="form.tipo" :options="tipoVeiculos" optionLabel="name"
                            optionValue="value" dataKey="value" required />
                        <label for="tipo" class="text-sm">Tipo</label>
                    </span>
                </div>

                <div>
                    <span class="p-float-label">
                        <InputText v-model="form.marca" id="marca" type="text" class="w-full" required maxlength="50" />
                        <label for="marca" class="text-sm">Marca</label>
                    </span>
                </div>

                <div>
                    <span class="p-float-label">
                        <InputText v-model="form.modelo" id="modelo" type="text" class="w-full" required
                            maxlength="50" />
                        <label for="modelo" class="text-sm">Modelo</label>
                    </span>
                </div>

                <div>
                    <span class="p-float-label">
                        <InputText v-model="form.ano_fabricacao" id="ano_fabricacao" type="number" class="w-full"
                            required />
                        <label for="ano_fabricacao" class="text-sm">Ano de Fabricação</label>
                    </span>
                </div>

                <div>
                    <span class="p-float-label">
                        <InputText v-model="form.ano_modelo" id="ano_modelo" type="number" class="w-full" required />
                        <label for="ano_modelo" class="text-sm">Ano do Modelo</label>
                    </span>
                </div>

                <div>
                    <span class="p-float-label">
                        <InputText v-model="form.placa" id="placa" type="text" class="w-full" required maxlength="7" />
                        <label for="placa" class="text-sm">Placa</label>
                    </span>
                </div>

                <div>
                    <span class="p-float-label">
                        <InputText v-model="form.renavam" id="renavam" type="text" class="w-full" required
                            maxlength="11" />
                        <label for="renavam" class="text-sm">Renavam</label>
                    </span>
                </div>

                <div>
                    <span class="p-float-label">
                        <InputText v-model="form.chassi" id="chassi" type="text" class="w-full" required
                            maxlength="17" />
                        <label for="chassi" class="text-sm">Chassi</label>
                    </span>
                </div>

                <div>
                    <span class="p-float-label">
                        <InputText v-model="form.cor" id="cor" type="text" class="w-full" required maxlength="20" />
                        <label for="cor" class="text-sm">Cor</label>
                    </span>
                </div>

                <div>
                    <span class="p-float-label">
                        <InputText v-model="form.quilometragem" id="quilometragem" type="number" class="w-full" required
                            step="0.01" />
                        <label for="quilometragem" class="text-sm">Quilometragem</label>
                    </span>
                </div>

                <div>
                    <span class="p-float-label">
                        <Dropdown class="w-full" v-model="form.combustivel" :options="tiposCombustivel"
                            optionLabel="name" optionValue="value" dataKey="value" required />
                        <label for="combustivel" class="text-sm">Combustível</label>
                    </span>
                </div>


                <div>
                    <span class="p-float-label">
                        <InputText v-model="form.valor_venda" id="valor_venda" type="number" class="w-full"
                            step="0.01" />
                        <label for="valor_venda" class="text-sm">Valor de Venda</label>
                    </span>
                </div>

                <div>
                    <span class="p-float-label">
                        <InputText v-model="form.valor_diaria" id="valor_diaria" type="number" class="w-full"
                            step="0.01" />
                        <label for="valor_diaria" class="text-sm">Valor da Diária</label>
                    </span>
                </div>

                <div>
                    <span class="p-float-label">
                        <Dropdown class="w-full" v-model="form.disponivel_venda" :options="verdadeiroEFalso"
                            optionLabel="name" optionValue="value" dataKey="value" required />
                        <label for="disponivel_venda" class="text-sm">Disponível para Venda</label>
                    </span>
                </div>

                <div>
                    <span class="p-float-label">
                        <Dropdown class="w-full" v-model="form.disponivel_locacao" :options="verdadeiroEFalso"
                            optionLabel="name" optionValue="value" dataKey="value" required />
                        <label for="disponivel_locacao" class="text-sm">Disponível para Locação</label>
                    </span>
                </div>









                <div>
                    <span class="p-float-label">
                        <Dropdown class="w-full" v-model="form.status" :options="statusOption" optionLabel="name"
                            optionValue="value" dataKey="value" required />
                        <label for="status" class="text-sm">Status</label>
                    </span>
                </div>

            </div>
            <div class="flex space-x-5 mt-8">
                <button type="submit" :disabled="sending"
                    class="p-2 flex rounded-md bg-primary text-white px-6 text-sm font-medium items-center"
                    :class="{ 'bg-opacity-80 cursor-not-allowed': submited }">
                    <svg role="status" v-show="submited" class="mr-2 w-4 h-4 animate-spin fill-primary"
                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor"></path>
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0403 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill"></path>
                    </svg>
                    Salvar
                </button>
                <Link :href="route('list.ConfigVeiculos')" as="button" type="button"
                    class="p-2 rounded-md bg-secundary text-white px-6 text-sm font-medium">
                Voltar
                </Link>
            </div>
        </section>
    </form>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";
import moment from "moment";
import { ref, computed, defineProps } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";
import MultiSelect from "primevue/multiselect";
import Dropdown from "primevue/dropdown";
import { useToast } from "vue-toastification";



const props = defineProps({
    errorBags: Object,

});

const toast = useToast();

const sendable = ref(false);

const currentNav = ref(1);


const statusOption = [
    { name: "Ativo", value: '0' },
    { name: "Inativo", value: '1' },
];

const tipoVeiculos = [
    { name: "Moto", value: 'Moto' },
    { name: "Carro", value: 'Carro' },
];


const tiposCombustivel = [
    { name: "Gasolina", value: 'Gasolina' },
    { name: "Álcool", value: 'Álcool' },
    { name: "Flex", value: 'Flex' },
    { name: "Diesel", value: 'Diesel' },
    { name: "Elétrico", value: 'Elétrico' },
    { name: "Híbrido", value: 'Híbrido' },
];

const verdadeiroEFalso = [
    { name: "Sim", value: '1' },
    { name: "Não", value: '0' },
]


const submited = ref(false);



const form = useForm({
    token: $propsPage?.value.ConfigVeiculos?.token,

    tipo: $propsPage?.value.ConfigVeiculos?.tipo, // 'Carro' ou 'Moto'
    marca: $propsPage?.value.ConfigVeiculos?.marca,
    modelo: $propsPage?.value.ConfigVeiculos?.modelo,
    ano_fabricacao: $propsPage?.value.ConfigVeiculos?.ano_fabricacao,
    ano_modelo: $propsPage?.value.ConfigVeiculos?.ano_modelo,
    placa: $propsPage?.value.ConfigVeiculos?.placa,
    renavam: $propsPage?.value.ConfigVeiculos?.renavam,
    chassi: $propsPage?.value.ConfigVeiculos?.chassi,
    cor: $propsPage?.value.ConfigVeiculos?.cor,
    quilometragem: $propsPage?.value.ConfigVeiculos?.quilometragem,
    combustivel: $propsPage?.value.ConfigVeiculos?.combustivel, // 'Gasolina', 'Álcool', 'Flex', 'Diesel', 'Elétrico', 'Híbrido'
    valor_venda: $propsPage?.value.ConfigVeiculos?.valor_venda,
    valor_diaria: $propsPage?.value.ConfigVeiculos?.valor_diaria,
    disponivel_venda: String($propsPage?.value.ConfigVeiculos?.disponivel_venda),
    disponivel_locacao: String($propsPage?.value.ConfigVeiculos?.disponivel_locacao),
    status: String($propsPage?.value.ConfigVeiculos?.status),

    deleted: $propsPage?.value.ConfigVeiculos?.deleted,
    created_at: $propsPage?.value.ConfigVeiculos?.created_at,
    updated_at: $propsPage?.value.ConfigVeiculos?.updated_at,

});

function getFormFiltered() {
    const newForm = {};
    for (let [key, value] of Object.entries(form)) {
        if (typeof value == "object" && value?.value) {
            newForm[key] = value.value;
        } else {
            newForm[key] = value;
        }
    }
    return newForm;
}

function validateForm() {
    if (typeof form.status !== 'undefined') {
        if (!form.status) {
            throw new Error(
                toast.error(
                    "O campo Stauts não pode ser enviado vazio."
                )
            );
        }
    }


}

function submit() {
    validateForm();
    submited.value = true;
    const submitForm = getFormFiltered();

    submitForm.post(
        route("update.ConfigVeiculos", { id: $propsPage.value?.ConfigVeiculos?.token }),
        {
            preserveState: true,
            onError: (errors) => {
                if (Array.isArray(errors)) {
                    errors.forEach((error) => {
                        toast.error(error);
                    })
                } else if (typeof errors == 'object') {
                    const errorsArray = Object.values(errors);
                    errorsArray.forEach((error) => {
                        toast.error(error);
                    })
                } else {
                    toast.error(errors);
                }
            },
            onSuccess: () => {

                toast.success("Salvo com sucesso!");
            },
            onFinish: () => (submited.value = false),
        }
    );
}

function attachAvatar(e) {
    form.anexo = e.target.files[0];
}
</script>

<style scoped>
.file-input {
    display: inline-block;
    text-align: left;
    background: #fff;
    width: 100%;
    position: relative;
    border-radius: 3px;
}

.file-input>[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    z-index: 10;
    cursor: pointer;
}

.file-input>.button {
    display: inline-block;
    cursor: pointer;
    background: #eee;
    padding: 8px 16px;
    border-radius: 2px;
    margin-right: 8px;
}

.file-input:hover>.button {
    background: rgb(25, 25, 112);
    color: white;
    border-radius: 6px;
    transition: all 0.2s;
}

.file-input>.label {
    color: #333;
    white-space: nowrap;
    opacity: 0.3;
}

.file-input.-chosen>.label {
    opacity: 1;
}
</style>
