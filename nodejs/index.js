var http = require('http');
var io = require('socket.io');

var server = http.createServer();
var ioServer = io(server);

var config = {
  url: '/backdoor',
  port: 8080
};

var backdoor = function(request, next, error) {
  console.log(request);

  for (var id in ioServer.sockets.sockets) {
    ioServer.sockets.sockets[id].emit('test');
  }
  
  next();
};

server.on('request', (request, response) => {
  if (request.url === config.url) {
    var body = [];

    var resultHandler = (result) => {
      response.writeHead(200, {"Content-Type": "application/json"});
      response.write(JSON.stringify(result));
      response.end();
    };
    var errorHandler = (error) => {
      resultHandler({status: 'error', error: error});
    };
    var successHandler = () => {
      resultHandler({status: 'ok'});
    };
    request.on('data', (chunk) => {
      body.push(chunk);
    }).on('end', () => {
      try {
        var data = JSON.parse(Buffer.concat(body).toString());
        backdoor(data, successHandler, errorHandler);
      } catch (e) {
        errorHandler(e.message);
      }
    });
  }
});

ioServer.on('connection', (socket) => {
  console.log(socket.id);
});

server.listen(config.port, () => {
  console.log('listening on *:' + config.port);
});
