<style>
        @media (max-width: 768px) {
                .responsive-card {
                        min-height: 300px;
                }
        }

        .responsive-card {
                border-radius: 18px;
                flex: 1;
        }
</style>

<div style="display: flex; height: 100%; flex-direction: column;">
        <div class="card card-bordered responsive-card p-0 overflow-hidden">
                <div class="card-body p-0">
                        <img src="{{asset('assets/html-template/src/images/p2p_add.jpeg')}}"
                             class="w-100 h-100" 
                             style="object-fit: cover; object-position: center;" 
                             alt="Advertisement" onclick="window.location.href = '{{route("user.work.jobs", ["type_of_user" => "employer", "type_of_work" => "p2p"])}}'">
                </div>
        </div>
        <div class="card card-bordered responsive-card" style="margin-bottom: 25px;">
                <div class="card-body d-flex align-items-center justify-content-center" style="height: 100%">
                        <div class="text-center">
                                <!-- <img src="{{asset('assets/html-template/src/images/advertise.svg')}}"
                                        style="width: 100px;" alt=""> -->
                                <h5>Advertise here</h5>
                                <p style="color: #777;">Advertise your products or services here</p>
                        </div>
                </div>
        </div>
</div>