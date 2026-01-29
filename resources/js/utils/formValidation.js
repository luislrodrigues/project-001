/**
 * Validación en frontend (mismas reglas que Livewire en main).
 * Retorna { valid: boolean, errors: { campo: [mensaje] } }.
 * Uso: const { valid, errors } = validateForm(form, rules); if (!valid) { errorsRef.value = errors; return; }
 */
const messages = {
  required: 'El campo es obligatorio.',
  email: 'El campo debe ser un correo válido.',
  min: (min) => `El campo debe tener al menos ${min} caracteres.`,
  max: (max) => `El campo no puede superar ${max} caracteres.`,
};

function isEmpty(value) {
  if (value === null || value === undefined) return true;
  if (typeof value === 'string') return value.trim() === '';
  if (Array.isArray(value)) return value.length === 0;
  return false;
}

function isValidEmail(value) {
  if (!value || typeof value !== 'string') return false;
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(value.trim());
}

/**
 * Reglas: { campo: 'required' | 'required|email' | { required: true, email: true } }
 * data: objeto con los valores (ej. form)
 */
export function validateForm(data, rules) {
  const errors = {};
  for (const [field, rule] of Object.entries(rules)) {
    const value = data[field];
    if (typeof rule === 'string') {
      if (rule.includes('required') && isEmpty(value)) {
        errors[field] = [messages.required];
      } else if (rule.includes('email') && !isEmpty(value) && !isValidEmail(value)) {
        errors[field] = [messages.email];
      }
    } else if (rule && typeof rule === 'object') {
      if (rule.required && isEmpty(value)) {
        errors[field] = [rule.message || messages.required];
      } else if (rule.email && !isEmpty(value) && !isValidEmail(value)) {
        errors[field] = [rule.message || messages.email];
      }
    }
  }
  return {
    valid: Object.keys(errors).length === 0,
    errors,
  };
}

export default validateForm;
