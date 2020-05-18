$(function () {

    if ($(".resumo").attr('name')) {
        $('.resumo').limit('350', '#charsResumo');
    }

    $('#expira').click(function () {
        $('.dt').slideToggle(function () {
            if ($('#expira').is(':checked')) {
                $("#dataFim").attr("disabled", "disabled");
            } else {
                $("#dataFim").removeAttr("disabled");
            }
        });
    });

    $('#destaque').click(function () {
        $('.dtq').slideToggle(function () {
            if ($('#destaque').is(':checked')) {
                $("#ordem").removeAttr("disabled");
            } else {
                $("#ordem").attr("disabled", "disabled");
            }
        });
    });


    //===== ToTop =====//

    $().UItoTop({
        easingType: 'easeOutQuart'
    });


    //===== Form Datapicker engine =====//

    if ($(".calendar").attr('name')) {
        $(".calendar").datepicker();
    }


    //===== Form validation engine =====//

    if (typeof jQuery.fn.fckEditorValidate == 'function') {
        jQuery.fn.fckEditorValidate({
            instanceName: 'descricao',
            validationErrorMessage: 'Este campo é obrigatório.'
        });
    }
    $("#valid").validate();


    //===== Form mask engine =====//

    if ($(".calendar").attr('name')) {
        $('.calendar').mask('99/99/9999');
    }


    //===== Left navigation submenu animation =====//	

    $("ul.sub li a").hover(function () {
        $(this).stop().animate({
            color: "#3a6fa5"
        }, 400);
    }, function () {
        $(this).stop().animate({
            color: "#494949"
        }, 400);
    });


    //===== Collapsible elements management =====//

    $('.active').collapsible({
        defaultOpen: 'current',
        cookieName: 'nav',
        speed: 300
    });

    $('.exp').collapsible({
        defaultOpen: 'current',
        cookieName: 'navAct',
        cssOpen: 'active',
        cssClose: 'inactive',
        speed: 300
    });

    $('.opened').collapsible({
        defaultOpen: 'opened,toggleOpened',
        cssOpen: 'inactive',
        cssClose: 'normal',
        speed: 200
    });

    $('.closed').collapsible({
        defaultOpen: '',
        cssOpen: 'inactive',
        cssClose: 'normal',
        speed: 200
    });
});