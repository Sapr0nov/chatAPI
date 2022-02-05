<!doctype html>
<html lang="ru">
<head>
	<title>Chat</title>
	<base href="https://chat.stacksite.ru/" />
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
</head>
<style>
#chatArea {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 600px;
    height: 40vh;
}
#chatArea > div{
    width: 100%;
}
#chatArea__history{
    height: 100%;
    width: 100%;
    outline:1px solid #ccc;
    display:flex;
    flex-direction: column;
    justify-content: start;
    align-items: start;
    overflow-y: scroll;
}
.chatArea__history--msg{
    width: 100%;
    display:flex;
    flex-direction: row;
}
.chatArea__history--msg p {
    padding: 0px 6px;
}
.chatArea__history--msg p:nth-child(1) {
    color: #999;
}
.chatArea__history--msg p:nth-child(2) {
    color: #333;
}
.chatArea__history--msg p:nth-child(3) {
    color: #000;
}
#chatArea__answer{
    position: relative;
    display: flex;
    flex-direction: row;
    height: 40px;
    width: 100%;
    outline:0px;
    border: 0px;
    padding: 0px;
    margin: 0px;
}
#chatArea__answer input{
    outline:1px solid #ccc;
    border: 0px;
    padding: 0px;
    margin: 0px;
    padding-left: 16px;
}
#chatArea__answer--name {
    width: 100px;
}
#chatArea__answer--name.disable {

    background-color: #00000011;
}
#chatArea__answer--btn {
    width: 90px;
    background: #ccc;
    color: #000;
}
input#chatArea__answer--btn {
    padding-left: 0px;
}
#chatArea__answer--msg {
    width: 100%;
}
#chatArea__answer--btn:hover{
    background: #666;
    color: #fff;
}
</style>

<body>

<div id="chatArea">
    <div id="chatArea__history"></div>
    <div id="chatArea__answer">
        <input id="chatArea__answer--name" class="disable" readonly="readonly" type="text" placeholder="имя" value="Гость">
        <input id="chatArea__answer--msg" type="text" placeholder="сообщение">
        <input id="chatArea__answer--btn" type="submit" value=">>" onclick="sendMsg()">
    </div>
</div>

<script>
/* format Date for SQL */
function twoDigits(d) {
    if(0 <= d && d < 10) return "0" + d.toString();
    if(-10 < d && d < 0) return "-0" + (-1*d).toString();
    return d.toString();
}

Date.prototype.toMysqlFormat = function() {
    return this.getFullYear() + "-" + twoDigits(1 + this.getMonth()) + "-" + twoDigits(this.getDate()) + " " + twoDigits(this.getHours()) + ":" + twoDigits(this.getMinutes()) + ":" + twoDigits(this.getSeconds());
};
/* Send message by Enter */
document.getElementById('chatArea').addEventListener('keydown',function(e) {
    if (e.keyCode == 13) {
        if (document.getElementById('chatArea__answer--msg').value != '') {
            sendMsg();
        }
        document.getElementById('chatArea__answer--name').blur();
    }
});
/* work with NikName field */
document.getElementById('chatArea__answer--name').addEventListener('click', function(e) {
    if (e.target.classList.contains("disable")) {
        e.preventDefault();
        e.stopPropagation();
        e.target.blur();
        return false;
    }
});
document.getElementById('chatArea__answer--name').addEventListener('change', function(e) {
    updateName(document.getElementById('chatArea__answer--name').value);
});

document.getElementById('chatArea__answer--name').addEventListener('dblclick', function(e) {
    e.target.classList.toggle('disable');
    if (e.target.hasAttribute('readonly')) {
        e.target.removeAttribute('readonly');
        document.getElementById('chatArea__answer--name').select();
    }else{
        e.target.setAttribute('readonly','readonly');
    }
});
document.getElementById('chatArea__answer--name').onblur = function(e) {
    e.target.classList.add('disable');
    e.target.setAttribute('readonly','readonly');
}

function scrollBottom() {
    element = document.getElementById('chatArea__history');
    element.scrollTop = element.scrollHeight;

}

function sendMsg() {
    var newBlock = document.createElement("div");

    var req = new XMLHttpRequest();
    req.open('POST', '/api/message_create.php', true);
    req.setRequestHeader('accept', 'application/json');
    req.type = 'json';
    req.responseType = 'json';

    time = new Date().toMysqlFormat();
    name = document.getElementById('chatArea__answer--name').value;
    body = document.getElementById('chatArea__answer--msg').value;

    var data = '';
    data = '{';
    data = data + '"toType": "' + 0 + '",';
    data = data + '"toId": "' + 0 + '",';
    data = data + '"time": "' + time + '",';
    data = data + '"jwt": "' + jwt + '",';
    data = data + '"body": "' + body + '",';
    data = data + '"attach": null}';

    document.getElementById('chatArea__answer--msg').value = '';
    req.send(data);
    req.onreadystatechange = function () {
        if (req.readyState === 4) {
            if (req.status == 200 && req.status < 300) {
                /* message send ok */
                var newBlock = document.createElement("div");
                newBlock.classList.add('chatArea__history--msg');
                newBlock.innerHTML =  "<p>" + time.slice(11) + "</p><p>" + name + "</p><p>" + body + "</p>";
                document.getElementById('chatArea__history').appendChild(newBlock);
                scrollBottom();
            }
        }
    }
}

function updateName() {

    var req = new XMLHttpRequest();
    req.open('POST', '/api/user_update.php', true);
    req.setRequestHeader('accept', 'application/json');
    req.type = 'json';
    req.responseType = 'json';

    time = new Date().toMysqlFormat();
    newName = document.getElementById('chatArea__answer--name').value;

    var  data = '{"jwt": "' + jwt + '", "firstname": "' + newName + '"}';
    req.send(data);
    req.onreadystatechange = function () {
        if (req.readyState === 4) {
            if (req.status == 200 && req.status < 300) {
                setCookie('chatName', newName, {secure: true, 'max-age': 36000});
                setCookie('jwt', req.response['jwt'], {secure: true, 'max-age': 36000});
            }
        }
    }
}

function getMsg() {
    
    var req = new XMLHttpRequest();
    req.open('POST', '/api/message_read.php', true);
    req.setRequestHeader('accept', 'application/json');
    req.type = 'json';
    req.responseType = 'json';

    var data = JSON.stringify({"jwt": jwt,"time": time});

    time = new Date().toMysqlFormat();

    req.send(data);
    req.onreadystatechange = function () {
        if (req.readyState === 4) {
            if (req.status == 200 && req.status < 300) {
                var arrMsg = req.response['messages'];  
                
                if (Array.isArray(arrMsg) && arrMsg.length > 0) {
                    arrMsg.forEach(function(msg) {
                        var newBlock = document.createElement("div");
                        newBlock.classList.add('chatArea__history--msg');

                        newBlock.innerHTML =  "<p>" + msg.time.slice(11) + "</p><p>" + msg.firstname + "</p><p>" + msg.body + "</p>";
                        document.getElementById('chatArea__history').appendChild(newBlock);
                        scrollBottom();
                    });                
                }
            }
        }
    }
}

function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

function setCookie(name, value, options = {}) {

    options = {
    path: '/',
    // при необходимости добавьте другие значения по умолчанию
    ...options
    };

    if (options.expires instanceof Date) {
    options.expires = options.expires.toUTCString();
    }

    let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

    for (let optionKey in options) {
    updatedCookie += "; " + optionKey;
    let optionValue = options[optionKey];
    if (optionValue !== true) {
        updatedCookie += "=" + optionValue;
    }
    }

    document.cookie = updatedCookie;
}

function deleteCookie(name) {
  setCookie(name, "", {
    'max-age': -1
  })
}

function chatInit() {
    var req = new XMLHttpRequest();
    req.open('POST', '/api/user_create.php', true);
    req.setRequestHeader('accept', 'application/json');
    req.type = 'json';
    req.responseType = 'json';

    var data = JSON.stringify({"firstname":"Гость","email":"guest@guest","password":""});

    time = new Date().toMysqlFormat();

    req.send(data);
    req.onreadystatechange = function () {
        if (req.readyState === 4) {
            if (req.status == 200 && req.status < 300) {
                var uid = req.response['chat'].uid;  
                setCookie('chatID', uid, {secure: true, 'max-age': 36000});
                setCookie('chatName', req.response['chat'].name, {secure: true, 'max-age': 36000});
                setCookie('jwt', req.response['chat'].jwt, {secure: true, 'max-age': 36000});
                uid = getCookie('chatID');
                name = getCookie('chatName');
                jwt = getCookie('jwt');
            }
        }
    }
};

let uid;

time = new Date();
time.setDate(time.getDate() - 1);
time = time.toMysqlFormat();

if (getCookie('chatID')) {
    uid = getCookie('chatID');
    name = getCookie('chatName');
    jwt = getCookie('jwt');

    document.getElementById('chatArea__answer--name').value = name;

}else{
    chatInit();
}

setInterval(() => {
    getMsg();
}, 2500);

</script>
</body>
</html>