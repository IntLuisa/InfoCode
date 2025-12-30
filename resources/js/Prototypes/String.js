String.prototype.pad = function (length) {
	if (this.length > length) {
		length = this.length;
	}

	return `000000000${this}`.substr(-length);
}

String.prototype.toNumber = function () {
	if (!isNaN(this)) {
		return this * 1;
	}

	const signal = this.indexOf("(") !== -1 || this.indexOf("-") !== -1 ? -1 : 1;
	const number = parseFloat(Math.round((this.replace(/[^0-9,]/g, '').replace(',', '.')) * 100) / 100);
	return isNaN(number) ? 0 : number * signal;
}

String.prototype.toCurrency = function () {
	return this.toNumber().toCurrency();
}

String.prototype.toDate = function () {
	try {
		const [date, time] = this.trim().split(' ');
		const [year, month, day] = date.includes('-')
			? date.split('-').map(Number)
			: date.split('/').reverse().map(Number);

		const _date = new Date(Date.UTC(year, month - 1, day));

		if (time) {
			const [hours, minutes, seconds] = time.split(':').map(Number);
			_date.setUTCHours(hours || 0, minutes || 0, seconds || 0);
		}

		if (isNaN(_date.getTime())) {
			throw true;
		}

		return _date;
	} catch (error) {
		return null;
	}
}

String.prototype.toArray = function (length = 1) {
	const char = '|/';
	return `${this}${char}`.repeat(length).split(char).slice(0, -1);
}

String.prototype.extensive = function (isCurrency = false) {
	const names = [
		["zero", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove", "dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezessete", "dezoito", "dezenove"],
		["dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa"],
		["cem", "cento", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos"],
		["mil", "milhão", "bilhão", "trilhão", "quadrilhão", "quintilhão", "sextilhão", "setilhão", "octilhão", "nonilhão", "decilhão", "undecilhão", "dodecilhão", "tredecilhão", "quatrodecilhão", "quindecilhão", "sedecilhão", "septendecilhão", "octencilhão", "nonencilhão"]
	];

	function hundreds(value) {
		const length = value.toNumber().toString().length + (value < 20 && value > 9 ? -2 : -1);
		const divider = `1${"0".repeat(length)}` * 1;
		const index = value / divider >> 0;
		const difference = value - (divider * index);

		return names[length][index + (value <= 100 && value > 19 ? -1 : 0)]
			+ (difference ? ` e ${hundreds(difference)}` : '')
	}

	const value = this.toNumber().toFixed(2);
	const [integer, float] = value.split(".");
	const length = integer.length;
	const groupBy3 = integer.slice(length % 3).match(/\d{3}/g) || [];
	const difference = length % 3 ? [integer.slice(0, length % 3)] : [];
	const arrayNumbers = difference.concat(groupBy3);

	for (let index = 0, length = arrayNumbers.length; index < length; index++) {
		const indexMilhar = (length - index - 2);
		const milhar = names[3][indexMilhar];
		const value = arrayNumbers[index];

		arrayNumbers[index] = value == '000' ? '' : (hundreds(value * 1)
			+ (milhar ? ` ${(indexMilhar > 0 && value > 1) ? milhar.replace("ão", "ões") : milhar}` : ''));
	}

	return `${arrayNumbers.join(' ')}${isCurrency ? ` rea${arrayNumbers.join() == 'um' ? 'l' : 'is'}` : ''}${float.toNumber() ? ` e ${hundreds(float)}${isCurrency ? ` centavo${float == 1 ? '' : 's'}` : ''}` : ''}`;
}