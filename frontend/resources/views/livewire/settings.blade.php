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
                              <button class="btn btn-warning" id="php-start" wire:click.prevent="socketinit">Initialize PHP Socket</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body" style="position: relative;">
                        <h3 class="card-title">Api Key</h3>
                        <input type="text" class="form-control" value="{{ App\Models\ApiKey::find(1)->api_key ?? "" }}" readonly/>
                        <div class="row g-2 align-items-center mb-n3 mt-3">
                            <div class="col-6 col-sm-4 col-md-2 col-xl mb-3">
                              <button class="btn btn-success" wire:click.prevent="apigenerate">Generate</button>
                              <button class="btn btn-danger" wire:click.prevent="apirevoke">Revoke</button>
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
        var socket = io('{{ env("WA_SOCKET","") }}');
        socket.emit('bridgeinit',true);
        socket.emit('statcheck', true);
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
            $('#status').text(data);
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
            // if(boot){
            //     socket.emit('start-init', 'true')
            // }
            // socket.emit('scanqr', 'true')
            socket.emit('ready','true');
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
        socket.on('frombridge', (io)=>{
            switch(io){
            case 0:
                Swal.fire({
                    title: 'Error!',
                    text: 'Socket is not ready!',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                })
                break;
            default:
                Swal.fire('Hello '+io)
            break;
        }
        })
    </script>
    <script type="text/javascript">
    // Livewire.on('socketAlert', socket => {
    //     switch(socket){
    //         case "0":
    //             Swal.fire({
    //                 title: 'Error!',
    //                 text: 'Socket is not ready!',
    //                 icon: 'error',
    //                 confirmButtonText: 'Ok'
    //             })
    //             break;
    //         default:
    //             Swal.fire('Hello '+socket)
    //         break;
    //     }
    // })
    </script>
@endsection
