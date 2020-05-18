$(function() {
    
    $('#expira').click(function(){
        $('.dt').slideToggle(
            function () {
                if($('#expira').is(':checked')){
                    console.log(1)
                    $("#dataFim").attr("disabled","disabled");
                }else{
                    console.log(2)
                    $("#dataFim").removeAttr("disabled");
                }
            });
    });
    
    
    //===== Banner - Posicao e Pagina =====//
    
    $('.pagina1, .todas').hide();
    
    var val = $('#idBannerArea').val();
    if(val) bAction(val);
    
    $('#idBannerArea').change(function(){
        val = $(this).val();
        bAction(val);
    });
    
    $('#todas').click(function(){
        $('.pagina').slideToggle(
            function () {
                if($('#todas').is(':checked')){
                    $("#idMenu").attr("disabled","disabled");
                }else{
                    $("#idMenu").removeAttr("disabled");
                }
            });
    });
    
    function bAction(val){
        
        if(val == 1 || val == 6){
            $('.pagina, .todas').slideUp(function(){
                $('#idMenu, #todas').attr("disabled","disabled");
            });
        }else if (val == 2 || val == 3 || val == 4){
            $('.todas').slideUp(function(){
                $('#todas').attr("disabled","disabled");
            });
            $('.pagina').slideDown(function(){
                $('#idMenu').removeAttr("disabled");
            });
        }else{
            if(val == 5){
                $('.todas').slideDown(function(){
                    $('#todas').removeAttr("disabled");
                });
                if(!$('#todas').is(':checked')){
                    $('.pagina').slideDown(function(){
                        $('#idMenu').removeAttr("disabled");
                    });
                }
            }
        }
    }
    
    
    //===== Form Datapicker engine =====//

    $.datepicker.setDefaults($.datepicker.regional['']);
    $(".calendar").datepicker($.datepicker.regional['pt-br']);


    //===== Form validation engine =====//
    
    $("#valid").validate();


    //===== Form mask engine =====//

    $('.calendar').mask('99/99/9999');


    //===== Left navigation submenu animation =====//	
		
    $("ul.sub li a").hover(function() {
        $(this).stop().animate({
            color: "#3a6fa5"
        }, 400);
    },function() {
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