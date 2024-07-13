import tippy from "tippy.js";
import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import flatpickr from "flatpickr";

$("#rangeDate").flatpickr({
    mode: "range",
    dateFormat: "Y-m-d",
});

import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
