// vite.config.js
import { defineConfig } from "file:///D:/laragon/www/ub-attendance/node_modules/vite/dist/node/index.js";
import laravel from "file:///D:/laragon/www/ub-attendance/node_modules/laravel-vite-plugin/dist/index.js";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/css/app.css",
        "resources/js/app.js",
        "resources/js/bootstrap.js"
      ],
      refresh: true
    })
  ],
  server: {
    host: "0.0.0.0",
    // Allow Vite to be accessed from any IP
    port: 5173,
    // Ensure the port matches your Vite server configuration
    hmr: {
      host: "192.168.33.11"
      // Adjust HMR host if necessary
    },
    cors: {
      origin: "http://192.168.33.11:8000",
      //add always huhu
      methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
      allowedHeaders: ["Content-Type", "Authorization"]
    }
  },
  //
  base: "/"
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJEOlxcXFxsYXJhZ29uXFxcXHd3d1xcXFx1Yi1hdHRlbmRhbmNlXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJEOlxcXFxsYXJhZ29uXFxcXHd3d1xcXFx1Yi1hdHRlbmRhbmNlXFxcXHZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9EOi9sYXJhZ29uL3d3dy91Yi1hdHRlbmRhbmNlL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSBcInZpdGVcIjtcbmltcG9ydCBsYXJhdmVsIGZyb20gXCJsYXJhdmVsLXZpdGUtcGx1Z2luXCI7XG5cbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZyh7XG4gICAgcGx1Z2luczogW1xuICAgICAgICBsYXJhdmVsKHtcbiAgICAgICAgICAgIGlucHV0OiBbXG4gICAgICAgICAgICAgICAgXCJyZXNvdXJjZXMvY3NzL2FwcC5jc3NcIixcbiAgICAgICAgICAgICAgICBcInJlc291cmNlcy9qcy9hcHAuanNcIixcbiAgICAgICAgICAgICAgICBcInJlc291cmNlcy9qcy9ib290c3RyYXAuanNcIixcbiAgICAgICAgICAgIF0sXG4gICAgICAgICAgICByZWZyZXNoOiB0cnVlLFxuICAgICAgICB9KSxcbiAgICBdLFxuICAgIHNlcnZlcjoge1xuICAgICAgICBob3N0OiBcIjAuMC4wLjBcIiwgLy8gQWxsb3cgVml0ZSB0byBiZSBhY2Nlc3NlZCBmcm9tIGFueSBJUFxuICAgICAgICBwb3J0OiA1MTczLCAvLyBFbnN1cmUgdGhlIHBvcnQgbWF0Y2hlcyB5b3VyIFZpdGUgc2VydmVyIGNvbmZpZ3VyYXRpb25cbiAgICAgICAgaG1yOiB7XG4gICAgICAgICAgICBob3N0OiBcIjE5Mi4xNjguMzMuMTFcIiwgLy8gQWRqdXN0IEhNUiBob3N0IGlmIG5lY2Vzc2FyeVxuICAgICAgICB9LFxuICAgICAgICBjb3JzOiB7XG4gICAgICAgICAgICBvcmlnaW46IFwiaHR0cDovLzE5Mi4xNjguMzMuMTE6ODAwMFwiLCAvL2FkZCBhbHdheXMgaHVodVxuICAgICAgICAgICAgbWV0aG9kczogW1wiR0VUXCIsIFwiUE9TVFwiLCBcIlBVVFwiLCBcIkRFTEVURVwiLCBcIk9QVElPTlNcIl0sXG4gICAgICAgICAgICBhbGxvd2VkSGVhZGVyczogW1wiQ29udGVudC1UeXBlXCIsIFwiQXV0aG9yaXphdGlvblwiXSxcbiAgICAgICAgfSxcbiAgICB9LFxuICAgIC8vXG4gICAgYmFzZTogXCIvXCIsXG59KTtcblxuLy9heWF3IGthbGltdGUgYW5nIG9yaWdpbiB1ZyBobXIgaG9zdFxuLy8gIGhtcjoge1xuLy8gICBob3N0OiBcIjE5Mi4xNjguMzMuMTFcIiwgLy8gQWRqdXN0IEhNUiBob3N0IGlmIG5lY2Vzc2FyeVxuLy99LFxuXG4vLyBvcmlnaW46IFwiaHR0cDovLzE5Mi4xNjguMzMuMTE6ODAwMFwiLFxuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUE4USxTQUFTLG9CQUFvQjtBQUMzUyxPQUFPLGFBQWE7QUFFcEIsSUFBTyxzQkFBUSxhQUFhO0FBQUEsRUFDeEIsU0FBUztBQUFBLElBQ0wsUUFBUTtBQUFBLE1BQ0osT0FBTztBQUFBLFFBQ0g7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLE1BQ0o7QUFBQSxNQUNBLFNBQVM7QUFBQSxJQUNiLENBQUM7QUFBQSxFQUNMO0FBQUEsRUFDQSxRQUFRO0FBQUEsSUFDSixNQUFNO0FBQUE7QUFBQSxJQUNOLE1BQU07QUFBQTtBQUFBLElBQ04sS0FBSztBQUFBLE1BQ0QsTUFBTTtBQUFBO0FBQUEsSUFDVjtBQUFBLElBQ0EsTUFBTTtBQUFBLE1BQ0YsUUFBUTtBQUFBO0FBQUEsTUFDUixTQUFTLENBQUMsT0FBTyxRQUFRLE9BQU8sVUFBVSxTQUFTO0FBQUEsTUFDbkQsZ0JBQWdCLENBQUMsZ0JBQWdCLGVBQWU7QUFBQSxJQUNwRDtBQUFBLEVBQ0o7QUFBQTtBQUFBLEVBRUEsTUFBTTtBQUNWLENBQUM7IiwKICAibmFtZXMiOiBbXQp9Cg==
