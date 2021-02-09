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
Handlebars.registerHelper('productTag', function (items) {
    let html = '';
    if (items.length > 0) {
        items.map(item => {
            html += item.sku + '*' + item.qty + ',';
        });
    } else {
        html = '---';
    }
    return html.slice(0, -1);
})
Handlebars.registerHelper("orderStatus", function (status) {
    const STATUS_NEW = 'new';
    const STATUS_PENDING = 'pending';
    const STATUS_SHIPPING = 'shipping';
    const STATUS_SHIPPED = 'shipped';


    const STATUS_PAYED = 'paid';
    const STATUS_UNPAID = 'unpaid';
    const STATUS_REFUND = 'refund';

    const STATUS_CANCEL = 'cancel';
    const STATUS_UNCROSS = 'uncross';
    const STATUS_CROSSED = 'crossed';

    const STATUS = {
        [STATUS_NEW]: 'Chưa vận chuyển',
        [STATUS_PENDING]: 'Chờ vận chuyển',
        [STATUS_SHIPPING]: 'Đang vận chuyển',
        [STATUS_SHIPPED]: 'Đã vận chuyển',
        [STATUS_UNPAID]: 'Chưa thanh toán',
        [STATUS_PAYED]: 'Đã thanh toán',
        [STATUS_REFUND]: 'Hoàn đơn',
        [STATUS_UNCROSS]: 'Chưa đối soát',
        [STATUS_CROSSED]: 'Đã đối soát',
        [STATUS_CANCEL]: 'Huỷ đơn',
    }
    return `<span class="badge badge-pill badge-info">${STATUS[status]}</span>`;
});
