import axios from "axios";
export const getId = () => {

    const getVeiculos = async () => {
        try {
            const response = await axios.get(route('get.nome.ConfigVeiculo'));
            return response.data;
        } catch (error) {
            console.error("Erro ao buscar veículos:", error);
            return [];
        }
    };

    const getClientes = async () => {
        try {
            const response = await axios.get(route('get.nome.ConfigCliente'));
            return response.data;
        } catch (error) {
            console.error("Erro ao buscar Clientes:", error);
            return [];
        }
    };

      return {
        getVeiculos,
        getClientes
      };
}
