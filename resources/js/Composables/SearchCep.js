import axios from "axios";
export const SearchCep = () => {

    const getAddressByCep = async (cep) => {
        console.log(cep)
        cep = cep.replace(/\D/g, ""); // Remove caracteres não numéricos

        if (cep.length !== 8) {
          return { error: "CEP inválido" };
        }

        try {
          const response = await axios.get(`https://viacep.com.br/ws/${cep}/json/`);
          if (response.data.erro) {
            return { error: "CEP não encontrado" };
          }
          return response.data; // Retorna o endereço completo
        } catch (error) {
          return { error: "Erro ao consultar o CEP" };
        }
      };

      return {
         getAddressByCep
      };
}
