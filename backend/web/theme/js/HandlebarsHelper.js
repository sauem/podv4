Handlebars.registerHelper("money", function (value, options) {
    let dl = options.hash['decimalLength'] || 0;
    let ts = options.hash['thousandsSep'] || ',';
    let ds = options.hash['decimalSep'] || '.';

    // Parse to float
    value = parseFloat(value);

    // The regex
    let re = '\\d(?=(\\d{3})+' + (dl > 0 ? '\\D' : '$') + ')';

    // Formats the number with the decimals
    let num = value.toFixed(Math.max(0, ~~dl));

    // Returns the formatted number
    return SYMBOL_MARKET + (ds ? num.replace('.', ds) : num).replace(new RegExp(re, 'g'), '$&' + ts);
});
Handlebars.registerHelper("empty", function (val) {
    if (typeof val === undefined || val === "" || val === null || !val) {
        return null;
    }
    return val;
});
Handlebars.registerHelper("dateFormat", function (time) {
    return moment.unix(time).format('DD/MM/YYYY');
});
Handlebars.registerHelper("cashSource", function (val) {
    const SOURCE_CASH = 'cash';
    const SOURCE_COD = 'cod';
    switch (val) {
        case SOURCE_CASH:
            return "Tiền mặt";
        case SOURCE_COD:
            return "Số dư COD";
        default:
            return "---";
    }
});