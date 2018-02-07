define([], function() {
    return {
        unix1: function(unix) {
            var newDate = new Date();
            newDate.setTime(unix * 1000);
            var Y = newDate.getFullYear();
            var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
            var D = newDate.getDate() < 10 ? ('0' + newDate.getDate()) : newDate.getDate();
            var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
            var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
            var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
            return Y + '/' + M + '/' + D;
        },
        unix2: function(unix) {
            var newDate = new Date();
            newDate.setTime(unix * 1000);
            var Y = newDate.getFullYear();
            var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
            var D = newDate.getDate() < 10 ? ('0' + newDate.getDate()) : newDate.getDate();
            var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
            var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
            var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
            return Y + '/' + M + '/' + D + ' ' + h + ':' + m + ':' + s;
        },
        unix3: function(unix) {
            var newDate = new Date();
            newDate.setTime(unix * 1000);
            var Y = newDate.getFullYear();
            var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
            var D = newDate.getDate() < 10 ? ('0' + newDate.getDate()) : newDate.getDate();
            var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
            var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
            var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
            return Y + '/' + M + '/' + D + ' ' + h + ':' + m + ':' + s;
        }
    }
})