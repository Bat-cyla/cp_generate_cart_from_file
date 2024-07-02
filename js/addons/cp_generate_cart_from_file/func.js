(function(_, $){
    $.ceEvent('on', 'ce.commoninit', function(context) {
        let field_artiqle = '';
        let field_amount = '';
        let filename = '';
        
        context.find('.cp-submit').attr('disabled', true).addClass('cp-disabled');
        context.find('a.cp-generate-cart-page-link').on('click', function() {
            ajaxGetModal();
        });
        context.find('a.cp-export-cart-page-link').on('click', function() {
            ajaxGetExportModal();
        });
        context.find('li.cp-generate-cart-page-link').on('click', function() {
            ajaxGetModal();
        });
        context.find('input.cp-upload-file').change(function() {
            filename = $(this).val().replace(/.*\\/, "");
            if (field_amount != '' && field_artiqle != '' && filename != '') {
                context.find('.cp-submit').attr('disabled', false).removeClass('cp-disabled');
            } else {
                context.find('.cp-submit').attr('disabled', true).addClass('cp-disabled');
            }
        });

        context.find('#field_amount').on('input', function() {
            this.value = this.value.toUpperCase();
            this.value = this.value.replace(/[^A-Z0-9\s]/gi, '');
            field_amount = $(this).val();
            if (field_amount != '' && field_artiqle != '' && filename != '') {
                context.find('.cp-submit').attr('disabled', false).removeClass('cp-disabled');
            } else {
                context.find('.cp-submit').attr('disabled', true).addClass('cp-disabled');
            }
        })

        context.find('#field_name').on('input', function() {
            this.value = this.value.toUpperCase();
            this.value = this.value.replace(/[^A-Z0-9\s]/gi, '');
        })

        context.find('#field_artiqle').on('input', function() {
            this.value = this.value.toUpperCase();
            this.value = this.value.replace(/[^A-Z0-9\s]/gi, '');
            field_artiqle = $(this).val();
            if (field_amount != '' && field_artiqle != '' && filename != '') {
                context.find('.cp-submit').attr('disabled', false).removeClass('cp-disabled');
            } else {
                context.find('.cp-submit').attr('disabled', true).addClass('cp-disabled');
            }
        })

        if ($('input.cp-variation-modal').length) {
            $('button.ui-dialog-titlebar-close').on('click', function() {
                ajaxFinish();
            });
            $(document).keydown(function(e) {
                // ESCAPE key pressed
                if (e.keyCode == 27) {
                    ajaxFinish();
                }
            });
        }
    });

    function ajaxGetModal()
    {
        $.ceAjax('request', fn_url('cp_generate_cart_from_file.view'), {
            method: 'get',
            force_exec: true,
            caching : false,
            result_ids: 'cp_generate_cart_modal',
        });
    }
    function ajaxGetExportModal()
    {
        $.ceAjax('request', fn_url('cp_generate_cart_export_file.view'), {
            method: 'get',
            force_exec: true,
            caching : false,
            result_ids: 'cp_export_cart_modal',
        });
    }
    function ajaxFinish()
    {
        $.ceAjax('request', fn_url('cp_generate_cart_from_file.finish'), {
            method: 'post',
            force_exec: true,
            caching : false,
        });
    }

})(Tygh, Tygh.$);