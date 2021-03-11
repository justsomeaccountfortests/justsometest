var instanse = false;
var state;
var mes;
var file;

function Chat() {
    this.update = updateChat;
    this.getState = getStateOfChat;
}

function getStateOfChat() {
    if (!instanse) {
        instanse = true;
        $.ajax({
            type: "POST",
            url: "process.php",
            data: {
                'function': 'getState',
                'file': file
            },
            dataType: "json",

            success: function (data) {
                state = data.state;
                instanse = false;
            },
        });
    }
}

function updateChat() {
    if (!instanse) {
        instanse = true;
        $.ajax({
            type: "POST",
            url: "process.php",
            data: {
                'function': 'update',
                'state': state,
                'file': file
            },
            dataType: "json",
            success: function (data) {
                if (data.text) {
                    for (var i = 0; i < data.text.length; i++) {
                        $('#notificationArea').append($("<p style='color: white'>" + data.text[i] + "</p>"));
                    }
                }
                document.getElementById('notificationArea').scrollTop = document.getElementById('notificationArea').scrollHeight;
                instanse = false;
                state = data.state;
            },
        });
    }
    else {
        setTimeout(updateChat, 1500);
    }
}