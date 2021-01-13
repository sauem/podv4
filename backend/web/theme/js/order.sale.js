//window.ORDER_ITEMS = [];
Number.prototype.format = function (n, x) {
    let re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
}

function orderSaleForm() {
    localStorage.removeItem('order_items');

    let html = $('#sku-template').html();
    let template = Handlebars.compile(html);
    let orderItemsResult = $('#result-sku-list');
    let htmlWarehouse = $('#warehouse-template').html();
    let templateWarehouse = Handlebars.compile(htmlWarehouse);
    let warehouseResult = $('#result-warehouse-list');
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
    window.ORDER_ITEMS = {
        total: {
            total_bill: 0,
            total_price: 0,
            total_ship: 0
        },
        items: []
    };
    this.orderExampleReq = async function (code) {
        return $.ajax({
            url: AJAX_PATH.getOrderExample,
            data: {code},
            cache: false,
            type: 'POST'
        });
    }
    this.setOrderExample = async function (code) {
        try {
            let {total_bill, skuItems} = await this.orderExampleReq(code);
            if (skuItems.length > 0) {
                skuItems.map(item => {
                    this.setOrderItems(item);
                    this.appendTemplateItem(item);
                })
            }
            this.setTotalPrice(total_bill);
        } catch (e) {
            console.log("order example error : ", e);
        }
    }
    this.getProductInfo = async function (sku) {
        return $.ajax({
            url: AJAX_PATH.getProductInfo,
            data: {sku: sku},
            cache: false,
            type: 'POST'
        })
    }
    this.showWarehouse = async function () {
        try {
            const res = await $.ajax({
                url: AJAX_PATH.getWarehouseAvailable,
                type: 'POST',
                data: {},
                cache: false
            });
            warehouseResult.html(templateWarehouse(res));
        } catch (e) {
            console.log("warehouse req:", e);
        }
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
            price: item.price > 0 ? item.price : 0,
            qty: item.qty > 0 ? item.qty : 0
        });
        this.resetLocalStorage(ORDER_ITEMS);
        return true;
    }
    this.resetLocalStorage = function (ORDER_ITEMS) {
        localStorage.setItem('order_items', JSON.stringify(ORDER_ITEMS.items));
    }
    this.removeOrderItems = function (sku) {
        if (!ORDER_ITEMS.items.some(value => value.sku === sku)) {
            toastr.warning('Sản phẩm không tồn tại!');
            return false;
        }
        let items = ORDER_ITEMS.items;
        ORDER_ITEMS.items = items.filter(item => item.sku !== sku);
        this.resetLocalStorage(ORDER_ITEMS);
        return true;
    }
    this.renderPrevtItem = function () {

        let items = JSON.parse(localStorage.getItem('order_items'));
        if (items !== null && items.length > 0) {
            for (let i in items) {
                this.setOrderItems(items[i]);
                this.appendTemplateItem(items[i]);
            }
        }
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
    this.setItemPrice = function (sku, price, qty) {
        let index = ORDER_ITEMS.items.findIndex(item => item.sku === sku);
        ORDER_ITEMS.items[index].price = price;
        ORDER_ITEMS.items[index].qty = qty;
        this.resetLocalStorage(ORDER_ITEMS);
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
            this.setItemPrice(sku, price, qty);
        } catch (e) {
            console.warn(JSON.parse(e.responseText).message);
        }
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
    this.valid = function () {
        if (ORDER_ITEMS.items.length <= 0) {
            toastr.error("Chưa có sản phẩm nào trong đơn hàng!");
            return false;
        }
    }
}

function setStatusCallback(element, ids) {
    let modal = new ModalRemote('#callback-modal', '#sale-box');
    modal.remote(element, {ids: ids, status: 'callback'});
}

async function changeStatus(model, status, element = null) {
    let ids = $('#default-tab').yiiGridView("getSelectedRows");
    // if (element) {
    //     ids = $(element).data('key');
    // }
    if (ids.length <= 0) {
        toastr.warning('Không có contact nào được chọn!');
        return false;
    }
    if (status === 'callback' | status == 'pending') {
        setStatusCallback(element, ids);
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