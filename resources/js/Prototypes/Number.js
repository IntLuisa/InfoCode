import './String';

Number.prototype.pad = function (length) {
    return this.toString().pad(length);
}

Number.prototype.toNumber = function () {
    return this;
}

Number.prototype.toCurrency = function (hasBracket = false, hasCipher = false) {
    const signal = ['', ''];
    const splited = [];

    let number = this;

    if (this < 0) {
        signal[0] = "-";
        if (hasBracket && !hasCipher) {
            signal[0] = "(";
            signal[1] = ")";
        }
        number *= -1;
    }

    const [integer, decimal] = number.toFixed(2).split(".");

    for (let length = integer.length; length > 0; length -= 3) {
        splited.push(integer.substring(length - 3, length));
    }

    return `${signal[0]}${hasCipher ? 'R$&nbsp;' : ''}${splited.reverse().join('.')},${(decimal || 0).pad(2)}${signal[1]}`
}

Number.prototype.extensive = function (params) {
    return this.toString().extensive(params);
}

Number.prototype.toArray = function (length) {
    return this.toString().toArray(length);
}