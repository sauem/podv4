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
    let url = new URL('http://' + window.location.hostname + AJAX_PATH.exportOrder);
    url.search = new URLSearchParams({ids: ids});
    window.location.href = url;
}