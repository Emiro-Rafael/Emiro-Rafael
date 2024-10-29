<div class="signin-modal modal fade" id="signinModal" tabindex="-1"
                aria-labelledby="signinModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered justify-content-center">
        <div class="modal-content p-3 p-lg-4 d-flex flex-column align-items-center">
            <div class="modal-header p-0 w-100 align-items-center">
                <h5 class="modal-title mb-3 h5 h4-lg font-weight-bold" id="signinModalLabel">Sign In</h5>
                <button type="button" class="close p-0 mb-0 mr-0" data-dismiss="modal" aria-label="Close">
                    <span class="h2 h1-lg text-secondary" aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <?php get_template_part( 'partials/login-form' );?>

        </div>
    </div>
</div> 