// cambia los estilos de la barra con el scroll
$(window).scroll(function() {
    var navbarColor = "28, 61, 108";
    var smallLogoHeight = $('.small-logo').height();
    var bigLogoHeight = $('.big-logo').height();
    var navbarHeight = $('.navbar').height();

    var smallLogoEndPos = 0;
    var smallSpeed = (smallLogoHeight / bigLogoHeight);

    var ySmall = ($(window).scrollTop() * smallSpeed);

    var smallPadding = navbarHeight - ySmall;
    if (smallPadding > navbarHeight) {
        smallPadding = navbarHeight;
    }
    if (smallPadding < smallLogoEndPos) {
        smallPadding = smallLogoEndPos;
    }
    if (smallPadding < 0) {
        smallPadding = 0;
    }

    $('.small-logo-container ').css({
        "padding-top": smallPadding + 10
    });
    var navOpacity = ySmall / smallLogoHeight;
    if (navOpacity > 1) {
        navOpacity = 1;
    }
    if (navOpacity < 0) {
        navOpacity = 0;
    }
    var navBackColor = 'rgba(' + navbarColor + ',' + navOpacity + ')';
    $('.navbar').css({
        "background-color": navBackColor
    });

    var shadowOpacity = navOpacity * 0.4;
    if (ySmall > 1) {
        $('.navbar').css({
            "box-shadow": "0 2px 3px rgba(0,0,0," + shadowOpacity + ")"
        });
    } else {
        $('.navbar').css({
            "box-shadow": "none"
        });
    }
    //////////// posicion de la barra
    var scroll = $(window).scrollTop();
    var topnav;
    if (scroll >= bigLogoHeight) {
        topnav = 0;
    } else {
        topnav = bigLogoHeight - scroll - 40;
    }
    $('.navbar').css({
        "top": topnav
    });
    ////////////
});

/* Esconde la barra cuando se colapsa*/
$(".link-barra").on("click", function(event) {
        $(".collapse").collapse('hide');
});