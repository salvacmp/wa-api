<div>
    {{-- In work, do what you enjoy. --}}
    <div class="col-lg-6">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-body" style="position: relative;">
                        <h3 class="card-title">QR Scan</h3>
                        <div class="spinner-grow" id="loading" style="display: none;"></div>
                        <span id="status"></span>
                        <span id="preinit">Click initialize to start</span>
                        <img src="" alt="QR Code" id="qrcode" style="display: none;">
                        <div class="row g-2 align-items-center mb-n3 mt-3">
                            <div class="col-6 col-sm-4 col-md-2 col-xl mb-3">
                              <button class="btn btn-primary" id="init">Initialize</button>
                              <button class="btn btn-danger" id="logout">Logout</button>
                            </div>
                          </div>
                    </div>
                </div>
            </div>
      </div>
</div>
@section('script')
    <script>
        let boot = true;
        var socket = io("http://localhost:8005");
        $(document).ready(function() {

        });
        socket.on('message', function(msg) {
            // $('#qrcode').hide();
            console.log(msg)
            $('#status').text(msg);
            $('#preinit').hide();
            $('#loading').show();
        });

        socket.on('qr', function(src) {
            $('#loading').hide();
            $('#qrcode').attr('src', src);
            $('#qrcode').show();
        });

        socket.on('ready', function(data) {
            $('#qrcode').hide();
            $('#loading').hide();
        });

        socket.on('authenticated', function(data) {
            $('#qrcode').hide();
            $('#loading').hide();
        });
        $('#init').on('click', function() {
            // socket.emit('checkstat', 'true')
            $('#qrcode').hide();
            if(boot){
                socket.emit('start-init', 'true')
            }
            socket.emit('scanqr', 'true')

        });
        $('#logout').on('click', function() {
            boot = true;
            $('#qrcode').hide();
            socket.emit('destroy', 'true')
            $('#qrcode').hide();
            $('#loading').show();
        });
        socket.on("disconnect", (reason) => {
            $('#status').text("Socket Offline");
            $('#preinit').hide();
        });
        if(!socket.connected){
            $('#status').text("Socket Offline");
            $('#preinit').hide();
        }
    </script>
@endsection
