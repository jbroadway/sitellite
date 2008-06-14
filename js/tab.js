function Tab (id) {
	this.id = id;
}

Tab.prototype.show = function () {
	document.getElementById (this.id).style.display = 'normal';
}

Tab.prototype.hide = function () {
	document.getElementById (this.id).style.display = 'none';
}

function Tabs () {
	this.tabs = new Array ();
}

Tabs.prototype.add = function (id) {
	this.tabs[id] = new Tab (id);
}

Tabs.prototype.show = function (id) {
	this.hideAll ();
	this.tabs[id].show ();
}

Tabs.prototype.hide = function (id) {
	this.tabs[id].hide ();
}

Tabs.prototype.hideAll = function () {
	for (i = 0; i < this.tabs.length; i++) {
		this.tabs[i].hide ();
	}
}
