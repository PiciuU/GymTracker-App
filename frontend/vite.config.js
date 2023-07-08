import { fileURLToPath, URL } from 'node:url'

import { defineConfig, loadEnv } from 'vite'
import vue from '@vitejs/plugin-vue'

import { VitePWA } from 'vite-plugin-pwa'

// https://vitejs.dev/config/
export default defineConfig(({ command, mode }) => {
    const env = loadEnv(mode, process.cwd())

    return {
        plugins: [
            vue(),
            VitePWA({
                registerType: 'autoUpdate',
                injectRegister: 'auto',
                workbox: {
                    cleanupOutdatedCaches: true,
                    globPatterns: ['**/*.{js,css,html,ico,png,svg,json,vue,txt,woff2}']
                },
                manifest: {
                    "theme_color": "#202020",
                    "background_color": "#121212",
                    "display": "standalone",
                    "scope": env.VITE_APP_PATH,
                    "start_url": env.VITE_APP_PATH,
                    "name": "GymTracker",
                    "short_name": "GymTracker",
                    "description": "Monitor and track your training progress with an innovative workout app.",
                    "icons": [
                        {
                            "src": `${env.VITE_APP_PATH}icons/icon-192x192.png`,
                            "sizes": "192x192",
                            "type": "image/png"
                        },
                        {
                            "src": `${env.VITE_APP_PATH}icons/icon-256x256.png`,
                            "sizes": "256x256",
                            "type": "image/png"
                        },
                        {
                            "src": `${env.VITE_APP_PATH}icons/icon-384x384.png`,
                            "sizes": "384x384",
                            "type": "image/png"
                        },
                        {
                            "src": `${env.VITE_APP_PATH}icons/icon-512x512.png`,
                            "sizes": "512x512",
                            "type": "image/png"
                        }
                    ]
                }
            })
        ],
        resolve: {
            alias: {
                '@': fileURLToPath(new URL('./src', import.meta.url))
            }
        },
        css: {
            preprocessorOptions: {
                scss: {
                    additionalData: `@import "@/assets/styles/variables.scss";`
                }
            }
        },
        base: env.VITE_APP_PATH
    }
})
