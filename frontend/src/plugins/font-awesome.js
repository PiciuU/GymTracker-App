/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

import { library } from '@fortawesome/fontawesome-svg-core';

/* import specific icons */
import { faBars, faAngleUp, faPen, faPlus, faXmark } from '@fortawesome/free-solid-svg-icons';

/* add icons to the library */
library.add(faBars, faAngleUp, faPen, faPlus, faXmark);

export const fontAwesome = {
    install: (app) => {
        app.component('font-awesome-icon', FontAwesomeIcon);
    },
};