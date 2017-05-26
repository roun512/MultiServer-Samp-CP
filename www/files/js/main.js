function CODCutStats() {
	var per = document.getElementById('cut_per').value;
	var score = document.getElementById('score');
	var money = document.getElementById('money');
	var kills = document.getElementById('kills');
	score.value = Math.round(score.value - ((score.value*per)/100));
	money.value = Math.round(money.value - ((money.value*per)/100));
	kills.value = Math.round(kills.value - ((kills.value*per)/100));
	var dchecked = document.getElementById('dchecked');
	if(dchecked.checked) {
		var deaths = document.getElementById('deaths');
		deaths.value = Math.round(deaths.value - ((deaths.value*per)/100));
	}
}

function CNRCutStats() {
	var per = document.getElementById('cut_per').value;
	var pscore = document.getElementById('pscore');
	var cscore = document.getElementById('cscore');
	var money = document.getElementById('money');
	var bmoney = document.getElementById('bmoney');
	var arrests = document.getElementById('arrests');
	pscore.value = Math.round(pscore.value - ((pscore.value*per)/100));
	cscore.value = Math.round(cscore.value - ((cscore.value*per)/100));
	money.value = Math.round(money.value - ((money.value*per)/100));
	bmoney.value = Math.round(bmoney.value - ((bmoney.value*per)/100));
	arrests.value = Math.round(arrests.value - ((arrests.value*per)/100));
	var achecked = document.getElementById('achecked');
	if(achecked.checked) {
		var arrested = document.getElementById('arrested');
		arrested.value = Math.round(arrested.value - ((arrested.value*per)/100));
	}
}