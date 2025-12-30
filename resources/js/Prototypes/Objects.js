Object.prototype.getKeys = function () {
	return Object.keys(this);
}

Object.prototype.getValues = function () {
	return Object.values(this);
}

Object.prototype.deny = function () {
	for (const keys of this.getKeys()) {
		if (typeof this[keys] === "object") {
			this[keys] = this[keys].deny();
		}
	}
	return Object.freeze(this);
}

Object.prototype.clone = function () {
	return JSON.parse(JSON.stringify(this));
}

Array.prototype.clone = function () {
	return JSON.parse(JSON.stringify(this));
}

Object.prototype.extend = function (object) {
	for (const property in object) {
		if (this.hasOwnProperty(property)) {
			this[property] = object[property] instanceof HTMLElement || object[property] instanceof Function || typeof object[property] !== 'object'
				? object[property]
				: (object[property]?.clone());
		}
	}
}