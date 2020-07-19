/**
 * Convert passed date string to 'YYYY-MM-DD'
 *
 * @param {string} date
 * @return {string}
 */
export function dateToLocalSql(date) {
    let
        d = new Date(date),
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
 * Convert passed time string to 'HH-MM-SS'
 *
 * @param time
 * @return {string}
 */
export function timeToLocalSql(time) {
    const d = new Date(time);
    return d.toLocaleTimeString([], {hour12: false});
}


export function utcToLocal(utcTime) {
    if (utcTime) {
        return new Date(utcTime + ' UTC').toLocaleString();
    }
}
