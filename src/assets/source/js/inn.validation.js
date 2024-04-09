yii.validation.inn = (value, messages, options) => {
  value = value.replace(/\D/g, '');

  if (options.skipOnEmpty && yii.validation.isEmpty(value)) {
    return;
  }

  if (typeof value !== 'string') {
    yii.validation.addMessage(messages, options.message, value);
    return;
  }

  const TYPE_ANY = 0;
  const TYPE_INDIVIDUAL = 1;
  const TYPE_LEGAL = 2;

  if (TYPE_ANY === options.type && !validateIndividual(value) && !validateLegal(value)) {
    yii.validation.addMessage(messages, options.message, value);
  } else if (TYPE_INDIVIDUAL === options.type && !validateIndividual(value)) {
    yii.validation.addMessage(messages, options.message, value);
  } else if (TYPE_LEGAL === options.type && !validateLegal(value)) {
    yii.validation.addMessage(messages, options.message, value);
  }

  function validateIndividual(inn) {
    if (12 !== inn.length) {
      return false;
    }

    const multipliers = getMultipliers();
    const checkDigit1 = calculateCheckDigit(inn, 10, multipliers);

    if (checkDigit1 !== parseInt(inn.slice(-2, -1))) {
      return false;
    }

    const firstMultiplier = 3;
    multipliers.unshift(firstMultiplier);
    const checkDigit2 = calculateCheckDigit(inn, 11, multipliers);

    return checkDigit2 === parseInt(inn.slice(-1));
  }

  function validateLegal(inn) {
    if (10 !== inn.length) {
      return false;
    }

    const multipliers = getMultipliers();
    multipliers.shift();
    const checkDigit = calculateCheckDigit(inn, 9, multipliers);

    return checkDigit === parseInt(inn.slice(-1));
  }

  function getMultipliers() {
    return [7, 2, 4, 10, 3, 5, 9, 4, 6, 8];
  }

  function calculateCheckDigit(inn, length, multipliers) {
    let sum = 0;

    for (let i = 0; i < length; i++) {
      sum += parseInt(inn.slice(i, i + 1)) * multipliers[i];
    }

    return sum % 11 % 10;
  }
};
