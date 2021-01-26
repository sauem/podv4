const MODULE_CONTACT = 'contact';
const MODULE_ORDER = 'order';
const MODULE_CONTACT_LOG = 'contact-log';
const MODULE_PRODUCT = 'product';
const MODULE_PRODUCT_CATEGORY = 'category';
const MODULE_ORDER_EXAMPLE = 'order-example';
const MODULE_TRACKING = 'tracking';
const MODULE_REFUND = 'refund';
const MODULE_PAID = 'paid';
const MODULE_CROSSED = 'crossed';
const MODULE_COUNTRY = 'country';

async function handleReadExcel(evt) {
    this.file = $(evt)[0].files[0];
    this.importWrap = $('.import-wrap');
    this.importArea = $(this.importWrap).find('.import-area');
    this.importNote = $(this.importWrap).find('.note');
    this.fileSizeText = $(this.importWrap).find('.fileSizeText');
    this.totalRowsText = $(this.importWrap).find('.totalRowsText');
    this.module = $(evt).data('module');

    this.config = {
        maxRow: 50000,
        loadingContent: '<div class="loading spinner-border avatar-lg text-success" role="status"></div>',
        fileType: ["xlsx", "csv", "slx", "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"],
    }
    if (!this.config.fileType.includes(this.file.type)) {
        toastr.warning("Định dạng file không đúng!");
        return false;
    }
    this.showLoading = function () {
        $(this.importArea).append(this.config.loadingContent);
    }
    this.hideLoading = function () {
        $(this.importArea).find('.loading').remove();
    }
    this.showNote = function () {
        $(this.importNote).show();
    }
    this.hideNote = function () {
        $(this.importNote).hide();
    }
    this.setFileInfo = function (totalRow, fileSize) {
        $(this.totalRowsText).text(totalRow);
        $(this.fileSizeText).text(bytesToSize(fileSize));
    }
    this.setFileName = function () {
        this.showNote();
        $(this.importNote).find('p').text(this.file.name);
    }
    // read file data
    this.hideNote();
    this.showLoading();
    try {
        const rows = await getFileRows.call(this);
        renderTemplate(this.module, rows);
        window.DATA = rows;
    } catch (e) {
        toastr.warning(e.message);
    } finally {
        this.hideLoading();
        this.setFileName();
    }
}

async function actionSave(module) {
    let url = AJAX_PATH[`${module}Import`];
    let data = window.DATA;
    let errorsHtml = $("#errors-template").html();
    let errTemp = Handlebars.compile(errorsHtml);
    let errorsTable = $("#errors-table");

    let index = 0,
        errorRows = 0;
    if (!data && data.length <= 0) {
        toastr.warning('Không tìm thấy dữ liệu!');
        return false;
    }
    while (typeof data[index] !== "undefined") {

        try {
            const res = await doPushData(url, data[index]);
            console.log("Reponse :", res);
        } catch (e) {
            console.log("Error :", e);
            errorRows++;
            if (!$("#tab-2").hasClass('active')) {
                $("a[href='#tab-2']").trigger("click");
            }
            let errorItem = {row: (index + 1), message: JSON.parse(e.responseText).message};
            $("#errors-result").prepend(errTemp(errorItem));
        }
        index++;
        initProgressData(index, data.length, errorRows);
    }
}

function initProgressData(currentRow, totalRows, errorRows = 0) {
    this.resultImport = $('#result-import');
    let progressPercent = Math.round(currentRow / totalRows * 100);
    let successRows = totalRows - errorRows;
    let progressTemp = $('#progress-template').html();
    let temp = Handlebars.compile(progressTemp);
    $(this.resultImport).html(temp({
        totalRows,
        successRows,
        errorRows,
        progressPercent
    }));
    if (progressPercent >= 100) {
        toastr.success('Import hoàn thành!');
    }
}

async function doPushData(url, row) {
    return $.ajax({
        url: url,
        type: 'POST',
        cache: false,
        data: {row: row},
    });
}

async function getFileRows() {
    return new Promise(resolve => {
        let reader = new FileReader();
        let instance = this;

        reader.readAsBinaryString(instance.file);
        reader.onerror = function (stuff) {
            toastr.warning(stuff.currentTarget.error.message);
        }
        reader.onload = function (evt) {
            let data = evt.target.result;
            let workbook = XLSX.read(data, {
                type: 'binary',
                cellDates: true
            });

            let firstSheet = workbook.SheetNames[0];
            let sheet = workbook.Sheets[firstSheet];
            let range = XLSX.utils.decode_range(sheet['!ref']);
            let highestRow = range.e.r - range.s.r - 1;
            instance.setFileInfo(highestRow, instance.file.size);
            resolve(processRow(sheet));
        }
        reader.onloadend = function (event) {
            instance.hideLoading();
            instance.setFileName();
        }
    });
}

function renderTemplate(module, rows) {
    let htmlTemplate = $(`#${module}-template`).html();
    let template = Handlebars.compile(htmlTemplate);
    $('#file-view-result').html(template(rows));
    initDataTable(module);
}

function initDataTable(module) {
    $(`#${module}-table`).DataTable({
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            }
        }, drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded")
        }
    });
}

function processRow(sheet) {
    let rowIndex = 2;
    let columnLength = 10;
    let rows = [];
    switch (module) {
        case MODULE_PRODUCT:
            columnLength = 8;
            break;
        case MODULE_ORDER:
            columnLength = 24;
            break;
        case MODULE_CONTACT:
            columnLength = 18;
            break;
        case MODULE_REFUND:
        case MODULE_PAID:
            columnLength = 5;
            break;
        case MODULE_CROSSED:
        case MODULE_ORDER_EXAMPLE:
        case MODULE_PRODUCT_CATEGORY:
            columnLength = 4;
            break;
        case MODULE_COUNTRY:
        case MODULE_TRACKING:
            columnLength = 6;
            break;
    }
    let row = getRow(sheet, rowIndex, columnLength);
    while (row !== null) {
        let item = mappingModel(module, row);
        rows.push(item);
        rowIndex++;
        row = getRow(sheet, rowIndex, columnLength);
    }
    return rows;
}

const mappingModel = (module, row) => {
    let item = null;
    switch (module) {
        case MODULE_PRODUCT_CATEGORY:
            item = categoryModel();
            item.name = row[0] ? row[0].v : null;
            item.country = row[1] ? row[1].v : null;
            item.partner = row[2] ? row[2].v : null;
            item.description = row[3] ? row[3].v : null;
            break;
        case MODULE_ORDER:
            item = orderModel();
            item.code = row[0] ? row[0].v : null;
            item.name = row[1] ? row[1].v : null;
            item.phone = row[2] ? row[2].v : null;
            item.address = row[3] ? row[3].v : null;
            item.option = row[4] ? row[4].v : null;
            item.zipcode = row[5] ? row[5].v : null;
            item.district = row[6] ? row[6].v : null;
            item.city = row[7] ? row[7].v : null;
            item.partner_name = row[8] ? row[8].v : null;
            item.sale = row[9] ? row[9].v : null;
            item.marketer = row[10] ? row[10].v : null;
            item.type = row[11] ? row[11].v : null;
            item.payment_method = row[12] ? row[12].v : null;
            item.bill_link = row[13] ? row[13].v : null;
            item.total_price = row[14] ? row[14].v : null;
            item.shipping_cost = row[15] ? row[15].v : null;
            item.total_bill = row[16] ? row[16].v : null;
            item.note = row[17] ? row[17].v : null;
            item.status = row[18] ? row[18].v : null;
            item.category = row[19] ? row[19].v : null;
            item.product_1 = row[20] ? row[20].v : null;
            item.product_2 = row[21] ? row[21].v : null;
            item.product_3 = row[22] ? row[22].v : null;
            item.product_4 = row[23] ? row[23].v : null;
            break;
        case MODULE_PRODUCT:
            item = productModel();
            item.sku = row[0] ? row[0].v : null;
            item.category_id = row[1] ? row[1].v : null;
            item.weight = row[2] ? row[2].v : null;
            item.size = row[3] ? row[3].v : null;
            item.prices = row[4] ? row[4].v : null;
            item.marketer_id = row[5] ? row[5].v : null;
            item.marketer_rage_start = row[6] ? getTimer(row[6].v) : null;
            item.marketer_rage_end = row[7] ? getTimer(row[7].v) : null;
            break;
        case MODULE_ORDER_EXAMPLE:
            item = orderExampleModel();
            item.category = row[0] ? row[0].v : null;
            item.option = row[1] ? row[1].v : null;
            item.items = row[2] ? row[2].v : null;
            item.total_bill = row[3] ? row[3].v : null;
            break;
        case MODULE_TRACKING:
            item = trackingModel();
            item.code = row[0] ? row[0].v : null;
            item.transport_partner = row[1] ? row[1].v : null;
            item.sub_transport = row[2] ? row[2].v : null;
            item.checking_number = row[3] ? row[3].v : null;
            item.sub_transport_tracking = row[4] ? row[4].v : null;
            item.transport_fee = row[5] ? row[5].v : null;
            break;
        case MODULE_COUNTRY:
            item = countryModel();
            item.name = row[0] ? row[0].v : null;
            item.code = row[1] ? row[1].v : null;
            item.zipcode = row[2] ? row[2].v : null;
            item.city = row[3] ? row[3].v : null;
            item.district = row[4] ? row[4].v : null;
            item.symbol = row[5] ? row[5].v : null;
            break;
        case MODULE_CONTACT:
            item = contactModel();
            item.register_time = row[0] ? getTimer(row[0].v) : null;
            item.code = row[1] ? row[1].v : null;
            item.name = row[2] ? row[2].v : null;
            item.phone = row[3] ? row[3].v : null;
            item.address = row[4] ? row[4].v : null;
            item.zipcode = row[5] ? row[5].v : null;
            item.option = row[6] ? row[6].v : null;
            item.note = row[7] ? row[7].v : null;
            item.country = row[8] ? row[8].v : null;
            item.partner = row[9] ? row[9].v : null;
            item.category = row[10] ? row[10].v : null;
            item.type = row[11] ? toUnicode(row[11].v).toLowerCase() : null;
            item.status = row[12] ? toUnicode(row[12].v).toLowerCase() : null;
            item.utm_source = row[13] ? row[13].v : null;
            item.utm_medium = row[14] ? row[14].v : null;
            item.utm_campaign = row[15] ? row[15].v : null;
            item.utm_term = row[16] ? row[16].v : null;
            item.utm_content = row[17] ? row[17].v : null;
            break;
        case MODULE_REFUND:
            item = refundModel();
            item.code = row[0] ? row[0].v : null;
            item.time_refund_success = row[1] ? getTimer(row[1].v) : null;
            item.checking_number = row[2] ? row[2].v : null;
            item.sku = row[3] ? row[3].v : null;
            item.qty = row[4] ? row[4].v : null;
            break;
        case MODULE_PAID:
            item = paidModel();
            item.code = row[0] ? row[0].v : null;
            item.checking_number = row[1] ? row[1].v : null;
            item.time_shipped_success = row[2] ? getTimer(row[2].v) : null;
            item.cod_cost = row[3] ? row[3].v : null;
            item.collection_fee = row[4] ? row[4].v : null;
            break;
        case MODULE_CROSSED:
            item = paidModel();
            item.code = row[0] ? row[0].v : null;
            item.remittance_date = row[1] ? getTimer(row[1].v) : null;
            item.partner = row[2] ? row[2].v : null;
            item.cross_check_code = row[3] ? row[3].v : null;
            break;
    }
    return item;
};

function getColumn(row, position, type = "time") {
    let item = row[position] ? row[position].v : null;
    if (type === "time") {
        return getTimer(item);
    }
    return item;
}

function categoryModel() {
    return {
        name: null,
        country: null,
        partner: null,
        description: null
    }
}

function productModel() {
    return {
        sku: null,
        category_id: null,
        marketer_id: null,
        marketer_rage_start: null,
        marketer_rage_end: null,
        weight: null,
        size: null,
        prices: null,
    }
}

function orderExampleModel() {
    return {
        category: null,
        option: null,
        total_bill: 0,
        items: null
    }
}

function refundModel() {
    return {
        code: null,
        time_refund_success: null,
        checking_number: null,
        sku: null,
        qty: null
    }
}

function paidModel() {
    return {
        code: null,
        time_shipped_success: null,
        checking_number: null,
        cod_cost: null,
        collection_fee: null
    }
}


function crossedModel() {
    return {
        code: null,
        remittance_date: null,
        partner: null,
        cross_check_code: null,
    }
}

function trackingModel() {
    return {
        code: null,
        transport_partner: null,
        checking_number: null,
        sub_transport: null,
        sub_transport_tracking: null,
        transport_fee: null
    }
}

function countryModel() {
    return {
        name: null,
        zipcode: null,
        code: null,
        city: null,
        district: null,
        symbol: null
    }
}

function contactModel() {
    return {
        register_time: null,
        code: null,
        phone: null,
        name: null,
        address: null,
        zipcode: null,
        option: null,
        note: null,
        country: null,
        partner: null,
        category: null,
        type: null,
        status: null,
        utm_source: null,
        utm_medium: null,
        utm_content: null,
        utm_term: null,
        utm_campaign: null,
    }
}

function orderModel() {
    return {
        code: null,
        name: null,
        phone: null,
        address: null,
        option: null,
        zipcode: null,
        district: null,
        city: null,
        type: null,
        partner_name: null,
        sale: null,
        marketer: null,
        payment_method: null,
        bill_link: null,
        total_price: null,
        shipping_cost: null,
        total_bill: null,
        note: null,
        status: null,
        category: null,
        product_1: null,
        product_2: null,
        product_3: null,
        product_4: null
    }
}

const getMaterial = () => {
    return [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        , 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL'
        , 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
    ];
};

function getRow(sheet, index, columnLength) {
    let alphabets = getMaterial();
    let row = [];
    for (let i = 0; i < columnLength; i++) {
        let col = alphabets[i];
        let cell = col + index;
        if (i === 0 && sheet[cell] === undefined) {
            return row = null;
        }
        if (sheet[cell] !== undefined) {
            row.push(sheet[cell]);
        } else {
            row.push(null);
        }
    }
    return row;
}

function bytesToSize(bytes) {
    let sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes === 0) return '0 Byte';
    let i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

function getTimer(time) {
    if (typeof time === 'string') {
        return moment(time, 'DD/MM/YYYY').unix();
    }
    if (typeof time === 'object') {
        return Math.round(time.getTime() / 1000);
    }
    return null;
}
