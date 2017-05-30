<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>test io</title>
    <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
    <script src="/js/socket.io/socket.io.js"></script>
    <script>
        var url = location.origin + ':8080' + '/';
        var socket = io(url);
        $(document).ready(function(){
            socket.on('test', function(){
                console.log('test');
            });
            $('button').click(function(){
                //TODO: send through ajax to php
                socket.emit('test', {test: 666});
            });
        });
    </script>
</head>
<body>
    <button>Click me!</button>
</body>
</html>
