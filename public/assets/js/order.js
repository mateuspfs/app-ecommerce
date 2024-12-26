// SWIPER
document.addEventListener('DOMContentLoaded', function () {
  const swiper = new Swiper('.swiper', {
    direction: 'horizontal',
    loop: false,
    allowTouchMove: false,
    autoHeight: true,
    pagination: {
      el: ".swiper-pagination",
      type: "progressbar",
    },
    navigation: {
      nextEl: ".next-button",
      prevEl: ".prev-button",
    }
  });
});

// PRODUCTS

/* Set values + misc */
var promoCode;
var promoPrice;
var fadeTime = 300;


/* Remove item from cart */
$('.remove-form').on('submit', function (e) {
  e.preventDefault();

  var form = $(this);
  var actionUrl = form.attr('action');

  $.ajax({
    url: actionUrl,
    method: 'POST',
    data: form.serialize(),
    success: function (response) {
      $.ajax({
        url: apiProductsCartUrl,
        method: 'GET',
        success: function (data) {
          const countProducts = $('#products-cart-count');
          countProducts.text(data);
        },
        error: function () {
          console.error('Failed to fetch count products.');
        }
      });
      form.closest('.basket-product').slideUp(fadeTime, function () {
        $(this).remove();
        recalculateCart();
        updateSumItems();
      });
    },
    error: function (xhr, status, error) {
      Swal.fire('Erro!', "ocorreu um erro ao remover, tente novamente", 'error');
    }
  });
});

$(document).ready(function () {
  updateSumItems();
});

// Apply Cupom

$('.promo-code-cta').click(function () {

  promoCode = $('#promo-code').val();

  $.ajax({
    url: apiVerifyCupom,
    method: 'POST',
    data: { cupom: promoCode },
    success: function (response) {
      if (response.valid === false) {
        Swal.fire('Cupom Inválido!', response.reason, 'error');
        $('#promo-code').val('');
      } else {
        promoPrice = response.discount;
        promoType = response.type;

        if (promoPrice) {
          $('.summary-promo').removeClass('hide');
          if (promoType === 'p') {
            $('.promo-value').text(parseInt(promoPrice) + '%');
          } else {
            $('.promo-value').text('R$' + promoPrice);
          }
          recalculateCart(true, promoType);
        }
      }
    },
    error: function () {
      console.log('Falha ao consultar se cupom é valido.');
    }
  });
});

$('#apply-credit-btn').click(function () {
  const creditAmountElement = document.getElementById('credit-amount');
  const creditUsageInput = document.getElementById('credit-usage');
  const basketTotalElement = document.getElementById('basket-total');

  let creditAmount = parseFloat(creditAmountElement.textContent.replace('R$', '').replace(',', '.'));
  let creditToUse = creditAmount;

  const basketTotal = parseFloat(basketTotalElement.textContent.replace('R$', '').replace(',', '.'));

  if (creditToUse > basketTotal) {
    creditToUse = basketTotal;
  }

  creditUsageInput.value = creditToUse.toFixed(2);

  creditAmountElement.textContent = 'R$ ' + (creditAmount - creditToUse).toFixed(2);
  $('.summary-credit').removeClass('hide');
  $('.credit-value').text('R$ ' + creditToUse.toFixed(2));

  recalculateCart();
});

/* Recalcular carrinho */
function recalculateCart(onlyTotal, promoType = null) {
  var subtotal = 0;

  /* Sum up row totals */
  $('.basket-product').each(function () {
    subtotal += parseFloat($(this).children('.subtotal').text());
  });

  /* Calculate totals */
  var total = subtotal;

  var promoPrice = extractNumber($('.promo-value').text());
  if (promoPrice) {
    if (promoType === 'p') {
      promoPrice = (subtotal * promoPrice) / 100;
    }
    total -= promoPrice;
    if (total < 0) total = 0;
  }

  let creditUsage = parseFloat($('#credit-usage').val().replace('R$', ''));
  if (creditUsage) {
    total -= creditUsage;
    if (total < 0) total = 0;
  }

  /* If switch for update only total, update only total display */
  if (onlyTotal) {
    /* Update total display */
    $('.total-value').fadeOut(fadeTime, function () {
      $('#basket-total').html(total.toFixed(2));
      $('.total-value').fadeIn(fadeTime);
    });
  } else {
    /* Update summary display */
    $('.final-value').fadeOut(fadeTime, function () {
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
  var productRow = $(quantityInput).parent().parent();
  var price = parseFloat(productRow.children('.price').text());
  var quantity = $(quantityInput).val();
  var linePrice = price * quantity;
  console.log(quantity);

  /* Update line price display and recalc cart totals */
  productRow.children('.subtotal').each(function () {
    $(this).fadeOut(fadeTime, function () {
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
  $('.quantity input').each(function () {
    sumItems += parseInt($(this).val());
  });
  $('.total-items').text(sumItems);
}

function extractNumber(text) {
  var match = text.match(/(\d+(\.\d+)?)/);
  return match ? parseFloat(match[0]) : null;
}

document.addEventListener('DOMContentLoaded', function () {
  const decreaseBtns = document.querySelectorAll('.decrease-qty');
  const increaseBtns = document.querySelectorAll('.increase-qty');

  decreaseBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      let quantityInput = btn.nextElementSibling;
      let currentValue = parseInt(quantityInput.value);
      if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
        updateQuantity(quantityInput);
      }
    });
  });

  increaseBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      let quantityInput = btn.previousElementSibling;
      let currentValue = parseInt(quantityInput.value);
      quantityInput.value = currentValue + 1;
      updateQuantity(quantityInput);
    });
  });
});

// VIACEP

function limpa_formulário_cep() {
  //Limpa valores do formulário de cep.
  document.getElementById('rua').value = ("");
  document.getElementById('bairro').value = ("");
  document.getElementById('cidade').value = ("");
  document.getElementById('uf').value = ("");
}

function meu_callback(conteudo) {
  if (!("erro" in conteudo)) {
    //Atualiza os campos com os valores.
    document.getElementById('rua').value = (conteudo.logradouro);
    document.getElementById('bairro').value = (conteudo.bairro);
    document.getElementById('cidade').value = (conteudo.localidade);
    document.getElementById('uf').value = (conteudo.uf);
  } //end if.
  else {
    //CEP não Encontrado.
    limpa_formulário_cep();
    Swal.fire('Cep não encontrado!', 'Revise se digitou correto', 'error');
  }
}

function pesquisacep(valor) {

  //Nova variável "cep" somente com dígitos.
  var cep = valor.replace(/\D/g, '');

  //Verifica se campo cep possui valor informado.
  if (cep != "") {

    //Expressão regular para validar o CEP.
    var validacep = /^[0-9]{8}$/;

    //Valida o formato do CEP.
    if (validacep.test(cep)) {

      //Preenche os campos com "..." enquanto consulta webservice.
      document.getElementById('rua').value = "...";
      document.getElementById('bairro').value = "...";
      document.getElementById('cidade').value = "...";
      document.getElementById('uf').value = "...";

      //Cria um elemento javascript.
      var script = document.createElement('script');

      //Sincroniza com o callback.
      script.src = 'https://viacep.com.br/ws/' + cep + '/json/?callback=meu_callback';

      //Insere script no documento e carrega o conteúdo.
      document.body.appendChild(script);

    } //end if.
    else {
      //cep é inválido.
      limpa_formulário_cep();
      Swal.fire('Cep Inválido!', 'Revise se digitou correto', 'error');
    }
  } //end if.
  else {
    //cep sem valor, limpa formulário.
    limpa_formulário_cep();
  }
};

// Criptografar do Cartão  
document.getElementById('cvv_c').addEventListener('blur', function () {
  const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
  if (paymentMethod === 'credit-card') {
    $.ajax({
      url: apiGetPublicKey,
      method: 'POST',
      success: function (response) {
        const cardNumber = document.getElementById('numero_c').value.replace(/\s+/g, ''); // Remove todos os espaços
        const expiry = document.getElementById('validade_c').value.split('/');
        const expMonth = expiry[0];
        const expYear = expiry[1];

        const card = PagSeguro.encryptCard({
          publicKey: response,
          holder: document.getElementById('nome_c').value,
          number: cardNumber,
          expMonth: expMonth,
          expYear: 20 + expYear,
          securityCode: document.getElementById('cvv_c').value
        });

        const encrypted = card.encryptedCard;
        const hasErrors = card.hasErrors;
        const errors = card.errors;

        if (!hasErrors) {
          document.getElementById('crypted_card').value = encrypted;
        } else {
          console.error(errors);
        }
      },
      error: function (xhr, status, error) {
        console.error('Erro ao obter chave pública');
      }
    });
  }
  console.log(document.getElementById('crypted_card').value);
});

document.getElementById('submit').addEventListener('click', function (event) {
  const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
  if (paymentMethod === 'credit-card') {
    const encrypted = document.getElementById('crypted_card').value;
    if (!encrypted) {
      event.preventDefault();
      Swal.fire('Aguarde validarmos seu cartão!', 'Revise se digitou corretamente', 'error');
    }
  }
});