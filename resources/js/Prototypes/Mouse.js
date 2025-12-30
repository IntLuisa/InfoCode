MouseEvent.prototype.getTarget = function () {
	const event = this || window.event;
	return event.target || event.srcElement;
}
