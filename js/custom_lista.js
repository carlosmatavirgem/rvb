$(function () {

    //===== Dynamic data table =====//

    $('#example').dataTable({
        "bJQueryUI": true,
        "iDisplayLength": 25,
        "sPaginationType": "full_numbers",
        "sDom": '<""f>t<"F"lp>',
        "aaSorting": [[0, "desc"]],
        "oLanguage": {
            "sProcessing": "Processando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "Não foram encontrados resultados",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando de 0 até 0 de 0 registros",
            "sInfoFiltered": "(filtrado de _MAX_ registros no total)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sPlaceholder": "Digite aqui...",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "Primeira",
                "sPrevious": "Anterior",
                "sNext": "Próxima",
                "sLast": "Última"
            }
        }
    });

    //===== Alert windows =====//

    $(".bConfirm").click(function () {

        var param = $(this).attr('id').split('-');
        var title = $(this).attr('rel');

        jConfirm('Tem certeza que deseja excluir o registro:  "<b>' + title + '</b>"?',
                'Excluir registro',
                function (r) {
                    if (r) {
                        $.post(wwwroot + '/admin/' + param[0] + '/excluir',
                                {
                                    bs: param[0],
                                    id: param[1]
                                },
                                function (e) {
                                    if (e) {
                                        jAlert('O registro "<b>' + title + '</b>" foi excluido com sucesso.', 'Excluir registro');
                                        $('tr#row-' + param[1]).remove();
                                    }
                                });
                    }
                });
    });

    $(".bSend").click(function () {

        var param = $(this).attr('id').split('-');
        var title = $(this).attr('rel');

        jConfirm('Tem certeza que deseja enviar a newsletter:  "<b>' + title + '</b>"?',
                'Enviar Newsletter',
                function (r) {
                    if (r) {
                        $.post(wwwroot + '/admin/' + param[0] + '/send',
                                {
                                    bs: param[0],
                                    id: param[1]
                                },
                                function (e) {
                                    if (e) {
                                        jAlert('A newsletter "<b>' + title + '</b>" foi programada com sucesso.', 'Enviar Newsletter');
                                    }
                                });
                    }
                });
    });


    //===== Top Tip de Imagem: Banner =====//

    $('.bannerImg').hover(function () {
        $(this).next('.topTipImg').fadeIn();
    }, function () {
        $(this).next('.topTipImg').fadeOut();
    });


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