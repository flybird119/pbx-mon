function loadMore() {
    var arg = '';
    var append = false;
    var start = $('#start').val();
    var end = $('#end').val();
    var duration = $('select[name="duration"]').val();
    var caller = $('#caller').val();
    var called = $('#called').val();

    if (last > 0) {
        arg += 'id=' + last.toString();
        append = true;
    }

    if (append) {
        arg += '&start=' + start;
        append = true;
    } else {
        arg += 'start=' + start;
    }

    if (append) {
        arg += '&end=' + end;
        append = true;
    } else {
        arg += 'end=' + end;
    }

    if (append) {
        arg += '&duration=' + billsec;
        append = true;
    } else {
        arg += 'duration=' + billsec;
    }

    if (append) {
        arg += '&caller=' + caller;
        append = true;
    } else {
        arg += 'caller=' + caller;
    }

    if (append) {
        arg += '&called=' + called;
        append = true;
    } else {
        arg += 'called=' + called;
    }

    reqwest({
        url: '/cdr/ajxquery?' + arg,
        method: 'get',
        success: function (resp) {
            var obj = JSON.parse(resp);
            last = obj.last;
            for (var i in obj.data) {
                var text = '<tr>' +
                    '<td>' + obj.data[i].id + '</td>' +
                    '<td>' + obj.data[i].caller + '</td>' +
                    '<td>' + obj.data[i].called + '</td>' +
                    '<td>' + getForSeconds(obj.data[i].duration) + '</td>' +
                    '<td>' + obj.data[i].src_ip + '</td>' +
                    '<td>' + obj.data[i].rpf + '</td>' +
                    '<td>' + obj.data[i].create_time + '</td>' +
                    '<td><a href="javascript:;" onClick="show(' +
                    "'" + obj.data[i].file + "'" +
                    ')"><span class="glyphicon glyphicon-headphones" aria-hidden="true"></span> 试 听</a></td>' +
                    '<td><a href="/record/' + obj.data[i].file + '">本地下载</a></td></tr>';
                $(str).appendTo("#list");
            }
            if (obj.data.length < 45) {
                $("#loading").css("display","none");
            }
        }
    });
}

function getForSeconds(totalSeconds) {  
    if (totalSeconds < 86400) {  
        var dt = new Date("01/01/2000 0:00");  
        dt.setSeconds(totalSeconds);  
        return formatForDate(dt);  
    } else {  
        return null;  
    }  
}  

function formatForDate(dt) {  
    var h = dt.getHours(),  
        m = dt.getMinutes(),  
        s = dt.getSeconds(),  
        r = "";  
    if (h > 0) {  
        r += (h > 9 ? h.toString() : "0" + h.toString()) + ":";  
    } else {
        r += "00:";
    }
    r += (m > 9 ? m.toString() : "0" + m.toString()) + ":"  
    r += (s > 9 ? s.toString() : "0" + s.toString());  
    return r;  
}

