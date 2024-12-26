/* Set values + misc */
var promoCode;
var promoPrice;
var fadeTime = 300;

/* Assign actions */
$('.quantity input').change(function() {
  updateQuantity(this);
});

/* Remove item from cart */
$('.remove-form').on('submit', function(e) {
  e.preventDefault(); 

  var form = $(this);
  var actionUrl = form.attr('action');

  $.ajax({
      url: actionUrl,
      method: 'POST',
      data: form.serialize(),
      success: function(response) {
            $.ajax({
              url: apiProductsCartUrl,
              method: 'GET',
              success: function(data) {
                  const countProducts = $('#products-cart-count');
                  countProducts.text(data);
              },
              error: function() {
                  console.error('Failed to fetch count products.');
              }
          });
          form.closest('.basket-product').slideUp(fadeTime, function() {
              $(this).remove();
              recalculateCart();
              updateSumItems();
          });
      },
      error: function(xhr, status, error) {
          Swal.fire('Erro!', "ocorreu um erro ao remover, tente novamente", 'error');
      }
  });
});

$(document).ready(function() {
  updateSumItems();
});


/* Recalculate cart */
function recalculateCart(onlyTotal) {
  var subtotal = 0;

  /* Sum up row totals */
  $('.basket-product').each(function() {
    subtotal += parseFloat($(this).children('.subtotal').text());
  });

  /* Calculate totals */
  var total = subtotal;

  /*If switch for update only total, update only total display*/
  if (onlyTotal) {
    /* Update total display */
    $('.total-value').fadeOut(fadeTime, function() {
      $('#basket-total').html(total.toFixed(2));
      $('.total-value').fadeIn(fadeTime);
    });
  } else {
    /* Update summary display. */
    $('.final-value').fadeOut(fadeTime, function() {
      $('#basket-subtotal').html(subtotal.toFixed(2));
      $('#basket-total').html(total.toFixed(2));
      if (total == 0) {
        $('.checkout-cta').fadeOut(fadeTime);
      } else {
        $('.checkout-cta').fadeIn(fadeTime);
      }
      $('.final-value').fadeIn(fadeTime);
    });
  }
}

/* Update quantity */
function updateQuantity(quantityInput) {
  /* Calculate line price */
  var productRow = $(quantityInput).parent().parent().parent();
  var price = parseFloat(productRow.children('.price').text());
  var quantity = $(quantityInput).val();
  var linePrice = price * quantity;

  /* Update line price display and recalc cart totals */
  productRow.children('.subtotal').each(function() {
    $(this).fadeOut(fadeTime, function() {
      $(this).text(linePrice.toFixed(2));
      recalculateCart();
      $(this).fadeIn(fadeTime);
    });
  });

  productRow.find('.item-quantity').text(quantity);
  updateSumItems();
}

function updateSumItems() {
  var sumItems = 0;
  $('.quantity-control-cart .quantity-field').each(function() {
      sumItems += parseInt($(this).val());
  });
  // console.log(sumItems);
  $('.total-items').text(sumItems);
}

$('.quantity-btn-cart').click(function() {
  var btn = $(this);
  var quantityInput = btn.siblings('.quantity-field');
  var currentValue = parseInt(quantityInput.val());
  var form = btn.closest('form');

  if (btn.hasClass('increase-qty')) {
      currentValue = currentValue + 1
      quantityInput.val(currentValue);
  } else if (btn.hasClass('decrease-qty')) {
      if (currentValue > 1) {
          currentValue = currentValue - 1
          quantityInput.val(currentValue);
      }
  }

  formData = new FormData(form[0]);

  $.ajax({
      url: attProductUrl,
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
          // console.log(response);
          if (response.success === true) {
              updateQuantity(quantityInput);
          } else {
            if (btn.hasClass('increase-qty')) {
              quantityInput.val(currentValue - 1);
            } else if (btn.hasClass('decrease-qty')) {
                quantityInput.val(currentValue + 1);
            }
            Swal.fire('Erro!', response.reason, 'error');
          }
      },
      error: function(xhr, status, error) {
          if (btn.hasClass('increase-qty')) {
              quantityInput.val(currentValue - 1);
          } else if (btn.hasClass('decrease-qty')) {
              quantityInput.val(currentValue + 1);
          }
          Swal.fire('Erro!', "ocorreu um erro, tente novamente", 'error');
      }
  });
});