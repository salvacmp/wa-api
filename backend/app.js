/**
 * App.js File
 * Main WAPI Socket Server
 * 
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
const io = require("socket.io")(server, { cors: { origin: "*", methods: ["GET", "POST"], credentials: true } });
const { phoneNumberFormatter } = require('./helpers/formatter');


/**
 * Config Here!
 */
const port = 8005;

/**
 * Initialize WA
 */
const conn = new WAConnection();
conn.version = [3, 3234, 9];
conn.setMaxListeners(0)
conn.autoReconnect = ReconnectMode.onConnectionLost;
conn.browserDescription = ['Whatsapp API', 'Chrome'];

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
async function init(reset) {
    if (conn.state != 'connecting' && !reset) {
        fs.existsSync('./whatsapp-session.json') && conn.loadAuthInfo('./whatsapp-session.json');
        await conn.connect({ timeoutMs: 30000 });
        const authInfo = conn.base64EncodedAuthInfo();
        fs.writeFileSync('./whatsapp-session.json', JSON.stringify(authInfo, null, '\t'))
        io.emit('message', "Howdy, " + conn.user.name + " (" + conn.user.jid + ")")
        const nomors = phoneNumberFormatter(conn.user.jid);
        const nomor = nomors.replace(/\D/g, '');
        io.emit('authenticated', conn.user)
    } else if (reset) {
        fs.existsSync('./whatsapp-session.json') && conn.loadAuthInfo('./whatsapp-session.json');
        await conn.connect({ timeoutMs: 30000 });
        const authInfo = conn.base64EncodedAuthInfo();
        fs.writeFileSync('./whatsapp-session.json', JSON.stringify(authInfo, null, '\t'))
        const nomors = phoneNumberFormatter(conn.user.jid);
        const nomor = nomors.replace(/\D/g, '');
    }
}

/**
 * Socket Function
 */
io.on("connection", async function(socket) {
    /**
     * Join Notification Room
     */
    socket.join("bridgenotif");

    /**
     * PHP Socket Connection Check
     */
    socket.on('php-init', () => {
        console.log('PHP Init Startup')
            // socket.emit('message', 'Hello PHP! Here\'s info for you!');
    })

    /**
     * When ready emitted
     */
    socket.on('ready', () => {
        if (fs.existsSync('./whatsapp-session.json') && conn.state == 'open') {
            io.emit('authenticated', conn.user)
                // io.emit('message', 'Connected')

        } else {
            io.emit('loader', '')
            socket.emit('message', 'Please wait..')
            init()
        }
    })

    /**
     * When qr event emitted
     */
    conn.on("qr", (qr) => {
        socket.emit('message', 'Getting QR Code')
        qrcode.toDataURL(qr, (err, url) => {
            socket.emit('message', 'QR Code received, scan please!')
            socket.emit("qr", url);
        });
    });

    /**
     * Session Destroyer
     */
    socket.on('destroy', async() => {
        socket.emit('message', 'Logout')
        if (fs.existsSync("./whatsapp-session.json") && conn.state == "open") {
            conn.logout()
            conn.clearAuthInfo();
            conn.close();
            fs.unlinkSync("./whatsapp-session.json");
            socket.emit('message', 'Session destroyed')

        } else if (fs.existsSync("./whatsapp-session.json")) {
            await init(true);
            conn.logout()
            conn.clearAuthInfo();
            conn.close();
            fs.unlinkSync("./whatsapp-session.json");
            // conn.close();
        } else {
            conn.logout()
        }
    })

    /**
     * Status Check
     */
    socket.on('statcheck', () => {
        if (fs.existsSync('./whatsapp-session.json') && conn.state == 'open') {
            io.emit('message', 'Whatsapp API Running')
        } else {
            io.emit('message', 'Please Initialize Whatsapp')
        }
    })

    /**
     * On Disconnected
     */
    conn.on('close', (reason) => {
        // console.log(reason)
        // conn.close();
        io.emit('message', 'Whatsapp Disconnected')
        if (fs.existsSync('./whatsapp-session.json')) {
            fs.unlinkSync("./whatsapp-session.json");
            conn.logout()
            conn.clearAuthInfo();
        }

    })

    /**
     * Broadcast Message
     */
    socket.on('bridgetransfer', (data) => {
        io.emit('message', data)
        io.in("bridgenotif").emit('frombridge', data)
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