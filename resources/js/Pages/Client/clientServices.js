import axios from "axios";

export const getAddressByCep = zip => {
    return new Promise((resolve, reject) => {
        const zip_code = zip.replace(/[^0-9]/g, '');

        if (!zip_code || zip_code.length < 8) {
            reject('CEP inválido');
            return;
        }

        axios
            .get(`https://viacep.com.br/ws/${zip_code}/json/`)
            .then(response => {
                resolve(response.data);
            })
            .catch(reject);
    });
};