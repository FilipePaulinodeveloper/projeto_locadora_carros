export const Mask = () => {

    const applyPhoneMask = (phone) => {
        if (!phone) return "";
        phone = phone.replace(/\D/g, "");

        if (phone.length > 10) {
          phone = phone.replace(/^(\d{2})(\d{5})(\d{4}).*/, "($1) $2-$3");
        } else if (phone.length > 6) {
          phone = phone.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, "($1) $2-$3");
        } else if (phone.length > 2) {
          phone = phone.replace(/^(\d{2})(\d{0,5})/, "($1) $2");
        } else {
          phone = phone.replace(/^(\d{0,2})/, "($1");
        }
        console.log(phone.length)
        return phone;
      };

    const applyCpfMask = (cpf) => {
        if (!cpf) return "";
        cpf = cpf.replace(/\D/g, "");
        cpf = cpf.substring(0, 11);


        cpf = cpf.replace(/^(\d{3})(\d)/, "$1.$2");
        cpf = cpf.replace(/^(\d{3})\.(\d{3})(\d)/, "$1.$2.$3");
        cpf = cpf.replace(/^(\d{3})\.(\d{3})\.(\d{3})(\d{0,2})/, "$1.$2.$3-$4");

        return cpf;
    };


    const applyDateMask = (date) => {
        if (!date) return "";

        date = date.replace(/\D/g, "");
        date = date.substring(0, 8);


        date = date.replace(/^(\d{2})(\d)/, "$1/$2");
        date = date.replace(/^(\d{2})\/(\d{2})(\d)/, "$1/$2/$3");

        return date;
    };

    const formatDateForDatabase = (date) => {
        if (!date) return "";


        const regex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
        const match = date.match(regex);

        if (!match) return "";

        const [, day, month, year] = match;
        return `${year}-${month}-${day}`;
    };





    return {
        applyPhoneMask,
        applyCpfMask,
        applyDateMask,
        formatDateForDatabase
    }
}
