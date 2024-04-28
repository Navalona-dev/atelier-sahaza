$(document).ready(function() {
    // Vérifiez s'il y a un élément avec la classe 'typed-text-output'
    if ($('.typed-text-output').length == 1) {
        // Récupérez le texte à taper
        var typed_strings = $('.typed-text').text();
        
        // Initialisation de Typed.js
        var typed = new Typed('.typed-text-output', {
            strings: typed_strings.split(', '), // Convertit la chaîne en un tableau en séparant les mots par ', '
            typeSpeed: 100,
            backSpeed: 20,
            smartBackspace: false,
            loop: true
        });
    }
});


$(document).ready(function() {
    $(".navbar").hide();
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        if (scroll > 50) {
            $(".navbar").slideDown('slow'); // Animation de glissement vers le bas
        } else {
            $(".navbar").slideUp('slow'); // Animation de glissement vers le haut
        }
    });
});

//section about

$(document).ready(function() {
    $(".long-description").hide();
    $(".btn-read-less").hide();

    $(".btn-read-more").click(function() {
        $(".short-description").hide();
        $(".long-description").show();
        $(".btn-read-less").show();
        $(this).hide();
    });

    $(".btn-read-less").click(function() {
        $(".short-description").show();
        $(".long-description").hide();
        $(".btn-read-more").show();
        $(this).hide();

    });
})
