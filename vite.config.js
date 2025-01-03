import { defineConfig } from "vite";
import react from '@vitejs/plugin-react';
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                
                // "resources/js/bootstrap.js",
            ],
            refresh: true,
        }),
        react(),
    ],
    server: {
        host: "0.0.0.0", // Allow Vite to be accessed from any IP
        port: 5173, // Ensure the port matches your Vite server configuration
        hmr: {
            host: "localhost", 
            // host: "192.168.33.11", // Adjust HMR host if necessary //
        },
        cors: {
            origin: "*",
            // origin: ["http://192.168.33.11:8000", "http://10.10.5.202:8000"],
            methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
            allowedHeaders: ["Content-Type", "Authorization"],
        },
    },
    //
    base: "/",
});
