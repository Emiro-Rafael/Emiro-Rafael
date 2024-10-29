const actionUrl = "/wp-admin/admin-ajax.php";
$(document).ready(function() {
    // stop videos from playing when modal is closed, sets header z-index back to default
    $('.modal.fade').on('hidden.bs.modal', function (e) {
        $('#unboxVidModal iframe').attr('src', $('#unboxVidModal iframe').attr('src'));
    });

    // change plus icon to minus when #readMoreAccordion is open
    $('#readMoreAccordionBtn').on('click', function (e) {
        if (!$('#readMoreCollapse').hasClass('show')) {
            $('#readMoreAccordionBtn').html('Read less <span class="h9 h7-xl ml-1 align-middle"><i class="fas fa-minus"></i></span>');
        } else {
            $('#readMoreAccordionBtn').html('Read more <span class="h9 h7-xl ml-1 align-middle"><i class="fas fa-plus"></i></span>');
        }
    });

    // change plus icon to minus when .size-accordions are open
    $('.size-btn').on('click', function (e) {
        let size = e.currentTarget.dataset.size
        if (!$(`#${size}Collapse`).hasClass('show')) {
            $(`#${size}AccordionBtn span`).html("<i class='fas fa-minus'></i>");
        } else {
            $(`#${size}AccordionBtn span`).html("<i class='fas fa-plus'></i>");
        }
    });

    // recipe video
    $('#openRecipeVid').on('click', function (e) {
        $('#thumbnail').addClass('open');
        $('#embedVid').addClass('open');
    });

    $('#closeRecipeVid').on('click', function (e) {
        $('#embedVid iframe').attr('src', $('#embedVid iframe').attr('src'));
        $('#embedVid').removeClass('open');
        $('#thumbnail').removeClass('open');
    });

    // recipe accordion
    $('#viewFullRecipeBtn').on('click', function (e) {
        $('#viewFullRecipeBtn').addClass('d-none');
        $('#viewLessRecipeBtn').removeClass('d-none');
    });

    $('#viewLessRecipeBtn').on('click', function (e) {
        $('#viewFullRecipeBtn').removeClass('d-none');
        $('#viewLessRecipeBtn').addClass('d-none');
    });

    // trivia questions
    let answers = $('form .single-answer');

    $(answers).on('click', function(e){
        let answer = e.currentTarget;

        let questionForm = $(answer).closest( "form.ajax_form" );

        $(questionForm).find("input[name='answer']").val( answer.dataset.answer );

        $(questionForm).submit();
        
        if (!$(answer).hasClass('correct')) {
            // show answer is incorrect
            $(answer).addClass('incorrect-answer text-white');
            $(answer).children('.user-percentage').addClass('incorrect-answer d-block').removeClass('d-none');;
            // show correct answer
            $(answer).siblings('.correct').addClass('correct-answer');
            $(answer).siblings('.correct').children('.user-percentage').addClass('correct-answer d-block').removeClass('d-none');
        } else {
            $(answer).addClass('correct-answer');
            $(answer).children('.user-percentage').addClass('correct-answer d-block').removeClass('d-none');
        }

        // show user-percentage for all answers
        $(answer).siblings().children('.user-percentage').addClass('d-block').removeClass('d-none');

        // disable all answer buttons
        $(answer).siblings().prop('disabled', true);
        $(answer).prop('disabled', true);
    });

    // snack poll
    let snackPollTriggers = $('.poll-modal-trigger');

    $(snackPollTriggers).on('click', function(e){
        var type;
        if ($(e.currentTarget).hasClass('best-btn')) {
            $('#snackPollCategory').text('Best');
            type = 'best';
        } else if ($(e.currentTarget).hasClass('worst-btn')) {
            $('#snackPollCategory').text('Worst');
            type = 'worst';
        } else if ($(e.currentTarget).hasClass('weird-btn')) {
            $('#snackPollCategory').text('Weirdest');
            type = 'weird';
        }

        $('#snackSuperlativeForm input[name="type"]').val(type);
        $('#snackSuperlativeForm button.single-snack').unbind('click');
        $('#snackSuperlativeForm button.single-snack').on('click', function(btn) {
            $('#snackSuperlativeForm input[name="snack_id"]').val( btn.currentTarget.dataset.snackid );
            $('#snackSuperlativeForm').submit();
            equalHeight(true);
        });
    });

    initializeStarReviews();
    document.body.addEventListener('containerLoaded', function(e) {
        initializeStarReviews();
    });

    const urlParams = new URLSearchParams( window.location.search );
    if(urlParams.has('ref') && urlParams.get('ref') == 'qr')
    {
        $('#signinModal').modal('show');
    }

    // add drink upgrade
    $('#addDrinkUpgrade').on('click', function(e) {
        $('#addDrinkUpgrade').attr('disabled', true);
        $('#addDrinkUpgrade').text('');
        $('#addDrinkUpgrade').html(`<div class="spinner-border text-light" role="status">
            <span class="sr-only">Loading...</span>
        </div>`);

        let postData = {
            action: 'add_drink',
            subscription_id: $("input[name='drinkless_subscription_id']").val(),
            old_plan: $("input[name='drinkless_subscription_plan']").val(),
            currency_code: $("input[name='user_currency_code']").val(),
            external_id: $("input[name='drinkless_subscription_stripe_id']").val(),
        };

        let originalText = 'Add Drink Upgrade';

        submitAjaxForm(actionUrl, postData, originalText, e.currentTarget);
    });
});



function initializeStarReviews()
{
    // review star btns
    let starBtns = $('.review-stars .star-btn');
    let rating = 0;
    $(starBtns).unbind('click');
    $(starBtns).on('click', function(e){
        let star = e.currentTarget;
        let parent = star.parentNode;

        parent.style.opacity = 0.2;

        // The equivalent of parent.children.indexOf(child)
        rating = Array.prototype.indexOf.call(parent.children, star) + 1;

        $(star).parent().children().each( function(idx, elem) {
            if(idx < rating)
            {
                $(elem).addClass('full').removeClass('empty');
            }
            else
            {
                $(elem).removeClass('full').addClass('empty');
            }
        });
        
        
        let postData = {
            "rating": rating,
            "snack_id": parent.dataset.snackid,
            "action": 'add-rating'
        };

        submitAjaxForm("/wp-admin/admin-ajax.php", postData, null, null);
            
        parent.style.opacity = 1;
    });
}