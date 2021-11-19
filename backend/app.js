const { Client, MessageMedia, ClientInfo } = require('whatsapp-web.js');
const express = require('express');
const { body, validationResult } = require('express-validator');
const socketIO = require('socket.io');
const qrcode = require('qrcode');
const http = require('http');
const fs = require('fs');
const { phoneNumberFormatter } = require('./helpers/formatter');
const fileUpload = require('express-fileupload');
const axios = require('axios');
const mime = require('mime-types');

const port = process.env.PORT || 8005;

const app = express();
const server = http.createServer(app);
const io = socketIO(server,{cors: {origin: "*",methods: ["GET", "POST"],credentials: true}});

app.use(express.json());
app.use(express.urlencoded({
  extended: true
}));
app.use(fileUpload({
  debug: true
}));

const SESSION_FILE_PATH = './whatsapp-session.json';
let sessionCfg;
if (fs.existsSync(SESSION_FILE_PATH)) {
  sessionCfg = require(SESSION_FILE_PATH);
}

app.get('/', (req, res) => {
  res.sendFile('index.html', {
    root: __dirname
  });
});

const client = new Client({
  restartOnAuthFail: true,
  puppeteer: {
    headless: true,
    args: [
      '--no-sandbox',
      '--disable-setuid-sandbox',
      '--disable-dev-shm-usage',
      '--disable-accelerated-2d-canvas',
      '--no-first-run',
      '--no-zygote',
      '--single-process',
      '--disable-gpu'
    ],
  },
  session: sessionCfg,
  qrTimeoutMs:10000,
  qrRefreshIntervalMs:9000
  // userAgent: 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/5361 (KHTML, like Gecko) Chrome/39.0.842.0 Mobile Safari/5361',
});

//TODO: Auto reply and webhook
client.on('message', msg => {
  // if (msg.body == '!ping') {
  //   msg.reply('pong');
  // } else if (msg.body == 'good morning') {
  //   msg.reply('selamat pagi');
  // } else if (msg.body == '!groups') {
  //   client.getChats().then(chats => {
  //     const groups = chats.filter(chat => chat.isGroup);

  //     if (groups.length == 0) {
  //       msg.reply('You have no group yet.');
  //     } else {
  //       let replyMsg = '*YOUR GROUPS*\n\n';
  //       groups.forEach((group, i) => {
  //         replyMsg += `ID: ${group.id._serialized}\nName: ${group.name}\n\n`;
  //       });
  //       replyMsg += '_You can use the group id to send a message to the group._'
  //       msg.reply(replyMsg);
  //     }
  //   });
  // }
});

client.initialize();


// Socket IO
io.on('connection', function(socket) {
  client.initialize();
  socket.emit('message', 'Socket ready! Press initialize to start.'); 
  socket.on('checkstat', function(socket){
    console.log('stat check init');
  })
  socket.on('php-init', (init)=>{
    console.log("PHP CONN" + init.init);
  })
  socket.on('start-init', function(socket){
    client.initialize();
    
  })
  socket.on('destroy', function(){
    socket.emit('message', 'Whatsapp is disconnected!');
    client.destroy();
    // client.initialize();
    
  })
  socket.on('scanqr',function(){
    socket.emit('message', 'scan qr initialized');
    console.log('scan qr initialized')
    client.on('qr', (qr) => {
      console.log('QR RECEIVED', qr);
      qrcode.toDataURL(qr, (err, url) => {
        socket.emit('qr', url);
        socket.emit('message', 'QR Code received, scan please!');
      });
    });
  })
  

  client.on('ready', () => {
    socket.emit('ready', 'Whatsapp is ready!');
    socket.emit('message', 'Whatsapp is ready!');
  });

  client.on('authenticated', (session) => {
    socket.emit('authenticated', 'Whatsapp is authenticated!');
    socket.emit('message', 'Whatsapp is authenticated!');
    console.log('AUTHENTICATED', session);
    sessionCfg = session;
    fs.writeFile(SESSION_FILE_PATH, JSON.stringify(session), function(err) {
      if (err) {
        console.error(err);
      }
    });
  });

  client.on('auth_failure', function(session) {
    socket.emit('message', 'Auth failure, restarting...');
  });

  client.on('disconnected', (reason) => {
    socket.emit('message', 'Whatsapp is disconnected!');
    fs.unlinkSync(SESSION_FILE_PATH, function(err) {
        if(err) return console.log(err);
        console.log('Session file deleted!');
    });
    client.destroy();
    client.initialize();

  });

});


const checkRegisteredNumber = async function(number) {
  const isRegistered = await client.isRegisteredUser(number);
  return isRegistered;
}

// Send message
// app.post('/send-message', [
//   body('number').notEmpty(),
//   body('message').notEmpty(),
// ], async (req, res) => {
//   const errors = validationResult(req).formatWith(({
//     msg
//   }) => {
//     return msg;
//   });

//   if (!errors.isEmpty()) {
//     return res.status(422).json({
//       status: false,
//       message: errors.mapped()
//     });
//   }

//   const number = phoneNumberFormatter(req.body.number);
//   const message = req.body.message;

//   const isRegisteredNumber = await checkRegisteredNumber(number);

//   if (!isRegisteredNumber) {
//     return res.status(422).json({
//       status: false,
//       message: 'The number is not registered'
//     });
//   }

//   client.sendMessage(number, message).then(response => {
//     res.status(200).json({
//       status: true,
//       response: response
//     });
//   }).catch(err => {
//     res.status(500).json({
//       status: false,
//       response: err
//     });
//   });
// });

// Send media
// TODO: send image
// app.post('/send-media', async (req, res) => {
//   const number = phoneNumberFormatter(req.body.number);
//   const caption = req.body.caption;
//   const fileUrl = req.body.file;

//   // const media = MessageMedia.fromFilePath('./image-example.png');
//   // const file = req.files.file;
//   // const media = new MessageMedia(file.mimetype, file.data.toString('base64'), file.name);
//   let mimetype;
//   const attachment = await axios.get(fileUrl, {
//     responseType: 'arraybuffer'
//   }).then(response => {
//     mimetype = response.headers['content-type'];
//     return response.data.toString('base64');
//   });

//   const media = new MessageMedia(mimetype, attachment, 'Media');

//   client.sendMessage(number, media, {
//     caption: caption
//   }).then(response => {
//     res.status(200).json({
//       status: true,
//       response: response
//     });
//   }).catch(err => {
//     res.status(500).json({
//       status: false,
//       response: err
//     });
//   });
// });

// const findGroupByName = async function(name) {
//   const group = await client.getChats().then(chats => {
//     return chats.find(chat => 
//       chat.isGroup && chat.name.toLowerCase() == name.toLowerCase()
//     );
//   });
//   return group;
// }

// Send message to group
// TODO: send message to group
// You can use chatID or group name, yea!
// app.post('/send-group-message', [
//   body('id').custom((value, { req }) => {
//     if (!value && !req.body.name) {
//       throw new Error('Invalid value, you can use `id` or `name`');
//     }
//     return true;
//   }),
//   body('message').notEmpty(),
// ], async (req, res) => {
//   const errors = validationResult(req).formatWith(({
//     msg
//   }) => {
//     return msg;
//   });

//   if (!errors.isEmpty()) {
//     return res.status(422).json({
//       status: false,
//       message: errors.mapped()
//     });
//   }

//   let chatId = req.body.id;
//   const groupName = req.body.name;
//   const message = req.body.message;

//   // Find the group by name
//   if (!chatId) {
//     const group = await findGroupByName(groupName);
//     if (!group) {
//       return res.status(422).json({
//         status: false,
//         message: 'No group found with name: ' + groupName
//       });
//     }
//     chatId = group.id._serialized;
//   }

//   client.sendMessage(chatId, message).then(response => {
//     res.status(200).json({
//       status: true,
//       response: response
//     });
//   }).catch(err => {
//     res.status(500).json({
//       status: false,
//       response: err
//     });
//   });
// });


server.listen(port, function() {
  console.log('App running on *: ' + port);
});

// Crash protection
process.on('uncaughtException', function (err) {
  console.error(err);
});