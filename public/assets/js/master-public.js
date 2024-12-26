$(document).ready(function() {
    $.ajax({
        url: apiCategoriesUrl,
        method: 'GET',
        success: function(data) {
            const dropdownMenu = $('.drop-categories');
            data.forEach(function(categoria) {
                const categoryLink = $('<a>', {
                    class: 'dropdown-item',
                    text: categoria.nome,
                    href: apiCategoriesProductUrl + '?categoria=' + categoria.id,
                    style: 'color: black;'
                });
                dropdownMenu.append(categoryLink);
            });
        },
        error: function() {
            console.error('Falha em buscar categorias.');
        }
    });

    $.ajax({
        url: apiProductsCartUrl,
        method: 'GET',
        success: function(data) {
            const countProducts = $('#products-cart-count');
            countProducts.text(data);
        },
        error: function() {
            console.error('Falha em buscar produtos no carrinho.');
        }
    });
});

$(document).ready(function() {
    $('.add-to-cart-form').on('submit', function(e) {
        e.preventDefault(); 
        var form = $(this);
        var actionUrl = form.attr('action'); 
        var formData = form.serialize();

        $.ajax({
            url: actionUrl,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success = true){
                    $.ajax({
                        url: apiProductsCartUrl,
                        method: 'GET',
                        success: function(data) {
                            const countProducts = $('#products-cart-count');
                            countProducts.text(data);
                        },
                        error: function() {
                            console.error('Falha em buscar produtos no carrinho.');
                        }
                    });

                    Swal.fire({
                        icon: 'success',
                        title: response.reason,
                        position: 'top',
                        showConfirmButton: false,
                        timer: 3000,
                        toast: true
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.reason,
                        position: 'top',
                        showConfirmButton: false,
                        timer: 3000,
                        toast: true
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr, status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao adicionar produto ao carrinho!',
                    position: 'top',
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true
                });
            }
        });
    });
});