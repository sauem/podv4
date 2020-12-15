/*!
 * Modal Remote
 */
(function ($) {
    $.fn.hasAttr = function (name) {
        return this.attr(name) !== undefined;
    };
}(jQuery));
$.fn.modal.Constructor.prototype.enforceFocus = function () {
};

function ModalRemote(modalId, containerId, pjaxOptions = {}) {

    let thiz = this;
    this.defauls = {
        okLabel: "OK",
        cancelLabel: "Cancel",
        loadingTitle: "Loading",
    };

    this.modal = $(modalId);

    this.dialog = $(modalId).find('.modal-dialog');

    this.header = $(modalId).find('.modal-header');

    this.content = $(modalId).find('.modal-body');

    this.footer = $(modalId).find('.modal-footer');

    this.loadingContent = '<div class="text-center"><div class="spinner-border text-secondary m-2" role="status"></div></div>';

    /**
     * Show the modal
     */
    this.show = function () {
        this.clear();
        $(thiz.modal).modal('show');
    }

    /**
     * Hide the modal
     */
    this.hide = function () {
        $(this.modal).modal('hide');
    }

    /**
     * Toogle show/hide modal
     */
    this.toggle = function () {
        $(this.modal).modal('toggle');
    }

    /**
     * Clear modal
     */
    this.clear = function () {
        $(this.modal).find('.modal-title').remove();
        $(this.content).html("");
        $(this.footer).html("");
    }


    /**
     * Set modal header
     * @param string content The content of modal header
     */
    this.setHeader = function (content) {
        $(this.header).html(content);
    }

    /**
     * Set modal content
     * @param string content The content of modal content
     */
    this.setContent = function (content) {
        $(this.content).html(content);
    }

    /**
     * Set modal footer
     * @param string content The content of modal footer
     */
    this.setFooter = function (content) {
        $(this.footer).html(content);
    }

    /**
     * Set modal footer
     * @param string title The title of modal
     */
    this.setTitle = function (title) {
        // remove old title
        $(this.header).find('h4.modal-title').remove();
        // add new title
        $(this.header).prepend('<h4 class="modal-title">' + title + '</h4>');
    }

    /**
     * Hiden close button
     */
    this.hidenCloseButton = function () {
        $(this.header).find('button.close').hide();
    }

    /**
     * Show close button
     */
    this.showCloseButton = function () {
        $(this.header).find('button.close').show();
    }

    /**
     * Add Button
     * @param string label The label of button
     * @param string classes The class of button
     * @param callable callback the callback when button click
     */
    this.addButton = function (label, classes, callback) {
        let buttonElm = document.createElement('button');
        buttonElm.setAttribute('class', classes === null ? 'btn btn-primary' : classes);
        buttonElm.innerHTML = label;
        let instance = this;
        $(this.footer).append(buttonElm);
        if (callback !== null) {
            $(buttonElm).click(function () {
                callback.call(instance, this);
            });
        }
    }

    /**
     * Show loading state in modal
     */
    this.displayLoading = function () {
        this.setContent(this.loadingContent);
        this.setTitle(this.defauls.loadingTitle);
    }

    /**
     * Show the confirm dialog
     * @param string title The title of modal
     * @param string message The message for ask user
     * @param string okLabel The label of ok button
     * @param string cancelLabel The class of cancel button
     * @param callable okCallback the callback when user cancel confirm
     * @param callable cancelCallback the callback when user accept confirm
     */
    this.confirm = function (title, message, okLabel, cancelLabel, okCallback, cancelCallback) {
        if (title !== undefined) {
            this.setTitle(title);
        }
        if (message !== undefined) {
            this.setContent(message);
        }
        this.addButton(cancelLabel === undefined ? this.defauls.cancelLabel : cancelLabel, 'btn btn-default pull-left', cancelCallback);
        this.addButton(okLabel === undefined ? this.defauls.okLabel : okLabel, 'btn btn-primary', okCallback);
    }

    /**
     * Set size of modal
     * large/normal/small/full
     * modal-full-width
     */
    this.setSize = function (size) {
        $(this.dialog).removeClass('modal-lg');
        $(this.dialog).removeClass('modal-sm');
        if (size == 'large')
            $(this.dialog).addClass('modal-lg');
        else if (size == 'small')
            $(this.dialog).addClass('modal-sm');
        else if (size == 'full')
            $(this.dialog).addClass('modal-full-width');
        else if (size == 'xl')
            $(this.dialog).addClass('modal-xl');
        else if (size !== 'normal')
            console.warn("Not define size" + size);
    }


    /**
     * Auto load content from a tag
     * Attribute to use for blind
     *    - href/data-url(If not set href will get data-url)
     *    - data-request-method   (string)
     *   - data-confirm-ok       (string)
     *   - data-confirm-cancel   (string)
     *   - data-confirm-title    (string)
     *   - data-confirm-message  (string)
     *   - data-modal-size        (small/normal/large)
     * Response json field
     *   - forceReload           (boolean)
     *   - forceClose            (boolean)
     *    - size                  (small/normal/large)
     *    - title                 (string/html)
     *   - content               (string/html)
     *   - footer                (string/html)
     */
    this.remote = function (elm, bulkData) {

        let url = $(elm).hasAttr('href') ? $(elm).attr('href') : $(elm).attr('data-url');
        let method = $(elm).hasAttr('data-request-method') ? $(elm).attr('data-request-method') : 'GET';
        let size = $(elm).hasAttr('data-modal-size') ? $(elm).attr('data-modal-size') : 'normal';

        if ($(elm).hasAttr('data-confirm-title') || $(elm).hasAttr('data-confirm-message')) {
            thiz.show();
            this.setSize(size);
            let instance = this;
            this.confirm(
                $(elm).attr('data-confirm-title'),
                $(elm).attr('data-confirm-message'),
                $(elm).attr('data-confirm-ok'),
                $(elm).attr('data-confirm-cancel'),
                function (e) {
                    doRemote.call(instance, url, method, bulkData);
                },
                function (e) {
                    this.hide();
                }
            )
        } else {
            doRemote.call(this, url, method, bulkData);
        }
    }


    /**
     * Send ajax request and wraper response to modal
     * @param ModalRemote modalRemote the instance of ModalRemote
     * @param string url The url of request
     * @param string method The method of request
     */
    function doRemote(url, method, bulkData) {
        let instance = this;
        $.ajax({
            url: url,
            method: method,
            data: bulkData,
            beforeSend: function () {
                beforeRemoteRequest.call(instance);
            },
            error: function (response) {
                errorRemoteResponse.call(instance, response);
            },
            success: function (response) {
                successRemoteResponse.call(instance, response);
            }
        });
    }

    /*
    * Before send request process
    * - Ensure clear and show modal
    * - Show loading state in modal
    */
    function beforeRemoteRequest() {
        this.show();
        this.displayLoading();
    }


    /**
     * When remote receive error response process
     */
    function errorRemoteResponse(response) {
        this.setTitle(response.status + response.statusText);
        this.setContent(response.responseText);
        this.addButton('Close', 'btn btn-default', function (button, event) {
            this.hide();
        })
    }

    /**
     * When remote receive success response process
     */
    function successRemoteResponse(response) {
        // reload datatable if response contain forceReload field
        if (response.forceReload !== undefined && response.forceReload) {
            //$.pjax.reload({container:containerId});
            $.pjax.reload(containerId, pjaxOptions);
        }

        // close modal if response contain forceClose field
        if (response.forceClose !== undefined && response.forceClose) {
            /**
             * Close modal and don't do anything
             */
            this.hide();
            return;
        }

        if (response.size !== undefined)
            this.setSize(response.size);

        if (response.title !== undefined)
            this.setTitle(response.title);

        if (response.content !== undefined)
            this.setContent(response.content);

        if (response.footer !== undefined)
            this.setFooter(response.footer);
        /**
         * Process when modal have form
         */
        if ($(this.content).find("form")[0] !== undefined) {

            let modalForm = $(this.content).find("form")[0];
            let modalFormSubmitBtn = $(this.footer).find('[type="submit"]')[0];


            if (modalFormSubmitBtn === undefined) {
                // If not found submit button throw warning message
                console.warn('Modal have form but have not any submit button');
            } else {
                let instance = this;

                // Submit form when user click submit button
                $(modalFormSubmitBtn).click(function (e) {
                    let url = $(modalForm).attr('action');
                    let method = $(modalForm).hasAttr('method') ? $(modalForm).attr('method') : 'GET';
                    let data = new FormData(modalForm);
                    $.ajax({
                        url: url,
                        method: method,
                        data: data,
                        processData: false,
                        contentType: false,
                        beforeSend: function () {
                            beforeRemoteRequest.call(instance);
                        },
                        error: function (response) {
                            errorRemoteResponse.call(instance, response);
                        },
                        success: function (response) {
                            successRemoteResponse.call(instance, response);
                        }
                    });
                });
            }
        }// End of found form check

    }// End of function successRemoteResponse


}// End of Object