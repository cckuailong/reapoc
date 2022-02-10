//@see http://stackoverflow.com/a/9318724
Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator) {
    var n = this,
    	decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
    	decSeparator = decSeparator == undefined ? "." : decSeparator,
    	thouSeparator = thouSeparator == undefined ? "," : thouSeparator,
    	sign = n < 0 ? "-" : "",
    	i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
    	j = (j = i.length) > 3 ? j % 3 : 0;
    return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
};