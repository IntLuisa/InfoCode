KeyboardEvent.prototype.getCode = function () {
	return this.which || this.keyCode;
}

KeyboardEvent.prototype.onlyNumber = function () {
	const code = this.getCode();
	if (code > 31 && (code < 48 || code > 57)) {
		this.preventDefault();
	}
}

KeyboardEvent.prototype.isEnter = function () {
	return this.getCode() === 13;
}