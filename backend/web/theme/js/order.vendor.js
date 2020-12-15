function HandleOrder() {

    this.create = function (modalId , boxId, button) {
        let modal = new ModalRemote(modalId, boxId);
        modal.remote(button, null);
    }
}