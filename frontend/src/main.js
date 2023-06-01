import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

import './assets/main.css'

import { library } from '@fortawesome/fontawesome-svg-core'

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/* import specific icons */
import { faBars, faAngleUp, faPen, faPlus, faXmark } from '@fortawesome/free-solid-svg-icons'

/* add icons to the library */
library.add(faBars, faAngleUp, faPen, faPlus, faXmark)

const app = createApp(App)

app.use(router)

app.component('font-awesome-icon', FontAwesomeIcon);

app.mount('#app')
