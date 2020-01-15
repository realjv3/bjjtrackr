/**
 * Some authorization related helpers
 */

/**
 * Is the currently logged in user a super admin
 * @return {boolean | *}
 */
export function isSuperAdmin() {
    const userRoles = user.role.map(role => role.id);
    return userRoles.includes(1);
}

/**
 * Is the currently logged in user an administrator
 * @return {boolean | *}
 */
export function isAdmin() {
    const userRoles = user.role.map(role => role.id);
    return userRoles.includes(2);
}

/**
 * Is the currently logged in user an instructor
 * @return {boolean | *}
 */
export function isInstructor() {
    const userRoles = user.role.map(role => role.id);
    return userRoles.includes(3);
}

/**
 * Is the currently logged in user a student
 * @return {boolean | *}
 */
export function isStudent() {
    const userRoles = user.role.map(role => role.id);
    return userRoles.includes(4);
}
