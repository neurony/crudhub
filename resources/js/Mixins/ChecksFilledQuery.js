export default {
    methods: {
        hasQueryFilled(query, ignore = []) {
            for (const key in query) {
                if (query.hasOwnProperty(key) && !ignore.includes(key) && query[key] !== null) {
                    return true;
                }
            }
            return false;
        }
    },
}
