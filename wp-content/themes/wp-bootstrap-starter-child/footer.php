<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WP_Bootstrap_Starter
 */
global $is_redesign_page;
global $hide_footer;
?>

<?php if(!$hide_footer): ?>
    <?php if(get_post_type( get_queried_object_id() ) == 'collection' || in_array('error404',get_body_class()) || get_page_template_slug() =='404.php'): ?>
    
        </div><!-- #content -->
        <?php get_template_part( 'footer-widget' ); ?>
        <footer id="colophon" class="site-footer bg-transparent <?php echo wp_bootstrap_starter_bg_class(); ?> mb-5 mb-md-0 pb-3" role="contentinfo">
            <?php get_template_part( 'partials/foot-color' ); ?>
        </footer><!-- #colophon -->
    <?php else : ?>
    
        </div><!-- #content -->
        <?php get_template_part( 'footer-widget' ); ?>
        <footer id="colophon" class="site-footer <?php echo wp_bootstrap_starter_bg_class(); ?> mb-5 mb-md-0 pb-3" role="contentinfo">
            <?php get_template_part( 'partials/foot' ); ?>
        </footer><!-- #colophon -->
    <?php endif; ?>
<?php endif; ?>
</div><!-- #page -->

<?php wp_footer(); ?>

<?php if($is_redesign_page): ?>
    <script>
      let validationErrors = {
        "required": "This field is required.",
        "invalid": "This field is invalid",
        "allowed_ext": "Allowed extensions: &1",
        "max_size": "Maximum file size: &1",
        "max_files": "Maximum number of files you can upload: &1",
        "minlength": "Minimum number of characters: &1",

        "firstname": {
          "required": "First name is required"
        },
        "lastname": {
          "required": "Last name is required"
        },
        "name": {
          "regex": "Only letters and spaces are allowed"
        },
        "email": {
          "regex": "Email must be a valid email address.",
          "required": "Email must be a valid email address."
        },
        "zipcode": {
          "required": "Zip code is required."
        },
        "language": {
          "required": "Language is required."
        },
        "city": {
          "required": "City is required.",
        },
        "phone": {
          "iti": ["Invalid number", "Invalid country code", "Few characters", "Many characters", "Invalid number" ],
          "required": "Phone number required"
        },
        "password": {
          "required": "The \"Password\" field is required.",
          "minlength": "The password must contain at least &1 characters",
          "regex": "Password must contain numbers and Latin letters."
        },
        "password_new": {
          "required": "The \"New Password\" field is required.",
          "minlength": "The password must contain at least &1 characters.",
          "regex": "The password must contain numbers and Latin letters."
        },
        "password_old": {
          "required": "The \"Current Password\" field is required.",
        },
        "password_repeat": {
          "required": "The \"Confirm password\" field is required.",
          "password_repeat": "Passwords do not match"
        },
        "email_or_customer": {
          "required": "Email address or id is required.",
          "email_or_customer": "Email address or id is invalid.",
        },


        "recipient_name": {
          "required": "Recipient name is required.",
        },
        "address": {
          "required": "Street address is required.",
        },
        "state": {
          "required": "State is required.",
        },
        "postal": {
          "required": "Postal code is required.",
        },
        "snacker_name": {
          "required": "Snacker name is required.",
        },
        "birthday": {
          "required": "Birthday is required.",
        },

        "subject": {
          "required": "The \"Subject\" field is required.",
        },
        "comment": {
          "required": "The \"Comment\" field is required.",
        },
        "mail_to": {
          "required": "The \"Mail to\" field is required.",
        },
        "replacement": {
          "required": "The \"Replacement reason\" field is required.",
        },
        "replacement_explain": {
          "required": "The \"Replacement explain\" field is required.",
        },
        "replacement_size": {
          "required": "The \"Replacement size\" field is required.",
        },
        "country": {
          "required": "The \"Country\" field is required.",
        }
      }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script src="<?=get_stylesheet_directory_uri()?>/assets/redesign/js/script.js"></script>
<?php else: ?>
<?php
global $user_data;
if(empty($user_data))
{
    get_template_part( 'modals/signin-modal' );
}
?>
<button id="appleid-signin" type="button" class="d-none"></button>
<?php endif; ?>
</body>
</html>