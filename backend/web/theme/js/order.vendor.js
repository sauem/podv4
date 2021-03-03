function HandleOrder() {

    this.create = function (modalId, boxId, button, data) {
        let modal = new ModalRemote(modalId, boxId);
        modal.remote(button, data);
    }
    this.export = async function (ids) {

        return $.ajax({
            url: AJAX_PATH.exportOrder,
            cache: false,
            type: 'POST',
            data: {ids},
        });
    }
}

async function exportOrderSelect(e) {
    let status = $(e).data('status');
    let params = $(`#form-order-${status}`).serialize();
    let url = AJAX_PATH.exportOrder + '?' + params;

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
                data: params,
                success: function (res) {
                    let a = document.createElement('a');
                    let url = window.URL.createObjectURL(res);
                    let date = new Date();
                    a.href = url;
                    a.download = `order_${date.getDay()}${date.getMonth()}${date.getFullYear()}.xlsx`;
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

function tabOrderContent() {
    this.cancelOrder = (element) => {
        let instance = this;
        let status = $(element).data('status') ? $(element).data('status') : 'default';
        let checked = [];
        let parent = $(element).data('table') ? $(element).data('table') : '#w1';
        $(parent + ' table tbody input[type="checkbox"]').each(function (key) {
            if ($(this).is(':checked')) {
                checked.push($(this).data('code'));
            }
            swal.fire({
                showCancelButton: true,
                title: 'Cảnh báo',
                text: 'Hủy các đơn hàng đã chọn',
            }).then(async (value) => {
                if (value.value) {
                    try {
                        const res = await $.ajax({
                            url: AJAX_PATH.cancelOrder,
                            data: {checked},
                            type: 'POST',
                            cache: false
                        });
                        let params = $(`form-order-${status}`).serializeArray();
                        if (status == 'default') {
                            let url = '/order/index';
                            url = url + '?' + new URLSearchParams($('#formOrder').serialize());
                            $.pjax.reload({replace: false, push: false, url: url, container: "#default-box"});
                        } else {
                            await instance.render.loadItems(params, true, status);
                        }
                    } catch (e) {
                        console.log(e);
                    }
                }
            });
        });

    }
    this.getData = async (params = {}, status = 'pending') => {
        return $.ajax({
            url: `/order/get-${status}`,
            data: params,
            cache: false,
            type: "GET"
        });
    }
    this.template = {
        pending: {
            result: $('#result-order-pending'),
            handle: Handlebars.compile($('#pending-order-template').html())
        },
        status: {
            result: $('#result-order-status'),
            handle: Handlebars.compile($('#status-order-template').html())
        },
        cancel: {
            result: $('#result-order-cancel'),
            handle: Handlebars.compile($('#cancel-order-template').html())
        }
    }
    this.setTemplate = (data, status, max, search) => {
        let result = null,
            handle = null;
        switch (status) {
            case 'pending':
                result = $('#result-order-pending');
                handle = Handlebars.compile($('#pending-order-template').html());
                break;
            case 'status':
                result = $('#result-order-status');
                handle = Handlebars.compile($('#status-order-template').html());
                break;
            case 'cancel':
                result = $('#result-order-cancel');
                handle = Handlebars.compile($('#cancel-order-template').html());
                break;
        }
        if (search) {
            $(result).html(handle(data));
        } else {
            $(result).append(handle(data));
        }
        if (data.length <= 0 || max) {
            $(`#${status}-box .btn-load-more`).hide();
            return false;
        }
    }
    let loadingContent = '<div class="text-center"><div class="spinner-border text-secondary m-2" role="status"></div></div>';
    this.render = {
        search: (status) => {
            $(`#result-order-${status}`).html('<tr><td colspan="11">' + loadingContent + '</td></tr>');
        },
        loadMore: async (element) => {
            let thiz = $(element);
            let status = thiz.data('status');
            let params = $(`#form-order-${status}`).serializeArray();
            let coffset = parseInt($(thiz).attr('data-offset'));
            params.push({
                name: 'offset',
                value: coffset
            });
            thiz.html(loadingContent);
            let {offset, shown, total} = await this.render.loadItems(params, false, status);
            if (total >= offset) {
                return false;
            }
            thiz.attr('data-offset', offset);
            thiz.html(`Load more items of ${shown}`);
        },
        loadItems: async (params = {}, search = false, status = 'pending') => {
            try {
                const {data, offset, max, shown} = await this.getData(params, status);

                this.setTemplate(data, status, max, search);
                return {offset, shown};
            } catch (e) {
                console.log(e);
            }
        }
    }
}
