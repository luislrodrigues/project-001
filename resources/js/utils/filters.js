/**
 * Helpers para filtros que admiten uno o varios valores.
 * Los selects múltiples trabajan con arrays; la URL y el backend
 * pueden recibir un valor único o un array de valores.
 */

/**
 * Normaliza un valor de filtro a un array de strings no vacíos.
 * @param {unknown} value
 * @returns {string[]}
 */
export function toArray(value) {
    if (Array.isArray(value)) {
        return value
            .filter((v) => v !== '' && v !== null && v !== undefined)
            .map((v) => String(v));
    }
    if (value === '' || value === null || value === undefined) {
        return [];
    }
    return [String(value)];
}

/**
 * Agrega el filtro a los parámetros solo si tiene valores.
 * @param {Record<string, unknown>} target
 * @param {string} key
 * @param {unknown} value
 */
export function setArrayParam(target, key, value) {
    const values = toArray(value);
    if (values.length) {
        target[key] = values;
    }
}

/**
 * Construye el par de query string para un filtro múltiple usando `clave[]`,
 * de modo que al recargar la página el backend reciba todos los valores.
 * @param {string} key
 * @param {unknown} value
 * @returns {Record<string, string[]>}
 */
export function arrayQuery(key, value) {
    const values = toArray(value);
    return values.length ? { [`${key}[]`]: values } : {};
}

/**
 * Lee un filtro múltiple desde la query del router, aceptando tanto `clave[]`
 * (formato propio) como `clave` repetida o simple (enlaces antiguos).
 * @param {Record<string, unknown>} query
 * @param {string} key
 * @returns {string[]}
 */
export function readArrayQuery(query, key) {
    const bracketed = query?.[`${key}[]`];
    if (bracketed !== undefined) return toArray(bracketed);
    return toArray(query?.[key]);
}

/**
 * Elimina claves vacías (strings vacíos, null, undefined y arrays vacíos).
 * @param {Record<string, unknown>} query
 * @returns {Record<string, unknown>}
 */
export function cleanQuery(query) {
    const out = {};
    Object.keys(query).forEach((key) => {
        const value = query[key];
        if (Array.isArray(value)) {
            if (value.length) out[key] = value;
        } else if (value !== '' && value !== null && value !== undefined) {
            out[key] = value;
        }
    });
    return out;
}

/**
 * Agrupa llamadas seguidas en una sola ejecución.
 * @param {Function} fn
 * @param {number} wait
 * @returns {Function}
 */
export function debounce(fn, wait = 250) {
    let timer = null;
    const debounced = (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            timer = null;
            fn(...args);
        }, wait);
    };
    debounced.cancel = () => {
        clearTimeout(timer);
        timer = null;
    };
    return debounced;
}

export default { toArray, setArrayParam, arrayQuery, readArrayQuery, cleanQuery, debounce };
