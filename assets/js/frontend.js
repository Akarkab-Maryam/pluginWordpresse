jQuery(document).ready(function($) {
    
    // Gestion des actions frontend
    $('.btn-plugin-action').on('click', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var action = button.data('action');
        var postId = button.data('post');
        
        $.ajax({
            url: myPluginAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'my_plugin_frontend',
                plugin_action: action,
                post_id: postId,
                nonce: myPluginAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    button.addClass('active').text('✓ ' + button.text());
                } else {
                    alert('Erreur lors de l\'action');
                }
            }
        });
    });
    
    // Gestion des formulaires shortcode
    $('.plugin-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var formData = form.serialize();
        
        $.ajax({
            url: myPluginAjax.ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    form.find('.form-message').remove();
                    form.prepend('<div class="form-message success">Formulaire envoyé avec succès!</div>');
                    form[0].reset();
                    
                    var redirectUrl = form.find('[name="redirect_url"]').val();
                    if (redirectUrl) {
                        setTimeout(function() {
                            window.location.href = redirectUrl;
                        }, 1500);
                    }
                } else {
                    form.find('.form-message').remove();
                    form.prepend('<div class="form-message error">Erreur lors de l\'envoi</div>');
                }
            }
        });
    });
    
    // Animation des éléments
    $('.my-plugin-display').each(function() {
        $(this).fadeIn(500);
    });
    
});