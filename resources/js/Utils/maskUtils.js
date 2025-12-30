export const formatCPF = value => {
    value = value.replace(/\D/g, "");

    if (value.length <= 11) {
        return value.substr(0, 11)
            .replace(/(\d{3})(\d)/, "$1.$2")
            .replace(/(\d{3})(\d)/, "$1.$2")
            .replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    } else {
        return value.substr(0, 14)
            .replace(/(\d{2})(\d)/, "$1.$2")
            .replace(/(\d{3})(\d)/, "$1.$2")
            .replace(/(\d{3})(\d)/, "$1/$2")
            .replace(/(\d{4})(\d{1,2})$/, "$1-$2");
    }
}

export const formatPhone = value => value
    .replace(/\D/g, "")
    .replace(/^(\d{2})(\d)/, "($1) $2")
    .replace(/(\d{4,5})(\d{4})$/, "$1-$2");

export const formatCEP = value => value.replace(/\D/g, "")
    .replace(/(\d{2})(\d)/, "$1.$2")
    .replace(/(\d{3})(\d)/, "$1-$2");

export const formatCurrency = value => {
    if (!isNaN(value)) {
        value = (+value).toFixed(2);
    };
    value = `${value}`.replace(/\D/g, "") || '0';
    value = (parseFloat(value) / 100).toFixed(2);
    value = isNaN(value) ? '0.00' : value;
    value = value.replace(".", ",");
    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return value;
}
