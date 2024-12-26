const mascaraMoeda = (event) => {
  const onlyDigits = event.target.value
      .replace(/\D/g, '')
      .padStart(3, '0');
  const digitsFloat = onlyDigits.slice(0, -2) + '.' + onlyDigits.slice(-2);
  event.target.value = maskCurrency(digitsFloat);
}

const maskCurrency = (valor, locale = 'pt-BR') => {
  return new Intl.NumberFormat(locale, {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
  }).format(valor);
}

function maskDescricao(descricao) {
  const maxLength = 30;
  if (descricao.length > maxLength) {
      return descricao.substring(0, maxLength) + '...';
  } else {
      return descricao;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.input-money').forEach(input => {
    if (input.value !== '') {
      input.value = maskCurrency(parseFloat(input.value));
    }
    input.addEventListener('input', mascaraMoeda);
  });
});

