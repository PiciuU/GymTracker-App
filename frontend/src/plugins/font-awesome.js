import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

import { library } from '@fortawesome/fontawesome-svg-core';

import { faBars, faAngleUp, faPlus, faXmark, faPen, faPenToSquare, faTrash } from '@fortawesome/free-solid-svg-icons';

library.add(faBars, faAngleUp, faPlus, faXmark, faPen, faPenToSquare, faTrash);

export const fontAwesome = {
    install: (app) => {
        app.component('font-awesome-icon', FontAwesomeIcon);
    },
};