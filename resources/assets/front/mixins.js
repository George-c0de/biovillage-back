// Форматирование даты и времени -----------------------------------------------
export const formatDateTime = {
    methods: {
        formatDate (date) {
            date = new Date(date)
            let d = ('0' + date.getDate()).slice(-2)
            let m = date.getMonth() + 1
            let y = date.getFullYear()
            return d + '.' + m + '.' + y
        },
        formatTime (date) {
            date = new Date(date)
            let h = ('0' + date.getHours()).slice(-2)
            let m = ('0' + date.getMinutes()).slice(-2)
            return h + ':' + m
        }
    }
}
// -----------------------------------------------------------------------------
