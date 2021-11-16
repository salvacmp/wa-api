<div class="flex-fill d-flex flex-column justify-content-center py-4">
    <div class="container-tight py-6">
        <div class="text-center mb-4">
            <a href="."><img src="//cdn.dsgroupmedia.com/logo/portbytefz.png" width="200px" alt="DSMG POS Logo"></a>
        </div>
        <form class="card card-md" wire:submit.prevent="login">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Login to your account</h2>
                <div class="mb-3">
                    <label class="form-label">E-Mail</label>
                    <input type="text" wire:model.defer="email" required class="form-control" placeholder="Enter email">
                </div>
                <div class="mb-2">
                    <label class="form-label">
                        Password
                    </label>
                    <div class="input-group input-group-flat">
                        <input type="password" wire:model.defer="password" class="form-control" placeholder="Password">

                    </div>
                </div>
                <div class="form-footer">
                    <input type="submit" value="Sign in" class="btn btn-primary w-100" />
                </div>
            </div>
        </form>
    </div>
</div>
