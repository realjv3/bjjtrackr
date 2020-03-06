/**
 * Some authorization related helpers
 */

/**
 * Is the currently logged in user a super admin
 * @return {boolean | *}
 */
export function isSuperAdmin() {
    const userRoles = user().roles.map(role => role.id);
    return userRoles.includes(1);
}

/**
 * Is the currently logged in user an administrator
 * @return {boolean | *}
 */
export function isAdmin() {
    const userRoles = user().roles.map(role => role.id);
    return userRoles.includes(2);
}

/**
 * Is the currently logged in user an instructor
 * @return {boolean | *}
 */
export function isInstructor() {
    const userRoles = user().roles.map(role => role.id);
    return userRoles.includes(3);
}

/**
 * Is the currently logged in user a student
 * @return {boolean | *}
 */
export function isStudent() {
    const userRoles = user().roles.map(role => role.id);
    return userRoles.includes(4);
}

/**
 * Is the currently logged in user a student
 * @return {boolean | *}
 */
export function isStudentOnly() {
    return ! isSuperAdmin() && ! isAdmin() && ! isInstructor();
}

/**
 * AJAX request headers
 * @return {Object}
 */
export const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
    "X-Requested-With": "XMLHttpRequest",
    "X-CSRF-TOKEN": CSRFToken,
};
