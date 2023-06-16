/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

import { library } from '@fortawesome/fontawesome-svg-core';

/* import specific icons */
import { faBars, faAngleUp, faPlus, faXmark, faPen, faPenToSquare } from '@fortawesome/free-solid-svg-icons';

/* add icons to the library */
library.add(faBars, faAngleUp, faPlus, faXmark, faPen, faPenToSquare);

export const fontAwesome = {
    install: (app) => {
        app.component('font-awesome-icon', FontAwesomeIcon);
    },
};