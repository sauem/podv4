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
    let ids = $('#w2').yiiGridView("getSelectedRows");

    if (ids.length <= 0) {
        toastr.warning('Chọn 1 đơn hàng để thực hiện thao tác!');
        return false;
    }
    swal.fire({
        title: 'Xin chờ....',
        onBeforeOpen: () => {
            swal.showLoading();
            $.ajax({
                xhrFields: {
                    responseType: 'blob'
                },
                url: 'http://' + window.location.hostname + AJAX_PATH.exportOrder,
                type: 'GET',
                data: {ids: ids},
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
