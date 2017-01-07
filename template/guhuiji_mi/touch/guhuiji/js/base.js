function menuShow() {
	if (G('#main').css('left') != '0px') {
		G('#main').css('left', '0px');
		G('#bug').css('left', '0px');
		G('body').css('height', '').css('overflow','');
		
	} else {
		G('#main').css('left', '240px');
		G('#bug').css('left', '240px');
		G('body').css('height', G('.nav_left').css('height')).css('overflow','hidden');
	}
}

function menuLeft(e) {
	if (G('#main').css('left') == '240px') {
		G('#main').css('left', '0px');
		G('#bug').css('left', '0px');
		G('body').css('height', '').css('overflow','');
		if (e && e.preventDefault) {
			e.preventDefault();
		} else {
			window.event.returnValue = false;
		}
	}

}