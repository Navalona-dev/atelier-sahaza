// JavaScript

$(document).ready(function() {
    $('.nav-link').click(function(event) {
        var menu = $(this).data('menu'); 

        $.ajax({
            url: '/admin/liste',
            method: 'GET',
            data: { menu: menu },
            success: function(response) {
                $('#content').html(response); 
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors du chargement de la page:', error);
            }
        });
    });
});

