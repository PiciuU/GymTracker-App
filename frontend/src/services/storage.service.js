export const getCookie = (name) => {
    const cookieName = `${name}=`;
    const cookies = document.cookie.split(';');

    for (let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i];
        while (cookie.charAt(0) === ' ') {
            cookie = cookie.substring(1);
        }
        if (cookie.indexOf(cookieName) === 0) {
            return cookie.substring(cookieName.length, cookie.length);
        }
    }
    return null;
};

export const setCookie = (name, value, days = 365) => {
    const expirationDate = new Date();
    expirationDate.setTime(expirationDate.getTime() + days * 24 * 60 * 60 * 1000);
    const expires = `expires=${expirationDate.toUTCString()}`;
    document.cookie = `${name}=${value};${expires};path=/`;
};

export const deleteCookie = (name) => {
    document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
};

export const getLocalStorage = (name) => {
    return localStorage.getItem(name);
};

export const setLocalStorage = (name, value) => {
    localStorage.setItem(name, value);
};

export const deleteLocalStorage = (name) => {
    localStorage.removeItem(name);
};