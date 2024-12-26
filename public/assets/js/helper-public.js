const mascaraMoeda = (event) => {
  const onlyDigits = event.target.value.replace(/\D/g, '').padStart(3, '0');
  const digitsFloat = onlyDigits.slice(0, -2) + '.' + onlyDigits.slice(-2);
  event.target.dataset.value = digitsFloat; 
  event.target.value = maskCurrency(digitsFloat);
}

const maskCurrency = (valor, locale = 'pt-BR') => {
  return new Intl.NumberFormat(locale, {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
  }).format(valor);
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.input-money').forEach(input => {
    if (input.value !== '') {
      const numericValue = parseFloat(input.value.replace(/\./g, '').replace(',', '.'));
      input.value = maskCurrency(numericValue);
      input.dataset.value = numericValue;
    }
    input.addEventListener('input', mascaraMoeda);
  });

  document.querySelector('form').addEventListener('submit', (e) => {
    document.querySelectorAll('.input-money').forEach(input => {
      input.value = input.dataset.value; 
    });
  });
});