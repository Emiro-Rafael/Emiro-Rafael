<!-- Unboxing Hero Video Modal -->
<div class="modal fade unboxing-hero-vid" id="unboxVidModal" tabindex="-1" role="dialog" aria-labelledby="heroVidModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered m-0 w-100" role="document">
        <div class="modal-content d-flex flex-column w-100 my-0 mx-auto">
            <div class="modal-header position-relative border-0">
                <button type="button" class="closeHeroVideo position-absolute p-0" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="hero-vid-container w-100"> 
                    <iframe class="responsive-iframe w-100 h-100" src="<?php echo $args->getSinglePostMetaByKey('hero-video');?>"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>