//window.ORDER_ITEMS = [];
Number.prototype.format = function (n, x) {
    let re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
}

function orderSaleForm() {
    let html = $('#sku-template').html();
    let template = Handlebars.compile(html);
    let orderItemsResult = $('#result-sku-list');
    let totalShip = $('#total-ship-text');
    let totalBill = $('#total-bill-text');
    let totalBillHidden = $('input#cost_bill');
    let totalPriceHidden = $('input#cost_product');
    let totalPriceInput = $('#total-price-input');
    let productPriceInput = $('input.input-product-price');
    let productQtyInput = $('input.input-product-qty');
    let cityInput = $('input.city');
    let districtInput = $('input.district');
    let countrySelect = $('select.country-select');
    initMaskMoney();
    let ORDER_ITEMS = {
        total: {
            total_bill: 0,
            total_price: 0,
            total_ship: 0
        },
        items: []
    };

    this.getProductInfo = async function (sku) {
        return $.ajax({
            url: AJAX_PATH.getProductInfo,
            data: {sku: sku},
            cache: false,
            type: 'POST'
        })
    }

    this.appendTemplateItem = function (item) {
        orderItemsResult.append(template(item));
        initMaskMoney();
    }
    this.removeItemTemp = function (element) {
        $(element).closest('.order-item').remove();
    }
    this.setOrderItems = function (item) {
        const {sku, id} = item;
        if (ORDER_ITEMS.items.some(value => value.sku === sku)) {
            toastr.warning('Sản phẩm đã tồn tại');
            return false;
        }
        ORDER_ITEMS.items.push({
            sku: sku,
            price: 0,
            qty: 0
        });
        return true;
    }
    this.removeOrderItems = function (key) {
        if (!ORDER_ITEMS.items.some(value => value.sku === key)) {
            toastr.warning('Sản phẩm không tồn tại!');
            return false;
        }
        let items = ORDER_ITEMS.items;
        ORDER_ITEMS.items = items.filter(id => key !== id);
        return true;
    }
    this.addProductItem = async function (id) {
        try {
            const res = await this.getProductInfo(id);
            if (this.setOrderItems(res)) {
                this.appendTemplateItem(res);
            }
        } catch (e) {
            toastr.warning(e.message);
        }
    }
    this.setShippingCost = function (number) {
        totalShip.text(number + SYMBOL_MARKET);
        ORDER_ITEMS.total.total_ship = parseFloat(number.replaceAll(',', ''));
        this.sumTotal();
    }
    this.sumTotal = function (overwrite = false) {
        let total = 0, price = 0;

        ORDER_ITEMS.items.map(item => {
            price += parseFloat(item.price);
        });
        if (!overwrite)
            ORDER_ITEMS.total.total_price = parseFloat(price);

        total = ORDER_ITEMS.total.total_price + ORDER_ITEMS.total.total_ship;
        ORDER_ITEMS.total.total_bill = total;

        //set view total price and value total price
        totalPriceInput.val(overwrite ? ORDER_ITEMS.total.total_price : price.format());
        totalPriceHidden.val(overwrite ? ORDER_ITEMS.total.total_price : price);
        //set view total bill and value total bill
        totalBillHidden.val(total);
        totalBill.text(total.format() + SYMBOL_MARKET);
    }
    this.setTotalPrice = function (value) {
        value = parseFloat(value.replaceAll(',', ''));
        ORDER_ITEMS.total.total_price = value;
        this.sumTotal(true);
    }
    this.setItemPrice = function (sku, price) {
        let index = ORDER_ITEMS.items.findIndex(item => item.sku === sku);
        ORDER_ITEMS.items[index].price = price;
        this.sumTotal();
    };
    this.removeProductItem = function (sku, element) {
        if (this.removeOrderItems(sku)) {
            this.removeItemTemp(element);
        }
    }
    this.loadItemPrice = async function (qty, sku, element) {
        try {
            const res = await this.getPrice(qty, sku);
            const {price} = res;
            element.val(price.format());
            this.setItemPrice(sku, price);
        } catch (e) {
            console.warn(JSON.parse(e.responseText).message);
        }
    }
    this.valid = function (form) {
        $(form).yiiActiveForm('validate');
    }
    this.changeZipcode = async function (zipcode) {
        try {
            let res = await $.ajax({
                url: AJAX_PATH.getZipcode,
                data: {zipcode},
                type: 'POST'
            });
            if (res) {
                const {name, city, zipcode, district, code} = res;
                cityInput.val(city);
                districtInput.valueOf(district);
                countrySelect.val(code);
            }
        } catch (e) {
            console.log(e);
        }
    }
    this.getPrice = async function getPrice(qty, sku) {
        return $.ajax({
            url: AJAX_PATH.getPrice,
            data: {qty, sku},
            type: 'POST',
            cache: false
        })
    }
}

async function changeStatus(model, status, element = null) {
    let ids = $('#default-tab').yiiGridView("getSelectedRows");
    if (element) {
        ids = $(element).data('key');
    }
    if (ids.length <= 0) {
        toastr.warning('Không có contact nào được chọn!');
        return false;
    }
    await swal.fire({
        text: `Đổi trạng thái lựa chọn sang ${status}`,
        type: 'info',
        showCancelButton: true,
        cancelButtonText: 'Hủy',
        confirmButtonText: 'Đồng ý',
    }).then(res => {
        if (res.value) {
            try {
                const res = service(model, status, ids);
                $.pjax.reload('#sale-box', {});
                if (res.assigned) {
                    toastr.success("Số điện thoại mới được áp dụng!");
                }
            } catch (e) {
                toastr.warning(e.message);
            }
            // window.location.reload();
        }
    })


    async function service(model, status, ids) {
        return $.ajax({
            url: AJAX_PATH.changeStatus,
            data: {model, status, ids},
            type: 'POST',
            cache: false
        });
    }


}