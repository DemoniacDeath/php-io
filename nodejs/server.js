var http = require('http');
var io = require('socket.io');

var server = http.createServer();
var ioServer = io(server);

var config = {
  url: '/backdoor',
  port: 8080
};

var backdoor = function(request, next, error) {
  if (!request.type)
    return error('request.type is missing');

  switch (request.type) {
    case 'join':
      if (!request.clientId) return error('request.clientId is missing');
      if (!request.roomId) return error('request.roomId is missing');
      var client = ioServer.sockets.connected[request.clientId];
      if (client) {
        client.join(request.roomId);
      }
      break;
    case 'leave':
      if (!request.clientId) return error('request.clientId is missing');
      if (!request.roomId) return error('request.roomId is missing');
      var client = ioServer.sockets.connected[request.clientId];
      if (client) {
        client.leave(request.roomId);
      }
      break;
    case 'send_to':
      if (!request.clientId) return error('request.clientId is missing');
      if (!request.message) return error('request.message is missing');
      var client = ioServer.sockets.connected[request.clientId];
      if (client) {
        client.emit(request.message, request.data);
      }
      break;
    case 'send_in':
      if (!request.roomId) return error('request.roomId is missing');
      if (!request.message) return error('request.message is missing');
      ioServer.sockets.in(request.roomId).emit(request.message, request.data);
      break;
    default:
      return error('request.type must be one of "join", "leave", "send_to" or "send_in"');
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

server.listen(config.port);
