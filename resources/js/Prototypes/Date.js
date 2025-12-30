Date.prototype.toDate = function () {
	return this
}

Date.prototype.toDataBase = function () {
	return (this.toJSON()).substr(0, 10)
}

Date.prototype.toDateTime = function () {
	return (this.toJSON()).replace('T', ' ').substr(0, 19)
}

Date.prototype.toBrFormat = function (time = false) {
	return (this.toDataBase()).split('-').reverse().join('/')
		+ (time ? ` às ${this.toJSON().substr(11, 5)}` : '')
}

Date.prototype.incrementMonths = function (increment = 1) {
	this.setMonth(this.getMonth() + increment);
	return this;
}

Date.prototype.incrementDays = function (increment = 1) {
	this.setDate(this.getDate() + increment);
	return this;
}