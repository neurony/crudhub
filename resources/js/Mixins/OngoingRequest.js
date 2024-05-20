import { request } from "../constants.js";

export default {
    created() {
        this.$inertia.on('start', () => {
            request.ongoing = true
        })
        this.$inertia.on('finish', () => {
            request.ongoing = false
        })
    }
}
