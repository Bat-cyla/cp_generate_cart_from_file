(function(_, $){

    $.ceEvent('on', 'ce.ajaxdone', function (elms, inline_scripts, params, data) {
        if (!data.variations_popup || !data.content) {
            return;
        }

        var params =  {
            keepInPlace: false,
            nonClosable: false,
            destroyOnClose: true,
            purpose: null,
            title: data.title || '',
            width: 'auto',
            height: 'auto',
            dialogClass: 'dialog-auto-sized'
        };

        content = $(data.content);

        content.ceDialog('open', params);

        $.commonInit(content);
    });


})(Tygh, Tygh.$);