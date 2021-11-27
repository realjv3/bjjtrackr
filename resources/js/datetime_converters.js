/**
 * Convert passed MySQL datetime string to 'YYYY-MM-DD'
 *
 * @param {string} utcDateTime
 * @return {string}
 */
export function utcDateTimeToLocalYMD(utcDateTime) {

    if ( ! utcDateTime.match(new RegExp(/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/))) {
        return 'Invalid dateTime';
    }
    let
        d = new Date(utcDateTime + ' UTC'),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) {
        month = '0' + month;
    }
    if (day.length < 2){
        day = '0' + day;
    }

    return [year, month, day].join('-');
}

/**
 * Convert passed MySQL datetime string to 'HH-MM-SS'
 *
 * @param {string} utcDateTime
 * @return {string}
 */
export function utcDateTimeToLocal24Time(utcDateTime) {

    if ( ! utcDateTime.match(new RegExp(/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/))) {
        return 'Invalid dateTime';
    }
    const d = new Date(utcDateTime + ' UTC');
    return d.toLocaleTimeString(['De'], {hour12: false});
}

/**
 * Convert passed UTC MySQL datetime string to locale datetime string
 *
 * @param {string} utcDateTime
 * @return {string}
 */
export function utcDateTimeToLocal(utcDateTime) {

    if (utcDateTime) {
        if ( ! utcDateTime.match(new RegExp(/\d{4}-\d{2}-\d{2}\s?|T?\d{2}:\d{2}:\d{2}(\.\d{6}Z)?/g))) {
            return 'Invalid dateTime';
        }
        let tz = '';
        if ( ! utcDateTime.match(new RegExp(/\.\d{6}Z/))) {
            tz = '.000000Z';
        }
        return new Date(utcDateTime + tz).toLocaleString();
    }
}

/**
 * Converts MySQL time string to locale
 *
 * @param {string} time
 * @return {string}
 */
export function timeToLocale(time) {

    if (time) {
        if ( ! time.match(new RegExp(/\d{1}:\d{2}(:\d{2})*/))) {
            return 'Invalid time';
        }
        const date = new Date();
        date.setHours(time.slice(0, 2));
        date.setMinutes(time.slice(3, 5));
        date.setSeconds(time.length === 0 ? 0 : time.slice(7, 9));
        return date.toLocaleTimeString();
    }
}

/**
 * Adds minutes to date, timezone agnostic
 * @param date {Date}
 * @param minutes {Number}
 *
 * @return {Date}
 */
export function addMinutes(date, minutes) {

    return new Date(date.getTime() + minutes * 60000)
}
