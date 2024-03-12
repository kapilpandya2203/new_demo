define([
    'jquery',
    'Magento_Ui/js/modal/modal-component'
], function ($, Modal) {
    'use strict';

    return Modal.extend({
        updateAndOpen: function (data) {
            let action = data.action;
            $('.issue_details_modal .modal-content .readme-content').remove();
            $.ajax({
                showLoader: true,
                type: "GET",
                dataType: "json",
                url: action,
                success: function (result) {
                    if (result.success) {
                        let modalHtml = result.modal_content;
                        let modalContainer = $('<div class="readme-content"/>').append(modalHtml);
                        $('.issue_details_modal .modal-content').append(modalContainer);
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
            this.openModal();
        }
    });
});
