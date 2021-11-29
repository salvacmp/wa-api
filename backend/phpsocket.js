/**
 * phpsocket.js File
 * PHP Socket server file
 * 
 * @warning Don't open this socket to public!! Only for PHP API Socket
 * @author Salvatore Cahyo <salva@dsgroupmedia.com>
 * @copyright Copyright (c) 2021 Salvatore Cahyo
 */

const {
    WAConnection,
    MessageType,
    Mimetype,
    ReconnectMode,
} = require("@adiwajshing/baileys");
const http = require("http");
const server = http.createServer();
const fs = require("fs");
const qrcode = require('qrcode');
const io = require("socket.io")(server);
const { phoneNumberFormatter } = require('./helpers/formatter');
const axios = require('axios');
const ioclient = require("socket.io-client");


/**
 * Config Here!
 */
const port = 17355;

/**
 * Initialize WA
 */
const conn = new WAConnection();
conn.version = [3, 3234, 9];
conn.setMaxListeners(0)
conn.autoReconnect = ReconnectMode.onConnectionLost;
conn.browserDescription = ['Whatsapp API', 'Chrome'];
const socketbridge = ioclient("ws://localhost:8005")
socketbridge.emit('bridgeinit', true)
socketbridge.emit('bridgetransfer', 0)
    // socketbridge.join('bridgenotif');

/**
 * Load Session
 */
const SESSION_FILE_PATH = './whatsapp-session.json';
let sessionCfg;
if (fs.existsSync(SESSION_FILE_PATH)) {
    sessionCfg = require(SESSION_FILE_PATH);
}

/**
 * Connect Function
 */
async function init() {
    if (conn.state != 'connecting') {
        fs.existsSync('./whatsapp-session.json') && conn.loadAuthInfo('./whatsapp-session.json');
        await conn.connect({ timeoutMs: 30000 });
        const authInfo = conn.base64EncodedAuthInfo();
        fs.writeFileSync('./whatsapp-session.json', JSON.stringify(authInfo, null, '\t'))

        const nomors = phoneNumberFormatter(conn.user.jid);
        const nomor = nomors.replace(/\D/g, '');
        // io.emit('authenticated', conn.user)
        socketbridge.emit('bridgetransfer', conn.user.name + " (" + conn.user.jid + ")")
    }
}
/**
 * Socket Function
 */
io.on("connection", function(socket) {
    /**
     * PHP Socket Connection Initiator
     */
    socket.on('php-init', () => {
        console.log('php init')
        if (fs.existsSync('./whatsapp-session.json') && conn.state !== 'open') {
            init()
        } else if (!fs.existsSync('./whatsapp-session.json')) {
            conn.logout()
            conn.clearAuthInfo();
            conn.close();
            socketbridge.emit('bridgetransfer', 0)
        } else if (conn.state == "open") {
            socketbridge.emit('bridgetransfer', conn.user.name + " (" + conn.user.jid + ")")
        } else {
            socketbridge.emit('bridgetransfer', 0)
        }

    })

    /**
     * WA Status Check
     */
    socket.on('statcheck', () => {
        if (fs.existsSync('./whatsapp-session.json') && conn.state == 'open') {
            io.emit('message', conn.user.jid)
            socketbridge.emit('bridgetransfer', conn.user.name + " (" + conn.user.jid + ")")
        } else {
            socketbridge.emit('bridgetransfer', 0)

        }
    })

    /**
     *  Send non-image message
     */
    socket.on('sendmsg', async(data) => {
        let number;
        if (conn.state !== 'open' && fs.existsSync('./whatsapp-session.json')) {
            await init();
        } else if (!fs.existsSync('./whatsapp-session.json')) {
            io.emit('fail', true);
        }
        if (data.number.length > 15) {
            number = data.number
        } else {
            number = phoneNumberFormatter(data.number)
        }
        let check = await conn.isOnWhatsApp(number);
        if (!check) {
            console.log(check)
            io.emit('message', "fail")
            return false
        } else {
            console.log(data.message)
            conn.sendMessage(number, data.message, MessageType.text).then(response => {
                // io.emit('message', "success send to: " + number)
                // socketbridge.emit('bridgeinit', true)
            }).catch(err => {
                io.emit('message', "fail")
            })
        }
    })

    /**
     * API Check
     */
    socket.on('apicheck', async(data) => {
        if (!fs.existsSync('./whatsapp-session.json')) {
            io.emit('fail', true);
        } else {
            io.emit('success', true);
        }
    })
})

/**
 * Start Server
 */
server.listen(port, function() {
    console.log('App running on *: ' + port);
});

/**
 * Crash Protection
 */
process.on('uncaughtException', function(err) {
    console.error(err);
});