/**
 * Helper para leer respuestas de la API con estructura unificada.
 *
 * Backend devuelve:
 * - Éxito: { success: true, message?, data?, meta?, links? }
 * - Error: { success: false, message, errors?, code? }
 *
 * Uso:
 * - En then: const { data, message } = parseResponse(response);
 * - En catch: const { message, errors } = parseError(error);
 */

/**
 * Parsea una respuesta exitosa de axios.
 * @param {import('axios').AxiosResponse} response
 * @returns {{ success: true, data: any, message?: string, meta?: object, links?: object }}
 */
export function parseResponse(response) {
    const body = response?.data ?? {};
    return {
        success: body.success !== false,
        data: body.data ?? null,
        message: body.message ?? null,
        meta: body.meta ?? null,
        links: body.links ?? null,
    };
}

/**
 * Parsea un error de axios (respuesta 4xx/5xx o error de red).
 * @param {import('axios').AxiosError} error
 * @returns {{ success: false, message: string, errors: object, code: number }}
 */
export function parseError(error) {
    const data = error?.response?.data ?? {};
    const code = error?.response?.status ?? 500;

    return {
        success: false,
        message: data.message || error?.message || 'Error de conexión',
        errors: data.errors ?? {},
        code,
    };
}

/**
 * Obtiene los errores de validación como objeto campo -> primer mensaje (string).
 * Útil para asignar a errors.value en formularios Vue.
 * @param {import('axios').AxiosError} error
 * @returns {Record<string, string>}
 */
export function getValidationErrors(error) {
    const data = error?.response?.data;
    if (error?.response?.status !== 422 || !data?.errors) {
        return {};
    }
    const e = data.errors;
    return Object.fromEntries(
        Object.entries(e).map(([k, v]) => [k, Array.isArray(v) ? v[0] : String(v)])
    );
}

/**
 * Indica si la respuesta/error es de validación (422).
 * @param {import('axios').AxiosError} error
 * @returns {boolean}
 */
export function isValidationError(error) {
    return error?.response?.status === 422;
}

export default {
    parseResponse,
    parseError,
    getValidationErrors,
    isValidationError,
};
