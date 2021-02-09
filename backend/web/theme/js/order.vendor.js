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

async function exportOrderSelect() {
    let params = $('#formOrderPending').serialize();
    let url = 'http://' + window.location.hostname + AJAX_PATH.exportOrder + '?' + params;

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
                    a.href = url;
                    a.download = 'demo.xlsx';
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
    this.resultOrder = $('#result-order-pending');
    this.templateOrder = Handlebars.compile($('#pending-order-template').html());

    this.getData = async (params = {}, url = '/order/get-pending') => {
        return $.ajax({
            url: url,
            data: params,
            cache: false,
            type: "GET"
        });
    }
    this.render = {
        pending: async (params = {}, search = false) => {
            try {
                const {data, offset} = await this.getData(params);
                if (data.length <= 0) {
                    toastr.warning('Không còn dữ liệu!');
                    $('.btn-load-more').hide();
                    return false;
                }
                if (search) {
                    this.resultOrder.html(this.templateOrder(data));
                } else {
                    this.resultOrder.append(this.templateOrder(data));
                }
                return offset;
            } catch (e) {
                console.log(e);
            }
        }
    }
}
