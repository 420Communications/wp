(function ($) {

    $(document).ready(function () {
        $('#is_listing_featured').hide();
        var package = $('#package');
        var pay_per_listng = $('#directorist-pay-per-listing');
        var pay_per_package = $('#directorist-pay-per-package');
        var plan_tax_area = $('.plan_tax_area');

        $('body').on('change', 'input[name="plan_type"]', function () {
            if ($(this).val() == "package") {

                $(this).prop("checked", false);
                let target_class = $(this).data('target');
                $('.' + target_class).toggleClass('active');
            } else if ($(this).val() == "pay_per_listng") {

                $('#regular_listing').fadeOut(300);
                $('#featured_listing').fadeOut(300);
                $('#is_listing_featured').fadeIn(300);
            }
        });

        // tax
        if ($('#plan_tax').is(':checked')) {
            $('.directorist-tax-area-hidden').show(300);
        } else {
            $('.directorist-tax-area-hidden').hide(300);
        }
        $(plan_tax_area).on('click', function () {
            if ($('#plan_tax').is(':checked')) {
                $('.directorist-tax-area-hidden').fadeIn(300);
            } else {
                $('.directorist-tax-area-hidden').fadeOut(300);
            }
        });


        // Switch Control
        $('body').on('click', '.directorist-switch-Yn', function () {
            if ($(this).children('input').is(':checked')) {
                $(this).siblings('.directorist-switch-Yn-content').addClass('directorist_visible');
                $(this).parent('td').siblings('td').children('.directorist-switch-Yn-content').addClass('directorist_visible');
            } else {
                $(this).siblings('.directorist-switch-Yn-content').removeClass('directorist_visible');
                $(this).parent('td').siblings('td').children('.directorist-switch-Yn-content').removeClass('directorist_visible');
            }
        });

        // Renew Check
        $('body').on('change', '#directorist_auto-renew-check', function () {
            if ($(this).is(':checked')) {
                $(this).closest('.directorist-renew-check-content').addClass('directorist_auto-renew-active');
            } else {
                $(this).closest('.directorist-renew-check-content').removeClass('directorist_auto-renew-active');
            }
        });


        // Input Control
        $('body').on('click', '.directorist_input-control', function () {
            if ($(this).children('input').is(':checked')) {
                $(this).siblings('.directorist_handle-input').removeClass('directorist_visible');
            } else {
                $(this).siblings('.directorist_handle-input').addClass('directorist_visible');
            }
        });

        // Price Set
        $('body').on('click', '.directorist_free-plan #free_plan', function () {
            if ($(this).is(':checked')) {
                $(this).parent('.directorist_free-plan').siblings('.directorist_tax-wrap, .directorist_price-input').fadeOut();
            } else {
                $(this).parent('.directorist_free-plan').siblings('.directorist_tax-wrap, .directorist_price-input').fadeIn();
            }
        });

        $('body').on('click', '.atpp_modal-ok', function (e) {
            e.preventDefault();

            $('#directorist-pay-per-package').prop("checked", true);
            $('.directorist-seelct-plan-modal').removeClass('active');
            $('.atbdp-directory-delete-cancel-link').removeClass('directorist-cptm-modal-toggle');

            let target_class = $(this).data('target');
            $('.' + target_class).removeClass('active');
            $('#regular_listing').fadeIn(300);
            $('#featured_listing').fadeIn(300);
            $('#is_listing_featured').fadeOut(300);
        });

        $('body').on('click', '.directorist-modal-cancel', function (e) {
            e.preventDefault();

            $('#directorist-pay-per-listing').prop("checked", true);
            let target_class = $(this).data('target');
            $('.' + target_class).removeClass('active');
        });

        window.onload = function () {

            var selectedVal = "";
            var selected = $("input[type='radio'][name='plan_type']:checked");
            if (selected.length > 0) {
                selectedVal = selected.val();
            }
            if (selectedVal === 'pay_per_listng') {
                $('#regular_listing').fadeOut();
                $('#featured_listing').fadeOut();
                $('#is_listing_featured').fadeIn(300);
            } else if (selectedVal === 'package') {
                $('#regular_listing').show();
                $('#featured_listing').show();
                $('#is_listing_featured').hide();
            }
            // Switch Control
            $('.directorist-switch-Yn').each((index, element) => {
                if ($(element).children('input').is(':checked')) {
                    $(element).siblings('.directorist-switch-Yn-content').addClass('directorist_visible');
                    $(element).parent('td').siblings('td').children('.directorist-switch-Yn-content').addClass('directorist_visible');
                } else {
                    $(element).siblings('.directorist-switch-Yn-content').removeClass('directorist_visible');
                    $(element).parent('td').siblings('td').children('.directorist-switch-Yn-content').removeClass('directorist_visible');
                }
            });

            // Renew Check
            $('#directorist_auto-renew-check').each((index, element) => {
                if ($(element).is(':checked')) {
                    $(element).closest('.directorist-renew-check-content').addClass('directorist_auto-renew-active');
                } else {
                    $(element).closest('.directorist-renew-check-content').removeClass('directorist_auto-renew-active');
                }
            });

            // Input Control
            $('.directorist_input-control').each((index, element) => {
                if ($(element).children('input').is(':checked')) {
                    $(element).siblings('.directorist_handle-input').removeClass('directorist_visible');
                } else {
                    $(element).siblings('.directorist_handle-input').addClass('directorist_visible');
                }
            });
            // Price Set
            $('.directorist_free-plan #free_plan').each((index, element) => {
                if ($(element).is(':checked')) {
                    $(element).parent('.directorist_free-plan').siblings('.directorist_tax-wrap, .directorist_price-input').fadeOut();
                } else {
                    $(element).parent('.directorist_free-plan').siblings('.directorist_tax-wrap, .directorist_price-input').fadeIn();
                }
            });
        };

        // load admin add listing form
        const directory_type = $('select[name="directory_type"]').val();

        if (directory_type) {
            plan_with_directory_type(directory_type);
        }
        $('body').on('change', 'select[name="directory_type"]', function () {
            plan_with_directory_type($(this).val());
        });

        function plan_with_directory_type(directory_type) {
            $.ajax({
                type: 'post',
                url: pricing_admin_js.ajaxurl,
                data: {
                    action: 'atbdp_dynamic_plan',
                    directory_type: directory_type,
                    listing_id: $('#directiost-listing-fields_wrapper').data('id'),
                    user_id: $('select[name=post_author_override]').val(),

                },
                success(response) {
                    if (response) {
                        $('.directorist-admin-form-plan-container')
                            .empty()
                            .html(response);
                    }
                },
            });
        }


        // load dynamic field
        $('body').on('change', 'input[name=assign_to_directory]', function () {
            var form_data = new FormData();
            var listing_type = $(this).val();
            var post_id = $('input[name="post_id"]').val();
            form_data.append('action', 'directorist_plan_dynamic_fields');
            form_data.append('post_id', post_id);
            form_data.append('listing_type', listing_type);
            $.ajax({
                method: 'POST',
                processData: false,
                contentType: false,
                url: pricing_admin_js.ajaxurl,
                data: form_data,
                beforeSend: function () {
                    $('#directorist-type-preloader').show();
                },
                success: function (response) {
                    $('.plan_dynamic_field').empty().append(response);
                },
                error: function (error) {
                    //console.log(error);
                },
                complete: function () {
                    $('#directorist-type-preloader').hide();
                }
            });
        });


        /* Description character limit: WPEditor */
        function checkTextareaLimit(textEditorSelector, type) {
            textEditorDoms = document.querySelectorAll(`${textEditorSelector}`);
            if (textEditorDoms.length) {
                textEditorDoms.forEach((textEditorElment) => {

                    if (type === "editor") {
                        if(document.getElementById('directorist_listing_content_max') === null){
                            return;
                        }
                        var textEditorElmentId = textEditorElment.querySelector('textarea').id;
                        var textareaMaxChar = document.getElementById('directorist_listing_content_max').value;
                    } else if (type === "textarea") {
                        if(textEditorElment.getAttribute("max") === null){
                            return;
                        }
                        var textEditorElmentId = textEditorElment.id;
                        var textareaMaxChar = Number(textEditorElment.getAttribute("max"));
                        textEditorElment.setAttribute("maxlength", textareaMaxChar);
                    }

                    var remainingCharCount = textareaMaxChar;
                    remainingDom = document.createElement('p');
                    remainingDom.classList.add('directorist-remaining-count');
                    remainingDom.innerText = plan_validator.remaining_text + " " + textareaMaxChar;
                    textEditorElment.insertAdjacentElement('afterend', remainingDom);
                    var maxCharAlertDom = document.createElement('p');
                    maxCharAlertDom.classList.add('directorist-textarea-max-alert');
                    maxCharAlertDom.innerText = plan_validator.max_exit;
                    /* get editor text contents character & word counts */
                    function getStats(id) {
                        if (type === "editor") {
                            var body = tinymce.get(id).getBody(),
                                text = tinymce.trim(body.innerText || body.textContent)
                        } else if (type === "textarea") {
                            var text = textEditorElment.value
                        }
                        return {
                            chars: text.length,
                            words: text.split(/[\w\u2019\'-]+/).length
                        };
                    }
                    /* append alert */
                    function appendAlert() {
                        if (!maxCharAlertDom.length) {
                            if (type === "editor") {
                                textEditorElment.nextSibling.insertAdjacentElement('afterend', maxCharAlertDom)
                            } else {
                                textEditorElment.nextSibling.insertAdjacentElement('afterend', maxCharAlertDom);
                            }
                        }
                    }
                    if (type === "editor") {
                        tinymce.activeEditor.on('keypress', function (e) {
                            if (remainingCharCount > 0) {
                                textEditorElment.nextSibling.innerText = `${plan_validator.remaining_text} ${textareaMaxChar - getStats(textEditorElmentId).chars}`;
                            }

                            /* append/remove max limit alert */
                            if (getStats(textEditorElmentId).chars >= textareaMaxChar) {
                                appendAlert()
                                e.preventDefault();
                            } else if (getStats(textEditorElmentId).chars < textareaMaxChar + 2) {
                                maxCharAlertDom.remove();
                            }
                        });
                        tinymce.activeEditor.on('keydown', function (e) {
                            if (e.keyCode == 8) {
                                /* remove alert */
                                if (getStats(textEditorElmentId).chars < textareaMaxChar + 2) {
                                    maxCharAlertDom.remove();
                                }
                                setTimeout(() => {
                                    if (getStats(textEditorElmentId).chars === 0) {
                                        remainingCharCount = textareaMaxChar;
                                        textEditorElment.nextSibling.innerText = `${plan_validator.remaining_text} ${textareaMaxChar}`;
                                    }
                                    let remChar = getStats(textEditorElmentId).chars;
                                    let remCharCount = textareaMaxChar - remChar;
                                    textEditorElment.nextSibling.innerText = `${plan_validator.remaining_text} ${remCharCount}`;
                                }, 0);
                                if (remainingCharCount >= 0 && remainingCharCount < textareaMaxChar) {
                                    remainingCharCount++;
                                    textEditorElment.nextSibling.innerText = `${plan_validator.remaining_text} ${remainingCharCount}`;
                                }
                            }
                        });
                        tinymce.activeEditor.on('paste', function (e) {
                            var currentText = tinymce.activeEditor.contentDocument.body.innerText;
                            let currentTextSanitize = currentText.replace(/%MCEPASTEBIN%/g, '');
                            var textLength = textareaMaxChar - currentTextSanitize.length + 3;
                            let clipTextLength = e.clipboardData.getData('text').length;
                            remainingCharCount = textareaMaxChar - (clipTextLength + currentTextSanitize.trim().length);
                            textEditorElment.nextSibling.innerText = `${plan_validator.remaining_text} ${remainingCharCount}`;
                            if (clipTextLength >= textLength) {
                                var sliceCopiedData = e.clipboardData.getData('text').slice(0, textLength);
                                tinymce.activeEditor.contentDocument.body.innerText = currentTextSanitize.trim() + ' ' + sliceCopiedData;
                                appendAlert();
                                remainingCharCount = 0;
                                textEditorElment.nextSibling.innerText = `${plan_validator.remaining_text} 0`;
                                e.preventDefault();
                            } else {
                                if (maxCharAlertDom.length) {
                                    maxCharAlertDom.remove();
                                }
                            }
                        });
                        tinymce.activeEditor.on('cut', function (e) {
                            setTimeout(() => {
                                if (getStats(textEditorElmentId).chars === 0) {
                                    remainingCharCount = textareaMaxChar;
                                    textEditorElment.nextSibling.innerText = `${plan_validator.remaining_text} ${textareaMaxChar}`;
                                }
                                let remChar = getStats(textEditorElmentId).chars;
                                let remCharCount = textareaMaxChar - remChar;
                                textEditorElment.nextSibling.innerText = `${plan_validator.remaining_text} ${remCharCount}`;
                            }, 100);
                            if (remainingCharCount >= 0 && remainingCharCount < textareaMaxChar) {
                                remainingCharCount++;
                                textEditorElment.nextSibling.innerText = `${plan_validator.remaining_text} ${remainingCharCount}`;
                            }
                        });
                    } else {
                        function letterCountWithAlert() {
                            if (remainingCharCount > 0) {
                                let remainingNumber = textareaMaxChar - getStats(textEditorElmentId).chars;
                                if (remainingNumber < 0) {
                                    remainingNumber = 0;
                                }
                                textEditorElment.nextSibling.innerText = `${plan_validator.remaining_text} ${remainingNumber}`;
                            }
                            /* append/remove max limit alert */
                            if (getStats(textEditorElmentId).chars >= textareaMaxChar) {
                                appendAlert()
                            } else if (getStats(textEditorElmentId).chars < textareaMaxChar + 2) {
                                maxCharAlertDom.remove();
                            }
                        }
                        textEditorElment.addEventListener('keydown', function () {
                            letterCountWithAlert();
                        });
                        textEditorElment.addEventListener('keyup', function () {
                            letterCountWithAlert();
                        });
                        textEditorElment.addEventListener('paste', function () {
                            setTimeout(() => {
                                letterCountWithAlert();
                            }, 100);
                        });
                        textEditorElment.addEventListener('cut', function () {
                            setTimeout(() => {
                                letterCountWithAlert();
                            }, 100);
                        });
                        textEditorElment.addEventListener('focus', function () {
                            let editorSliceText = textEditorElment.value.slice(0, textareaMaxChar);
                            textEditorElment.value = editorSliceText;
                            letterCountWithAlert();
                        });
                    }
                });
            }
        }

        checkTextareaLimit('#wp-listing_content-wrap', 'editor');
        checkTextareaLimit('.directorist-custom-field-textarea textarea', 'textarea');
        checkTextareaLimit('.directorist-form-description-field textarea.directorist-form-element', 'textarea');

    });

})(jQuery);

