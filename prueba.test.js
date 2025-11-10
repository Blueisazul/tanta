const sumar = require('./prueba.js');

test('suma de 2 + 3 debe ser 5', () => {
  expect(sumar(2, 3)).toBe(5);
});