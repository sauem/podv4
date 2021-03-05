window.PHONES = [];


$(document).on('shown.bs.modal', '.modal', function () {
    $('.selectpicker').selectpicker({
        showIcon: true
    });
    initMaskMoney();
})

$(document).on('hide.bs.modal', '.modal', function () {
    window.ORDER_ITEMS = [];
})

const toNumber = function (value) {
    return value.replaceAll(SYMBOL_MARKET.trim(), '');
}
const initToggleTab = function () {
    let toggleTab = $('a[data-toggle="tab"]');
    toggleTab.off('shown.bs.tab');
    $(document).on('shown.bs.tab', toggleTab, function (e) {

        let activeTab = $(e.target);
        let url = activeTab.attr('data-url');
        if (typeof (url) == 'undefined') {
            return;
        }
        let container = $(activeTab.attr('href'));
        container.html('<div class="text-center"><div class="spinner-border text-secondary m-2" role="status"></div></div>');
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'JSON',
            success: function (data) {
                if (data.status === 1) {
                    container.html(data.content);
                    activeTab.removeAttr('data-url');
                }
            },
            error: function (e) {
                console.log(e.statusText);
            }
        });
    });
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


function initMaskMoney() {
    $(document).ready(function () {
        $('[data-toggle="input-mask"]').each(function (a, e) {
            let t = $(e).data("maskFormat"), n = $(e).data("reverse");
            null != n ? $(e).mask(t, {reverse: n}) : $(e).mask(t)
        })
    }), jQuery(function (a) {
        a(".autonumber").autoNumeric("init")
    });
}

function initPicker() {
    $('.selectpicker').selectpicker();
}

function initMoney(number, options) {
    let dl = options.hash['decimalLength'] || 0;
    let ts = options.hash['thousandsSep'] || ',';
    let ds = options.hash['decimalSep'] || '.';

    // Parse to float
    let value = parseFloat(value);

    // The regex
    let re = '\\d(?=(\\d{3})+' + (dl > 0 ? '\\D' : '$') + ')';

    // Formats the number with the decimals
    let num = value.toFixed(Math.max(0, ~~dl));

    // Returns the formatted number
    return (ds ? num.replace('.', ds) : num).replace(new RegExp(re, 'g'), '$&' + ts);
}

function approvePhone() {
    this.phones = $('#contact-box').find('input[type="checkbox"]:checked');
    this.modal = $('#approve-modal');
    this.form = $(this.modal).find('form#approveForm');
    this.phoneResult = $(this.modal).find('#phones-result');
    this.jsHTML = $('#phones-template').html();
    this.temp = Handlebars.compile(this.jsHTML);
    if (this.phones.length <= 0) {
        toastr.warning('Hãy chọn 1 số điện thoại!');
        return false;
    }

    this.hideModal = function () {
        $(this.modal).modal('hide');
    }
    this.showModal = function () {
        $(this.modal).modal({backdrop: 'static'});
    }
    this.render = function () {
        $(this.phoneResult).html(this.temp(PHONES));
    }
    this.setData = function () {
        this.phones.each(function (index) {
            let phone = $(this).data('phone'),
                country = $(this).data('country');
            if (typeof phone === "undefined") {
                return true;
            }
            PHONES.push({
                phone: phone,
                country: country
            });
        });
    }
    this.approve = function () {
        this.setData();
        this.showModal();
        this.render();
    }
    this.removePhone = function (phone) {
        let cof = confirm("Loại bỏ số điện thoại này?");
        let res = null;
        if (cof) {
            res = PHONES.filter(item => item.phone !== phone);
            $(this).parent().remove();
            window.PHONES = res;
        }
        if (PHONES.length <= 0) {
            this.hideModal();
        }
        this.render();
    }
    this.service = async function (saleID) {
        return $.ajax({
            type: 'POST',
            url: AJAX_PATH.assignPhone,
            data: {phones: window.PHONES, saleID: saleID},
            cache: false,
        });
    }
    this.requestServer = async function () {
        let saleID = $(this.form).find('select[name="user_id"]').val();
        try {
            const r = await this.service(saleID);
            $.pjax.reload('#contact-box', {});
            this.modal.modal('hide');

        } catch (e) {
            console.log(e);
        }
    }
}

async function exportContactHistories(e) {

    let url = AJAX_PATH.exportHistories;

    swal.fire({
        title: 'Xin chờ....',
        onBeforeOpen: () => {
            swal.showLoading();
            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                url: url,
                type: 'GET',
                data: {},
                success: function (res) {
                    let a = document.createElement('a');
                    let url = window.URL.createObjectURL(res);
                    let date = new Date();
                    a.href = url;
                    a.download = `contact_histories_${date.getDay()}${date.getMonth()}${date.getFullYear()}.xlsx`;
                    document.body.append(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                    swal.close();
                }
            })
        }
    });
}

