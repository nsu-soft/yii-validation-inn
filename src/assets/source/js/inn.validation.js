yii.validation.inn = (value, messages, options) => {
  value = value.replace(/\D/g, '');

  if (options.skipOnEmpty && yii.validation.isEmpty(value)) {
    return;
  }

  if (typeof value !== 'string') {
    yii.validation.addMessage(messages, options.message, value);
    return;
  }

  const INN_INDIVIDUAL_LENGTH = 12;
  const INN_LEGAL_LENGTH = 10;

  if (![INN_INDIVIDUAL_LENGTH, INN_LEGAL_LENGTH].includes(value.length)) {
    yii.validation.addMessage(messages, options.message, value);
    return;
  }

  const multipliers = [7, 2, 4, 10, 3, 5, 9, 4, 6, 8];

  if (INN_INDIVIDUAL_LENGTH === value.length && !validateIndividual(value, multipliers)) {
    yii.validation.addMessage(messages, options.message, value);
  } else if (INN_LEGAL_LENGTH === value.length && !validateLegal(value, multipliers)) {
    yii.validation.addMessage(messages, options.message, value);
  }

  function validateIndividual(inn, multipliers) {
    const INN_LENGTH = 10;
    const checkDigit1 = calculateCheckDigit(inn, INN_LENGTH, multipliers);

    if (checkDigit1 !== parseInt(inn.slice(-2, -1))) {
      return false;
    }

    const firstMultiplier = 3;
    multipliers.unshift(firstMultiplier);
    const checkDigit2 = calculateCheckDigit(inn, INN_LENGTH + 1, multipliers);

    return checkDigit2 === parseInt(inn.slice(-1));
  }

  function validateLegal(inn, multipliers) {
    multipliers.shift();
    const INN_LENGTH = 9;
    const checkDigit = calculateCheckDigit(inn, INN_LENGTH, multipliers);

    return checkDigit === parseInt(inn.slice(-1));
  }

  function calculateCheckDigit(inn, length, multipliers) {
    let sum = 0;

    for (let i = 0; i < length; i++) {
      sum += parseInt(inn.slice(i, i + 1)) * multipliers[i];
    }

    return sum % 11 % 10;
  }
};
