/**
 * Some authorization related helpers
 */

/**
 * Is the user a super admin
 * @param {Object} user
 * @return {boolean | *}
 */
export function isSuperAdmin(user) {
    const userRoles = user.roles.map(role => role.id);
    return userRoles.includes(1);
}

/**
 * Is the user an administrator
 * @param {Object} user
 * @return {boolean | *}
 */
export function isAdmin(user) {
    const userRoles = user.roles.map(role => role.id);
    return userRoles.includes(2);
}

/**
 * Is the user an instructor
 * @param {Object} user
 * @return {boolean | *}
 */
export function isInstructor(user) {
    const userRoles = user.roles.map(role => role.id);
    return userRoles.includes(3);
}

/**
 * Is the user a student
 * @param {Object} user
 * @return {boolean | *}
 */
export function isStudent(user) {
    const userRoles = user.roles.map(role => role.id);
    return userRoles.includes(4);
}

/**
 * Is the user a student
 * @param {Object} user
 * @return {boolean | *}
 */
export function isStudentOnly(user) {
    return ! isSuperAdmin(user) && ! isAdmin(user) && ! isInstructor(user);
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
