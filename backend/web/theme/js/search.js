function initSearch() {
    this.getSearchContent = async function (urlSearch, params = {}) {
        return $.ajax({
            url: urlSearch,
            dataType: 'json',
            cache: false,
            processData: false,
            data: params,
            type: 'GET'
        });
    }
    this.search = async function (url, params, reloadContainer = null) {

    }
}