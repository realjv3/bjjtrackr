/**
 * Convert passed MySQL datetime string to 'YYYY-MM-DD'
 *
 * @param {string} dateTime
 * @return {string}
 */
export function dateTimeToYMD(dateTime) {

    if ( ! dateTime.match(new RegExp(/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.000000Z/))) {
        return 'Invalid dateTime';
    }
    let
        d = new Date(dateTime),
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
 * @param {string} dateTime
 * @return {string}
 */
export function dateTimeTo24Time(dateTime) {

    if ( ! dateTime.match(new RegExp(/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.000000Z/))) {
        return 'Invalid dateTime';
    }
    const d = new Date(dateTime);
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
        if ( ! utcDateTime.match(new RegExp(/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.000000Z/))) {
            return 'Invalid dateTime';
        }
        return new Date(utcDateTime).toLocaleString();
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
