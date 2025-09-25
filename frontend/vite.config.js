import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import path from "path";

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [
        react({
            jsxRuntime: "automatic",
            jsxImportSource: "react",
        }),
    ],
    server: {
        port: 3000,
        open: true,
        cors: true,
        // Optimasi untuk development
        hmr: {
            overlay: false, // Disable error overlay untuk performa lebih baik
        },
        // Pre-bundle dependencies untuk loading yang lebih cepat
        optimizeDeps: {
            include: ["react", "react-dom", "mapbox-gl", "recharts"],
        },
    },
    build: {
        outDir: "dist",
        sourcemap: false, // Disable sourcemap untuk build yang lebih cepat
        // Optimasi chunk splitting
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ["react", "react-dom"],
                    mapbox: ["mapbox-gl"],
                    charts: ["recharts"],
                },
            },
        },
        // Optimasi minification
        minify: "terser",
        terserOptions: {
            compress: {
                drop_console: true, // Remove console.log in production
                drop_debugger: true,
            },
        },
    },
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "./src"),
            "@components": path.resolve(__dirname, "./src/components"),
        },
    },
    // Optimasi untuk development
    esbuild: {
        logOverride: { "this-is-undefined-in-esm": "silent" },
    },
    // Optimasi CSS
    css: {
        devSourcemap: false,
    },
});
