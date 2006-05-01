function chloc(newloc) {
	window.location = newloc;
}
function chweek() {
	var weeknum;
	weeknum = document.forms["selweek"].week.options[document.forms["selweek"].week.selectedIndex].value
	chloc("#week"+weeknum);
}